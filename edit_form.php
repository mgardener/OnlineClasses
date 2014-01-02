<?php

class block_meetingcenter_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // A sample string variable with a default value.
        $mform->addElement('text', 'config_blocktitle', get_string('block_title', 'block_meetingcenter'));
        $mform->setDefault('config_blocktitle', get_string('pluginname', 'block_meetingcenter'));
        $mform->setType('config_blocktitle', PARAM_MULTILANG);

    }
}
