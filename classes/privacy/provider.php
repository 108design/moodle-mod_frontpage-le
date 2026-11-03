<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
namespace mod_frontpage\privacy;
final class provider implements \core_privacy\local\metadata\null_provider {
    public static function get_reason(): string {return 'privacy:metadata';}
}
