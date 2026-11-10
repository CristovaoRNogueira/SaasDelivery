
// Receber o SELETOR da janela modal cadastrar
const cadastrarModal = new bootstrap.Modal(document.getElementById("cadastrarModal"));

// Receber o SELETOR da janela modal visualizar
const visualizarModal = new bootstrap.Modal(document.getElementById("visualizarModal"));

function carregarEventos() {

    var calendarEl = document.getElementById('calendar');

  // Recupere o valor do atributo data-target-pesq-events
  //const pesq_events = inputClienteId.getAttribute('data-target-pesq-events');

    var containerEl = document.getElementById('external-events-list');
    new FullCalendar.Draggable(containerEl, {
      itemSelector: '.fc-event',
      eventData: function (eventEl) {
        return {
          title: eventEl.innerText.trim()
        }
      }
    });


    var calendar = new FullCalendar.Calendar(calendarEl, {
      // Criar o cabeçalho do calendário
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      // Definir o idioma usado no calendário
      locale: 'pt-br',
      navLinks: true,// Permitir clicar nos nomes dos dias da semana 
      selectable: true,// Permitir clicar e arrastar o mouse sobre um ou vários dias no calendário
      selectMirror: true,// Indicar visualmente a área que será selecionada antes que o usuário solte o botão do mouse para confirmar a seleção
      editable: true,// Permitir arrastar e redimensionar os eventos diretamente no calendário.
      dayMaxEvents: true,// Número máximo de eventos em um determinado dia, se for true, o número de eventos será limitado à altura da célula do dia
      droppable: true,// PERMITIR ARRASTAR OS EVENTOS PARA O CALENDÁRIO
      events: 'paginas/' + pag + "/listar.php",
      
      drop: function (info) {
        // Exibe o evento no console para depuração
        console.log(info);
        // Verificar se a data de início do evento (dateStr) está correta
        if (info.dateStr) {
          // Atribuir data de início com hora fixa (08:00)
          document.querySelector('#data_inicio').value = info.dateStr;
          // Atribuir data de fim com hora fixa (18:00)
          document.querySelector('#data_fim').value = info.dateStr;
        }
        document.querySelector('#cad_title').value = info.textContent;
        // Certifique-se de que o modal foi instanciado corretamente
        const cadastrarModal = new bootstrap.Modal(document.getElementById('cadastrarModal'));

        // Mostrar o modal
        cadastrarModal.show();
      }
,



      // ############################### AO ARRASTAR O EVENTO ENTRE DATAS ##########################
      eventDrop: function (info) {
        // Captura os dados do evento arrastado
        var id = info.event.id; // ID do evento
        var start = info.event.start; // Nova data e hora de início

        // Preservar a hora original ao alterar apenas a data
        var originalStart = new Date(info.oldEvent.start);
        start.setHours(originalStart.getHours(), originalStart.getMinutes(), originalStart.getSeconds(), originalStart.getMilliseconds());

        // Cria um FormData e adiciona os dados do evento
        var formData = new FormData();
        formData.append('id', id); // Adiciona o ID do evento
        formData.append('start', start.toISOString()); // Converte para string ISO

        // Enviar os dados para o servidor
        $.ajax({
          url: 'paginas/' + pag + '/editar_events.php', // URL para atualizar o evento
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


      // EVENTO AO CLICALR NO EVENTO
      eventClick: function (event) {
        const evento = event.event; // Armazena o objeto do evento para simplificar o código
      
        // Preencher os campos do modal com os dados do evento
        document.getElementById("visualizar_id").innerText = evento.id;
        document.getElementById("visualizar_title").innerText = evento.title;
        document.getElementById("visualizar_obs").innerText = evento.extendedProps.obs || "Sem observações";
        document.getElementById("visualizar_user_id").innerText = evento.extendedProps.user_id || "Desconhecido";
        document.getElementById("visualizar_hora_alerta").innerText = evento.extendedProps.hora_alerta || "Não definido";
        document.getElementById("visualizar_hora_inicio").innerText = evento.extendedProps.hora_inicio || "Não definido";
      
        // Definir a cor do evento
        const color = evento.extendedProps.color || evento.backgroundColor || evento.borderColor || "#000000"; // Cor padrão caso nenhuma esteja definida
        document.getElementById("visualizar_color").style.backgroundColor = color; // Define a cor no elemento
        document.getElementById("visualizar_color_value").innerText = color; // Mostra o valor da cor como texto

         // Mostrar as datas formatadas (somente a data, sem a hora)
         const startDate = new Date(event.event.start); // Cria um objeto de data a partir da data de início
       
      
       // Formatar as datas (somente a data, sem a hora)
       const formattedStart = startDate.toLocaleDateString('pt-BR'); // Formato brasileiro (dd/mm/yyyy)

       // Mostrar as datas formatadas na modal
       document.getElementById("visualizar_start").innerText = formattedStart;
       // Abrir a janela modal visualizar
       visualizarModal.show();
      },
      


      // EVENTO AO ARRASTAR A DATA DO EVENTO
      eventResize: function (info) {
        // Captura os dados do evento arrastado
        var id = info.event.id; // ID do evento
        var start = info.event.start; // Nova data e hora de início

        // Preservar a hora original ao alterar apenas a data
        var originalStart = new Date(info.oldEvent.start);
        start.setHours(originalStart.getHours(), originalStart.getMinutes(), originalStart.getSeconds(), originalStart.getMilliseconds());


        // Cria um FormData e adiciona os dados do evento
        var formData = new FormData();
        formData.append('id', id); // Adiciona o ID do evento
        formData.append('start', start.toISOString()); // Converte para string ISO

        // Enviar os dados para o servidor
        $.ajax({
          url: 'paginas/' + pag + '/editar_events.php', // URL para atualizar o evento
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

      // EVENTO AO CLICAR NO DIA
      select:  function (event) {
        document.querySelector('#titulo_inserir').innerHTML = 'Adicionar Evento';
        limparCampos();
        // Obter os dados do evento da modal de visualização
        document.getElementById('btnCancelar').style.display = 'none';
        document.querySelector('#cad_start').value = event.startStr;
        document.querySelector('#hora_inicio').value = "08:00";
        // Abrir a janela modal cadastrar
        cadastrarModal.show();
      },
      

      //EVENTO AO ARRASTAR UM EVENTO SEM DATA PARA O EVENTO 01
      drop: function (event) {
        // Verificar se os dados do evento são válidos
        if (!event.event || !event.draggedEl) {
          console.error("Dados do evento inválidos");
          return;
        }

        // Captura os dados do evento arrastado
        var title = event.event.title; // Título do evento
        var start = event.event.start || new Date(); // Data de início (ou data atual)
        var id = event.draggedEl.getAttribute('data-id') || null; // ID do evento

        // Estruturar os dados em FormData
        var formData = new FormData();
        formData.append('title', title);
        formData.append('start', start.toISOString()); // Formato ISO para envio
        if (id) {
          formData.append('id', id); // Apenas adiciona se o ID existir
        }

        // Enviar os dados para o servidor
        $.ajax({
          url: 'paginas/' + pag + "/editar_events.php", // URL do script no servidor
          type: 'POST',
          data: formData,
          success: function (response) {
            try {
              var data = JSON.parse(response); // Parse da resposta JSON
              if (data.status) {
                alertsucesso(data.message);
                carregarEventos(); // Recarregar os eventos do calendário
                carregarEventosExternos(); // Recarregar eventos externos, se necessário
              } else {
                $('#mensagem').addClass('text-danger').text(data.message); // Mensagem de erro
              }
            } catch (e) {
              console.error("Erro ao processar resposta do servidor:", e);
              $('#mensagem').addClass('text-danger').text('Erro ao processar a resposta do servidor.');
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.error("Erro na requisição AJAX:", textStatus, errorThrown);
            $('#mensagem').addClass('text-danger').text('Erro ao salvar o evento: ' + textStatus);
          },
          cache: false,
          contentType: false,
          processData: false,
        });
      },
      //EVENTO AO ARRASTAR UM EVENTO SEM DATA PARA O EVENTO 02
      eventReceive: function (info) {
        // Captura os dados do evento arrastado
        var title = info.event.title; // Título do evento
        var start = info.event.start; // Data de início
        var id = info.draggedEl.getAttribute('data-id'); // ID do evento


        // Aqui você pode enviar os dados para o servidor, se necessário
        // Exemplo de chamada AJAX para salvar o evento
        var formData = new FormData();
        formData.append('title', title);
        formData.append('start', start.toISOString());
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



      // Quando um evento é renderizado
      eventDidMount: function (info) {
        // Aplica a cor do evento
        info.el.style.backgroundColor = info.event.extendedProps.color; // Define a cor de fundo
        info.el.style.borderColor = info.event.extendedProps.color; // Define a cor da borda
      },

    });
    calendar.render();
    




    //VALIDAR CAMPOS
    //PEGAR O FORMULÁRIO
  let form_add_event = document.querySelector('#formAgenda');

  form_add_event.addEventListener('submit', function (event) {
    event.preventDefault();
    let title = document.querySelector('#cad_title');


    if (title.value == '') {
      title.style.borderColor = 'red';
      title.focus();
      return false;
    }
    if (start.value == '') {
      start.style.borderColor = 'red';
      start.focus();
      return false;
    }
    this.submit();
  });

}