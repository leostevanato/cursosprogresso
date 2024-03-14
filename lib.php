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
 * Library of interface functions and constants.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function cursosprogresso_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE: return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_MOD_INTRO:     return true;
        case FEATURE_MOD_PURPOSE:   return MOD_PURPOSE_CONTENT;
        case FEATURE_NO_VIEW_LINK:  return true;
        default: return null;
    }
}

/**
 * Saves a new instance of the mod_cursosprogresso into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_cursosprogresso_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function cursosprogresso_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('cursosprogresso', $moduleinstance);
    
    $cursos = new stdClass();
    $cursos->cursosprogressoid = $id;
    $cursos->cursoscsv = implode(',', $moduleinstance->selectedcourses);
    $cursos->timemodified = time();

    $DB->insert_record('cursosprogresso_cursos', $cursos);

    return $id;
}

/**
 * Updates an instance of the mod_cursosprogresso in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_cursosprogresso_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function cursosprogresso_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;
    
    $cursos = new stdClass();
    $cursos->cursosprogressoid = $moduleinstance->id;
    $cursos->cursoscsv = implode(',', $moduleinstance->selectedcourses);
    $cursos->timemodified = time();
    
    $cursosprogresso_cursos = $DB->get_record('cursosprogresso_cursos', array('cursosprogressoid' => $moduleinstance->id));
    
    if ($cursosprogresso_cursos && $cursosprogresso_cursos->id > 0) {
        $cursos->id = $cursosprogresso_cursos->id;
        $DB->update_record('cursosprogresso_cursos', $cursos);
    } else {
        $DB->insert_record('cursosprogresso_cursos', $cursos);
    }

    return $DB->update_record('cursosprogresso', $moduleinstance);
}

/**
 * Removes an instance of the mod_cursosprogresso from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function cursosprogresso_delete_instance($id) {
    global $DB;

    if (!$DB->get_record('cursosprogresso', array('id' => $id))) {
        return false;
    }

    if (!$DB->delete_records('cursosprogresso_cursos', array("cursosprogressoid" => $id))) {
        return false;
    }

    if (!$DB->delete_records('cursosprogresso', array("id" => $id))) {
        return false;
    }

    return true;
}

/**
 * Sets the special label display on course page.
 *
 * @param cm_info $cm Course-module object
 */
function cursosprogresso_cm_info_view(cm_info $cm) {
    $cm->set_custom_cmlist_item(true);
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function cursosprogresso_get_coursemodule_info($cm) {
    global $DB;

    if (!$cursosprogresso = $DB->get_record('cursosprogresso', ['course' => $cm->course], 'id,name')) {
        return false;
    }
    
    $selectedcourses_html = montar_lista_cursos($cm);

    $info = new cached_cm_info();

    $info->content = '<b>'. $cursosprogresso->name . '</b><br>'. $selectedcourses_html;

    return $info;
}

function array_para_html_list($array, $ulClass = "", $liClass = "") {
    if (!is_array($array)) {
        return $array;
    }

    $html = "<ul class=\"$ulClass\">";
    
    foreach ($array as $item) {
        $html .= "<li class=\"$liClass\">";

        $html .= '(' . $item['courseid'] . ') ' . $item['fullname'] . icone_curso_completado(aluno_concluiu_curso($item['courseid']));

        $html .= "</li>";
    }

    $html .= "</ul>";

    return $html;
}

function icone_curso_completado($completado = false) {
    return ($completado) ?
        '<i class="fa-solid fa-circle-check ml-2" style="color: #579368;"></i>'
        :
        '<i class="fa-solid fa-circle-xmark ml-2" style="color: #fb1818;"></i>';
}

function montar_lista_cursos($cm) {
    global $DB;

    if (!$cursosprogresso = $DB->get_record('cursosprogresso', ['course' => $cm->course], 'id,name')) {
        return false;
    }
    
    $selectedcourses = $DB->get_field('cursosprogresso_cursos', 'cursoscsv', ['cursosprogressoid' => $cursosprogresso->id]);
    $selectedcourses = explode(',', $selectedcourses);

    $cursos = [];

    foreach ($selectedcourses as $courseid) {
        $cursos[] = ['courseid' => $courseid, 'fullname' => get_course($courseid)->fullname];
    }

    if (empty($cursos)) {
        return '<p>Nenhum curso selecionado.</p>';
    } else {
        return array_para_html_list($cursos, "mt-2");
    }
}

// Função para verificar se o aluno concluiu um curso
function aluno_concluiu_curso($id_curso, $id_usuario = null) {
    global $DB;
    global $USER;

    $id_usuario = $id_usuario ?? $USER->id;

    // Obter o registro de conclusão do curso
    $registro_conclusao = $DB->get_record('course_completions', array('course' => $id_curso, 'userid' => $id_usuario));

    // Se o registro de conclusão existir, o aluno concluiu o curso
    return !empty($registro_conclusao);
}