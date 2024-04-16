export const init = ({emblemaUrl}) => {
  document.querySelector("#area-emblema > a.botao")?.setAttribute("href", emblemaUrl);
  document.querySelector("#area-emblema > a.botao")?.classList.remove("bloqueado");
};