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
 * driver block caps.
 *
 * @package    block_onlineclasses
 * @copyright  Michael Gardener <mgardener@cissq.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext('block_onlineclasses/gotomeeting_key',
                                            get_string('onlineclasses_settings_key', 'block_onlineclasses'),
                                            get_string('onlineclasses_settings_key_desc', 'block_onlineclasses'),
                                            '',
                                            PARAM_TEXT,
                                            100));
$settings->add(new admin_setting_configtext('block_onlineclasses/gotomeeting_callbackurl',
                                            get_string('onlineclasses_settings_callbackurl', 'block_onlineclasses'),
                                            get_string('onlineclasses_settings_callbackurl_desc', 'block_onlineclasses'),
                                            $CFG->wwwroot.'/blocks/onlineclasses/token.php',
                                            PARAM_TEXT,
                                            100));