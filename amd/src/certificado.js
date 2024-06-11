export const init = (certificadoUrl) => {
  let botaoCertificado = document.querySelector("a.botao-gerar-certificado");

  if (botaoCertificado) {
    botaoCertificado.setAttribute("href", certificadoUrl);
    botaoCertificado.dataset.status = "liberado";
    botaoCertificado.textContent = "Receber meu certificado";
  }
};