<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Native backup and restore integration for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Backs up the binding together with its activity-owned page and immutable history.
 */
final class backup_frontpage_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the XML structure used by this native backup or restore step.
     *
     * @return backup_nested_element
     */
    protected function define_structure(): backup_nested_element {
        global $DB;
        $frontpage = new backup_nested_element('frontpage', ['id'], [
            'name', 'intro', 'introformat', 'embedincourse', 'completionallactivities', 'timecreated', 'timemodified',
        ]);
        $ownedpage = new backup_nested_element('ownedpage', ['id'], [
            'slug', 'title', 'status', 'enabled', 'shell', 'showtopnav', 'showdrawer', 'showpageindex',
            'showfooter', 'usecontentcontainer', 'contentwidth', 'contentwidthvalue', 'visibility',
            'accessjson',
         'navplacement',
         'sortorder',
         'draftrevisionid',
         'publishedrevisionid',
         'createdby',
         'modifiedby',

            'timecreated', 'timemodified',
        ]);
        $revisions = new backup_nested_element('revisions');
        $revision = new backup_nested_element('revision', ['id'], [
            'revisionnumber', 'schemaversion', 'documentjson', 'summary', 'draftversion', 'createdby',
            'timecreated', 'publishedby', 'timepublished', 'ischeckpoint', 'originjson',
        ]);
        $resources = new backup_nested_element('ownedresources');
        $resource = new backup_nested_element(
            'ownedresource',
            [ 'id' ],
            [
            'cmid',
            'origin',
            'status',
            'orphanedsince',
            'createdby',
            'timecreated',
            'timemodified',
            ]
        );
        $ownedpage->add_child($resources);
        $resources->add_child($resource);
        $resource->set_source_table('local_frontpage_resource', ['sourcepageid' => backup::VAR_PARENTID]);
        $resource->annotate_ids('user', 'createdby');
        $frontpage->add_child($ownedpage);
        $ownedpage->add_child($revisions);
        $revisions->add_child($revision);

        $frontpage->set_source_table('frontpage', ['id' => backup::VAR_ACTIVITYID]);
        $ownedpage->set_source_sql(
            'SELECT p.* FROM {local_frontpage_page} p JOIN {frontpage} f ON f.pageid = p.id WHERE f.id = ?',
            [backup::VAR_ACTIVITYID]
        );
        $revision->set_source_table(
            'local_frontpage_revision',
            ['pageid' => backup::VAR_PARENTID],
            'revisionnumber ASC'
        );

        $frontpage->annotate_files('mod_frontpage', 'intro', null);
        $ownedpage->annotate_ids('user', 'createdby');
        $ownedpage->annotate_ids('user', 'modifiedby');
        $revision->annotate_ids('user', 'createdby');
        $revision->annotate_ids('user', 'publishedby');
        $pageid = (int) $DB->get_field('frontpage', 'pageid', ['id' => $this->task->get_activityid()], MUST_EXIST);
        foreach (\local_frontpage\local\revision_files::areas_for_page($pageid) as $area) {
            $revision->annotate_files('local_frontpage', $area, 'id');
        }
        return $this->prepare_activity_structure($frontpage);
    }
}
