<?php
/**
 * @package mod_frontpage
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license 108design source-available software license; see LICENSE.md
 */
require_once($CFG->dirroot.'/course/moodleform_mod.php');
final class mod_frontpage_mod_form extends moodleform_mod {
    private function completion_suffix(): string {
        return method_exists($this,'get_suffix')?$this->get_suffix():'';
    }
    public function definition(): void {
        $f=$this->_form;$f->addElement('header','general',get_string('general','form'));
        $f->addElement('text','name',get_string('frontpagename','mod_frontpage'),['size'=>64]);$f->setType('name',PARAM_TEXT);
        $f->addRule('name',null,'required',null,'client');$this->standard_intro_elements();
        \local_frontpage\output\context_help::prepare('activitysettings');
        $this->standard_coursemodule_elements();$this->add_action_buttons();
    }
    public function add_completion_rules(): array {
        $key='completionallactivities'.$this->completion_suffix();
        $this->_form->addElement('checkbox',$key,'',get_string('completionallactivities','mod_frontpage'));
        $this->_form->addHelpButton($key,'completionallactivities','mod_frontpage');return [$key];
    }
    public function completion_rule_enabled($data): bool {return !empty($data['completionallactivities'.$this->completion_suffix()]);}
    public function data_postprocessing($data): void {
        parent::data_postprocessing($data);
        if(empty($data->completionunlocked)){return;}$suffix=$this->completion_suffix();
        if(($data->{'completion'.$suffix}??0)!=COMPLETION_TRACKING_AUTOMATIC||empty($data->{'completionallactivities'.$suffix})) {
            $data->{'completionallactivities'.$suffix}=0;
        }
    }
}
