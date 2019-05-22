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
 * Testing the helper.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill (merrill@oakland.edu)
 * @copyright  2019 Oakland University (https://www.oakland.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use profilefield_akindiid\helper;

/**
 * Helper for dealing with IDs.
 *
 * @package    profilefield_akindiid
 * @author     Eric Merrill (merrill@oakland.edu)
 * @copyright  2019 Oakland University (https://www.oakland.edu)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_testcase extends \advanced_testcase {


    public function test_generate_random_id() {

        // We do this a lot of times to make sure that we are likely to catch any errors.
        for ($i = 0; $i < 100000; $i++) {
            //$random = helper::generate_random_id(1);
            $random = $this->run_protected_method(helper::class, 'generate_random_id', [1]);

            $this->assertNotEmpty($random);

            // Make sure it is 6 digits long.
            $res = (bool)preg_match('/^([0-9]){6}$/', $random);
            $this->assertTrue($res, "The key '{$random}' was generated and failed.");

            // Confirm that a digit isn't repeated more that 3 times in a row.
            $res = (bool)preg_match('/([0-9])\1{2}/', $random);
            $this->assertFalse($res, "The key '{$random}' was generated and failed.");
        }



    }


    protected function run_protected_method($obj, $name, $args = []) {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        if ($method->isStatic()) {
            $obj = null;
        }
        return $method->invokeArgs($obj, $args);
    }

}
