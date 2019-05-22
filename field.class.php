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
 * Text profile field with access restrictions.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2019 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class profile_field_text_access
 *
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2019 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_akindiid extends profile_field_base {

    public function edit_field_add($mform) {

        $fieldtype = 'static';

        // Create the form field.
        $mform->addElement($fieldtype, $this->inputname, format_string($this->field->name));
        $mform->setType($this->inputname, PARAM_TEXT);
        //$mform->setValue($this->inputname, "Adsa");


        return true;
    }

    public function is_visible() {
        global $USER, $PAGE;

        $view = parent::is_visible();

        if ($view) {
            return true;
        }

        $context = $PAGE->context;

        // We only show the field to people with this capability.
        return has_capability('profilefield/akindiid:view', $context);
    }

    public function display_data() {
        // Default formatting.
        $data = parent::display_data();

        if (empty($data)) {
            return false;
        }

        $format = 'A%u%u%u-%u%u%u';

        $values = str_split($data);


        $result = vsprintf($format, $values);

        return $result;
    }

    public function get_field_properties() {
        return array(PARAM_TEXT, NULL_NOT_ALLOWED);
    }

}


