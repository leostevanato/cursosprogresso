<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_cursosprogresso.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$c = optional_param('c', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('cursosprogresso', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('cursosprogresso', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('cursosprogresso', array('id' => $c), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('cursosprogresso', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

$PAGE->set_url('/mod/cursosprogresso/view.php', array('id' => $cm->id));

require_login($course, true, $cm);

$url = course_get_url($course);
$url->set_anchor('module-' . $id);
redirect($url);