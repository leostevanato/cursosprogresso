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
        case FEATURE_MOD_ARCHETYPE:         return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_MOD_INTRO:             return true;
        case FEATURE_NO_VIEW_LINK:          return true;
        case FEATURE_COMPLETION_HAS_RULES:  return true;
        default:                            return null;
    }
}

// Adicionando o ícone do plugin
function mod_cursosprogresso_get_icon($size = null) {
    return new pix_icon('icon', '', 'mod_cursosprogresso');
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

    $moduleinstance->timemodified = time();
    $moduleinstance->selectedcourses = implode(',', $moduleinstance->selectedcourses);
    // Se o nome do campo no formulário for diferente do nome do campo no banco
    // os valores são referenciados assim:
    // $moduleinstance->htmlidcourseslist = $moduleinstance->listacursoshtmlid;
    // $moduleinstance->htmlclasscourseitem = $moduleinstance->itemcursohtmlclass;
    // $moduleinstance->dividprogressbar = $moduleinstance->barraprogressodivid;

    $id = $DB->insert_record('cursosprogresso', $moduleinstance);

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
    
    $moduleinstance->selectedcourses = implode(',', $moduleinstance->selectedcourses);

    $moduleinstance->timemodified = time();
    
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

    if (!$DB->delete_records('cursosprogresso', array("id" => $id))) {
        return false;
    }

    return true;
}

/**
 * Exibe o conteúdo HTML direto na página do curso sem precisar abrir um link.
 * https://moodledev.io/docs/apis/plugintypes/mod/visibility
 *
 * @param cm_info $cm Course-module object
 */
function cursosprogresso_cm_info_view(cm_info $cm) {
    global $DB, $PAGE, $USER;

    if (!$cursosprogresso = $DB->get_record('cursosprogresso', ['id' => $cm->instance], 'id,name,selectedcourses,showprogressbar,dividprogressbar,completioncoursescomplete')) {
        return false;
    }
    
    $PAGE->requires->js_call_amd('mod_cursosprogresso/cursosprogressomain', 'init');

    $renderer = $PAGE->get_renderer('mod_cursosprogresso');

    $emblema_issued = [];
    
    $cursoid = $cm->get_course()->id;

    $emblemas = badges_get_badges(BADGE_TYPE_COURSE, $cursoid);

    if (count($emblemas) > 0) {
        foreach ($emblemas as $emblema) {
            if ($emblema->is_issued($USER->id)) {
                $emblema_issued[] = $emblema->id;
            }
        }

        if (count($emblema_issued) > 0) {
            $PAGE->requires->js_call_amd('mod_cursosprogresso/emblema', 'init', [
                'emblemaUrl' => new moodle_url('/badges/view.php', array('type' => 2, 'id' => $cursoid))
            ]);
        }
    }

    $listacursos = new \mod_cursosprogresso\output\lista_cursos($cm);
    $selectedcourses_html = $renderer->render($listacursos);

    $cursos_completados_pct = $listacursos->get_cursos_completados_porcentagem();

    $completion = new completion_info($cm->get_course());

    if ($completion->is_enabled($cm) && $cursosprogresso->completioncoursescomplete == 1) {
        // O método update_state verifica se o usuário atual cumpriu as regras de conclusão da
        // atividade definidas na função cursosprogresso_get_completion_state. Se sim, ela atualiza 
        // o estado da atividade como completa.
        $completion->update_state($cm, COMPLETION_COMPLETE);
    }

    $barraprogresso_html = "";
    
    $pbdivid = $cursosprogresso->showprogressbar ? 'bp_cursos_completados' : $cursosprogresso->dividprogressbar;

    $barraprogresso = new \mod_cursosprogresso\output\barra_progresso($pbdivid, $cursos_completados_pct, $cursosprogresso->showprogressbar);
    
    $barraprogresso_html = $renderer->render($barraprogresso);
    
    if (has_capability('moodle/site:config', $cm->context) || !empty($selectedcourses_html) || !empty($barraprogresso_html)) {
        $conteudo_html = '<div class="text-start text-left font-weight-bold fw-bold">'. $cursosprogresso->name . '</div>'. $selectedcourses_html .'<br>'. $barraprogresso_html;
    } else {
        // Adiciona div com essa classe para que o ul possa ser escondido via js quando o plugin não
        // possuir nenhum conteúdo (por exemplo, caso se esteja usando HTML sem ser do plugin).
        $conteudo_html = '<div class="esconder-ul-pai"></div>';
    }

    $cm->set_content($conteudo_html);
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

    if ($cursosprogresso = $DB->get_record('cursosprogresso', ['id' => $cm->instance], 'id, name, intro, introformat, completioncoursescomplete')) {
        $info = new cached_cm_info();
        $info->name  = $cursosprogresso->name;

        if ($cm->showdescription) {
            // Convert intro to html. Do not filter cached version, filters run at display time.
            // No filtering here because this info is cached and filtered later.
            $info->content = format_module_intro('cursosprogresso', $cursosprogresso, $cm->id, false);
        }

        // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
        if ($cm->completion == COMPLETION_TRACKING_AUTOMATIC) {
            $info->customdata['customcompletionrules']['completioncoursescomplete'] = $cursosprogresso->completioncoursescomplete;
        }

        return $info;
    } else {
        return false;
    }
}

// Função para descobrir onde a função informada foi definida.
function arquivo_funcao_definida($nome_funcao) {
    return "A função <b>". $nome_funcao ."</b> foi definida no arquivo " . (new ReflectionFunction("get_courses"))->getFileName();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function cursosprogresso_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * Obtains the automatic completion state for this activity based on any conditions
 * in activity settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function cursosprogresso_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Get cursosprogresso details
    $cursosprogresso = $DB->get_record('cursosprogresso', ['id' => $cm->instance], '*', MUST_EXIST);

    $listacursos = new \mod_cursosprogresso\output\lista_cursos($cm);

    // If completion option is enabled, evaluate it and return true/false
    if ($cursosprogresso->completioncoursescomplete == 1) {
        return $listacursos->get_cursos_completados_porcentagem() >= 99;
    } else {
        // Completion option is not enabled so just return $type
        return $type;
    }
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_cursosprogresso_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules']) || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completioncoursescomplete':
                if (!empty($val)) {
                    $descriptions[] = get_string('completioncoursescompletedesc', 'mod_cursosprogresso', $val);
                }
                break;
            default:
                break;
        }
    }
    return $descriptions;
}

function curso_simplecertificate_ativo($cursoid) {
    global $DB;

    $dbman = $DB->get_manager();

    if (!$dbman->table_exists('simplecertificate')) {
        return false;
    }
    
    return $DB->get_record('simplecertificate', ['course' => $cursoid], 'id');
}