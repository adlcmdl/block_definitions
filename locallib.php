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

/*
 * Retrieve a definition from the internet.
 * 
 * $word string The word or phrase to look up
 * $format string The return format. Use "tabs" to return in tab format
 */

function block_definitions_retrieve_definition($word, $format = 'normal') {
    $uri = 'https://dictionaryapi.com/api/v3/references/'
            . urlencode(get_config('block_definitions', 'dictionary'))
            . '/json/'
            . $word
            . '?key='
            . urlencode(get_config('block_definitions', 'api_collegiate'));

    $definitions = json_decode(file_get_contents($uri));

    $ret = array();
    $matchfound = false;
    $nomatch = true;
    $closematch = false;
    $hideoffensive = get_config('block_definitions', 'hideoffensive');

    $tabs = array();    //For use when returnin in tabbed format.
    $panels = array();    //For use when returnin in tabbed format.
    $closematches = array();
    $x = 0;
    
    // Little bit of cleanup
    $word = strtolower($word);
    $word = trim($word);
    $exact = true;
    
    if (gettype($definitions[0]) === "string") {
        //It's an array of close matches
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
                    //We didn't get an exact match so let's return everything
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
                if (strtolower($w[0]) !== $word) {
                    //Find what it's similar to
                    foreach ($definition->vrs as $vrs) {
                        if (str_replace('*', '', $vrs->va) === $word) {
                            $panel->hasins = true;
                            $panel->ins = '<i>' . $vrs->vl . '</i> ' . str_replace('*', '', $vrs->va);
                        }
                    }
                }
                $cxs = array();
                if (property_exists($definition, 'cxs')) {
                    foreach($definition->cxs as $c) {
                        $cx = new stdClass();
                        $cx->html = '<i>' . $c->cxl . '</i> ';
                        $a = 0;
                        foreach($c->cxtis as $cxtis) {
                            if ($a > 1) {
                                $cx->html .= ', ';
                            }
                            $cw = explode(':', $cxtis->cxt);
                            $cx->html .= '<a href="#" data-define="' . $cw[0] . '">' . $cw[0] . '</a>';
                            $a++;
                        }
                        $cxs[] = $cx;
                    }
                    $panel->hascxs =true;
                } else {
                    $panel->hascxs =false;
                }
                $panel->cxs = $cxs;
                $def = array();
                $i = 1;
                foreach ($definition->shortdef as $d) {
                    $a = new stdClass();
                    $a->num = $i;
                    $a->text = $d;
                    $def[] = $a;
                    $i++;
                }
                $panel->def = $def;

                $tabs[] = $tab;
                $panels[] = $panel;
            }
        }
    }
    if ($format === 'tabs') {
        $ret = new stdClass();
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
