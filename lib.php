<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
defined('MOODLE_INTERNAL') || die();

/** Declare Moodle feature support. */
function frontpage_supports(string $feature): mixed {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_RESOURCE,
        FEATURE_MOD_INTRO, FEATURE_SHOW_DESCRIPTION, FEATURE_COMPLETION_TRACKS_VIEWS,
        FEATURE_COMPLETION_HAS_RULES, FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_GROUPS, FEATURE_GROUPINGS => false,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

/** Add one bridge instance and its activity-owned Frontpage page. */
function frontpage_add_instance(stdClass $data, ?moodleform_mod $mform = null): int {
    global $DB, $USER;
    $transaction = $DB->start_delegated_transaction();
    $now = time();
    $data->embedincourse = 0;
    $data->pageid = null;
    $data->timecreated = $now;
    $data->timemodified = $now;
    // Moodle's web lifecycle fills cm.instance only after add_instance returns. Bind it inside
    // this transaction before context-bound template/publication checks; never weaken owner checks.
    $pendingcm = $DB->get_record_sql('SELECT cm.id, cm.instance FROM {course_modules} cm
        JOIN {modules} m ON m.id = cm.module
        WHERE cm.id = :cmid AND cm.course = :courseid AND m.name = :modulename',
        ['cmid' => (int) $data->coursemodule, 'courseid' => (int) $data->course,
            'modulename' => 'frontpage'], MUST_EXIST);
    if (!empty($pendingcm->instance)) {
        throw new invalid_parameter_exception('The new Frontpage course module is already bound.');
    }
    $instanceid = $DB->insert_record('frontpage', $data);
    $DB->set_field('course_modules', 'instance', $instanceid, ['id' => $pendingcm->id]);
    $context = context_module::instance((int) $data->coursemodule);
    $service = \local_frontpage\local\service\factory::page_service();
    $page = $service->create_activity_page(
        (string) $data->name,
        'course-' . (int) $data->course . '-frontpage-' . $instanceid,
        $context,
        'mod_frontpage',
        $instanceid,
        (int) $USER->id,
    );
    $DB->set_field('frontpage', 'pageid', $page->id, ['id' => $instanceid]);
    $transaction->allow_commit();
    return $instanceid;
}

/** Update bridge metadata; the Frontpage page remains independently revisioned. */
function frontpage_update_instance(stdClass $data, ?moodleform_mod $mform = null): bool {
    global $DB;
    $existing = $DB->get_record('frontpage', ['id' => $data->instance], '*', MUST_EXIST);
    $data->embedincourse = 0;
    $data->id = $data->instance;
    $data->timemodified = time();
    $DB->set_field('local_frontpage_page', 'title', $data->name, ['id'=>$existing->pageid]);
    return $DB->update_record('frontpage', $data);
}

/** Delete the bridge and only the page it owns. */
function frontpage_delete_instance(int $id): bool {
    global $DB;
    $instance = $DB->get_record('frontpage', ['id' => $id]);
    if (!$instance) {
        return false;
    }
    if (!empty($instance->pageid)) {
        \local_frontpage\local\service\factory::page_service()->delete_activity_page(
            (int) $instance->pageid,
            'mod_frontpage',
            $id,
        );
    }
    $DB->delete_records('frontpage', ['id' => $id]);
    return true;
}

/** Provide course cache metadata. */
function frontpage_get_coursemodule_info(stdClass $coursemodule): cached_cm_info|false {
    global $CFG, $DB;
    require_once($CFG->libdir . '/completionlib.php');
    $instance = $DB->get_record('frontpage', ['id' => $coursemodule->instance],
        'id,name,intro,introformat,completionallactivities,embedincourse');
    if (!$instance) {
        return false;
    }
    $info = new cached_cm_info();
    $info->name = $instance->name;
    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('frontpage', $instance, $coursemodule->id, false);
    }
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules']['completionallactivities'] =
            (int) $instance->completionallactivities;
    }
    return $info;
}

/** Recalculate completion after the activity-owned page is published. */
function frontpage_page_published(stdClass $page): void {
    global $CFG, $DB;

    if (($page->ownercomponent ?? '') !== 'mod_frontpage' || empty($page->ownerinstanceid)) {
        return;
    }
    $instance = $DB->get_record('frontpage', ['id' => $page->ownerinstanceid]);
    if (!$instance || empty($instance->completionallactivities)) {
        return;
    }
    // Publication can call this directly, outside Moodle's activity/completion entry points.
    require_once($CFG->libdir . '/completionlib.php');
    $cm = get_coursemodule_from_instance('frontpage', $instance->id, $instance->course, false);
    if (!$cm || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return;
    }
    (new completion_info(get_course($instance->course)))->reset_all_state($cm);
}

