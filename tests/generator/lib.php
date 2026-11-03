<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
defined('MOODLE_INTERNAL') || die();
final class mod_frontpage_generator extends testing_module_generator {
    public function create_instance($record=null,?array $options=null) {
        $record=(array)$record+['name'=>'Frontpage activity','intro'=>'','introformat'=>FORMAT_HTML,'completionallactivities'=>0];
        return parent::create_instance($record,$options);
    }
}
