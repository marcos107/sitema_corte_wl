<style>
.button-container {
    display: flex;
    justify-content: center; /* Centraliza os botões horizontalmente */
    align-items: center; /* Centraliza os botões verticalmente */
    gap: 10px; /* Adiciona um espaço entre os botões */
}



.button-container button {
  width: 50%; /* Faz com que o contêiner ocupe 100% da largura disponível na div */

    flex: none; /* Garante que os botões não ocupem a largura total */
}


</style>
<script>


  const element = document.getElementById("projetistas");
  if (element) {
    element.remove();
  }
  const element1 = document.getElementById("cortadores");
  if (element1) {
    element1.remove();
  }




  document.getElementById("data_inicial").addEventListener('dblclick', () => {
    document.getElementById("data_inicial").showPicker();
  });
  document.getElementById("data_final").addEventListener('dblclick', () => {
    document.getElementById("data_final").showPicker();
  });


  function cadastrar() {
    document.getElementById('cadastrar_btn').disabled = true;
    const dataInicial = document.getElementById("data_inicial").value;
    const dataFinal = document.getElementById("data_final").value;
    desenhistas = getSelectedCheckboxValues_desenhista();
    cortador    = getSelectedCheckboxValues_cortador();

    $.ajax({
      url: '<?= base_url('public/adm/relatorio_analitico') ?>',
      type: "POST",
      dataType: "json", // Espera uma resposta JSON
      data: { dataInicial: dataInicial, dataFinal: dataFinal, desenhistas: desenhistas,cortador: cortador  },
      success: function (response) {


        if (!response.ok) {
          //response.msg
          for (const chave in response.msg) {
            const valor = response.msg[chave];
            alert_personalizado(chave, valor);
          }

        } else {





          // Decodifica o conteúdo PDF
          var pdfContent = atob(response.pdf);
          var pdfFileName = response.nome_pdf;

          // Cria um blob a partir do conteúdo PDF
          var blob = new Blob([new Uint8Array([...pdfContent].map(char => char.charCodeAt(0)))], { type: 'application/pdf' });

          // Cria um link temporário para download do PDF
          var a = document.createElement('a');
          var url = window.URL.createObjectURL(blob);
          a.href = url;
          a.download = pdfFileName;
          document.body.append(a);
          a.click();
          a.remove();
          window.URL.revokeObjectURL(url);

        }

        setTimeout(function () {
          document.getElementById('cadastrar_btn').disabled = false;
        }, 500);

      },
      error: function (jqXHR, textStatus, errorThrown) {
        document.getElementById('cadastrar_btn').disabled = false;

        // console.log(textStatus, errorThrown);
      }
    });
  }


  document.addEventListener("DOMContentLoaded", function () {
    const dataInicial = document.getElementById("data_inicial");
    const dataFinal = document.getElementById("data_final");

    // Obtendo a data de hoje e subtraindo um dia para obter o dia anterior
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    const maxDate = yesterday.toISOString().split('T')[0];

    // Definindo o valor máximo para data_inicial e data_final
    dataInicial.setAttribute("max", maxDate);
    dataFinal.setAttribute("max", maxDate);

    dataInicial.addEventListener("change", function () {
      if (dataInicial.value) {
        dataFinal.removeAttribute("disabled");
        dataFinal.setAttribute("min", dataInicial.value);

        // Verificando se a data final é menor que a data inicial
        if (dataFinal.value && dataFinal.value < dataInicial.value) {
          alert_personalizado("Período", "A data final não pode ser anterior à data inicial.");
          dataFinal.value = "";
        }
      } else {
        dataFinal.setAttribute("disabled", "true");
        dataFinal.value = ""; // Zerar a data final quando a data inicial for desmarcada
      }
    });

    dataFinal.addEventListener("change", function () {
      if (dataFinal.value > maxDate) {
        alert_personalizado("Período", "A data final não pode ser anterior à data inicial.");

        dataFinal.value = "";
      }

      // Verificando se a data final é menor que a data inicial
      if (dataInicial.value && dataFinal.value < dataInicial.value) {
        alert_personalizado("Período", "A data final não pode ser anterior à data inicial.");

        dataFinal.value = "";
      }
    });
  });


  function createControls_cortador() {
    const container = document.getElementById('group_3');

    // Create button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container';

    // Create select all button
    const selectAllButton = document.createElement('button');
    selectAllButton.id = 'selectAll';
    selectAllButton.textContent = 'Selecionar Todos';
    selectAllButton.className = 'btn btn-outline-primary btn-sm';
    selectAllButton.addEventListener('click', selectAllCheckboxes_cortador);
    buttonContainer.appendChild(selectAllButton);

    // Create deselect all button
    const deselectAllButton = document.createElement('button');
    deselectAllButton.id = 'deselectAll';
    deselectAllButton.textContent = 'Desmarcar Todos';
    deselectAllButton.className = 'btn btn-outline-primary btn-sm';
    deselectAllButton.addEventListener('click', deselectAllCheckboxes_cortador);
    buttonContainer.appendChild(deselectAllButton);

    // Add button container to the top of the group_3 container
    container.appendChild(buttonContainer);
}


