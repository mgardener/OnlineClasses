<?php
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/onlineclasses/lib.php');
require_once($CFG->dirroot.'/blocks/onlineclasses/meetings_form.php');

/** Include eventslib.php */
require_once($CFG->libdir.'/eventslib.php');
/** Include formslib.php */
//require_once($CFG->libdir.'/formslib.php');
/** Include calendar/lib.php */
require_once($CFG->dirroot.'/calendar/lib.php');


//global $CFG, $DB, $OUTPUT, $PAGE, $SITE;
$id        = optional_param('id', 0, PARAM_INT);
//  Paging options:
$page      = optional_param('page', 0, PARAM_INT);
$perpage   = optional_param('perpage', 20, PARAM_INT);
$sort      = optional_param('sort', 'starttime', PARAM_ALPHANUM);
$dir       = optional_param('dir', 'DESC', PARAM_ALPHA);
//  ACTION
$action    = optional_param('action', false, PARAM_ALPHA);
$meetingid = optional_param('meetingid', 0, PARAM_INT);
//  COURSE
$courseid = optional_param('courseid', 0, PARAM_INT);

require_login();
//require_capability('block/onlineclasses:viewblock',context_system::instance(),$USER->id);


$PAGE->set_url('/blocks/onlineclasses/meetings.php');
$PAGE->set_pagelayout('standard');
//$PAGE->set_context(context_system::instance());
$PAGE->set_context(context_course::instance($courseid));

$meetings_breadcrumb = get_string('meetings_breadcrumb', 'block_onlineclasses');
$meetings_title      = get_string('meetings_title', 'block_onlineclasses');

/// Print header
if($course = $DB->get_record('course', array('id'=>$courseid))){
    $PAGE->navbar->add($course->shortname, $CFG->wwwroot.'/course/view.php?id='.$courseid);
}

$PAGE->navbar->add($meetings_breadcrumb,$CFG->wwwroot.'/blocks/onlineclasses/meetings.php?courseid='.$courseid);


$heading = $SITE->fullname;


$messages = array();

