<?php
/**
 * Native backup and restore integration for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/frontpage/backup/moodle2/backup_frontpage_stepslib.php');

/**
 * Backup task for the Frontpage bridge.
 */
final class backup_frontpage_activity_task extends backup_activity_task {
    /**
     * Define the activity-specific backup or restore settings.
     *
     * @return void
     */
    protected function define_my_settings() {
    }

    /**
     * Register the activity-specific backup or restore structure steps.
     *
     * @return void
     */
    protected function define_my_steps() {
        $this->add_step(new backup_frontpage_activity_structure_step('frontpage_structure', 'frontpage.xml'));
    }

    /**
     * Encode native content links for the course backup.
     *
     * @param mixed $content Content to process.
     * @return mixed
     */
    public static function encode_content_links($content) {
        return $content;
    }
}
