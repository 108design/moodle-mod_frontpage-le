<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
defined('MOODLE_INTERNAL') || die();

/** Restores every activity with an independent page, revision ids, and file item ids. */
final class restore_frontpage_activity_structure_step extends restore_activity_structure_step {
    private ?int $newpageid = null;
    public array $ownedresources = [];
    private ?int $olddraftrevisionid = null;
    private ?int $oldpublishedrevisionid = null;

    protected function define_structure(): array {
        return $this->prepare_activity_structure([
            new restore_path_element('frontpage', '/activity/frontpage'),
            new restore_path_element('frontpage_ownedpage', '/activity/frontpage/ownedpage'),
            new restore_path_element('frontpage_ownedresource', '/activity/frontpage/ownedpage/ownedresources/ownedresource'),
            new restore_path_element('frontpage_revision', '/activity/frontpage/ownedpage/revisions/revision'),
        ]);
    }

    protected function process_frontpage(array $data): void {
        global $DB;
        $record = (object) $data;
        $oldid = $record->id;
        unset($record->id);
        if (!empty($record->embedincourse)) {throw new restore_step_exception('frontpage_unsupported_display');}
        $record->embedincourse = 0;
        $record->course = $this->get_courseid();
        $record->pageid = null;
        $record->timemodified = $this->apply_date_offset($record->timemodified);
        $newid = $DB->insert_record('frontpage', $record);
        $this->apply_activity_instance($newid);
        $this->set_mapping('frontpage', $oldid, $newid, true);
    }

    protected function process_frontpage_ownedpage(array $data): void {
        global $DB;
        $record = (object) $data;
        $oldid = (int) $record->id;
        $this->olddraftrevisionid = empty($record->draftrevisionid) ? null : (int) $record->draftrevisionid;
        $this->oldpublishedrevisionid = empty($record->publishedrevisionid)
            ? null : (int) $record->publishedrevisionid;
        unset($record->id, $record->draftrevisionid, $record->publishedrevisionid);
        $record->ownercomponent = 'mod_frontpage';
        $record->ownerinstanceid = $this->get_new_parentid('frontpage');
        $record->contextid = $this->task->get_contextid();
        $record->slug = $this->unique_slug((string) $record->slug);
        $record->createdby = $this->get_mappingid('user', $record->createdby, 0);
        $record->modifiedby = $this->get_mappingid('user', $record->modifiedby, 0);
        $record->timecreated = $this->apply_date_offset($record->timecreated);
        $record->timemodified = $this->apply_date_offset($record->timemodified);
        $this->newpageid = $DB->insert_record('local_frontpage_page', $record);
        $DB->set_field('frontpage', 'pageid', $this->newpageid,
            ['id' => $this->get_new_parentid('frontpage')]);
        $this->set_mapping('local_frontpage_page', $oldid, $this->newpageid);
    }

    protected function process_frontpage_revision(array $data): void {
        global $DB;
        $record = (object) $data;
        $oldid = (int) $record->id;
        unset($record->id);
        $record->pageid = $this->newpageid;
        $record->createdby = $this->get_mappingid('user', $record->createdby, 0);
        $record->publishedby = empty($record->publishedby) ? null
            : $this->get_mappingid('user', $record->publishedby, 0);
        $record->timecreated = $this->apply_date_offset($record->timecreated);
        $record->timepublished = empty($record->timepublished) ? null
            : $this->apply_date_offset($record->timepublished);
        $newid = $DB->insert_record('local_frontpage_revision', $record);
        $this->set_mapping('local_frontpage_revision', $oldid, $newid, true);
    }

    protected function process_frontpage_ownedresource(array $data): void {
        $data['createdby']=$this->get_mappingid('user',$data['createdby'],0);
        $this->task->ownedresources[]=$data;
    }
    protected function after_execute(): void {
        global $DB;
        $this->add_related_files('mod_frontpage', 'intro', null);
        // Moodle matches file areas exactly; discover the annotated source areas and admit only revision-owned ones.
        $annotatedareas = $DB->get_fieldset_select('backup_files_temp', 'DISTINCT filearea',
            'backupid = :backupid AND component = :component', [
                'backupid' => $this->get_restoreid(),
                'component' => 'local_frontpage',
            ]);
        $areas = array_values(array_unique(array_merge(
            \local_frontpage\local\revision_files::AREAS,
            array_filter($annotatedareas,
                [\local_frontpage\local\revision_files::class, 'is_hero_background_area'])
        )));
        sort($areas, SORT_STRING);
        foreach ($areas as $area) {
            $this->add_related_files('local_frontpage', $area, 'local_frontpage_revision');
        }
        if ($this->newpageid !== null) {
            $DB->update_record('local_frontpage_page', (object) [
                'id' => $this->newpageid,
                'draftrevisionid' => $this->olddraftrevisionid === null ? null
                    : $this->get_mappingid('local_frontpage_revision', $this->olddraftrevisionid),
                'publishedrevisionid' => $this->oldpublishedrevisionid === null ? null
                    : $this->get_mappingid('local_frontpage_revision', $this->oldpublishedrevisionid),
            ]);
        }
    }

    private function unique_slug(string $slug): string {
        global $DB;
        $base = substr($slug . '-copy', 0, 240);
        $candidate = $base;
        for ($suffix = 2; $DB->record_exists('local_frontpage_page', ['slug' => $candidate]); $suffix++) {
            $candidate = substr($base, 0, 245 - strlen((string) $suffix)) . '-' . $suffix;
        }
        return $candidate;
    }
}
