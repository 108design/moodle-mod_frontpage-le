<?php
/**
 * Render the authorised published page or its editable preview.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/completionlib.php');
$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('frontpage', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
require_login($course, false, $cm);
$context = context_module::instance($id);
require_capability('mod/frontpage:view', $context);
$instance = $DB->get_record('frontpage', ['id' => $cm->instance], '*', MUST_EXIST);
$page = \local_frontpage\local\service\factory::page_repository()->get_page($instance->pageid);
$canmanage = \local_frontpage\local\page_access::can_manage($page);
if (optional_param('edit', 0, PARAM_BOOL) && $canmanage) {
    redirect(\local_frontpage\local\url\page_url::get_edit_url($page));
}
$PAGE->set_url(new moodle_url('/mod/frontpage/view.php', ['id' => $id]));
$PAGE->set_cm($cm, $course);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($instance->name);
$PAGE->set_heading(format_string($course->fullname));
if ($canmanage) {
    \local_frontpage\output\context_help::prepare('editor');
}
$revision = null;
if (!empty($page->publishedrevisionid) && \local_frontpage\local\page_access::can_view_published($page)) {
    $revision = \local_frontpage\local\service\factory::page_repository()->get_revision(
        $page->id,
        $page->publishedrevisionid
    );
    \local_frontpage\local\page_css::prepare($page, $revision);
}
$event = \mod_frontpage\event\course_module_viewed::create(['objectid' => $instance->id, 'context' => $context]);
$event->trigger();
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($instance->name));
if ($canmanage) {
    echo html_writer::div(
        \local_frontpage\output\authoring::link(
            \local_frontpage\local\url\page_url::get_edit_url($page),
            get_string('editcontent', 'mod_frontpage'),
            't/edit'
        ),
        'mb-4'
    );
}
if ($revision) {
    echo (
        new \local_frontpage\output\page_renderer($OUTPUT, $page, (int)$revision->id)
    )->render(
        $revision->documentjson
    );
    (new completion_info($course))->set_module_viewed($cm);
} else {
    echo $OUTPUT->notification(get_string('notpublished', 'mod_frontpage'), 'info');
}
echo $OUTPUT->footer();
