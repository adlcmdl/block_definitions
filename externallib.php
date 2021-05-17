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
 * External API
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/blocks/definitions/locallib.php");

/**
 * Definition block functions
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_definitions_external extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function get_definition_parameters() {
        return new external_function_parameters(
                array(
                    'word' => new external_value(PARAM_TEXT, 'the word to define'),
                    'dictionary' => new external_value(PARAM_TEXT, '')
                )
        );
    }

    /**
     * Get definition.
     *
     * @param string $word The word to define
     * @param string $dictionary The dictionary to use
     * @return array An array of definitions
     */
    public static function get_definition($word, $dictionary) {
        $params = self::validate_parameters(
                        self::get_definition_parameters(),
                        array(
                            'word' => $word,
                            'dictionary' => $dictionary
                        )
        );

        $ret = block_definitions_retrieve_definition($word, $dictionary, 'tabs');
        $ret->containerid = "D" . uniqid();
        return $ret;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_definition_returns() {
        return new external_single_structure(
            array(
                'template' => new external_value(PARAM_TEXT, 'The name of the template to render'),
                'containerid' => new external_value(PARAM_TEXT, 'A unique ID for the tab container', VALUE_OPTIONAL),
                'title' => new external_value(PARAM_TEXT, 'The title of the modal'),
                'matchfound' => new external_value(PARAM_BOOL, 'Set to true if at least one definition was found', VALUE_OPTIONAL),
                'nomatch' => new external_value(PARAM_BOOL, 'Set to true if not matches were found', VALUE_OPTIONAL),
                'closematch' => new external_value(PARAM_BOOL,
                        'Set to true if no entry was found, but close matches were', VALUE_OPTIONAL),
                'showtabs' => new external_value(PARAM_BOOL, 'Should the tabs be displayed', VALUE_OPTIONAL),
                'tabs' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'selected' => new external_value(PARAM_BOOL, 'Is this tab selected?'),
                            'id' => new external_value(PARAM_TEXT, 'A unique ID for this tab'),
                            'target' => new external_value(PARAM_TEXT, 'The target panel for this tab'),
                            'title' => new external_value(PARAM_TEXT, 'The title of this tab')
                        )
                    )
                , 'The tabs for each definition', VALUE_OPTIONAL),
                'panels' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_TEXT, 'A unique ID for this panel'),
                            'word' => new external_value(PARAM_TEXT, 'The word'),
                            'selected' => new external_value(PARAM_BOOL, 'Is this panel selected?'),
                            'hasins' => new external_value(PARAM_BOOL, 'Is this word related to a similar one'),
                            'ins' => new external_value(PARAM_RAW, 'Similar word'),
                            'fl' => new external_value(PARAM_TEXT, 'Functional label'),
                            'def' => new external_multiple_structure(
                                    new external_single_structure(
                                            array(
                                                'num' => new external_value(PARAM_INT, 'The definition numnber'),
                                                'text' => new external_value(PARAM_TEXT, 'The definition text'),
                                                'syn_heading' => new external_value(PARAM_RAW,
                                                        'The heading for the synonym list', VALUE_OPTIONAL),
                                                'syn_list' => new external_value(PARAM_RAW,
                                                        'A comma-separated list of synonyms', VALUE_OPTIONAL),
                                                'rel_heading' => new external_value(PARAM_RAW,
                                                        'The heading for the related words list', VALUE_OPTIONAL),
                                                'rel_list' => new external_value(PARAM_RAW,
                                                        'A comma-separated list of related words', VALUE_OPTIONAL),
                                                'near_heading' => new external_value(PARAM_RAW,
                                                        'The heading for the near antonym words list', VALUE_OPTIONAL),
                                                'near_list' => new external_value(PARAM_RAW,
                                                        'A comma-separated list of near antonym words', VALUE_OPTIONAL),
                                                'ant_heading' => new external_value(PARAM_RAW,
                                                        'The heading for the antonym words list', VALUE_OPTIONAL),
                                                'ant_list' => new external_value(PARAM_RAW,
                                                        'A comma-separated list of antonym words', VALUE_OPTIONAL)
                                                )
                                        )
                                ),
                            'hascxs' => new external_value(PARAM_BOOL, 'Does this panel have a cross-reference?'),
                            'cxs' => new external_multiple_structure(
                                    new external_single_structure(
                                            array(
                                                'html' => new external_value(PARAM_RAW, 'Cross-reference HTML')
                                            )
                                    )
                            )
                        )
                    ), 'The panels for each definition', VALUE_OPTIONAL
                ),
                'closematches' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'word' => new external_value(PARAM_TEXT, 'The word')
                            )
                        ), 'A list of close matches', VALUE_OPTIONAL
                ),
                'modalmessage' => new external_value(PARAM_RAW,
                        'The message to display in a modal if there is an error', VALUE_OPTIONAL),
            )
        );
    }

}
