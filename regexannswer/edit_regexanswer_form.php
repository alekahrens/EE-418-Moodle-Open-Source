<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//TODO: NOTE chnaged all types qtype_regexanswer to qtype_regexanswer
/**
 * Defines the editing form for the regexanswer question type.
 *
 * @package    qtype
 * @subpackage regexanswer
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * RegEx Answer question editing form definition.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexanswer_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $menu = [
            get_string('caseno', 'qtype_regexanswer'),
            get_string('caseyes', 'qtype_regexanswer')
        ];
        $mform->addElement('select', 'usecase',
            get_string('casesensitive', 'qtype_regexanswer'), $menu);
        $mform->setDefault('usecase', $this->get_default_value('usecase', $menu[0]));

        $mform->addElement('static', 'answersinstruct',
            get_string('correctanswers', 'qtype_regexanswer'),
            get_string('filloutoneanswer', 'qtype_regexanswer'));
        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_regexanswer', '{no}'),
            question_bank::fraction_options());

        $this->add_interactive_settings();
    }

    protected function get_more_choices_string() {
        return get_string('addmoreanswerblanks', 'qtype_regexanswer');
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $answercount++;
                if ($preg_error = $this->is_valid_php_regex($trimmedanswer)) {
                    $errors["answeroptions[{$key}]"] = $this->get_preg_error_message($preg_error);
                }
                else {
                    if ($data['fraction'][$key] == 1) {
                        $maxgrade = true;
                    }
                }
            } else if ($data['fraction'][$key] != 0 ||
                !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answeroptions[{$key}]"] = get_string('answermustbegiven', 'qtype_regexanswer'); //todo: change this here...?
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answeroptions[0]'] = get_string('notenoughanswers', 'qtype_regexanswer', 1); //todo: chnage this here
        }
        if ($maxgrade == false) {
            $errors['answeroptions[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    function is_valid_php_regex($pattern) {
        preg_match($pattern, "Lorem ipsum");//dummy check
        return preg_last_error();
    }

    function get_preg_error_message($preg_error) {
        $errors = array(
            PREG_NO_ERROR               => 'No errors',
            PREG_INTERNAL_ERROR         => 'Invalid Regex : There was an internal PCRE error',
            PREG_BACKTRACK_LIMIT_ERROR  => 'Invalid Regex : Backtrack limit was exhausted',
            PREG_RECURSION_LIMIT_ERROR  => 'Invalid Regex : Recursion limit was exhausted',
            PREG_BAD_UTF8_ERROR         => 'Invalid Regex : The offset didn\'t correspond to the begin of a valid UTF-8 code point',
            PREG_BAD_UTF8_OFFSET_ERROR  => 'Invalid Regex : Malformed UTF-8 data',
        );
        return $errors[$preg_error];
    }

    public function qtype() {
        return 'regexanswer';
    }
}
