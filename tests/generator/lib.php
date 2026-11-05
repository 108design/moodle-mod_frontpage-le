<?php
/**
 * Regression tests for lib.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Generate Frontpage activities for isolated Moodle tests.
 */
final class mod_frontpage_generator extends testing_module_generator {
    /**
     * Generate a native activity and its owned page for a test.
     *
     * @param mixed $record Record to process.
     * @param array|null $options Presentation or operation options.
     * @return mixed
     */
    public function create_instance($record = null, ?array $options = null) {
        $record = (array)$record + [
        'name' => 'Frontpage activity',
         'intro' => '',
         'introformat' => FORMAT_HTML,
         'completionallactivities' => 0,
        ];
        return parent::create_instance($record, $options);
    }
}
