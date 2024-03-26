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

namespace core_course;
namespace mod_cursosprogresso\output;

use completion_completion;
use templatable;
use renderer_base;
use renderable;

class lista_cursos implements templatable, renderable {
    /** @var The course module. */
    protected $cm;
    /** @var array Cursos selecionados. */
    protected $cursos_selecionados = [];

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

        if (!$cursosprogresso = $DB->get_record('cursosprogresso', ['id' => $this->cm->instance], 'id, name,selectedcourses')) {
            return false;
        }
        
        $selectedcourses = $cursosprogresso->selectedcourses;
        $selectedcourses = explode(',', $selectedcourses);
        
        foreach ($selectedcourses as $courseid) {
            $completion = new completion_completion(['course' => $courseid, 'userid' => $USER->id]);

            $data['cursos'][] = [
                'courseid' => $courseid,
                'fullname' => get_course($courseid)->fullname,
                'completed' => $completion->is_complete()
            ];
        }

        $this->cursos_selecionados = $data;

        return $data;
    }

    public function get_cursos_selecionados() {
        return $this->cursos_selecionados;
    }

    public function get_cursos_completados_porcentagem() {
        if (empty($this->cursos_selecionados) || count($this->cursos_selecionados['cursos']) == 0) {
            return 0;
        }

        $conta_completos = array_reduce($this->cursos_selecionados['cursos'], function($carry, $item) { return $carry + ($item['completed'] ? 1 : 0); }, 0);

        $conta_completos_pct = 0;

        if ($conta_completos > 0) {
            $conta_completos_pct = ($conta_completos / count($this->cursos_selecionados['cursos'])) * 100;
        }

        return $conta_completos_pct;
    }

    public function get_usuario_curso_status($usuarioid, $cursoid) {
        $coursecontext = \context_course::instance($cursoid);

        if (is_enrolled($coursecontext, $usuarioid)) {
            $completion = new completion_completion(['course' => $cursoid, 'userid' => $usuarioid]);

            return $completion->is_complete() ? 'completo' : 'incompleto';
        }
        
        return "nao_inscrito";
    }
}