function createCheckboxes_cortador(data) {
    const container = document.getElementById('group_3');

    for (const key in data) {
      if (data.hasOwnProperty(key)) {
        const label = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = key;

        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(data[key]));

        // Adiciona estilo para espaçamento
        label.style.margin = '5px';

        container.appendChild(label);
      }
    }

    updateColumns_cortador();
  }

  function updateColumns_cortador() {
    const container = document.getElementById('group_3');
    const containerWidth = container.offsetWidth;
    const checkboxWidth = 200; // Largura aproximada de cada checkbox com label
    const columns = Math.floor(containerWidth / checkboxWidth);
    container.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
  }

  function selectAllCheckboxes_cortador() {
    const checkboxes = document.querySelectorAll('#group_3 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
  }

  function deselectAllCheckboxes_cortador() {
    const checkboxes = document.querySelectorAll('#group_3 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
  }



  function getSelectedCheckboxValues_cortador() {
    const checkboxes = document.querySelectorAll('#group_3 input[type="checkbox"]');
    const selectedValues = [];
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
        selectedValues.push(checkbox.value);
      }
    });
    return selectedValues;
  }


  function alert_personalizado(titulo, bory) {
    $(document).Toasts('create', {
      class: 'bg-danger',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 13000,
      body: bory
    });
  }


  function createControls_desenhista() {
    const container = document.getElementById('group_2');

    // Create button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container';

    // Create select all button
    const selectAllButton = document.createElement('button');
    selectAllButton.id = 'selectAll';
    selectAllButton.textContent = 'Selecionar Todos';
    selectAllButton.className = 'btn btn-outline-primary btn-sm';
    selectAllButton.addEventListener('click', selectAllCheckboxes_desenhista);
    buttonContainer.appendChild(selectAllButton);

    // Create deselect all button
    const deselectAllButton = document.createElement('button');
    deselectAllButton.id = 'deselectAll';
    deselectAllButton.textContent = 'Desmarcar Todos';
    deselectAllButton.className = 'btn btn-outline-primary btn-sm';
    deselectAllButton.addEventListener('click', deselectAllCheckboxes_desenhista);
    buttonContainer.appendChild(deselectAllButton);

    // Add button container to the top of the group_3 container
    container.appendChild(buttonContainer);
}

  function createCheckboxes_desenhista(data) {
    const container = document.getElementById('group_2');

    for (const key in data) {
      if (data.hasOwnProperty(key)) {
        const label = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = key;

        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(data[key]));

        // Adiciona estilo para espaçamento
        label.style.margin = '5px';

        container.appendChild(label);
      }
    }

    updateColumns_desenhista();
  }

  function updateColumns_desenhista() {
    const container = document.getElementById('group_2');
    const containerWidth = container.offsetWidth;
    const checkboxWidth = 200; // Largura aproximada de cada checkbox com label
    const columns = Math.floor(containerWidth / checkboxWidth);
    container.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
  }

  function selectAllCheckboxes_desenhista() {
    const checkboxes = document.querySelectorAll('#group_2 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
  }

  function deselectAllCheckboxes_desenhista() {
    const checkboxes = document.querySelectorAll('#group_2 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
  }



  function getSelectedCheckboxValues_desenhista() {
    const checkboxes = document.querySelectorAll('#group_2 input[type="checkbox"]');
    const selectedValues = [];
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
        selectedValues.push(checkbox.value);
      }
    });
    return selectedValues;
  }


  // Inicializa controles e checkboxes
  function init() {
    createControls_desenhista();
    createControls_cortador();

    // Faz a requisição AJAX
    $.ajax({
      url: '<?= base_url('public/adm/lista_desenhistas') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        createCheckboxes_desenhista(response.lista);
        selectAllCheckboxes_desenhista();
      }
    });
    $.ajax({
      url: '<?= base_url('public/adm/lista_cortadores') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        createCheckboxes_cortador(response.lista);
        selectAllCheckboxes_cortador();
      }
    });

    // Ajusta o número de colunas baseado no tamanho da tela
    window.addEventListener('resize', updateColumns_desenhista);
    window.addEventListener('resize', updateColumns_cortador);
  }

  // Chama a função init na carga da página
  window.onload = init;


</script>