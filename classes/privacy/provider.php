<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Native provider support for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

namespace mod_frontpage\privacy;

/**
 * Declare that this component stores no personal data.
 */
final class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Explain why this component does not store personal data.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
