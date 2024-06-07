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
    protected $cursosselecionados = [];

    /** @var array Configurações do plugin. */
    protected $configuracoes = [];

    /** @var array Dados para javascript. */
    protected $dadosjs = [];

    /**
     * Constructor for this class.
     *
     * @param $cm The course module.
     */
    public function __construct($cm) {
        global $DB, $USER;
        
        $this->cm = $cm;

        if (!$cursosprogresso = $DB->get_record('cursosprogresso', ['id' => $this->cm->instance], 'id,name,selectedcourses,showcourseslist,htmlidcourseslist,htmlclasscourseitem')) {
            return false;
        }

        $selectedcourses = explode(',', $cursosprogresso->selectedcourses);
        
        foreach ($selectedcourses as $courseid) {
            $status = $this->get_usuario_curso_status($USER->id, $courseid);

            $this->cursosselecionados['cursos'][] = [
                'courseid' => $courseid,
                'fullname' => get_course($courseid)->fullname,
                'completed' => $status == "completo" ? true : false,
                'status' => $status
            ];

            $this->dadosjs[] = [
                'id' => $courseid,
                's' => substr($status, 0, 1) // c, i ou n (completo, incompleto, não inscrito)
            ];
        }

        $this->configuracoes = [
            'showdefault' => $cursosprogresso->showcourseslist,
            'courseListHtmlId' => $cursosprogresso->htmlidcourseslist,
            'courseListHtmlClass' => $cursosprogresso->htmlclasscourseitem
        ];
    }

    /**
     * Exporta os dados para o template.
     *
     * @param renderer_base $output renderer base output.
     * @return array Data to render.
     */
    public function export_for_template(renderer_base $output): array {
        global $PAGE;

        if (!$this->configuracoes['showdefault']) {
            $PAGE->requires->js_call_amd('mod_cursosprogresso/listacursos', 'init', [$this->dadosjs]);
        }

        return array_merge($this->cursosselecionados, $this->configuracoes);
    }

    /**
     * @return array cursos selecionados
     */
    public function get_cursos_selecionados() {
        return $this->cursosselecionados;
    }

    /**
     * @return int porcentagem de cursos completos
     */
    public function get_cursos_completados_porcentagem() {
        if (empty($this->cursosselecionados) || count($this->cursosselecionados['cursos']) == 0) {
            return 0;
        }

        $conta_completos = array_reduce($this->cursosselecionados['cursos'], function($carry, $item) { return $carry + ($item['completed'] ? 1 : 0); }, 0);

        $conta_completos_pct = 0;

        if ($conta_completos > 0) {
            $conta_completos_pct = ($conta_completos / count($this->cursosselecionados['cursos'])) * 100;
        }

        return $conta_completos_pct;
    }

    /**
     * Verifica se o usuário tem um certificado emitido pelo simplecertificate em determinado curso.
     * Pode ser usado como uma camada extra para verificação de conclusão de um curso.
     * 
     * @param mixed $usuarioid
     * @param mixed $cursoid
     * 
     * @return boolean
     */
    public function get_usuario_simplecertificate($usuarioid, $cursoid) {
        global $DB;

        $dbman = $DB->get_manager();

        if (!$dbman->table_exists('simplecertificate')) {
            return false;
        }
        
        if ($certificado_curso = $DB->get_record('simplecertificate', ['course' => $cursoid], 'id')) {
            if ($DB->get_record('simplecertificate_issues', ['certificateid' => $certificado_curso->id, 'userid' => $usuarioid, 'timedeleted' => null])) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Pega o status do usuário em determinado curso.
     * 
     * @param mixed $usuarioid
     * @param mixed $cursoid
     * 
     * @return string 'completo', 'incompleto' ou 'nao_inscrito'
     */
    public function get_usuario_curso_status($usuarioid, $cursoid) {
        $coursecontext = \context_course::instance($cursoid);

        if (is_enrolled($coursecontext, $usuarioid)) {
            $completion = new completion_completion(['course' => $cursoid, 'userid' => $usuarioid]);

            if ($completion->is_complete()) {
                return 'completo';
            }
            
            return $this->get_usuario_simplecertificate($usuarioid, $cursoid) ? 'completo' : 'incompleto';
        }

        return 'nao_inscrito';
    }
}
