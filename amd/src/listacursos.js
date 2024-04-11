export const init = (cursos) => {
  const cursosCards = document.querySelectorAll(".curso-card");

  let cursoArray = [];
  let cursoStatus = "";
  let cursoStatusTexto = "";

  cursosCards.forEach(cursoElemento => {
    cursoArray = cursos.find(curso => curso.id == cursoElemento.dataset.cursoid);

    if (typeof cursoArray != "undefined" && cursoArray.hasOwnProperty("s")) {
      switch (cursoArray.s) {
        case "c": // Completo
          cursoStatus = "concluido";
          cursoStatusTexto = "Concluído";
          break;
        case "i": // Incompleto
          cursoStatus = "continuar";
          cursoStatusTexto = "Continuar";
          break;
        default:
          cursoStatus = "acessar";
          cursoStatusTexto = "Acessar";
      }

      cursoElemento.querySelector(".curso-card-status").dataset.status = cursoStatus;
      cursoElemento.querySelector(".curso-card-status").textContent = cursoStatusTexto;
    }
  });
};