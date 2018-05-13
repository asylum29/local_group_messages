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

defined('MOODLE_INTERNAL') || die;

function local_group_messages_extend_navigation_course(navigation_node $navigation, $course, $context)
{
    global $PAGE;
    
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }

    $sendmessage = has_capability('moodle/site:sendmessage', context_system::instance());
    $managegroups = has_capability('moodle/course:managegroups', $context);
    if (!$sendmessage || !$managegroups) {
        return;
    }

    $groups = groups_get_all_groups($PAGE->course->id);
    if (count($groups) == 0) {
        return;
    }
    
    $url = new moodle_url('/local/group_messages/index.php', array('id' => $PAGE->course->id));
    $navigation->get('users')->add(get_string('key1', 'local_group_messages'), $url, navigation_node::TYPE_SETTING,
        null, null, new pix_icon('t/message', ''));
}
