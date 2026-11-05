<?php
/**
 * Connect native Moodle hooks to the component runtime.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

namespace mod_frontpage;

/**
 * Reacts to completion changes in course activities referenced by Frontpages.
 */
final class hook_callbacks {
    /**
     * @var bool Updating.
     */
    private static bool $updating = false;

    /**
     * Recalculate aggregate completion after a referenced course module changes for one learner.
     *
     * @param \core\event\course_module_completion_updated $event Native Moodle event.
     * @return void
     */
    public static function course_module_completion_updated(
        \core\event\course_module_completion_updated $event
    ): void {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        $cmid = (int) $event->contextinstanceid;
        $userid = (int) $event->relateduserid;
        if (self::$updating || $cmid <= 0 || $userid <= 0) {
            return;
        }
        $cm = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm || $cm->modname === 'frontpage') {
            return;
        }
        $instances = self::get_referencing_instances($cmid);
        if (!$instances) {
            return;
        }
        self::$updating = true;
        try {
            foreach ($instances as $instance) {
                $frontpagecm = get_coursemodule_from_instance('frontpage', $instance->id, $instance->course, false);
                if (!$frontpagecm || $frontpagecm->completion != COMPLETION_TRACKING_AUTOMATIC) {
                    continue;
                }
                $completion = new \completion_info(get_course($instance->course));
                $completion->update_state($frontpagecm, COMPLETION_UNKNOWN, $userid);
            }
        } finally {
            self::$updating = false;
        }
    }

    /**
     * Recalculate all users when a referenced module's settings may have changed.
     *
     * @param \core\event\course_module_updated $event Native Moodle event.
     * @return void
     */
    public static function course_module_updated(\core\event\course_module_updated $event): void {
        self::reset_referencing_instances((int) $event->objectid);
    }

    /**
     * Recalculate all users when a referenced module is removed.
     *
     * @param \core\event\course_module_deleted $event Native Moodle event.
     * @return void
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event): void {
        self::reset_referencing_instances((int) $event->objectid);
    }

    /**
     * Reset the referencing instances.
     *
     * @param int $cmid Course-module ID.
     * @return void
     */
    private static function reset_referencing_instances(int $cmid): void {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        foreach (self::get_referencing_instances($cmid) as $instance) {
            $frontpagecm = get_coursemodule_from_instance('frontpage', $instance->id, $instance->course, false);
            if (!$frontpagecm || $frontpagecm->completion != COMPLETION_TRACKING_AUTOMATIC) {
                continue;
            }
            (new \completion_info(get_course($instance->course)))->reset_all_state($frontpagecm);
        }
    }

    /**
     * Return the referencing instances.
     *
     * @param int $cmid Course-module ID.
     * @return array
     */
    private static function get_referencing_instances(int $cmid): array {
        global $DB;

        $sql = 'SELECT DISTINCT f.id, f.course
                  FROM {frontpage} f
                  JOIN {local_frontpage_page} p ON p.id = f.pageid
                  JOIN {local_frontpage_cmref} ref ON ref.revisionid = p.publishedrevisionid
                 WHERE ref.cmid = :cmid AND f.completionallactivities = 1';
        return $DB->get_records_sql($sql, ['cmid' => $cmid]);
    }
}
