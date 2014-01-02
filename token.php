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
 * meetingcenter block caps.
 *
 * @package    block_onlineclasses
 * @copyright  Michael Gardener <mgardener@cissq.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( '../../config.php');
require_once($CFG->dirroot.'/blocks/onlineclasses/lib.php');

require_login(0, false);

# get our parameters

$code     = optional_param('code', 0, PARAM_TEXT);
$redirect = optional_param('redirect', '', PARAM_TEXT);
$admin    = optional_param('admin', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$renew    = optional_param('renew', 0, PARAM_INT);

$api_key    = get_config('block_onlineclasses', 'gotomeeting_key');
$return_url = get_config('block_onlineclasses', 'gotomeeting_callbackurl');

if(! $api_key || ! $return_url){
    print_error(get_string('blocksettingerror', 'block_onlineclasses'));
}

if($courseid){
    $return_url .= "?courseid=$courseid";
}

if((! $token = $DB->get_record('onlineclasses_token', array('userid'=>$USER->id, 'disabled'=>0))) || ($renew)){

    $citrix = new CitrixAPI();
    $oauth = $citrix->getOAuthToken($api_key, $return_url);

    $data = json_decode($oauth);
    $data->userid = $USER->id;
    $data->courseid = $courseid;
    $data->timecreated = time();
    $DB->insert_record('onlineclasses_token', $data);

    if($prevTokens = $DB->get_records('onlineclasses_token', array('disabled'=>0, 'organizer_key'=>$data->organizer_key, 'account_key'=>$data->account_key))){
        unset($data->userid);
        unset($data->courseid);
        foreach ($prevTokens as $prevToken) {
            $data->id = $prevToken->id;
            $DB->update_record('onlineclasses_token', $data);
        }
    }
    redirect($return_url);
}else{

    $table = new html_table();
    $table->head = array('','');

    //$table->align = array('left', 'left');
    $table->wrap = array('', '');
    $table->width = '100%';
    //$table->size = array('*', '*');

    $token_fields = array('account_type','firstname','lastname','email','access_token');

    foreach ($token_fields as $key) {
        $cell_1 = new html_table_cell(get_string($key, 'block_onlineclasses'));
        $cell_2 = new html_table_cell($token->$key);
        $row = new html_table_row();
        $row->cells = array($cell_1, $cell_2);
        $table->data[] = $row;
    }

    $cell_1 = new html_table_cell(get_string('expirationdate', 'block_onlineclasses'));
    $expdate = date('m/d/Y H:i', ($token->expires_in + $token->timecreated));
    $cell_2 = new html_table_cell($expdate);
    $row = new html_table_row();
    $row->cells = array($cell_1, $cell_2);
    $table->data[] = $row;

    $PAGE->set_url('/blocks/onlineclasses/token.php');
    $PAGE->set_pagelayout('standard');

    $context = context_course::instance($courseid, MUST_EXIST);
    $PAGE->set_context($context);

    $name = get_string('token', 'block_onlineclasses');
    $title = get_string('token', 'block_onlineclasses');

    if($course = $DB->get_record('course', array('id'=>$courseid))){
        $PAGE->navbar->add($course->shortname, $CFG->wwwroot.'/course/view.php?id='.$courseid);
    }
    $PAGE->navbar->add($name, '');

    $heading = $SITE->fullname;

    $PAGE->set_title($title);
    $PAGE->set_heading($heading);
    $PAGE->set_cacheable(true);
    //$PAGE->set_button($button);
    $output = $OUTPUT->header();
    echo $output;

    echo "<div id='token-wrapper'>";
    echo "<h1 class='head-title'>$title</h1>\n";
    echo "<p> An access token contains the security information for a login session and identifies the user, the user's groups, and the user's privileges</p>\n";
    echo html_writer::table($table);

    $addUserButton = $OUTPUT->single_button(new moodle_url($CFG->wwwroot.'/blocks/onlineclasses/token.php?authorize=1&renew=1&courseid='.$courseid), get_string('renewtoken', 'block_onlineclasses'), 'get');

    echo '<div class="tokenbuttonwrapper">'.$addUserButton.'</div>';
    echo get_string('renewtoken_desc', 'block_onlineclasses');
    echo '</div>';
    echo $OUTPUT->footer();
}