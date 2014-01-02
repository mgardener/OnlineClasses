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
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class meeting_form extends moodleform {
    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        $mform->addElement('header', '', get_string('meeting', 'block_onlineclasses'), '');

        $mform->addElement('text', 'subject', get_string('subject', 'block_onlineclasses'), 'maxlength="254"');
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', '', 'required', null, 'client');

        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'block_onlineclasses'));
        $mform->addRule('starttime', '', 'required', null, 'client');

        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'block_onlineclasses'));
        $mform->addRule('endtime', '', 'required', null, 'client');

        /*
        $options = array(
            'Immediate' => 'Immediate',
            'Scheduled' => 'Scheduled',
            'Recurring' => 'Recurring'
        );
        $select = $mform->addElement('select', 'meetingtype', get_string('meetingtype', 'block_onlineclasses'), $options);
        $select->setSelected('Scheduled');

        $mform->addElement('selectyesno', 'passwordrequired', get_string('passwordrequired', 'block_onlineclasses'));

        $options = array(
            'PSTN' => 'PSTN',
            'Free' => 'Free',
            'Hybrid' => 'Hybrid',
            'Private' => 'Private'
        );

        $select = $mform->addElement('select', 'conferencecallinfo', get_string('conferencecallinfo', 'block_onlineclasses'), $options);
        $select->setSelected('Hybrid');
         */

        $mform->addElement('hidden', 'action', 'add');
        $mform->setType('action', PARAM_RAW);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'meetingid', 0);
        $mform->setType('meetingid', PARAM_INT);


        // buttons
        $this->add_action_buttons(true, get_string('createmeeting', 'block_onlineclasses'));

    }
}
