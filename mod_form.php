<?php
// This file is part of Moodle - https://moodle.org/
/**
 * Native mod form entry point for Frontpage Course Activity.
 *
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
/**
 * Configure the native activity and its completion rules.
 */
final class mod_frontpage_mod_form extends moodleform_mod {
    /**
     * Return the native suffix used by custom completion form controls.
     *
     * @return string
     */
    private function completion_suffix(): string {
        return method_exists($this, 'get_suffix') ? $this->get_suffix() : '';
    }
    /**
     * Define the native form fields and their defaults.
     *
     * @return void
     */
    public function definition(): void {
        $f = $this->_form;
        $f->addElement('header', 'general', get_string('general', 'form'));
        $f->addElement('text', 'name', get_string('frontpagename', 'mod_frontpage'), ['size' => 64]);
        $f->setType('name', PARAM_TEXT);
        $f->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements();
        \local_frontpage\output\context_help::prepare('activitysettings');
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
    /**
     * Add the activity's custom completion controls to the form.
     *
     * @return array
     */
    public function add_completion_rules(): array {
        $key = 'completionallactivities' . $this->completion_suffix();
        $this->_form->addElement('checkbox', $key, '', get_string('completionallactivities', 'mod_frontpage'));
        $this->_form->addHelpButton($key, 'completionallactivities', 'mod_frontpage');
        return [$key];
    }
    /**
     * Check whether the submitted custom completion rule is enabled.
     *
     * @param mixed $data Submitted or stored data.
     * @return bool
     */
    public function completion_rule_enabled($data): bool {
        return !empty($data['completionallactivities' . $this->completion_suffix()]);
    }
    /**
     * Normalise activity settings after the native form has been submitted.
     *
     * @param mixed $data Submitted or stored data.
     * @return void
     */
    public function data_postprocessing($data): void {
        parent::data_postprocessing($data);
        if (empty($data->completionunlocked)) {
            return;
        }
        $suffix = $this->completion_suffix();
        if (
            ($data->{'completion' . $suffix} ?? 0) != COMPLETION_TRACKING_AUTOMATIC
            || empty($data->{'completionallactivities' . $suffix})
        ) {
            $data->{'completionallactivities' . $suffix} = 0;
        }
    }
}
