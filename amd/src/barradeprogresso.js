/**
 * Converte uma determinada string de seletor em um seletor de ID válido
 * cortando espaços em branco, substituindo vários espaços por hífens e
 * garantindo que comece com '#'.
 *
 * @param {string} seletor - A string seletor a ser convertida.
 * @return {string} A string convertida.
 */
function converterParaSeletorId(seletor) {
  seletor = seletor.trim().replace(/\s+/g, '-');
  if (!seletor.startsWith('#')) {
    seletor = '#' + seletor;
  }
  return seletor;
}

/**
 * Formata um determinado valor percentual em uma string com um número
 * especificado de casas decimais e um símbolo de porcentagem opcional.
 *
 * @param {number} porcentagem - O valor percentual a ser formatado
 * @param {number} casasDecimais - O número de casas decimais a serem
 * incluídas na porcentagem formatada (o padrão é 0)
 * @param {boolean} incluirSimbolo - Se deve incluir o símbolo de
 * porcentagem na string formatada (o padrão é true)
 * @return {string} O valor percentual formatado como uma string
 */
function formatarPorcentagem(porcentagem, casasDecimais = 0, incluirSimbolo = true) {
  porcentagem = parseFloat(porcentagem);

  if (porcentagem < 100) {
    porcentagem = porcentagem.toFixed(casasDecimais);
    const [integerPart, decimalPart] = porcentagem.split('.');
    porcentagem = decimalPart > 0 ? porcentagem : integerPart;
  }

  return incluirSimbolo ? `${porcentagem}%` : porcentagem;
}

export const init = ({barraprogressodivid, barraprogressopct}) => {
  barraprogressodivid = converterParaSeletorId(barraprogressodivid);

  const barraProgressoContainer = document.querySelector(barraprogressodivid);

  if (barraProgressoContainer) {
    const barraProgresso = barraProgressoContainer.querySelector('.barra-de-progresso');
    const bpCarregando = barraProgresso.querySelector('.carregando');
    const bpBarra = barraProgresso.querySelector('.barra');
    const bpPctTexto = barraProgresso.querySelector('.porcetagem-texto');
    const bpBarraCirculo = bpBarra.querySelector('.circulo-indicador');
    const bpBarraProgresso = bpBarra.querySelector('.progresso');
    const larguraBarraCirculo = `${(30 / 2)}px`;

    bpBarraCirculo.style.left = `calc(${barraprogressopct}% - ${larguraBarraCirculo})`;
    bpBarraProgresso.style.width = `${barraprogressopct}%`;
    bpPctTexto.textContent = formatarPorcentagem(barraprogressopct, 1);
    bpCarregando.style.display = 'none';
    bpBarra.style.display = 'block';
    bpPctTexto.style.display = 'block';
  }
};