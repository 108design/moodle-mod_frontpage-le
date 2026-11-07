<?php
/**
 * Version and dependency metadata for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();
$plugin->component = 'mod_frontpage';
$plugin->version = 2026010106;
$plugin->requires = 2024100700;
$plugin->maturity = MATURITY_BETA;
$plugin->release = '0.7.0-beta';
$plugin->dependencies = ['local_frontpage' => 2026010108];
