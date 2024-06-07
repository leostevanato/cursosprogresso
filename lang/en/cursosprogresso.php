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
 * Plugin strings are defined here.
 *
 * @package     mod_cursosprogresso
 * @category    string
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Cursos Progresso';
$string['modulenameplural'] = 'Cursos Progresso';
$string['modulename_help'] = 'Mostra uma lista de cursos selecionados e se quais o aluno já concluiu.';
$string['modulename_link'] = 'mod/cursosprogresso/view';
$string['pluginname'] = 'Cursos Progresso';
$string['pluginadministration'] = 'Cursos Progresso administração';

$string['cursosprogressofieldset'] = 'Opções de Cursos Progresso';
$string['selectedcourses'] = 'Cursos selecionados';
$string['showcourseslist'] = 'Exibir lista de cursos padrão';
$string['showcourseslist_help'] = 'Sim - exibe a lista de cursos do plugin na posição onde o plugin foi adiciona.

Não - Exibe a lista de cursos no elemento com o ID informado. Nesse caso, o elemento deve ser adicionado de forma manual.';

$string['htmlidcourseslist'] = 'ID do elemento da lista de cursos';
$string['htmlclasscourseitem'] = 'Classe do elemento do item de curso';
$string['htmlclasscoursestatus'] = 'Classe do elemento do item de curso que informa o status';

$string['showprogressbar'] = 'Exibir barra de progresso padrão';
$string['showprogressbar_help'] = 'Sim - exibe a barra de progresso do plugin na posição onde o plugin foi adiciona.

Não - Exibe a barra de progresso no elemento com o ID informado. Nesse caso, o elemento deve ser adicionado de forma manual.';
$string['dividprogressbar'] = 'ID da div da barra de progresso';

$string['completioncoursescomplete'] = 'Completar os cursos selecionados';
$string['completioncoursescompletegroup'] = 'Requer que os cursos selecionados sejam completados';
$string['completioncoursescompletedesc'] = 'O aluno deve completar todos os cursos selecionados.';
