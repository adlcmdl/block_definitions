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
 * Version details for the definitions block.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2025011700;         // The current plugin version (Date: YYYYMMDDXX).
$plugin->release = '1.2.1';
$plugin->requires  = 2019111800;         // Requires this Moodle version. (3.8).
$plugin->maturity = MATURITY_STABLE;
$plugin->component = 'block_definitions'; // Full name of the plugin (used for diagnostics).
