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
 * onlineclasses block caps.
 *
 * @package    block_onlineclasses
 * @copyright  Michael Gardener <mgardener@cissq.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_onlineclasses extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_onlineclasses');
    }

    public function specialization() {
        if (!empty($this->config->blocktitle)) {
            $this->title = $this->config->blocktitle;
        } else {
            $this->title = get_string('pluginname', 'block_onlineclasses');
        }
    }

    function get_content() {
        global $CFG, $OUTPUT, $USER, $DB, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';
        /*
        $context = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $isteacher = has_capability('moodle/grade:viewall', $context);

        if (!$isteacher) {
            return $this->content;
        }
        */

        if ($COURSE->id == SITEID) {
            return $this->content;
        }
      // user/index.php expect course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);

        if (empty($currentcontext)) {
            return $this->content;
        }

        $isteacher = has_capability('moodle/grade:viewall', $currentcontext);

        if ($isteacher) {

            $this->content->text .= '<div class="onlineclasses_button center"><form action="' . $CFG->wwwroot . '/blocks/onlineclasses/meetings.php?courseid='.$COURSE->id.'" method="post">
                                       <button class="onlineclasses_button btn btn-large btn-block btn-primary" type="submit" value="Submit">'.get_string('schedulenewmeeting', 'block_onlineclasses').'</button>
                                       </form>
                                     </div>';

        }


        if ($meetings = $DB->get_records_select("onlineclasses_meeting", "starttime > ". time().' AND courseid = '.$COURSE->id)) {
            $this->content->text .= '<div class="onlineclasses_upcomingevent">'.get_string('upcomingmeeting', 'block_onlineclasses').'</div>';

            foreach ($meetings as $meeting) {
                $this->content->text .= '<div class="onlineclasses_upcomingevents"><a target="_blank" onclick="return confirm(\'Do you want to join meeting?\')" href="' . $meeting->joinurl . '">' . $meeting->subject . '</a><br/>'.date('l, F j, Y \a\t g:i A', $meeting->starttime).'</div>';
            }
        }

        if ($isteacher) {
            $this->content->text .= '<div class="onlineclasses_button center">
                                        <a href="' . $CFG->wwwroot . '/blocks/onlineclasses/token.php?authorize=1&courseid='.$COURSE->id.'">'.get_string('settings', 'block_onlineclasses').'</a>
                                     </div>';
        }


        return $this->content;
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_multiple() {
          return false;
    }

    function has_config() {
        return true;
    }

    public function cron() {
        global $CFG, $DB;

        mtrace( "BLOCK OnlineClasses" );
        $today = time();

        return true;
    }
}