if (($action) && ($courseid)){
    switch ($action) {
        //DELETE ACTION
        case 'delete':
            if($token = $DB->get_record('onlineclasses_token', array('userid'=>$USER->id, 'disabled'=>0))){
                if (is_siteadmin($USER)) {
                    if(! $meeting = $DB->get_record('onlineclasses_meeting', array('id'=>$id))){
                        print_error(get_string('meetingnotfound', 'block_onlineclasses').$id);
                    }
                }else{
                    if(! $meeting = $DB->get_record('onlineclasses_meeting', array('id'=>$id, 'userid'=>$USER->id))){
                        print_error(get_string('meetingnotfound', 'block_onlineclasses').$id);
                    }
                }

                $citrix = new CitrixAPI($token->access_token, $token->organizer_key);

                if($return = $citrix->deleteMeeting($meeting)){
                    $DB->delete_records_select('onlineclasses_meeting', "id=$id");
                    $DB->delete_records_select('event', "id=$meeting->eventid");
                }else{
                    redirect($CFG->wwwroot.'/blocks/onlineclasses/token.php?authorize=1&courseid='.$courseid, get_string('deleteerror', 'block_onlineclasses'), 0);
                }

                redirect(new moodle_url('/blocks/onlineclasses/meetings.php?courseid='.$courseid), get_string('deletesuccessful', 'block_onlineclasses'),0);
                exit; //never reached

                $meeting->action = 'edit';
                $meeting->courseid = $courseid;

                $mform_meeting->set_data($meeting);
                print_header_simple($meetings_title, $heading, $navigation, '', '', true, '', '');
                $mform_meeting->display();
            }else{
                redirect($CFG->wwwroot.'/blocks/onlineclasses/token.php?authorize=1&courseid='.$courseid, get_string('notoken', 'block_onlineclasses'), 10);
            }
            break;

        //EDIT ACTION
        case 'edit':
            if($token = $DB->get_record('onlineclasses_token', array('userid'=>$USER->id, 'disabled'=>0))){
                if (is_siteadmin($USER)) {
                    if(! $meeting = $DB->get_record('onlineclasses_meeting', array('id'=>$id))){
                        print_error(get_string('meetingnotfound', 'block_onlineclasses').$id);
                    }
                }else{
                    if(! $meeting = $DB->get_record('onlineclasses_meeting', array('id'=>$id, 'userid'=>$USER->id))){
                        print_error(get_string('meetingnotfound', 'block_onlineclasses').$id);
                    }
                }

                $mform_meeting = new meeting_form();

                if ($mform_meeting->is_cancelled()) {
                    redirect(new moodle_url('/blocks/onlineclasses/meetings.php?courseid='.$courseid));

                } else if ($meeting_data = $mform_meeting->get_data()) {

                    $citrix = new CitrixAPI($token->access_token, $token->organizer_key);
                    if($oauth = $citrix->editMeeting($meeting_data)){
                        $today = time();
                        $data = new object();
                        $data->id = $id;
                        $data->subject = $meeting_data->subject;
                        $data->timemodified = $today;
                        $data->starttime = $meeting_data->starttime;
                        $data->endtime = $meeting_data->endtime;

                        $DB->update_record('onlineclasses_meeting', $data);

                        //Update Calender
                        $rec = new object();
                        $rec->id           = $meeting->eventid;
                        $rec->name         = $data->subject;
                        $rec->timestart    = $data->starttime;
                        $rec->timemodified = $today;
                        $DB->update_record('event', $rec);

                    }else{
                        redirect($CFG->wwwroot.'/blocks/onlineclasses/token.php?authorize=1&courseid='.$courseid, get_string('updateerror', 'block_onlineclasses'),0);
                    }
                    redirect(new moodle_url('/blocks/onlineclasses/meetings.php?courseid='.$courseid), get_string('updatesuccessful', 'block_onlineclasses'),0);
                    exit; //never reached
                }

                $meeting->action = 'edit';
                $meeting->courseid = $courseid;

                $mform_meeting->set_data($meeting);
                //PAGE HEADER
                $PAGE->set_title($meetings_title);
                $PAGE->set_heading($heading);
                $PAGE->set_cacheable(true);
                //$PAGE->set_button($button);
                $output = $OUTPUT->header();
                echo $output;

                $mform_meeting->display();
            }else{
                redirect($CFG->wwwroot.'/blocks/onlineclasses/token.php?authorize=1&courseid='.$courseid, get_string('notoken', 'block_onlineclasses'), 10);
            }
            break;

        //ADD ACTION
        case 'add':
            if($token = $DB->get_record('onlineclasses_token', array('userid'=>$USER->id, 'disabled'=>0))){
                $mform_meeting = new meeting_form();

                if ($mform_meeting->is_cancelled()) {
                    redirect(new moodle_url('/blocks/onlineclasses/meetings.php?courseid='.$courseid));

                } else if ($meeting_data = $mform_meeting->get_data()) {

                    $citrix = new CitrixAPI($token->access_token, $token->organizer_key);
                    $oauth = $citrix->createMeeting($meeting_data);

                    $today = time();
                    $data = json_decode(strtolower($oauth));
                    $data = reset($data);
                    $meetingid = 0;
                    $eventid = 0;

                    $data->userid       = $USER->id;
                    $data->courseid     = $meeting_data->courseid;
                    $data->subject      = $meeting_data->subject;
                    $data->timecreated  = $today;
                    $data->timemodified = $today;
                    $data->starttime    = $meeting_data->starttime;
                    $data->endtime      = $meeting_data->endtime;

                    if ($meetingid = $DB->insert_record('onlineclasses_meeting', $data)) {
                        $event = new stdClass();
                        $event->name         = $data->subject;
                        $event->courseid     = $data->courseid;
                        $event->groupid      = 0;
                        $event->userid       = $USER->id;
                        $event->modulename   = '0';
                        $event->instance     = '0';
                        $event->eventtype    = 'course';
                        $event->timestart    = $data->starttime;
                        $event->timeduration = 0;
                        /*
                        $description = '<p>1.  Please join my meeting, '.date('l, F j, Y \a\t g:i A T', $data->starttime).'.<br />
                                        <a href="'.$data->joinurl.'">'.$data->joinurl.'</a></p>
                                        <p>2.  Use your microphone and speakers (VoIP) - a headset is recommended.  Or, call in using your telephone.</p>
                                        <p>'.str_replace('access code:', 'Access Code:', str_replace('ca:','Dial', nl2br($data->conferencecallinfo))).'<br />
                                        Audio PIN: Shown after joining the meeting</p>
                                        <p>Meeting ID: '.$data->meetingid.' </p>';
                        */
                        $description = '<p>1.  Please join my meeting.<br />
                                        <a href="'.$data->joinurl.'">'.$data->joinurl.'</a></p>
                                        <p>2.  Use your microphone and speakers (VoIP) - a headset is recommended.  Or, call in using your telephone.</p>
                                        <p>'.str_replace('access code:', 'Access Code:', str_replace('ca:','Dial', nl2br($data->conferencecallinfo))).'<br />
                                        Audio PIN: Shown after joining the meeting</p>
                                        <p>Meeting ID: '.$data->meetingid.' </p>';

                        $event->description  = $description;

                        $event = calendar_event::create($event);
                        if($eventid = $event->__get('id')){
                            $rec = new object();
                            $rec->id = $meetingid;
                            $rec->eventid = $eventid;
                            $DB->update_record('onlineclasses_meeting', $rec);
                        }
                    }




                    redirect(new moodle_url('/blocks/onlineclasses/meetings.php?courseid='.$courseid), 'Sucessfully added.',0);
                    exit; //never reached
                }

                $toform = new object();
                $toform->action = 'add';
                $toform->courseid = $courseid;

                $mform_meeting->set_data($toform);

                $PAGE->set_title($meetings_title);
                $PAGE->set_heading($heading);
                $PAGE->set_cacheable(true);
                //$PAGE->set_button($button);
                $output = $OUTPUT->header();
                echo $output;

                $mform_meeting->display();
            }else{
                redirect($CFG->wwwroot.'/blocks/onlineclasses/token.php?authorize=1&courseid='.$courseid, get_string('notoken', 'block_onlineclasses'), 10);
            }
            break;

    }//switch
//End of action block
}else{
    $PAGE->set_title($meetings_title);
    $PAGE->set_heading($heading);
    $PAGE->set_cacheable(true);
    //$PAGE->set_button($button);
    $output = $OUTPUT->header();
    echo $output;




    echo "<div id='meeting-list-wrapper'>";
    echo "<h1 class='head-title'>$meetings_title</h1>\n";



    $sqlMeetingsCount = "SELECT COUNT(mm.id)
                      FROM {onlineclasses_meeting} mm
                INNER JOIN {user} u
                        ON mm.userid = u.id
                INNER JOIN {course} c
                        ON mm.courseid = c.id
                     WHERE mm.disabled = :disabled
                       AND mm.userid = :userid
                       AND mm.courseid = :courseid";


    // use paging
    $totalcount = $DB->count_records_sql($sqlMeetingsCount, array('disabled'=>0, 'userid'=>$USER->id, 'courseid'=>$courseid));
    $baseurl = 'meetings.php';
    $pagingbar = new paging_bar($totalcount, $page, $perpage, $baseurl, 'page');
    echo $OUTPUT->render($pagingbar);

    $columns = array('rowcount', 'firstname', 'lastname', 'subject', 'joinurl', 'uniquemeetingid', 'starttime', 'endtime', 'timemodified', 'edit');

    foreach ($columns as $column) {
        $string[$column] = get_string($column, 'block_onlineclasses');

        if ($sort != $column) {
            $columnicon = "";
            if ($column == "lastaccess") {
                $columndir = "DESC";
            } else {
                $columndir = "ASC";
            }
        } else {
            $columndir = $dir == "ASC" ? "DESC":"ASC";
            if ($column == "lastaccess") {
                $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
            } else {
                $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
            }
            $columnicon = "<img class='iconsort' src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

        }
        if (($column == 'rowcount') || ($column == 'edit')){
            $$column = $string[$column];
        }else{
            $$column = "<a href=\"meetings.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
        }

    }



    if ($sort) {
        $sortSq = " ORDER BY $sort $dir";
    }

    $sqlMeetings = "SELECT mm.id,
                           mm.userid,
                           u.firstname,
                           u.lastname,
                           mm.subject,
                           mm.courseid,
                           c.fullname AS coursename,
                           c.shortname AS courseshortname,
                           mm.joinurl, mm.maxParticipants,
                           mm.uniquemeetingid,
                           mm.conferenceCallInfo,
                           mm.meetingid,
                           mm.timecreated,
                           mm.timemodified,
                           mm.starttime,
                           mm.endtime,
                           mm.disabled
                      FROM {onlineclasses_meeting} mm
                INNER JOIN {user} u
                        ON mm.userid = u.id
                INNER JOIN {course} c
                        ON mm.courseid = c.id
                     WHERE mm.disabled = :disabled
                       AND mm.userid = :userid
                       AND mm.courseid = :courseid
                           $sortSq";


    $table = new html_table();
    $table->head = array(
           $rowcount,
           $firstname,
           $lastname,
           $subject,
           $joinurl,
           $uniquemeetingid,
           $starttime,
           $endtime,
           $timemodified,
           $edit
    );

    $table->align = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'center');
    //$table->wrap = array('nowrap','nowrap', 'nowrap','nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap');
    $table->width = '100%';
    //$table->size = array('*', '*', '*', '*', '*', '*', '*', '*', '*', '*');

    // iterate
    //for ($i = ($page * $perpage); ($i < ($page * $perpage) + $perpage) && ($i < $totalcount); $i++)
    $meetings = $DB->get_records_sql($sqlMeetings, array('disabled'=>0, 'userid'=>$USER->id, 'courseid'=>$courseid), $page * $perpage, $perpage);

    $counter = ($page * $perpage);

    foreach ($meetings as $meeting) {
       $row = new html_table_row();
       $cell_rowcount = ++$counter;

       $cell_firstname = new html_table_cell($meeting->firstname);
       $cell_lastname = new html_table_cell($meeting->lastname);

       $cell_subject = new html_table_cell($meeting->subject);

       $cell_joinurl = new html_table_cell('<a href="'.$meeting->joinurl.'">'.$meeting->joinurl.'</a>');

       $cell_uniquemeetingid = new html_table_cell($meeting->uniquemeetingid);
       $cell_starttime = new html_table_cell(($meeting->starttime > 0)?userdate($meeting->starttime):'-');
       $cell_endtime = new html_table_cell(($meeting->endtime > 0)?userdate($meeting->endtime):'-');
       $cell_timemodified = new html_table_cell(($meeting->timemodified > 0)?userdate($meeting->timemodified):'-');

       $actionurl_edit = new moodle_url('/blocks/onlineclasses/meetings.php?', array('id' => $meeting->id,'courseid' => $courseid,'action' => 'edit'));
       $actionurl_delete = new moodle_url('/blocks/onlineclasses/meetings.php?', array('id' => $meeting->id,'courseid' => $courseid,'action' => 'delete'));


       $actionHTML = '<a style="margin-right: 3px;" class"actionlink" href="'.$actionurl_edit->out().'" title="EDIT">';
       $actionHTML .= '<img class="actionicon" width="16" height="16" alt="EDIT" src="'.$CFG->wwwroot.'/blocks/onlineclasses/pix/cog_edit.png">';
       $actionHTML .= '</a>';

       $actionHTML .= "<a style=\"margin-right: 3px;\" onclick=\"return confirm('".get_string('deletemeeting_apr', 'block_onlineclasses')."')\" href=\"".$actionurl_delete->out()."\" title='DELETE'>";
       $actionHTML .= '<img class="actionicon" width="16" height="16" alt="DELETE" src="'.$CFG->wwwroot.'/blocks/onlineclasses/pix/delete.png">';
       $actionHTML .= '</a>';

       $cell_edit = new html_table_cell($actionHTML);

       $row->cells = array($cell_rowcount, $cell_firstname, $cell_lastname, $cell_subject, $cell_joinurl, $cell_uniquemeetingid, $cell_starttime, $cell_endtime, $cell_timemodified, $cell_edit);
       $table->data[] = $row;

    }

    echo html_writer::table($table);

    $addUserButton = $OUTPUT->single_button(new moodle_url($CFG->wwwroot.'/blocks/onlineclasses/meetings.php?courseid='.$courseid.'&action=add'), get_string('addmeeting', 'block_onlineclasses'), 'get');

    echo '<div class="adduserbuttonwrapper">'.$addUserButton.'</div>';
    echo "</div>";
}


echo $OUTPUT->footer();
