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
 * Helper for dealing with IDs.
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
 * Helper for dealing with IDs.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill (merrill@oakland.edu)
 * @copyright  2019 Oakland University (https://www.oakland.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    protected static $tried = [];
    protected static $fieldid = null;

    public static function populate_all_users($fieldid = null) {
        global $DB;

        if (!empty($fieldid)) {
            self::$fieldid = $fieldid;
        }

        $usercount = $DB->count_records('user');

        $lock = lock_factory::get_lock('idcreation');

        if (empty($lock)) {
            debugging("Could not get the idcreation lock. Exiting.", DEBUG_DEVELOPER);

            return;
        }

        // We are going to use a progress bar if there are lots of users.
        // Will throw a NO_OUTPUT_BUFFERING error, but I don't think there is anything that can be done.
        if ($usercount > 10000) {
            $progress = new \core\progress\display(true);
        } else {
            $progress = new \core\progress\none();
        }

        $users = $DB->get_recordset('user', null, '', 'id');

        $progress->start_progress('populate_users', $usercount);

	    $count = 0;
        foreach ($users as $user) {
            self::set_user_value($user->id, true);
            $count++;
            $progress->progress($count);
        }

        $progress->end_progress();
        $users->close();

        if ($lock) {
            $lock->release();
        }
    }

    public static function set_user_value($userid, $locked = false) {
        global $DB;

        $fieldid = self::get_field_id();

        if (empty($fieldid)) {
            return false;
        }

        if ($DB->record_exists('user_info_data', ['fieldid' => $fieldid, 'userid' => $userid])) {
            return false;
        }

        if (!$locked) {
            $lock = lock_factory::get_lock('idcreation');

            if (empty($lock)) {
                debugging("Could not get the idcreation lock. Exiting.", DEBUG_DEVELOPER);

                return;
            }
        }

        $random = self::generate_unique_id($fieldid);

        $data = new stdClass();
        $data->userid = $userid;
        $data->fieldid = $fieldid;
        $data->data = $random;
        $data->dataformat = FORMAT_PLAIN;
        $data->id = $DB->insert_record('user_info_data', $data);

        if ($lock) {
            $lock->release();
        }

        return (bool)$data->id;
    }

    protected static function generate_unique_id($fieldid) {
        global $DB;

        // TODO - limit number of attempts.
        $good = false;
        while (!$good) {
            $random = self::generate_random_id($fieldid);

            // Check if somebody already has this key.
            $select = 'fieldid = :fieldid
                        AND '.$DB->sql_compare_text('data').' = :data';
            if ($DB->record_exists_select('user_info_data', $select, ['fieldid' => $fieldid, 'data' => $random])) {
                continue;
            }

            $good = true;
        }

        return $random;
    }

    protected static function generate_random_id($fieldid) {

        // TODO - limit the number of times we do through this.
        $good = false;
        while (!$good) {
            $random = mt_rand(1001, 999999);

            $random = str_pad($random, 6, '0', STR_PAD_LEFT);

            // We are going to track ones we try so that we don't retry them...
            if (isset(self::$tried[$fieldid][$random])) {
                continue;
            }

            self::$tried[$fieldid][$random] = 1;

            // We don't want any with 3 or more identical characters in a row.
            if (preg_match('/([0-9])\1{2}/', $random)) {
                continue;
            }

            $good = true;
        }

        return $random;
    }

    protected static function get_field_id() {
        global $DB;

        if (!is_null(self::$fieldid)) {
            return self::$fieldid;
        }

        $records = $DB->get_records('user_info_field', ['datatype' => 'akindiid'], 'id ASC', 'id');

        if (empty($records)) {
            self::$fieldid = false;
        } else {
            $record = reset($records);
            self::$fieldid = $record->id;
        }


        return self::$fieldid;
    }


}
