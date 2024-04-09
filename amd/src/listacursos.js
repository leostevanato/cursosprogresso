/**
 * Converte uma determinada string de seletor em um seletor ID ou class válido
 * cortando espaços em branco, substituindo vários espaços por hífens e
 * garantindo que comece com '#' ou '.'.
 *
 * @param {string} seletor - A string seletor a ser convertida.
 * @param {string} tipo - id ou class.
 * @return {string} A string convertida.
 */
function converterParaSeletor(seletor, tipo) {
  const char = tipo === 'id' ? '#' : '.';

  seletor = seletor.trim().replace(/\s+/g, '-');
  if (!seletor.startsWith(char)) {
    seletor = char + seletor;
  }
  return seletor;
}

export const init = ({cursos, courseListHtmlId, courseListHtmlClass}) => {
  const cursosId = converterParaSeletor(courseListHtmlId, "id");
  const cursoClass = converterParaSeletor(courseListHtmlClass, "class");
  const cursosCards = document.querySelectorAll(cursoClass);

  let cursoArray = [];
  let cursoStatus = "";
  let cursoStatusTexto = "";

  cursosCards.forEach(cursoElemento => {
    cursoArray = cursos.find(curso => curso.courseid == cursoElemento.dataset.cursoid);

    if (cursoArray && cursoArray.length > 0) {
      switch (cursoArray.status) {
        case "completo":
          cursoStatus = "concluido";
          cursoStatusTexto = "Concluido";
          break;
        case "incompleto":
          cursoStatus = "continuar";
          cursoStatusTexto = "Continuar";
          break;
        default:
          cursoStatus = "acessar";
          cursoStatusTexto = "Acessar";
          break;
      }

      cursoElemento.querySelector(".curso-card-status").dataset.status = cursoStatus;
      cursoElemento.querySelector(".curso-card-status").textContent = cursoStatusTexto;
    }
  });

  // Retorno sem uso no momento, apenas para usar o cursosId e o grunt não reclamar.
  return cursosId + ' ' + cursoClass;
};