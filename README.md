# Cursos Progresso #

Lista cursos e mostra quais o aluno já completou.

O plugin gera uma lista de cursos previamente selecionados. Quando o aluno acessa o
curso onde o recurso foi adicionado, ele consegue ver quais cursos ele já completou.

## Desenvolvimento ##

Caso se esteja desenvolvendo o Moodle localmente (Ex: xampp) é recomendado que se instale
o nvm e o grunt. O grunt minifica o javascript para o formato usado pelo Moodle. O ideal
é usar o grunt com o watchman, através do comando grunt watch ou senão lembrar de executar
o grunt direcionado para a pasta onde os arquivos js estão, caso contrário o grunt vai
compilar todos os javascript do Moodle e levará muito tempo.
https://moodledev.io/general/development/tools/nodejs

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/cursosprogresso

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

Escola da Câmara dos Deputados @ 2024

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
