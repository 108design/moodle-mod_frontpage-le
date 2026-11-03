<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/frontpage/backup/moodle2/restore_frontpage_stepslib.php');

/** Restore task for the Frontpage bridge. */
final class restore_frontpage_activity_task extends restore_activity_task {
    public array $ownedresources = [];
    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new restore_frontpage_activity_structure_step('frontpage_structure', 'frontpage.xml'));
    }

    /** Remap JSON-held course-module references after every activity mapping is available. */
    public function after_restore(): void {
        global $DB;

        $instance = $DB->get_record('frontpage', ['id' => $this->get_activityid()], 'id,course,pageid,embedincourse');
        if (!$instance || empty($instance->pageid)) {
            return;
        }

        $mapper = function(string $type, int $oldid) use ($DB, $instance): ?int {
            $mapping = restore_dbops::get_backup_ids_record($this->get_restoreid(), $type, $oldid);
            if (!$mapping || (int) $mapping->newitemid <= 0) {
                if ($type === 'course_module' && (int)$this->get_info()->original_course_id === (int)$instance->course
                        && $DB->record_exists('course_modules',['id'=>$oldid,'course'=>$instance->course])) {return $oldid;}
                return null;
            }
            $newid = (int) $mapping->newitemid;
            $valid = match ($type) {
                'course_module' => $DB->record_exists('course_modules', [
                    'id' => $newid, 'course' => $instance->course,
                ]),
                'group' => $DB->record_exists('groups', ['id' => $newid, 'courseid' => $instance->course]),
                'grouping' => $DB->record_exists('groupings', ['id' => $newid, 'courseid' => $instance->course]),
                'role' => $DB->record_exists('role', ['id' => $newid]),
                'cohort' => $DB->record_exists('cohort', ['id' => $newid]),
                default => false,
            };
            return $valid ? $newid : null;
        };
        $page = $DB->get_record('local_frontpage_page', ['id' => $instance->pageid], '*', MUST_EXIST);
        $revisions = $DB->get_records('local_frontpage_revision', ['pageid' => $instance->pageid]);
        foreach ($revisions as $revision) {
            $documentjson = \mod_frontpage\local\restore_reference_mapper::document(
                (string) $revision->documentjson, $mapper);
            if ($documentjson === null) {
                throw new restore_step_exception('frontpage_invalid_document');
            }

            $revision->documentjson = (new \local_frontpage\local\document\validator())->validate_and_normalise($documentjson);
            $DB->update_record('local_frontpage_revision', $revision);
            \local_frontpage\local\course_resource_manager::sync_revision(
                (int) $revision->id,
                (int) $instance->pageid,
                $revision->documentjson,
            );
        }
        foreach($this->ownedresources as $resource) {
            $mapping=restore_dbops::get_backup_ids_record($this->get_restoreid(),'course_module',(int)$resource['cmid']);
            if(!$mapping||!$mapping->newitemid){continue;}
            $cm=get_coursemodule_from_id('',(int)$mapping->newitemid,0,false,IGNORE_MISSING);
            if($cm && (int)$cm->course===(int)$instance->course) {
                \local_frontpage\local\course_resource_manager::register($cm->id,$resource['origin'],
                    (int)$resource['createdby'],(int)$instance->pageid);
            }
        }
    }

    public static function define_decode_contents() {
        return [];
    }

    public static function define_decode_rules() {
        return [];
    }
}
