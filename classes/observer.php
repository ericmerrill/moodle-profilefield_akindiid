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
 * An observer for dealing with updating user fields.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill (merrill@oakland.edu)
 * @copyright  2019 Oakland University (https://www.oakland.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace profilefield_akindiid;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * An observer for dealing with updating user fields.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill (merrill@oakland.edu)
 * @copyright  2019 Oakland University (https://www.oakland.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    // Make sure the user has an Akindi ID assigned.
    public static function update_user($event) {
        global $DB;

        // Observers should never throw exceptions, so we are going to wrap a generic catch.
        try {
            $userid = $event->objectid;

            if (!empty($userid)) {
                helper::set_user_value($userid);
            }

        } catch (\Exception $e) {
            // Do nothing.
            debugging("An exception occurred in the akindiid user observer.", DEBUG_DEVELOPER, $e->getTrace());
            return;
        }

    }

    public static function field_created($event) {
        // Observers should never throw exceptions, so we are going to wrap a generic catch.
        try {
            $fieldid = $event->objectid;

            if (!empty($fieldid)) {
                $field = $event->get_record_snapshot('user_info_field', $fieldid);
                if ($field->datatype !== 'akindiid') {
                    // This isn't a field of our type...
                    return;
                }
                helper::populate_all_users($fieldid);
            }

        } catch (\Exception $e) {
            // Do nothing.
            debugging("An exception occurred in the akindiid field observer.", DEBUG_DEVELOPER, $e->getTrace());
            return;
        }
    }

}
