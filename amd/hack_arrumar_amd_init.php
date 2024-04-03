<?php
// https://tracker.moodle.org/browse/MDL-74908
/*
  HACK para que os arquivos .min.js da pasta amd funcionem corretamente no Moodle 3.6.
  Após os arquivos serem gerados usando grunt, devemos rodar esse arquivo via terminal
  para que o arquivo seja atualizado.
*/
$buildpath = getcwd() . '/build/';
$searchpattern = $buildpath  . "*.min.js";

foreach (glob($searchpattern) as $filepath) {
  if (!is_dir($filepath)) {
    $filecontents = file_get_contents($filepath);
    echo 'denaming: '  . basename($filepath) . PHP_EOL;

    $newfilecontents = preg_replace('/^\s*define\s*\(\s*[\'"][a-z0-9_\/-]+[\'"]\s*,/m', 'define(', $filecontents);

    //echo $newfilecontents;
    file_put_contents($filepath, $newfilecontents);
  }
}
