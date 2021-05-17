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
 * Local functions used in the definitions Block.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Retrieve a definition from the internet.
 *
 * @param string $word The word or phrase to look up
 * @param string $dictionary The dictionary to use
 * @param string $format The return format. Use "tabs" to return in tab format
 * @return stdClass The data for use externally.
 */
function block_definitions_retrieve_definition($word, $dictionary, $format = 'normal') {
    global $SESSION;

    if ($dictionary == 'thesaurus') {
        $SESSION->block_definition_dictionary = 'thesaurus';
        $dic = 'thesaurus';
        $api = get_config('block_definitions', 'api_thesaurus');
    } else {
        $SESSION->block_definition_dictionary = 'dictionary';
        $dic = get_config('block_definitions', 'dictionary');
        $api = get_config('block_definitions', 'api_collegiate');
    }

    if (strlen($api) == 0) {
        // We don't have the api key. Bail gracefully.
        return block_definitions_no_key();
    }

    $uri = 'https://dictionaryapi.com/api/v3/references/'
            . urlencode($dic)
            . '/json/'
            . $word
            . '?key='
            . urlencode($api);

    $definitions = json_decode(file_get_contents($uri));

    $ret = array();
    $matchfound = false;
    $nomatch = true;
    $closematch = false;
    $hideoffensive = get_config('block_definitions', 'hideoffensive');

    // For use when returnin in tabbed format.
    $tabs = array();
    // For use when returnin in tabbed format.
    $panels = array();
    $closematches = array();
    $x = 0;

    // Little bit of cleanup.
    $word = strtolower($word);
    $word = trim($word);
    $exact = true;

    if (gettype($definitions[0]) === "string") {
        // It's an array of close matches.
        $closematch = true;
        $nomatch = false;
        $closematches = array();
        foreach ($definitions as $definition) {
            $d = new stdClass();
            $d->word = $definition;
            $closematches[] = $d;
        }
    } else {
        foreach ($definitions as $definition) {
            /*
             * For some reason the API likes to give similar words as well (eg. "battle" will also return "battle-ax")
             * Make sure the we're only retrieving the actual word we're looking up.
             *
             * If the first matching word isn't an exact match, then we probably don't have an exact match
             * so we're going to retrieve the closest match.
             */

            if ($hideoffensive && $definition->meta->offensive) {
                continue;
            }
            $matchfound = true;
            $nomatch = false;
            $x++;
            $w = explode(':', $definition->meta->id);
            if ((strtolower($w[0]) === $word || $exact === false) || ($x === 1 && strtolower($w[0]) !== $word)) {
                if ($x === 1 && strtolower($w[0]) !== $word) {
                    // We didn't get an exact match so let's return everything.
                    $exact = false;
                }
                $tab = new stdClass();
                $panel = new stdClass();
                if ($x === 1) {
                    $tab->selected = true;
                    $panel->selected = true;
                } else {
                    $tab->selected = false;
                    $panel->selected = false;
                }
                $tab->id = 'tab_def_' . $x;
                $panel->id = 'panel_def_' . $x;
                $tab->target = $panel->id;

                $title = $w[0];
                if (count($w) > 1) {
                    $title .= ' (' . $w[1] . ')';
                }

                $tab->title = $title;
                $panel->word = $w[0];
                $panel->fl = $definition->fl;
                $panel->hasins = false;
                $panel->ins = '';
                if (property_exists($definition, 'vrs')) {
                    // Find what it's similar to.
                    foreach ($definition->vrs as $vrs) {
                        $panel->hasins = true;
                        $panel->ins = '<i>' . $vrs->vl . '</i> ' . str_replace('*', '', $vrs->va);
                    }
                }
                $cxs = array();
                if (property_exists($definition, 'cxs')) {
                    foreach ($definition->cxs as $c) {
                        $cx = new stdClass();
                        $cx->html = '<i>' . $c->cxl . '</i> ';
                        $a = 0;
                        foreach ($c->cxtis as $cxtis) {
                            if ($a > 1) {
                                $cx->html .= ', ';
                            }
                            $cw = explode(':', $cxtis->cxt);
                            $cx->html .= '<a href="#" data-define="' . $cw[0] . '">' . $cw[0] . '</a>';
                            $a++;
                        }
                        $cxs[] = $cx;
                    }
                    $panel->hascxs = true;
                } else {
                    $panel->hascxs = false;
                }
                $panel->cxs = $cxs;
                $def = array();
                if ($dictionary === 'thesaurus') {
                    $i = 1;
                    foreach ($definition->def as $d) {
                        foreach ($d->sseq as $sense) {
                            $sense = $sense[0][1];
                            $a = new stdClass();
                            $a->num = $i;
                            foreach ($sense->dt as $d) {
                                if ($d[0] === 'text') {
                                    $a->text = $d[1];
                                }
                            }

                            if (property_exists($sense, 'syn_list')) {
                                $synlist = array();
                                foreach ($sense->syn_list as $s) {
                                    $sr = array();
                                    foreach ($s as $syn) {
                                        $sr[] = $syn->wd;
                                    }
                                    $synlist[] = implode(', ', $sr);
                                }
                                $a->syn_heading = 'Synonyms for <em>' . $w[0] . '</em>';
                                $a->syn_list = implode('<br>', $synlist);
                            }

                            if (property_exists($sense, 'rel_list')) {
                                $rellist = array();
                                foreach ($sense->rel_list as $s) {
                                    $sr = array();
                                    foreach ($s as $syn) {
                                        $sr[] = $syn->wd;
                                    }
                                    $rellist[] = implode(', ', $sr);
                                }
                                $a->rel_heading = 'Words related to <em>' . $w[0] . '</em>';
                                $a->rel_list = '';
                                foreach ($rellist as $r) {
                                    $a->rel_list .= '<p>' . $r . '</p>';
                                }
                            }

                            if (property_exists($sense, 'near_list')) {
                                $nearlist = array();
                                foreach ($sense->nearlist as $s) {
                                    $sr = array();
                                    foreach ($s as $syn) {
                                        $sr[] = $syn->wd;
                                    }
                                    $nearlist[] = implode(', ', $sr);
                                }
                                $a->near_heading = 'Near Antonyms for <em>' . $w[0] . '</em>';
                                $a->near_list = '';
                                foreach ($nearlist as $r) {
                                    $a->near_list .= '<p>' . $r . '</p>';
                                }
                            }

                            if (property_exists($sense, 'ant_list')) {
                                $antlist = array();
                                foreach ($sense->ant_list as $s) {
                                    $sr = array();
                                    foreach ($s as $syn) {
                                        $sr[] = $syn->wd;
                                    }
                                    $antlist[] = implode(', ', $sr);
                                }
                                $a->ant_heading = 'Antonyms for <em>' . $w[0] . '</em>';
                                $a->ant_list = '';
                                foreach ($antlist as $r) {
                                    $a->ant_list .= '<p>' . $r . '</p>';
                                }
                            }

                            $def[] = $a;
                            $i++;
                        }
                    }
                } else {
                    $i = 1;
                    foreach ($definition->shortdef as $d) {
                        $a = new stdClass();
                        $a->num = $i;
                        $a->text = $d;
                        $def[] = $a;
                        $i++;
                    }
                }
                $panel->def = $def;

                $tabs[] = $tab;
                $panels[] = $panel;
            }
        }
    }
    if ($format === 'tabs') {
        $ret = new stdClass();
        $ret->template = $dictionary;
        $ret->title = get_string('definitionfor', 'block_definitions') . $word;
        if (count($tabs) > 1) {
            $ret->showtabs = true;
        } else {
            $ret->showtabs = false;
        }
        $ret->matchfound = $matchfound;
        $ret->closematch = $closematch;
        $ret->nomatch = $nomatch;
        $ret->tabs = $tabs;
        $ret->panels = $panels;
        if ($closematch) {
            $ret->closematches = $closematches;
        } else {
            $ret->closematches = array();
        }
    }

    return $ret;
}

/**
 * Used to gracefully fail when there's no api key
 *
 * @return stdClass The data used externally.
 */
function block_definitions_no_key() {
    $ret = new stdClass();
    $ret->template = 'errormessage';
    $ret->title = 'Not Configured Properly';
    $ret->modalmessage = get_string('nokey', 'block_definitions');

    return $ret;
}
