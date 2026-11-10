// Executar qua do o documento HTML for completamente carregado
document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');

  // Receber o SELETOR da janela modal cadastrar
  const cadastrarModal = new bootstrap.Modal(document.getElementById("cadastrarModal"));

  // Receber o SELETOR da janela modal visualizar
  const visualizarModal = new bootstrap.Modal(document.getElementById("visualizarModal"));



  var calendar = new FullCalendar.Calendar(calendarEl, {

    // Criar o cabeçalho do calendário
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    initialView: 'dayGridMonth',
    locale: 'pt-br',// Definir o idioma usado no calendário
    navLinks: true,// Permitir clicar nos nomes dos dias da semana 
    selectable: true, // Permitir clicar e arrastar o mouse sobre um ou vários dias no calendário
    selectMirror: true,// Indicar visualmente a área que será selecionada antes que o usuário solte o botão do mouse para confirmar a seleção
    editable: true, // Permitir arrastar e redimensionar os eventos diretamente no calendário.
    dayMaxEvents: true, // Número máximo de eventos em um determinado dia, se for true, o número de eventos será limitado à altura da célula do dia
    droppable: true,// PERMITIR ARRASTAR OS EVENTOS PARA O CALENDÁRIO
    events: 'paginas/' + pag + "/listar.php", //listar so eventos

    dateClick: function (info) {
      abrirModal(info);

    },



    // ############################### AO ARRASTAR O EVENTO ENTRE DATAS ##########################
    eventDrop: function (info) {

      // Captura os dados do evento arrastado
      var id = info.event.id; // ID do evento
      var start = info.event.start; // Nova data de início
      var end = info.event.end; // Nova data de fim (pode ser null)

      // Se o evento não tiver uma data de fim, defina-a como a data de início
      if (!end) {
        end = start; // Define a data de fim como a data de início
      }

      // Cria um FormData e adiciona os dados do evento
      var formData = new FormData();
      formData.append('id', id); // Adiciona o ID do evento
      formData.append('start', start.toISOString()); // Converte para string ISO
      formData.append('end', end.toISOString()); // Converte para string ISO

      // Enviar os dados para o servidor
      $.ajax({
        url: 'paginas/' + pag + "/editar_events.php", // URL para atualizar o evento
        type: 'POST',
        data: formData,
        success: function (response) {
          // Parseia a resposta JSON
          var data = JSON.parse(response);
          if (data.status) {
            alertsucesso(data.message);
            carregarEventos(); // Recarregar eventos
          } else {
            $('#mensagem').addClass('text-danger');
            $('#mensagem').text(data.message);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#mensagem').addClass('text-danger');
          $('#mensagem').text('Erro ao atualizar o evento: ' + textStatus);
        },
        cache: false,
        contentType: false,
        processData: false,
      });
    },

    // ###################### EVENTO MOSTRAR EVENTO###################
    eventClick: function (event) {

      // Enviar para a janela modal os dados do evento
      document.getElementById("visualizar_id").innerText = event.event.id;
      document.getElementById("visualizar_title").innerText = event.event.title;
      document.getElementById("visualizar_obs").innerText = event.event.extendedProps.obs;
      document.getElementById("visualizar_user_id").innerText = event.event.extendedProps.user_id;
      document.getElementById("visualizar_hora_alerta").innerText = event.event.extendedProps.hora_alerta;

      // Definir a cor do evento
      var color = event.event.extendedProps.color || event.event.backgroundColor || event.event.borderColor; // Obtém a cor
      document.getElementById("visualizar_color").style.backgroundColor = color; // Define a cor no elemento
      document.getElementById("visualizar_color_value").innerText = color; // Mostra o valor da cor como texto

      // Mostrar o nome da cor
      var colorName = colorNames[color] || "Cor Desconhecida"; // Obtém o nome da cor ou "Cor Desconhecida"
      document.getElementById("visualizar_color_name").innerText = colorName; // Mostra o nome da cor

      document.getElementById("visualizar_start").innerText = event.event.start.toLocaleString();

      // Abrir a janela modal visualizar
      visualizarModal.show();
    },

    // Quando um evento é renderizado
    eventDidMount: function (info) {
      // Aplica a cor do evento
      info.el.style.backgroundColor = info.event.extendedProps.color; // Define a cor de fundo
      info.el.style.borderColor = info.event.extendedProps.color; // Define a cor da borda
    },

    // #####################EVENTO AO ARRASTAR A DATA DO EVENTO####################################
    eventResize: function (event) {
      alert('event Resize')
    },

    // ###################### EVENTO AO CLICAR EM UMA DATA ADD EVENTO ##############################
    select: function (event) {
      limparCampos();
      // Obter os dados do evento da modal de visualização
      document.getElementById('btnCancelar').style.display = 'none';

      // Receber o SELETOR do campo usuário do formulário cadastrar
      var cadUserId = document.getElementById('cad_user_id');

      // Chamar a função para converter a data selecionada para ISO8601 e enviar para o formulário
      document.getElementById("cad_start").value = converterData(event.start);
      document.getElementById("cad_end").value = converterData(event.start);

      // Abrir a janela modal cadastrar
      cadastrarModal.show();

    },


    // ############### QUANDO UM EVENTO É ARRASTADO PARA O CALENDÁRIO ###########################
    eventReceive: function (info) {
      // Captura os dados do evento arrastado
      var title = info.event.title; // Título do evento
      var start = info.event.start; // Data de início
      var end = info.event.end; // Data de fim (pode ser null)
      var id = info.draggedEl.getAttribute('data-id'); // ID do evento

      // Se o evento não tiver uma data de fim, defina-a como a data de início
      if (!end) {
        end = start; // Define a data de fim como a data de início
      }

      // Aqui você pode enviar os dados para o servidor, se necessário
      // Exemplo de chamada AJAX para salvar o evento
      var formData = new FormData();
      formData.append('title', title);
      formData.append('start', start.toISOString());
      formData.append('end', end.toISOString());
      formData.append('id', id); // Adiciona o ID do evento

      // Enviar os dados para o servidor
      $.ajax({
        url: 'paginas/' + pag + "/editar_events.php",
        type: 'POST',
        data: formData,
        success: function (response) {
          // Parseia a resposta JSON
          var data = JSON.parse(response);
          if (data.status) {
            alertsucesso(data.message);
            carregarEventos(); // Recarregar eventos
            carregarEventosExternos();
          } else {
            $('#mensagem').addClass('text-danger');
            $('#mensagem').text(data.message);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#mensagem').addClass('text-danger');
          $('#mensagem').text('Erro ao salvar o evento: ' + textStatus);
        },
        cache: false,
        contentType: false,
        processData: false,
      });
    },


  });


  calendar.render();



  //FUNÇÕES #####################################
  function abrirModal(info) {
    // Receber o SELETOR do campo usuário do formulário cadastrar
    var cadUserId = document.getElementById('cad_user_id');

    // Chamar a função para converter a data selecionada para ISO8601 e enviar para o formulário
    document.getElementById("cad_start").value = converterData(info.start);
    document.getElementById("cad_end").value = converterData(info.start);


    // Abrir a janela modal cadastrar
    cadastrarModal.show();
  }


});