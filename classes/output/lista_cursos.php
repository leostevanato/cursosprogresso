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
 * Exibe a lista de cursos selecionados.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cursosprogresso\output;

use templatable;
use renderer_base;
use renderable;

class lista_cursos implements templatable, renderable {

    /** @var The course module. */
    protected $cm;

    /**
     * Constructor for this class.
     *
     * @param $cm The course module.
     */
    public function __construct($cm) {
        $this->cm = $cm;
    }
    
    /**
     * Exports the navigation buttons around the book.
     *
     * @param renderer_base $output renderer base output.
     * @return array Data to render.
     */
    public function export_for_template(renderer_base $output): array {
        global $DB;
        global $USER;

        $data = [];

        if (!$cursosprogresso = $DB->get_record('cursosprogresso', ['course' => $this->cm->course], 'id, name')) {
            return false;
        }
        
        $selectedcourses = $DB->get_field('cursosprogresso_cursos', 'cursoscsv', ['cursosprogressoid' => $cursosprogresso->id]);
        $selectedcourses = explode(',', $selectedcourses);
        
        foreach ($selectedcourses as $courseid) {
            $data['cursos'][] = [
                'courseid' => $courseid,
                'fullname' => get_course($courseid)->fullname,
                'completed' => !empty($DB->get_record('course_completions', array('course' => $courseid, 'userid' => $USER->id)))
            ];
        }
        
        return $data;
    }
}