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

/**
 * local_group_messages
 *
 * @package    local_group_messages
 * @copyright  2018 Aleksandr Raetskiy <ksenon3@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_group_messages;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class message_form extends \moodleform
{
    public function definition()
    {
        $mform = &$this->_form;
        $course = $this->_customdata['course'];

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $options = $this->get_group_options($course->id);
        $mform->addElement('select', 'group', get_string('key5', 'local_group_messages'), $options);
        $mform->addRule('group', get_string('required'), 'required');

        $mform->addElement('textarea', 'message', get_string('key4', 'local_group_messages'),
            'wrap="virtual" cols="50" rows="4"');
        $mform->addRule('message', get_string('required'), 'required');

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('key6', 'local_group_messages'));
        $buttonarray[] = &$mform->createElement('cancel', '', get_string('key7', 'local_group_messages'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }

    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if (!isset($data['group']) || $data['group'] < 0) { // форма сама проверит группы, которые не существуют в курсе
            $errors['group'] = get_string('required');
        }

        if (!isset($data['message']) || trim($data['message']) === '') {
            $errors['message'] = get_string('required');
        }

        return $errors;
    }

    protected function get_group_options($courseid) {
        $options = array();
        $groups = groups_get_all_groups($courseid);
        $options[-1] = get_string('key3', 'local_group_messages');
        if (count($groups) > 1) {
            $options[0] = get_string('key2', 'local_group_messages');
        }
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        return $options;
    }
}
