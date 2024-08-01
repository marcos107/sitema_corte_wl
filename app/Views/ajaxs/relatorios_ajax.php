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


    $.ajax({
      url: '<?= base_url('public/relatorio') ?>',
      type: "POST",
      dataType: "json", // Espera uma resposta JSON
      data: { dataInicial: dataInicial, dataFinal: dataFinal, relatorio: document.getElementById('rad_1').checked,selectedValues: getSelectedCheckboxValues() },
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

  document.getElementById('checkbox_ativo').addEventListener('click', function () {
    Object.entries(data_glob.lista).forEach(([key, value]) => {
          addCheckboxes(value,data_glob.id_groups[key]);
          
        });
  });

  document.getElementById('checkbox_desativado').addEventListener('click', function () {
    Object.entries(data_glob.lista).forEach(([key, value]) => {
          addCheckboxes(value,data_glob.id_groups[key]);
          
        });
  });


  // Adicionar novo checkbox "Desativado" dentro de uma nova div no group_3


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

  // Chama a função init na carga da página
  //window.onload = init;

  //çççç

  function inicio() {
    $.ajax({
      url: '<?= base_url('public/lista_usuarios_niveis') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        response.id_groups = [];
        Object.entries(response.lista).forEach(([key, value]) => {
          let id_goup = createControls(key);

          // Adiciona o id_goup ao array response.id_groups
          response.id_groups[key] = id_goup;

          createCheckboxes(value, id_goup);
        });
       data_glob = response;
       
        // Agora response.id_groups contém todos os id_goup adicionados
      
      }
    });
  }

  function getNextGroupId() {
    const existingGroups = document.querySelectorAll('[id^="group_"]');
    let maxId = 0;

    existingGroups.forEach(group => {
      const idNumber = parseInt(group.id.split('_')[1]);
      if (idNumber > maxId) {
        maxId = idNumber;
      }
    });

    return `group_${maxId + 1}`;
  }

  function createControls(label) {
    const nextGroupId = getNextGroupId();
    const container = document.createElement('div');
    container.id = nextGroupId;
    container.className = 'form-group';
    container.innerHTML = `<label>${label}</label>`;

    // Create button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container';

    // Define os estilos do contêiner para alinhamento à esquerda
    buttonContainer.style.display = 'flex';
    buttonContainer.style.justifyContent = 'flex-start';
    buttonContainer.style.padding = '5px';

    // Create select all button
    const selectAllButton = document.createElement('button');
    selectAllButton.id = `selectAll_${nextGroupId}`;
    selectAllButton.textContent = 'Selecionar Todos';
    selectAllButton.className = 'btn btn-outline-primary btn-sm';
    selectAllButton.style.width = '150px';  // Define a largura do botão
    selectAllButton.addEventListener('click', () => selectAllCheckboxes(nextGroupId));
    buttonContainer.appendChild(selectAllButton);

    // Create deselect all button
    const deselectAllButton = document.createElement('button');
    deselectAllButton.id = `deselectAll_${nextGroupId}`;
    deselectAllButton.textContent = 'Desmarcar Todos';
    deselectAllButton.className = 'btn btn-outline-primary btn-sm';
    deselectAllButton.style.width = '150px';  // Define a largura do botão
    deselectAllButton.addEventListener('click', () => deselectAllCheckboxes(nextGroupId));
    buttonContainer.appendChild(deselectAllButton);

    // Add button container to the top of the new group container
    container.appendChild(buttonContainer);

    // Append the new group container to the parent container
    document.getElementById('inputs_body').appendChild(container);

    return nextGroupId;
  }

  var data_glob = {
    ativo: null,
    desativado: null
  };;


  function addCheckboxes(items, groupId) {

    const checkboxAtivo = document.getElementById('checkbox_ativo');
    const checkboxDesativado = document.getElementById('checkbox_desativado');

    // Seleciona todos os elementos que têm um ID que começa com 'group_' e termina com '_checkbox_label'
    var elements = document.querySelectorAll('[id="'+groupId + '_checkbox_label"');

    // Itera sobre os elementos selecionados e remove cada um deles
    elements.forEach(function (element) {
      element.parentNode.removeChild(element);
    });
    console.log(groupId + '_checkbox_label');



    if (checkboxAtivo.checked) {
      if (items.ativo) {
        for (const key in items.ativo) {
          if (items.ativo.hasOwnProperty(key)) {
            const label = document.createElement('label');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = key;
            checkbox.checked = true;
            checkbox.id = `${groupId}_checkbox`;
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(items.ativo[key]));
            label.id = `${groupId}_checkbox_label`;

            // Adiciona estilo para espaçamento
            label.style.margin = '5px';

            document.getElementById(groupId).appendChild(label);
          }
        }
      }
    }
    if (checkboxDesativado.checked) {
      if (items.desativado) {
        for (const key in items.desativado) {
          if (items.desativado.hasOwnProperty(key)) {
            const label = document.createElement('label');
            label.id = `${groupId}_checkbox_label`;
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = key;
            checkbox.checked = true;
            checkbox.id = `${groupId}_checkbox`;
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(items.desativado[key]));

            // Adiciona estilo para espaçamento
            label.style.margin = '5px';

            document.getElementById(groupId).appendChild(label);
          }
        }
      }
    }

  }

  function createCheckboxes(data, groupId) {
    const container = document.getElementById(groupId);
    const checkboxes = container.querySelectorAll(`.${groupId}_checkbox`);
    checkboxes.forEach(checkbox => checkbox.remove());



    addCheckboxes(data,groupId);


    document.querySelectorAll(`.${groupId}_checkbox`).forEach(function (checkbox) {
      checkbox.addEventListener('click', function (event) {
        checkIfAllUnchecked(event.target.checked, groupId);
      });
    });
    updateColumns(groupId);
  }

  function updateColumns(groupId) {
    const container = document.getElementById(groupId);
    const containerWidth = container.offsetWidth;
    const checkboxWidth = 200; // Largura aproximada de cada checkbox com label
    const columns = Math.floor(containerWidth / checkboxWidth);
    container.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
  }

  function selectAllCheckboxes(groupId) {
    const checkboxes = document.querySelectorAll(`#${groupId} input[type="checkbox"]`);
    checkboxes.forEach(checkbox => checkbox.checked = true);
  }

  function deselectAllCheckboxes(groupId) {
    const checkboxes = document.querySelectorAll(`#${groupId} input[type="checkbox"]`);
    checkboxes.forEach(checkbox => checkbox.checked = false);
    checkIfAllUnchecked(false, groupId);
  }

  function getSelectedCheckboxValues(groupId) {
    const checkboxes = document.querySelectorAll(`#${groupId} input[type="checkbox"]`);
    const selectedValues = [];
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
        selectedValues.push(checkbox.value);
      }
    });
    return selectedValues;
  }

  function checkIfAllUnchecked(checked, groupId) {
    const checkboxes = document.querySelectorAll(`#${groupId} input[type="checkbox"]`);
    const allUnchecked = Array.from(checkboxes).every(checkbox => !checkbox.checked);

    if (allUnchecked) {
      // Do something if all checkboxes are unchecked
    }
  }
  inicio();


  function getSelectedCheckboxValues() {
    const selectedValues = {};

    // Itera sobre cada chave em response.id_groups
    Object.entries(data_glob.id_groups).forEach(([key, groupId]) => {
        // Seleciona todos os checkboxes dentro do grupo específico
        const checkboxes = document.querySelectorAll('[id="'+groupId + '_checkbox"]');
       // console.log('[id="'+groupId + '_checkbox"');
        selectedValues[key] = [];

        // Itera sobre os checkboxes e adiciona os valores selecionados ao array correspondente
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedValues[key].push(checkbox.value);
            }
        });
    });

    return selectedValues;
}


</script>