// Executar qua do o documento HTML for completamente carregado
document.addEventListener('DOMContentLoaded', function () {

    // Chamar a função carregar eventos
    var calendar = carregarEventos();

    // Renderizar o calendário
    calendar.render();

});