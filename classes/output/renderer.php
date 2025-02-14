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
 * Block definitions renderer.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_definitions\output;

use plugin_renderer_base;
use renderable;

/**
 * Block definitions renderer.
 *
 * @package    block_definitions
 * @copyright  2021 Pembina Hills School Division. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render search form.
     *
     * @param renderable $searchform The search form.
     * @return string
     */
    public function render_search_form(renderable $searchform) {
        return $this->render_from_template('block_definitions/search_form', $searchform->export_for_template($this));
    }

}
