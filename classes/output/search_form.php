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
 * Search form renderable.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_definitions\output;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Search form renderable class.
 *
 * @package    block_definitions
 * @copyright  2021 Pembina Hills School Division. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_form implements renderable, templatable {

    /** @var moodle_url The form action URL. */
    protected $actionurl;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->actionurl = new moodle_url('');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $SESSION;

        if (property_exists($SESSION, 'block_definition_dictionary')) {
            $dictionary = $SESSION->block_definition_dictionary;
        } else {
            $dictionary = 'dictionary';
        }
        $data = [
            'actionurl' => $this->actionurl->out(false),
            'mwlogo' => $output->image_url('mwlogo', 'block_definitions')->out(),
            'selectdictionary' => $dictionary === 'dictionary' ? true : false,
            'selectthesaurus' => $dictionary === 'thesaurus' ? true : false
            ];
        return $data;
    }

}
