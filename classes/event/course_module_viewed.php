<?php
/**
 * Event fired when a Frontpage bridge activity is viewed.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

namespace mod_frontpage\event;

/**
 * Event fired when a Frontpage bridge activity is viewed.
 */
final class course_module_viewed extends \core\event\course_module_viewed {
    /**
     * Set the native event metadata.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['objecttable'] = 'frontpage';
        parent::init();
    }

    /**
     * Return the native object mapping used when restoring this event.
     *
     * @return array
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'frontpage', 'restore' => 'frontpage'];
    }
}
