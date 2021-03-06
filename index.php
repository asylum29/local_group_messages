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

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

$PAGE->set_url('/local/group_messages/index.php', array('id' => $id));

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/site:sendmessage', context_system::instance());
require_capability('moodle/course:managegroups', $context);

$groups = groups_get_all_groups($course->id);
if (count($groups) == 0)  {
    print_error('error');
}

$str_title = get_string('key1', 'local_group_messages');
$PAGE->set_title($str_title);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('incourse');

$sendmsgform = new \local_group_messages\message_form(null, array('course' => $course));
if ($sendmsgform->is_cancelled()) {
    $redirecturl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $id));
    redirect($redirecturl);
} else if ($data = $sendmsgform->get_data()) {
    $users = array();
    if ($data->group == 0) {
        foreach ($groups as $group) {
            $users += groups_get_members($group->id);
        }
    } else {
        $users = groups_get_members($data->group);
    }
    foreach ($users as $user) {
        $message = new stdClass();
        $message->component         = 'local_group_messages';
        $message->name              = 'message';
        $message->userfrom          = $USER;
        $message->userto            = $user;
        $message->subject           = get_string('key9', 'local_group_messages', fullname($USER));
        $message->fullmessage       = $data->message;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml   = '';
        $message->smallmessage      = '';
        $message->notification      = 0;
        message_send($message);
    }
    redirect($PAGE->url, get_string('key8', 'local_group_messages'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($str_title);
$sendmsgform->display();
echo $OUTPUT->footer();
