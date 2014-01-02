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
 * Strings for component 'block_onlineclasses', language 'en'
 *
 * @package   block_onlineclasses
 * @copyright Michael Gardener <mgardener@cissq.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['onlineclasses:addinstance'] = 'Add a onlineclasses block';
$string['onlineclasses:myaddinstance'] = 'Add a onlineclasses block to my moodle';
$string['pluginname'] = 'Online Classes';
$string['schedulenewmeeting'] = 'Schedule new class';
$string['adminhead'] = 'Admin head';
$string['admindescription'] = 'Admin description';
$string['upcomingmeeting'] = 'Upcoming classes';
$string['gotomeeting'] = 'GoToMeeting';

# settings
$string['onlineclasses_settings_heading'] = 'GoToMeeting';
$string['onlineclasses_settings_heading_desc'] = 'These settings are for the GoToMeeting plugin. ';
$string['onlineclasses_settings_enabled'] = 'Enable GoToMeeting';
$string['onlineclasses_settings_enabled_desc'] = 'Enables/disables the GoToMeeting plugin.';

$string['onlineclasses_settings_admin_key'] = 'Setup admin key.';
$string['onlineclasses_settings_key'] = 'GoToMeeting API Key';
$string['onlineclasses_settings_key_desc'] = 'GoToMeeting Application Developer API Key';
$string['onlineclasses_settings_connect_message'] = 'Connect Message';
$string['onlineclasses_settings_connect_message_desc'] = 'Message displayed on the GoToMeeting connect window.';

# connect.php
$string['onlineclasses_connecting'] = "You are being connected to GoToMeeting. Once it opens you may ";
$string['onlineclasses_close'] = "close both windows.";

# token.php
$string['renewtoken_desc'] = "
    <p>If your token has expired, you cannot use it to log on. You must request a replacement token.
    To get a new token.</p>
    <ol><li>Click the button.</li>
    <li>Log in to GoToMeeting.</li>
    <li>Click &quot;Allow&quot;.</li></ol>";
$string['onlineclasses_token_link'] = "Allow Access";

$string['block_title'] = 'Block Title';
$string['settings'] = 'Settings';
$string['token'] = 'Token';
$string['renewtoken'] = 'Renew token';
$string['onlineclasses_settings_callbackurl'] = 'Callback-url';
$string['onlineclasses_settings_callbackurl_desc'] = "The [callback-url] should be the same URL your sending the
            request from and the API key is provided in your Citrix developer account, on your application screen.
            This code will redirect you to Citrix for validation, redirect you back to your [callback-url]
            and eventually dump the data on your screen, which you can then store manually or handle in a session.";
$string['account_type'] = 'Account Type';
$string['email'] = 'Email';
$string['access_token'] = 'Access Token';
$string['expirationdate'] = 'Token Expiration Date';

# meetings.php
$string['meetings_breadcrumb'] = 'Class List';
$string['meetings_title'] = 'Classes';
$string['rowcount'] = '';
$string['firstname'] = 'First Name';
$string['lastname'] = 'Last Name';
$string['subject'] = 'Subject';
$string['joinurl'] = 'Join URL';
$string['uniquemeetingid'] = 'Meeting ID';
$string['starttime'] = 'Time Start';
$string['endtime'] = 'Time End';
$string['timemodified'] = 'Time Modified';
$string['edit'] = '';
$string['addmeeting'] = 'Add new class';

# meetings_form.php
$string['meeting'] = 'Online Class';
$string['createmeeting'] = 'Create Online Class';
$string['subject'] = 'Subject';
$string['starttime'] = 'Start Time';
$string['endtime'] = 'End Time';
$string['meetingtype'] = 'Meeting Type';
$string['passwordrequired'] = 'Password Required';
$string['conferencecallinfo'] = 'Conference Call Info';
$string['deletemeeting_apr'] = 'Do you want to delete scheduled meeting?';
$string['meetingnotfound'] = 'Wrong meeting id: ';
$string['addsuccessful'] = 'Successfully added!';
$string['updatesuccessful'] = 'Successfully updated!';
$string['updateerror'] = 'Update error!';
$string['deletesuccessful'] = 'Successfully deleted!';
$string['deleteerror'] = 'Delete error!';
$string['notoken'] = " To continue we need to be authorized to create GoToMeeting sessions on your behalf.
                        <ol><li>Click the link below.</li>
                        <li>Log in to GoToMeeting.</li>
                        <li>Click &quot;Allow&quot;.</li></ol>";
$string['blocksettingerror'] = 'Please check GotoMeeting API key and call back url settings';