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
 * Global settings for the definitions block.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $dictionaries = array('collegiate' => 'Collegiate Dictionary');

    // The Dictionary to use.
    $title = get_string('config_dictionary', 'block_definitions');
    $desc = get_string('config_dictionary_desc', 'block_definitions');
    $settings->add(new admin_setting_configselect('block_definitions/dictionary', $title, $desc, 'collegiate', $dictionaries));

    // The API Key for the collegiate dictionary.
    $title = get_string('collegiate_api', 'block_definitions');
    $desc = get_string('collegiate_api_desc', 'block_definitions');
    $settings->add(new admin_setting_configtext('block_definitions/api_collegiate', $title, $desc, ''));

    // The API Key for the thesaurus.
    $title = get_string('thesaurus_api', 'block_definitions');
    $desc = get_string('thesaurus_api_desc', 'block_definitions');
    $settings->add(new admin_setting_configtext('block_definitions/api_thesaurus', $title, $desc, ''));

    // Should we hide offensive words?
    $title = get_string('hideoffensive', 'block_definitions');
    $desc = get_string('hideoffensivedesc', 'block_definitions');
    $settings->add(new admin_setting_configcheckbox('block_definitions/hideoffensive', $title, $desc, '0'));
}
