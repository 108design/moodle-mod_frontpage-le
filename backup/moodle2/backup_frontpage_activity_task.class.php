<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/frontpage/backup/moodle2/backup_frontpage_stepslib.php');

/** Backup task for the Frontpage bridge. */
final class backup_frontpage_activity_task extends backup_activity_task {
    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new backup_frontpage_activity_structure_step('frontpage_structure', 'frontpage.xml'));
    }

    public static function encode_content_links($content) {
        return $content;
    }
}
