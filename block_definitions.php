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
 * Contains the class for the definitions block.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once('locallib.php');

/**
 * Display the dictionary block
 */
class block_definitions extends block_base {

    /**
     * Initialise the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_definitions');
    }

    /**
     * Return the content of this block.
     *
     * @return stdClass the content
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $output = $this->page->get_renderer('block_definitions');
        $searchform = new \block_definitions\output\search_form();
        $this->content->text = $output->render($searchform);
        return $this->content;
    }

    /**
     * Allows the block to load any JS it requires into the page.
     *
     * By default this function simply permits the user to dock the block if it is dockable.
     */
    public function get_required_javascript() {
        parent::get_required_javascript();

        $this->page->requires->js_call_amd('block_definitions/definitions', 'init');
    }

    /**
     * Returns true if this block has global config.
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'site' => false,
            'course-view' => true,
            'mod' => true
        );
    }

}
