<style>
  .button-container {
    display: flex;
    justify-content: center;
    /* Centraliza os botões horizontalmente */
    align-items: center;
    /* Centraliza os botões verticalmente */
    gap: 10px;
    /* Adiciona um espaço entre os botões */
  }



  .button-container button {
    width: 50%;
    /* Faz com que o contêiner ocupe 100% da largura disponível na div */

    flex: none;
    /* Garante que os botões não ocupem a largura total */
  }
</style>
<script>
  cadastrar_glob = false;
  function cadastrar() {
    cadastrar_glob = true;
    document.getElementById('cadastrar_btn').disabled = true;
    const dataInicial = document.getElementById("data_inicial").value;
    const dataFinal = document.getElementById("data_final").value;
    desenhistas = getSelectedCheckboxValues_desenhista();
    cortador = getSelectedCheckboxValues_cortador();

    $.ajax({
      url: '<?= base_url('public/relatorio_analitico') ?>',
      type: "POST",
      dataType: "json", // Espera uma resposta JSON
      data: { dataInicial: dataInicial, dataFinal: dataFinal, desenhistas: desenhistas, cortador: cortador, relatorio: document.getElementById('rad_1').checked },
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
        cadastrar_glob = false;

      },
      error: function (jqXHR, textStatus, errorThrown) {
        cadastrar_glob = false;
        document.getElementById('cadastrar_btn').disabled = false;

        // console.log(textStatus, errorThrown);
      }
    });
  }


  const element = document.getElementById("projetistas");
  if (element) {
    element.remove();
  }
  const element1 = document.getElementById("cortadores");
  if (element1) {
    element1.remove();
  }
  const element2 = document.getElementById("rad_1");
  if (element2) {
    element2.remove();
  }
  const element3 = document.getElementById("checkbox_ativo");
  if (element3) {
    element3.remove();
  }
  const group0 = document.getElementById('group_0');
  const group1 = document.getElementById('group_1');
  const group2 = document.getElementById('group_2');
  const group3 = document.getElementById('group_3');
  const group4 = document.getElementById('group_4');
  const group5 = document.getElementById('group_5');
  const cadastrar_btn = document.getElementById('cadastrar_btn');

  cadastrar_btn.style.width = '50%';

  group0.style.display = 'inline-block';
  group0.style.width = '250px';
  group0.style.verticalAlign = 'top';
  group0.style.padding = '5px';

  group1.style.display = 'inline-block';
  group1.style.width = '250px';
  group1.style.verticalAlign = 'top';
  group1.style.padding = '5px';

  group2.style.display = 'inline-block';
  group2.style.width = '200px';
  group2.style.verticalAlign = 'top';
  group2.style.padding = '5px';

  group3.style.display = 'inline-block';
  group3.style.width = '200px';
  group3.style.verticalAlign = 'top';
  group3.style.padding = '5px';

  group4.style.display = 'inline-block';
  group4.style.width = '600px';
  group4.style.verticalAlign = 'top';
  group4.style.padding = '5px';

  group5.style.display = 'inline-block';
  group5.style.width = '600px';
  group5.style.verticalAlign = 'top';
  group5.style.padding = '5px';

  group4.parentNode.insertBefore(document.createElement('br'), group4);

  // Função para criar e adicionar novos elementos dentro de uma nova div em um grupo existente
  function addElementsToNewDiv(inputType, inputId, inputName, labelText, newDiv = document.createElement('div')) {

    // Cria uma nova div com a classe especificada
    newDiv.style = 'display: flex; justify-content: flex-start; padding: 5px; padding-right: 15px;';


    const input = document.createElement('input');
    input.type = inputType;
    input.style = 'padding: 5px; ';
    input.id = inputId;
    if (inputName) {
      input.name = inputName;
    }

    const label = document.createElement('span');
    label.innerHTML = labelText;
    label.style = 'padding: 5px; vertical-align: inherit; padding-right: 10px;';


    newDiv.appendChild(input);
    newDiv.appendChild(label);






    return newDiv
  }

  // Adicionar novo radio button "Sintético" dentro de uma nova div no group_2
  group2.appendChild(addElementsToNewDiv('radio', 'rad_2', 'tipo_relatorio', 'Sintético', addElementsToNewDiv('radio', 'rad_1', 'tipo_relatorio', 'Analítico')));

  group3.appendChild(addElementsToNewDiv('checkbox', 'checkbox_desativado', '', 'Desativado', addElementsToNewDiv('checkbox', 'checkbox_ativo', '', 'Ativo')));

  document.getElementById('rad_1').checked = true;
  document.getElementById('checkbox_ativo').checked = true;
  // Adicionar novo checkbox "Desativado" dentro de uma nova div no group_3


  function areAllCheckboxesDesenhistaUnchecked() {
    var checkboxes = document.querySelectorAll('#checkbox_desenhistas');
    for (var i = 0; i < checkboxes.length; i++) {
      if (checkboxes[i].checked) {
        return false; // Retorna false se pelo menos um checkbox estiver marcado
      }
    }
    return true; // Retorna true se todos os checkboxes estiverem desmarcados
  }

  function areAllCheckboxesCortadorUnchecked() {
    var checkboxes = document.querySelectorAll('#checkbox_cortadores');
    for (var i = 0; i < checkboxes.length; i++) {
      if (checkboxes[i].checked) {
        return false; // Retorna false se pelo menos um checkbox estiver marcado
      }
    }
    return true; // Retorna true se todos os checkboxes estiverem desmarcados
  }

  function checkIfAllDesenhistaUnchecked(event) {


    if (!event) { // Apenas trata se o checkbox estiver sendo desmarcado
      var allUncheckedDesenhista = areAllCheckboxesDesenhistaUnchecked();
      var allUncheckedCortador = areAllCheckboxesCortadorUnchecked();

      if (allUncheckedDesenhista && allUncheckedCortador) {
        document.getElementById('cadastrar_btn').disabled = true;

        alert_personalizado("Usuários", "Ao menso um Projetista ou Cortador precisa estar selecionado");
      }
    } else {
      var isValid = document.getElementById('checkbox_desativado').checked || document.getElementById('checkbox_ativo').checked;
      if (!cadastrar_glob && isValid) {
        document.getElementById('cadastrar_btn').disabled = false;
      }
    }
  }







  document.getElementById("data_inicial").addEventListener('dblclick', () => {
    document.getElementById("data_inicial").showPicker();
  });
  document.getElementById("data_final").addEventListener('dblclick', () => {
    document.getElementById("data_final").showPicker();
  });




  document.addEventListener("DOMContentLoaded", function () {
    const dataInicial = document.getElementById("data_inicial");
    const dataFinal = document.getElementById("data_final");

    // Obtendo a data de hoje e subtraindo um dia para obter o dia anterior
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate());
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


  });


  function createControls_cortador() {
    const container = document.getElementById('group_5');

    // Cria o contêiner do botão
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container';

    // Define os estilos do contêiner para alinhamento à esquerda
    buttonContainer.style.display = 'flex';
    buttonContainer.style.justifyContent = 'flex-start';
    buttonContainer.style.padding = '5px';


    // Create select all button
    const selectAllButton = document.createElement('button');
    selectAllButton.id = 'selectAll';
    selectAllButton.textContent = 'Selecionar Todos';
    selectAllButton.className = 'btn btn-outline-primary btn-sm';
    selectAllButton.style.width = '150px';  // Define a largura do botão
    selectAllButton.addEventListener('click', selectAllCheckboxes_cortador);
    buttonContainer.appendChild(selectAllButton);

    // Create deselect all button
    const deselectAllButton = document.createElement('button');
    deselectAllButton.id = 'deselectAll';
    deselectAllButton.textContent = 'Desmarcar Todos';
    deselectAllButton.className = 'btn btn-outline-primary btn-sm';
    deselectAllButton.style.width = '150px';  // Define a largura do botão
    deselectAllButton.addEventListener('click', deselectAllCheckboxes_cortador);
    buttonContainer.appendChild(deselectAllButton);

    // Add button container to the top of the group_3 container
    container.appendChild(buttonContainer);
  }


  function createCheckboxes_cortador(data) {
    ativo = document.getElementById('checkbox_ativo').checked;
    desativado = document.getElementById('checkbox_desativado').checked;
    const container = document.getElementById('group_5');
    const checkboxes = container.querySelectorAll('#checkbox_cortador');
    checkboxes.forEach(checkbox => checkbox.remove());


    if (ativo)
      for (const key in data.ativo) {
        if (data.ativo.hasOwnProperty(key)) {
          const label = document.createElement('label');
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.id = "checkbox_cortadores";
          checkbox.value = key;
          label.id = 'checkbox_cortador';
          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(data.ativo[key]));

          // Adiciona estilo para espaçamento
          label.style.margin = '5px';

          container.appendChild(label);
        }
      }

    if (desativado)
      for (const key in data.desativado) {
        if (data.desativado.hasOwnProperty(key)) {
          const label = document.createElement('label');
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.id = "checkbox_cortadores";
          label.id = 'checkbox_cortador';
          checkbox.value = key;

          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(data.desativado[key]));

          // Adiciona estilo para espaçamento
          label.style.margin = '5px';

          container.appendChild(label);
        }
      }

    document.querySelectorAll('#checkbox_cortadores').forEach(function (checkbox) {
      checkbox.addEventListener('click', function (event) {
        checkIfAllDesenhistaUnchecked(event.target.checked);
      });
    });

    updateColumns_cortador();
  }

  function updateColumns_cortador() {
    const container = document.getElementById('group_5');
    const containerWidth = container.offsetWidth;
    const checkboxWidth = 200; // Largura aproximada de cada checkbox com label
    const columns = Math.floor(containerWidth / checkboxWidth);
    container.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
  }

  function selectAllCheckboxes_cortador() {
    const checkboxes = document.querySelectorAll('#group_5 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
  }

  function deselectAllCheckboxes_cortador() {
    const checkboxes = document.querySelectorAll('#group_5 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    checkIfAllDesenhistaUnchecked(false);
  }



  function getSelectedCheckboxValues_cortador() {
    const checkboxes = document.querySelectorAll('#group_5 input[type="checkbox"]');
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
    const container = document.getElementById('group_4');

    // Create button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container';

    // Define os estilos do contêiner para alinhamento à esquerda
    buttonContainer.style.display = 'flex';
    buttonContainer.style.justifyContent = 'flex-start';
    buttonContainer.style.padding = '5px';

    // Create select all button
    const selectAllButton = document.createElement('button');
    selectAllButton.id = 'selectAll';
    selectAllButton.textContent = 'Selecionar Todos';
    selectAllButton.className = 'btn btn-outline-primary btn-sm';
    selectAllButton.style.width = '150px';  // Define a largura do botão
    selectAllButton.addEventListener('click', selectAllCheckboxes_desenhista);
    buttonContainer.appendChild(selectAllButton);

    // Create deselect all button
    const deselectAllButton = document.createElement('button');
    deselectAllButton.id = 'deselectAll';
    deselectAllButton.textContent = 'Desmarcar Todos';
    deselectAllButton.className = 'btn btn-outline-primary btn-sm';
    deselectAllButton.style.width = '150px';  // Define a largura do botão
    deselectAllButton.addEventListener('click', deselectAllCheckboxes_desenhista);
    buttonContainer.appendChild(deselectAllButton);

    // Add button container to the top of the group_3 container
    container.appendChild(buttonContainer);
  }

  function createCheckboxes_desenhista(data) {

    ativo = document.getElementById('checkbox_ativo').checked;
    desativado = document.getElementById('checkbox_desativado').checked;
    const container = document.getElementById('group_4');
    const checkboxes = container.querySelectorAll('#checkbox_desenhista');
    checkboxes.forEach(checkbox => checkbox.remove());

    if (ativo)
      for (const key in data.ativo) {
        if (data.ativo.hasOwnProperty(key)) {
          const label = document.createElement('label');
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.value = key;
          checkbox.id = "checkbox_desenhistas";
          label.id = "checkbox_desenhista";

          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(data.ativo[key]));

          // Adiciona estilo para espaçamento
          label.style.margin = '5px';

          container.appendChild(label);
        }
      }

    if (desativado)
      for (const key in data.desativado) {
        if (data.desativado.hasOwnProperty(key)) {
          const label = document.createElement('label');
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.value = key;
          checkbox.id = "checkbox_desenhistas";
          label.id = "checkbox_desenhista";
          label.appendChild(checkbox);
          label.appendChild(document.createTextNode(data.desativado[key]));

          // Adiciona estilo para espaçamento
          label.style.margin = '5px';

          container.appendChild(label);
        }
      }
    document.querySelectorAll('#checkbox_desenhistas').forEach(function (checkbox) {
      checkbox.addEventListener('click', function (event) {
        checkIfAllDesenhistaUnchecked(event.target.checked);
      });
    });
    updateColumns_desenhista();
  }

  function updateColumns_desenhista() {
    const container = document.getElementById('group_4');
    const containerWidth = container.offsetWidth;
    const checkboxWidth = 200; // Largura aproximada de cada checkbox com label
    const columns = Math.floor(containerWidth / checkboxWidth);
    container.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
  }

  function selectAllCheckboxes_desenhista() {
    const checkboxes = document.querySelectorAll('#group_4 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
  }

  function deselectAllCheckboxes_desenhista() {
    const checkboxes = document.querySelectorAll('#group_4 input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    checkIfAllDesenhistaUnchecked(false);
  }



  function getSelectedCheckboxValues_desenhista() {
    const checkboxes = document.querySelectorAll('#group_4 input[type="checkbox"]');
    const selectedValues = [];
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
        selectedValues.push(checkbox.value);
      }
    });
    return selectedValues;
  }




  function handleCheckboxChange(event) {
    var isValid = document.getElementById('checkbox_desativado').checked || document.getElementById('checkbox_ativo').checked;
    if (!isValid) {
      document.getElementById('cadastrar_btn').disabled = true;
      alert_personalizado("Visualizar usuários", "É preciso manter ao menos uma das duas opção selecionada.");

    } else {
      var allUncheckedDesenhista = areAllCheckboxesDesenhistaUnchecked();
      var allUncheckedCortador = areAllCheckboxesCortadorUnchecked();
      if (!cadastrar_glob && (!areAllCheckboxesDesenhistaUnchecked() || !areAllCheckboxesCortadorUnchecked())) {
        document.getElementById('cadastrar_btn').disabled = false;
      }

    }
  }

  document.getElementById('checkbox_desativado').addEventListener('click', handleCheckboxChange);
  document.getElementById('checkbox_ativo').addEventListener('click', handleCheckboxChange);


  desenhistas_glob = '';
  cortadores_glob = '';


  // Inicializa controles e checkboxes
  function init() {
    createControls_desenhista();
    createControls_cortador();
    console.log('response');
    // Faz a requisição AJAX
    $.ajax({
      url: '<?= base_url('public/lista_desenhistas') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        desenhistas_glob = response.lista;
        createCheckboxes_desenhista(response.lista);
        selectAllCheckboxes_desenhista();
      }
    });
    $.ajax({
      url: '<?= base_url('public/lista_cortadores') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        cortadores_glob = response.lista;
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