<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Native hook registrations for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();
$callbacks = [];

// Product identity on native administration and upgrade surfaces.
$callbacks[] = ['hook' => \core\hook\output\before_standard_head_html_generation::class,
    'callback' => [\mod_frontpage\local\admin_branding::class, 'head']];
$callbacks[] = ['hook' => \core\hook\output\before_http_headers::class,
    'callback' => [\mod_frontpage\local\admin_branding::class, 'prepare']];

// Independent signed update-package guard.
$callbacks[] = [
    'hook' => \core\hook\after_config::class,
    'callback' => [\mod_frontpage\local\update_guard::class, 'after_config'],
    'priority' => 1002,
];
