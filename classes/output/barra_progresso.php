<?php
/**
 * Barra de progresso dos cursos completados.
 *
 * @package     mod_cursosprogresso
 * @copyright   Escola da Câmara dos Deputados @ 2024
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace mod_cursosprogresso\output;

 use templatable;
 use renderer_base;
 use renderable;

/**
 * Barra de progresso class.
 */
class barra_progresso implements renderable, templatable {
  /** @var string html id */
  private $html_id;
  /** @var int porcentagem inicial */
  private $percentage = 0;
  /** @var bool mostrar a barra de progresso padrão */
  private $showdefault = true;
  /** @var string total width com unidade de medida */
  private $width;

  /**
   * Constructor
   *
   * @param string $htmlid O HTML ID.
   * @param int $percentage A porcentagem.
   * @param bool $showdefault Se é pra mostrar a barra de progresso padrão.
   * @param int $width A largura do elemento.
   */
  public function __construct($htmlid = '', $percentage = 0, $showdefault = true, $width = "400px") {
      if (!empty($htmlid)) {
          $this->html_id  = $htmlid;
      } else {
          $this->html_id  = 'pbar_'.uniqid();
      }

      $this->percentage = $percentage;
      $this->showdefault = $showdefault;
      $this->width = $width;
  }

  /**
   * Export for template.
   *
   * @param  renderer_base $output The renderer.
   * @return array
   */
  public function export_for_template(renderer_base $output) {
    global $PAGE;

    if (!$this->showdefault) {
        $PAGE->requires->js_call_amd('mod_cursosprogresso/barradeprogresso', 'initBarraProgresso', [[
            'barraprogressodivid' => $this->html_id,
            'barraprogressopct' => $this->percentage
        ]]);
    }

    return [
        'html_id' => $this->html_id,
        'width' => $this->width,
        'percentage' => $this->percentage,
        'showdefault' => $this->showdefault
    ];
  }
}