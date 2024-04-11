export const init = () => {
  document.querySelector(".esconder-ul-pai")?.closest("ul").classList.remove("d-block");
  document.querySelector(".esconder-ul-pai")?.closest("ul").classList.add("d-none");
};