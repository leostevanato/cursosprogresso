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
 * The main mod_cursosprogresso configuration form.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_cursosprogresso_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        // $this->standard_intro_elements(get_string('moduleintro'));

        $this->standard_intro_elements();
       
        $mform->addElement('header', 'cursosprogressofieldset', get_string('cursosprogressofieldset', 'mod_cursosprogresso'));

        // Adicionando o elemento de seleção de cursos como multiselect.
        $mform->addElement('autocomplete', 'selectedcourses', get_string('selectedcourses', 'mod_cursosprogresso'), $this->get_cursos_options(), ['multiple' => true]);

        // Preencher o multiselect com os cursos selecionados, caso existam.
        $mform->getElement('selectedcourses')->setSelected($this->get_cursos_selecionados());

        // Adicionando o elemento indicando se é pra usar a barra de progresso.
        $mform->addElement('selectyesno', 'showprogressbar', get_string('showprogressbar', 'mod_cursosprogresso'));
        // Se o campo showprogressbar não estiver setado no banco, 
        $mform->getElement('showprogressbar')->setSelected($this->get_mostrar_barra_progresso());
        $mform->addHelpButton('showprogressbar', 'showprogressbar', 'mod_cursosprogresso');
        
        $mform->addElement('text', 'barraprogressodivid', 'ID da div da barra de progresso');
        $mform->setType('barraprogressodivid', PARAM_NOTAGS);
        $mform->hideIf('barraprogressodivid', 'showprogressbar', 'eq', 1);

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    // Função para obter os cursos para preencher as options do select.
    private function get_cursos_options() {
        global $COURSE;
        
        $courseoptions = array();
        $courses = get_courses("all", "fullname ASC"); // Obtendo todos os cursos.

        foreach ($courses as $course) {
            if ($COURSE->id != $course->id && $course->format != "site") {
                $courseoptions[$course->id] = $course->fullname;
            }
        }

        return $courseoptions;
    }

    // Função para obter os cursos selecionados do banco de dados.
    private function get_cursos_selecionados() {
        global $COURSE;
        global $DB;

        if (!$selectedcourses = $DB->get_field('cursosprogresso', 'cursoscsv', ['course' => $COURSE->id])) {
            return false;
        }
                
        $selectedcourses = explode(',', $selectedcourses);

        return $selectedcourses;
    }

    // Função para obter a opção show progress bar do banco de dados.
    private function get_mostrar_barra_progresso() {
        global $COURSE;
        global $DB;
        
        // Aqui retornamos true pois o valor padrão do campo select é SIM, então não podemos retornar false.
        if (!$showprogressbar = $DB->get_field('cursosprogresso', 'showprogressbar', ['course' => $COURSE->id])) {
            return true;
        }

        return $showprogressbar;
    }

    protected function specific_definition($mform) {
        // Processar os dados do formulário ao salvar.
        if ($data = $this->get_data()) {
            // Manipular os cursos selecionados.
            $selectedcourses = isset($data->selectedcourses) ? $data->selectedcourses : array();
            $this->config->selectedcourses = implode(',', $selectedcourses);
        }
    }
}
