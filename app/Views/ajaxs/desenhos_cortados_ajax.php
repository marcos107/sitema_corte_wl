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
  function modal_login() {
    // HTML da sobreposição como uma string
    var overlayHTML = `
<div id="inicioOverlay" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 1001;
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none; /* Impede interação direta na camada de fundo */
">
    <div class="modal-dialog" style="
        background: #fff;
        width: 400px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
        position: relative;
        animation: fadeIn 0.3s ease-in-out;
        pointer-events: auto; /* Permite interação com os elementos do modal */
    ">
        <div class="modal-header" style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        ">
            <h4 class="modal-title" style="
                margin: 0;
                font-size: 1.5em;
                color: #333;
                font-weight: bold;
            ">Login</h4>
            <button type="button" class="close" onclick="fecharmodal()" style="
                background: none;
                border: none;
                font-size: 1.5em;
                cursor: pointer;
                color: #555;
            ">×</button>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                <label for="nome" style="font-weight: bold; font-size: 1em; color: #333;">Login</label>
                <input id="nome" type="text" placeholder="Digite seu nome" style="
                    width: 100%;
                    padding: 10px;
                    margin-top: 5px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    font-size: 1em;
                ">
            </div>
            <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                <label for="senha" style="font-weight: bold; font-size: 1em; color: #333;">Senha</label>
                <input id="senha" type="password" placeholder="Digite sua senha" style="
                    width: 100%;
                    padding: 10px;
                    margin-top: 5px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    font-size: 1em;
                ">
            </div>
        </div>
        <div class="modal-footer" style="
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        ">
            <button onclick="fecharmodal()" style="
                background: #f44336;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1em;
                width: 45%;
                transition: 0.3s;
            " onmouseover="this.style.background='#d32f2f'" onmouseout="this.style.background='#f44336'">Cancelar</button>
            <button onclick="login()" style="
                background: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1em;
                width: 45%;
                transition: 0.3s;
            " onmouseover="this.style.background='#0056b3'" onmouseout="this.style.background='#007bff'">Login</button>
        </div>
    </div>
</div>
    `;

    // Insere a sobreposição logo após a div com ID "cadastro1"
    var cadastroDiv = document.getElementById('list1');
    if (cadastroDiv) {
      cadastroDiv.insertAdjacentHTML('afterend', overlayHTML);
    }


  }

  function fecharmodal() {
    // Oculta a sobreposição quando o botão "Começar" é clicado
    var overlay = document.getElementById('inicioOverlay');
    if (overlay) {
      overlay.style.display = 'none';
    }
  }


  function login() {
    nome = document.getElementById("nome").value;
    senha = document.getElementById("senha").value;
    if (nome != '' && senha != '') {
      $.ajax({
        url: '<?= base_url('public/login_verificar_permissao') ?>',
        type: "POST",
        dataType: "json", // Indicar que o retorno é em formato JSON
        data: {
          nome: nome,
          senha: senha
        },
        success: function(response) {

          if (response.ok == "true") {
          
            lista_recolcoar();
            fecharmodal();
            
          }
          if (response.mensagem != null) {
          
            alert_personalizado("Login", response.mensagem);
          }

        }
      });
    } else {
      document.getElementById("error").innerHTML = 'Preencha senha e name';
    }
  }

  // Cria o HTML do botão
  var buttonHTML = `
<div id="container" style="display: inline-flex; align-items: center;">
    <select id="processos_desenho" class="custom-select" style="height: 35px">
        <option value="">Processos</option>
    </select>
    <button type="button" class="btn btn-outline-primary" id="pesquisar" style="margin-left: 10px;">
        Pesquisar
    </button>

    <button type="button" onclick="modal_login()" class="btn btn-outline-warning" id="btn_pendente" style="margin-left: 10px;">
        Pendente
    </button>
</div>


`;

  function lista_recolcoar() {
    $.ajax({
      url: '<?= base_url('public/lista_recolcoar') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function(response) {

        console.log(response);
        document.getElementById('modal_sizer').classList.add('modal-xl');
        const modalBory = document.getElementById('modal_bory');
        const modalTitulo = document.getElementById('modal_titulo');
        const botaoConfirmarModal = document.getElementById('botao_confirmar_modal');

        modalBory.innerHTML = ''; // Limpa o conteúdo anterior
        modalTitulo.textContent = "Modificar prioridade desenho";
        botaoConfirmarModal.innerHTML = "Confirmar";

        // Ajusta a largura do modal
        document.getElementById('modal_sizer').classList.add('modal-xxl'); // Ajuste para um tamanho maior

        /* -------------------------------------
           CRIA A TABELA
           ------------------------------------- */
        const tabela = document.createElement("table");
        tabela.classList.add("table", "table-bordered", "table-striped");
        tabela.id = "tabelaPrioridade";


        tabela.innerHTML = response.lista;
        modalBory.appendChild(tabela);

        /* -------------------------------------
           INICIALIZA O DATATABLE
           ------------------------------------- */
        // $("#tabelaPrioridade").DataTable({
        //   responsive: true,
        //   lengthChange: false,
        //   autoWidth: false,
        //   pageLength: 10,
        //   language: {
        //     decimal: "",
        //     emptyTable: "Sem dados disponíveis",
        //     infoEmpty: "Mostrando de 0 até 0 de 0 registros",
        //     infoFiltered: "(filtrado de _MAX_ registros no total)",
        //     infoPostFix: "",
        //     thousands: ",",
        //     lengthMenu: "Mostrar _MENU_ registros",
        //     loadingRecords: "A carregar dados...",
        //     processing: "A processar...",
        //     search: "Buscar:",
        //     zeroRecords: "Não foram encontrados resultados",
        //     paginate: {
        //       first: "Primeiro",
        //       last: "Último",
        //       next: "Seguinte",
        //       previous: "Anterior",
        //     },
        //     aria: {
        //       sortAscending: ": clique para ordenar ascendente (ASC)",
        //       sortDescending: ": clique para ordenar descendente (DESC)",
        //     },
        //   },
        // });
        document.getElementById("botao_confirmar_modal").remove();
        document.getElementById("botao_fechar_modal").innerHTML = "Fechar";
        mostrarModal(); // Exibe o modal


      }
    });
  }

  function solicitar_recolocar_desenho(id) {

    if (!mostrarConfirmacao("Deseja solicitar para recolocar esse desenho?"))  return;

    $.ajax({
      url: '<?= base_url('public/solicitar_recolocar_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id
      },
      success: function(response) {

        console.log(response);

        lista();

      }
    });
  }

  function recolocar_desenho(id, status) {
    if (!mostrarConfirmacao("Deseja "+status+" com esse pedido?"))  return;
    $.ajax({
      url: '<?= base_url('public/recolocar_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id,
        status: status
      },
      success: function(response) {

        console.log(response);
        document.getElementById("Linha_"+id).remove();
        
        lista();

      }
    });
  }


  processos = "";

  function processo_lista() {
    $.ajax({
      url: '<?= base_url('public/processos_lista') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON

      async: false, // Define a requisição como síncrona

      success: function(response) {

        processos = response.lista;


        if (!document.getElementById('processos_desenho'))
          return;
        // Seleciona o elemento <select> onde as opções serão adicionadas
        var selectElement = document.getElementById('processos_desenho');

        // Limpa as opções existentes no <select>
        selectElement.innerHTML = '';
        // Cria a opção padrão e adiciona ao início do <select>
        var defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Processos';
        selectElement.appendChild(defaultOption);
        // Itera sobre cada processo na lista
        processos.forEach(function(processo) {
          // Cria um novo elemento <option>
          var optionElement = document.createElement('option');
          optionElement.value = processo.nome; // Define o nome do processo como o valor da opção
          optionElement.textContent = processo.nome; // Define o nome do processo como o texto da opção

          // Adiciona a nova opção ao <select>
          selectElement.appendChild(optionElement);
        });
      }
    });
  }





  // Seleciona o campo de data final
  var dataFinal = document.getElementById("dataInicial");

  // Insere o HTML do botão depois do campo de data final
  dataFinal.insertAdjacentHTML("afterend", buttonHTML);

  // Adiciona o evento de clique para chamar a função lista()
  document.getElementById("pesquisar").addEventListener("click", pesquisar);
  processo_lista();

  data = "";
  data1 = "";
  processo_nome = "";

  function pesquisar() {
    data = document.getElementById('dataInicial').value;
    data1 = document.getElementById('dataFinal').value;
    processo_nome = document.getElementById("processos_desenho").value;
    if (processo_nome == '') {
      alert_personalizado("Processos", 'Escolha um Processos.');
      return;
    }
    lista();
  }


  lista_temp = "";

  function lista() {
    document.getElementById("pesquisar").disabled = true;
    $.ajax({
      url: '<?= base_url('public/desenhos_cortados') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        data: data,
        data1: data1,
        processo: processo_nome
      },
      success: function(response) {
        pendente = response.pendente;
        console.log(response);





        
      
        let botao = document.getElementById('btn_pendente');
        if (pendente == 'false') {


          if (botao) {
            botao.disabled = true;
           // botao.classList.remove("ativo"); // Remove a classe anterior, se houver
           // botao.classList.add('btn btn-outline-warning');
          }

        } else {


          if (botao) {
            botao.disabled = false;

           // botao.class.add('btn btn-outline-warning');
            //botao.classList.remove("ativo"); // Remove a classe anterior, se houver
            // botao.classList.add('btn btn-outline-primary');
          }
        }



        if (response.lista != lista_temp) {
          $('#example1').DataTable().destroy();





          // Recriar e configurar a tabela DataTable

          var div = $('#minhaDiv');

          div.load(location.href + ' #minhaDiv');
          // Selecione o elemento <tbody> pelo seu ID
          var lista = document.getElementById('lista');
          // Substitua o conteúdo do elemento <tbody> com o novo HTML
          lista.innerHTML = response.lista;
          $(function() {
            $("#example1").DataTable({

              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "buttons": ["colvis"],
              "language": {
                "decimal": "",
                "emptyTable": "Sem dados disponíveis",
                "infoEmpty": "Mostrando de 0 até 0 de 0 registos",
                "infoFiltered": "(filtrado de MAX registos no total)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": " MENU",
                "loadingRecords": "A carregar dados...",
                "processing": "A processar...",
                "search": "Buscar:",
                "zeroRecords": "Não foram encontrados resultados",
                "paginate": {
                  "first": "Primeiro",
                  "last": "Último",
                  "next": "Seguinte",
                  "previous": "Anterior"

                },
                "aria": {
                  "sortAscending": ": clique para ordenar ascendente (ASC)",
                  "sortDescending": ": clique para ordenar descendente (DESC)"
                }
              }

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

          });



          lista_temp = response.lista;
        }
        document.getElementById("pesquisar").disabled = false;

      }
    });
  }
  // Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', lista);



  const dataInicialInput = document.getElementById('dataInicial');
  const dataFinalInput = document.getElementById('dataFinal');










  function subistituir_desenho_modal(id) {

    $.ajax({
      url: '<?= base_url('public/subistituir_desenho_modal') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      data: {
        id: id
      },
      success: function(response) {





        console.log(response);
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');


        botao_confirmar_modal.innerHTML = "Confirmar";
        var modal_titulo = document.getElementById('modal_titulo');
        var modal_bory = document.getElementById('modal_bory');
        modal_titulo.textContent = "Subistiruit desenho";





        modal_bory.innerHTML = '';






        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        inputElement = document.createElement("input");
        inputElement.type = 'text';
        inputElement.id = 'novo_nome_arquivo';
        inputElement.classList.add("form-control");
        inputElement.value = response.nome;
        divElemnt.innerHTML = '';
        labelElement = document.createElement("label");
        labelElement.textContent = "Novo nome do arquivo";
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt); //coloca o input name no modal


        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");
        inputElement = document.createElement("input");
        inputElement.type = 'file';
        inputElement.id = 'novo_arquvivo';
        inputElement.classList.add("form-control");
        divElemnt.innerHTML = '';
        labelElement = document.createElement("label");
        labelElement.textContent = "Novo arquivo";
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt); //coloca o input name no modal

        botao_confirmar_modal.onclick =
          function() {
            var nome = document.getElementById("novo_nome_arquivo").value;
            var fileInput = document.getElementById('novo_arquvivo');
            var file = fileInput.files[0];

            $.ajax({
              url: '<?= base_url('public/desenho_novo_nome') ?>',
              type: "POST",
              dataType: "json",
              data: {
                nome: nome
              },
              success: function(response) {
                console.log(response);
              }
            });

            var formData = new FormData();
            formData.append('file', file);

            $.ajax({
              url: '<?= base_url('public/subistituir_desenho') ?>',
              type: "POST",
              dataType: "json",
              processData: false,
              contentType: false,
              data: formData,
              success: function(response) {
                console.log(response);
                fecharModal();
                if (response.ok == 'true') {
                  fecharModal();
                  alert_certo('Desenho', response.mensagem);
                } else {
                  fecharModal();
                  alert_personalizado('Desenho', response.mensagem);
                }
              },
              error: function(xhr, status, error) {

                confirmarModal();
              }
            });
          };


        mostrarModal();

      }

    });


  }

  function confirmarModal() {
    var nome = document.getElementById("novo_nome_arquivo").value;
    var fileInput = document.getElementById('novo_arquvivo');
    var file = fileInput.files[0];
    console.log(fileInput.files);
    $.ajax({
      url: '<?= base_url('public/desenho_novo_nome') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON

      data: {
        nome: nome
      },

      success: function(response) {
        console.log(response);
      }
    });


    var formData = new FormData();
    formData.append('file', file);
    $.ajax({
      url: '<?= base_url('public/subistituir_desenho') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      processData: false,
      contentType: false,
      data: formData,

      success: function(response) {
        console.log(response);
        fecharModal();
        if (response.ok == 'true') {

          fecharModal();

          alert_certo('Desenho', response.mensagem);
        } else {


          fecharModal();
          alert_personalizado('Desenho', response.mensagem);
        }


      }
    });

  }




  // function recolocar_desenho(id) {
  //   if (mostrarConfirmacao("Recolocar desenho?")) {
  //     $.ajax({
  //       url: '<?= base_url('public/recolocar_desenho') ?>',
  //       type: "POST",
  //       dataType: "json", // Indicar que o retorno é em formato JSON
  //       // async: true,

  //       data: {
  //         id: id
  //       },

  //       success: function(response) {
  //         console.log(response);
  //         lista();



  //       }
  //     });

  //   }
  // }

  function mostrarConfirmacao(texto = '') {
    // Exibe a caixa de diálogo de confirmação e armazena a resposta em uma variável
    var resposta = window.confirm(texto);
    // Verifica a resposta e faz algo com ela
    return resposta;

  }










  function alert_certo(titulo, bory) {
    $(document).Toasts('create', {
      class: 'bg-success',
      title: titulo,
      subtitle: 'Subtitle',
      autohide: true,
      delay: 5000,
      body: bory
    });
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

  // // Repetir função a cada segundo
  // setInterval(lista, 5000);
</script>
<style>
  .quebrar {
    word-break: break-all;
    white-space: normal;
  }
</style>