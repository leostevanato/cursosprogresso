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
 * Moodle renderer usado para exibir partes do plugin.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_cursosprogresso\output;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    /**
     * Renderers a lista de cursos.
     *
     * @param lista_cursos $listacursos Objeto da lista de cursos.
     * @return string The rendered html.
     */
    public function render_lista_cursos(lista_cursos $listacursos): string {
        $data = $listacursos->export_for_template($this);
        return parent::render_from_template('mod_cursosprogresso/lista_cursos', $data);
    }

    /**
     * Renderers a barra de progresso de conclusão dos cursos.
     *
     * @param barra_progresso $barraprogresso Objeto da barra de progresso.
     * @return string The rendered html.
     */
    public function render_barra_progresso(barra_progresso $barraprogresso): string {
        $data = $barraprogresso->export_for_template($this);
        return parent::render_from_template('mod_cursosprogresso/barra_progresso', $data);
    }
}
