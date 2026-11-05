<?php
/**
 * Completion rule aggregating Moodle completion of modules included on the published Frontpage.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

namespace mod_frontpage\completion;

use core_completion\activity_custom_completion;

/**
 * Completion rule aggregating Moodle completion of modules included on the published Frontpage.
 */
final class custom_completion extends activity_custom_completion {
    /**
     * Rule.
     */
    public const RULE = 'completionallactivities';

    /**
     * Resolve the activity's custom completion state.
     *
     * @param string $rule Rule.
     * @return int
     */
    public function get_state(string $rule): int {
        global $DB, $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        $this->validate_rule($rule);
        $instance = $DB->get_record('frontpage', ['id' => $this->cm->instance], 'id,course,pageid', MUST_EXIST);
        $page = $DB->get_record(
            'local_frontpage_page',
            ['id' => $instance->pageid],
            'id,publishedrevisionid',
            MUST_EXIST
        );
        if (empty($page->publishedrevisionid)) {
            return COMPLETION_INCOMPLETE;
        }
        $revision = $DB->get_record(
            'local_frontpage_revision',
            ['id' => $page->publishedrevisionid],
            'id,documentjson',
            MUST_EXIST
        );
        $document = json_decode($revision->documentjson, true);
        if (!is_array($document)) {
            return COMPLETION_INCOMPLETE;
        }
        $segments = $document['segments'] ?? [];
        if (!empty($document['aboveHeaderSegment'])) {
            $segments[] = $document['aboveHeaderSegment'];
        }
        $cmids = [];
        $disabledchildren = [];
        foreach ($segments as $container) {
            if (($container['type'] ?? '') === 'grid' && !($container['props']['enabled'] ?? true)) {
                foreach ($container['props']['segmentids'] ?? [] as $childid) {
                    $disabledchildren[$childid] = true;
                }
            }
        }
        foreach ($segments as $segment) {
            if (isset($disabledchildren[$segment['id'] ?? ''])) {
                continue;
            }
            if (
                ($segment['type'] ?? '') === 'course_module' && !empty($segment['props']['placeholder'])
                    && ($segment['props']['enabled'] ?? true)
            ) {
                return COMPLETION_INCOMPLETE;
            }
            if (($segment['type'] ?? '') !== 'course_module' || empty($segment['props']['cmid'])) {
                continue;
            }
            $enabled = $segment['props']['enabled'] ?? true;
            if ($enabled) {
                $cmids[(int) $segment['props']['cmid']] = true;
            }
        }
        if (!$cmids) {
            return COMPLETION_INCOMPLETE;
        }

        $modinfo = get_fast_modinfo($instance->course, $this->userid);
        $completion = new \completion_info(get_course($instance->course));
        $tracked = 0;
        foreach (array_keys($cmids) as $cmid) {
            try {
                $childcm = $modinfo->get_cm($cmid);
            } catch (\moodle_exception) {
                return COMPLETION_INCOMPLETE;
            }
            if ($childcm->modname === 'frontpage' || !empty($childcm->deletioninprogress)) {
                return COMPLETION_INCOMPLETE;
            }
            if ($childcm->completion == COMPLETION_TRACKING_NONE) {
                continue;
            }
            $tracked++;
            $data = $completion->get_data($childcm, false, $this->userid);
            if ((int) $data->completionstate === COMPLETION_INCOMPLETE) {
                return COMPLETION_INCOMPLETE;
            }
        }
        return $tracked > 0 ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Return the custom completion rules supported by this activity.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [self::RULE];
    }

    /**
     * Return the labels for the supported custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        return [self::RULE => get_string('completiondetail:allactivities', 'mod_frontpage')];
    }

    /**
     * Return the display order of the custom completion rules.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return ['completionview', self::RULE];
    }
}
