<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Database metadata for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => \core\event\course_module_completion_updated::class,
        'callback' => \mod_frontpage\hook_callbacks::class . '::course_module_completion_updated',
    ],
    [
        'eventname' => \core\event\course_module_updated::class,
        'callback' => \mod_frontpage\hook_callbacks::class . '::course_module_updated',
    ],
    [
        'eventname' => \core\event\course_module_deleted::class,
        'callback' => \mod_frontpage\hook_callbacks::class . '::course_module_deleted',
    ],
];
