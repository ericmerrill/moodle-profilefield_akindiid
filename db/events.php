<?php
// This file is part of the login plugin for Moodle
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
 * Observers to register.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill (merrill@oakland.edu)
 * @copyright  2019 Oakland University (https://www.oakland.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(
    // Detect course restore events.
    [
        'eventname' => '\core\event\user_created',
        'callback' => '\profilefield_akindiid\observer::update_user',
        'internal' => false
    ],
    [
        'eventname' => '\core\event\user_updated',
        'callback' => '\profilefield_akindiid\observer::update_user',
        'internal' => false
    ],
    [
        'eventname' => '\core\event\user_info_field_created',
        'callback' => '\profilefield_akindiid\observer::field_created',
        'internal' => false
    ]
);
