<?php
    require_once( '../../config.php');
    require_once($CFG->dirroot.'/blocks/meetingcenter/lib.php');

    $courseid = optional_param('courseid', 0, PARAM_INT);

    require_login(0, false);

    if($token = $DB->get_record('meetingcenter_token', array('userid'=>$USER->id, 'disabled'=>0))){ //print_r($token);die;

        // sample GoToMeeting API call: create meeting
        // docs: https://developer.citrixonline.com/api/gotomeeting-rest-api/apimethod/create-meeting

        // set valid access_token from OAuth flow
        $access_token = $token->access_token;

        //$token->organizer_key

        $url = "https://api.citrixonline.com/G2M/rest/meetings";
        $headers = array (
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: OAuth oauth_token=$access_token"
        );
        $data = array (
            'subject' => 'Sample meeting created via API by CISSQ-3',
            'starttime' => '2013-11-01T08:00:00',
            'endtime' => '2013-11-01T09:00:00',
            'timezonekey' => '',
            'meetingtype' => 'Scheduled',
            'passwordrequired' => 'false',
            'conferencecallinfo' => 'Hybrid' // normal PSTN + VOIP options
        );
        $data_json = json_encode ($data);

        $ch = curl_init();
        curl_setopt_array ($ch, array (
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data_json,
        ));
        /*
        $results = curl_exec ($ch);
        $info = curl_getinfo ($ch);
        curl_close ($ch);

        $data = json_decode($results);
        $data = reset($data);
        $data->userid = $USER->id;
        $data->courseid = $courseid;
        $data->timecreated = time();
        $data->timestart = time();//FORM DAN AL
        $data->timeend = time();//FORM DAN AL
        $DB->insert_record('meetingcenter_meeting', $data);
        */
        /** sample output:

        data sent: {"subject":"Sample meeting created via API","starttime":"2013-03-01T08:00:00","endtime":"2013-03-01T09:00:00","timezonekey":"","meetingtype":"Scheduled","passwordrequired":"false","conferencecallinfo":"Hybrid"}
        data returned: [{"joinURL":"https:\/\/www3.gotomeeting.com\/join\/569897550","maxParticipants":51,"uniqueMeetingId":200000000016584808,"conferenceCallInfo":"United States: +1 (636) 277-0130\nAccess Code: 569-897-550","meetingid":569897550}]

        1.  Please join my meeting, Friday, November 01, 2013 at 4:00 AM Eastern Daylight Time.
        https://www1.gotomeeting.com/join/925660992

        2.  Use your microphone and speakers (VoIP) - a headset is recommended.  Or, call in using your telephone.

        Dial +1 (647) 497-9372
        Access Code: 925-660-992
        Audio PIN: Shown after joining the meeting

        Meeting ID: 925-660-992

        GoToMeeting®
        Online Meetings Made Easy®

        Not at your computer? Click the link to join this meeting from your iPhone®, iPad® or Android® device via the GoToMeeting app.


         **/
    }else{
        redirect($CFG->wwwroot.'/blocks/meetingcenter/token.php?authorize=1&courseid='.$courseid);
    }
?>