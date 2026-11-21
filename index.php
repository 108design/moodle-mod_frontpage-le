<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Display the searchable public course catalogue.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

require_once(__DIR__ . '/../../config.php');
$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course);
$PAGE->set_url(new moodle_url('/mod/frontpage/index.php', ['id' => $id]));
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('modulenameplural', 'mod_frontpage'));
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();
foreach (get_all_instances_in_course('frontpage', $course) as $activity) {
    echo html_writer::div(
        html_writer::link(
            new moodle_url('/mod/frontpage/view.php', [ 'id' => $activity->coursemodule ]),
            format_string($activity->name)
        ),
        'mb-3'
    );
}
echo $OUTPUT->footer();
