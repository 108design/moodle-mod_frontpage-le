<?php
/**
 * Capability definitions for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();
$capabilities = [
    'mod/frontpage:editcss' => ['riskbitmask' => RISK_XSS, 'captype' => 'write', 'contextlevel' => CONTEXT_MODULE,
        'archetypes' => ['editingteacher' => CAP_ALLOW, 'manager' => CAP_ALLOW]],
    'mod/frontpage:addinstance' => [
        'riskbitmask' => RISK_CONFIG,
         'captype' => 'write',
         'contextlevel' => CONTEXT_COURSE,

        'archetypes' => [ 'editingteacher' => CAP_ALLOW, 'manager' => CAP_ALLOW ],
         'clonepermissionsfrom' => 'moodle/course:manageactivities',
    ],

    'mod/frontpage:view' => ['captype' => 'read', 'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
        'guest' => CAP_ALLOW,
         'student' => CAP_ALLOW,
         'teacher' => CAP_ALLOW,
         'editingteacher' => CAP_ALLOW,
         'manager' => CAP_ALLOW,
    ]],

    'mod/frontpage:managecontent' => ['riskbitmask' => RISK_XSS, 'captype' => 'write', 'contextlevel' => CONTEXT_MODULE,
        'archetypes' => ['editingteacher' => CAP_ALLOW, 'manager' => CAP_ALLOW]],
    'mod/frontpage:publishcontent' => [
        'riskbitmask' => RISK_XSS,
         'captype' => 'write',
         'contextlevel' => CONTEXT_MODULE,

        'archetypes' => ['editingteacher' => CAP_ALLOW, 'manager' => CAP_ALLOW],
    ],
];
