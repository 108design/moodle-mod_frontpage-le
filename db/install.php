<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Installation callbacks for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Initialise persistent component state for Frontpage Course Activity.
 *
 * @return void
 */
function xmldb_frontpage_install(): void {
    $origin = get_config('mod_frontpage', 'distributionorigin');
    if ($origin && $origin !== 'marketplace') {
        throw new moodle_exception('invaliddata', 'error');
    }
    set_config('distributionorigin', 'marketplace', 'mod_frontpage');
}
