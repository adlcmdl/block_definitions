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
 * Client-side script to look up words and present them in a dialog.
 *
 * @package   block_definitions
 * @author    Tim Martinez <tim.martinez@adlc.ca>
 * @copyright 2021 Pembina Hills School Division. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/templates',
    'core/modal_factory'], function($, Ajax, Notification, Templates, ModalFactory) {

    /**
     * Take the response from the server and display it.
     * @param object response The response from the Ajax request to the server.
     * @returns void
     */
    function _displayDefinition(response) {
        var template = 'block_definitions/' + response.template;
        Templates.render(template, response).then(function(html, javascript) {
            ModalFactory.create({
                title: 'Definition',
                body: html,
                footer: '<button id="closeDialog" type="button" class="btn btn-primary" data-action="hide">Close</button>'
            }).done(function(modal) {
                modal.show();
                Templates.runTemplateJS(javascript);
                $('a[data-define]').on('click', function() {
                    modal.hide();
                    modal.destroy();
                    $('#definitions_search').val($(this).attr('data-define'));
                    searchDefinition($(this).attr('data-define'), $('input[name="selectdictionary"]:checked').val());
                });
            });
        }).fail(Notification.exception);
    }

    /**
     * Given a word and dictionary, search for the word using AJAX.
     *
     * @param string def The word to search for.
     * @param string dic The dictionary to use.
     * @returns void
     */
    function searchDefinition(def, dic) {
        var data;
        Ajax.call([{
                methodname: 'block_definitions_get_definition',
                args: {word: def, dictionary: dic},
                done: function(data) {
                    _displayDefinition(data);
                }.bind(data),

                fail: Notification.exception
            }]);
    }

    return {
        init: function() {
            $(document).on('click', '#searchform_button', function(e) {
                e.preventDefault();
                var def = $('#definitions_search').val();
                var dic = $('input[name="selectdictionary"]:checked').val();
                searchDefinition(def, dic);
            });
        }
    };
});