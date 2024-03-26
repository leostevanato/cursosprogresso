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

export const init = ({courseListHtmlId, courseListHtmlClass}) => {
  const cursosId = converterParaSeletor(courseListHtmlId, "id");
  const cursoClass = converterParaSeletor(courseListHtmlClass, "class");

  return cursosId + ' ' + cursoClass;
};