<script>

    document.getElementById("nome_usuario").addEventListener("input", function () {
        var input = this;
        var maxLength = 17;
        var valor = input.value;
        input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

    });

    document.getElementById("senha_usuario").addEventListener("input", function () {
        var input = this;
        var maxLength = 50;
        var valor = input.value;
        input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

    });
    document.getElementById("email_usuario").addEventListener("input", function () {
        var input = this;
        var maxLength = 50;
        var valor = input.value;
        input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

    });



    lista_temp = "";
    function value_funcao() {
        $.ajax({
            url: '<?= base_url('public/lista_nivel') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (response.lista.toString() != lista_temp) {
                    // Obter referência ao elemento select
                    var funcao = document.getElementById("funcao_usuario");
                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Função do novo usuario';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element;
                        novoOption.textContent = element;

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp = response.lista.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    document.addEventListener('DOMContentLoaded', value_funcao);




    
    function cadastrar() {//faz o cadastro do furmalho no banco de dados e mostra a mensagem de retorno
        var nome = document.getElementById("nome_usuario").value;
        var senha = document.getElementById("senha_usuario").value;
        var funcao = document.getElementById("funcao_usuario").value;
        var email = document.getElementById("email_usuario").value;
        var whazapp = document.getElementById("whazapp_usuario").value;
        $.ajax({
            url: '<?= base_url('public/user_cadastrar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nome: nome, senha: senha, nivel: funcao, email: email, whazapp: whazapp },
            success: function (response) {

                if (!response.ok) {
                    //response.msg
                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                }else{
                    alert_certo('Cadastrado','Usuário cadastrado com sucesso.');
                    document.getElementById("nome_usuario").value = '';
                    document.getElementById("senha_usuario").value ='';
                    document.getElementById("funcao_usuario").value = '';
                    document.getElementById("email_usuario").value = '';
                    document.getElementById("whazapp_usuario").value = '';
                    lista();
                    
                }

            }
        });
    }







    lista_temp = "";




<?php $processo = "user"; ?>
function desativar(id) {
  $.ajax({
    url: '<?= base_url('public/adm/troca_status/' . $processo . '/desativado') ?>',
    type: "POST",
    dataType: "json", // Indicar que o retorno é em formato JSON
    data: { id: id },
    success: function (response) {

      lista();
    }
  });
}




const closemodal_atualizar = () => {
  document.getElementById('modal_atualizar').innerHTML = "";
}


function ativar(id) {

  $.ajax({
    url: '<?= base_url('public/adm/troca_status/' . $processo . '/ativo') ?>',
    type: "POST",
    dataType: "json", // Indicar que o retorno é em formato JSON
    data: { id: id },
    success: function (response) {

      lista();
    }
  });
}









function lista() {
  var ativos = document.getElementById('checkbox_ativos').checked;
  var desativados = document.getElementById('checkbox_desativado').checked;
  $.ajax({
    url: '<?= base_url('public/user_modificar') ?>',
    type: "POST",
    dataType: "json", // Indicar que o retorno é em formato JSON
    data: { ativos: ativos, desativados: desativados },
    success: function (response) {
      if (response.lista != lista_temp) {
        $('#example1').DataTable().destroy();





        // Recriar e configurar a tabela DataTable


        var div = $('#minhaDiv');

        div.load(location.href + ' #minhaDiv');
        // Selecione o elemento <tbody> pelo seu ID
        var lista = document.getElementById('lista');
        // Substitua o conteúdo do elemento <tbody> com o novo HTML
        lista.innerHTML = response.lista;
        $(function () {
          $("#example1").DataTable({

            "responsive": true, "lengthChange": false, "autoWidth": false,
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
    }
  });
}
// Executar função ao abrir o site
document.addEventListener('DOMContentLoaded', lista);


id_g = '';
function modal_modificar(id) {
  id = id.replace('modal_', '');
  id_g = id;
  $.ajax({
    url: '<?= base_url('public/user_modificar_modal') ?>',
    type: "POST",
    dataType: "json", // Indicar que o retorno é em formato JSON
    data: { id: id },
    success: function (response) {
      
      
      var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
      botao_confirmar_modal.innerHTML = "Confirmar";
      var modal_titulo = document.getElementById('modal_titulo');
      var modal_bory = document.getElementById('modal_bory');
      modal_titulo.textContent = "Modificar o usuário: " + response.nome;
      const selectElement = document.createElement("select");
      var inputElement = document.createElement("input");

      var divElemnt = document.createElement("div");
      divElemnt.classList.add("form-group");

      modal_bory.innerHTML='';
      // Limpar o select
      selectElement.innerHTML = '';
      // Criar um novo elemento option
      
      var novoOption = document.createElement("option");
      
      

      divElemnt = document.createElement("div");
      divElemnt.classList.add("form-group");
      inputElement = document.createElement("input");
      inputElement.type = 'text';
      inputElement.id = 'nome_usuario';
      inputElement.classList.add("form-control");
      inputElement.value = response.nome;
      // Adiciona o evento de input para truncar o valor
inputElement.addEventListener("input", function () {
  var input = this;
  var maxLength = 17;
  var valor = input.value;
  input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 
});
      labelElement = document.createElement("label");
      labelElement.textContent  = "Nome";
      divElemnt.innerHTML = '';
      divElemnt.appendChild(labelElement);
      divElemnt.appendChild(inputElement);
      modal_bory.appendChild(divElemnt);//coloca o input name no modal









      divElemnt = document.createElement("div");
      divElemnt.classList.add("form-group");
      inputElement = document.createElement("input");
      inputElement.type = 'text';
      inputElement.id = 'senha_usuario';
      inputElement.classList.add("form-control");
      inputElement.value = response.senha;
      // Adiciona o evento de input para truncar o valor
inputElement.addEventListener("input", function () {
  var input = this;
  var maxLength = 50;
  var valor = input.value;
  input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 
});
      divElemnt.innerHTML = '';
      labelElement = document.createElement("label");
      labelElement.textContent  = "Senha";
      divElemnt.appendChild(labelElement);
      divElemnt.appendChild(inputElement);
      modal_bory.appendChild(divElemnt);//coloca o input name no modal





      divElemnt = document.createElement("div");
      divElemnt.classList.add("form-group");
      // Definir o valor e texto do novo elemento option
      novoOption.value = response.nivel;
      novoOption.textContent = response.nivel;
      selectElement.id = 'funcao_usuario';
      selectElement.classList.add("custom-select");
      selectElement.appendChild(novoOption);
      labelElement = document.createElement("label");
      labelElement.textContent  = "Função";
      divElemnt.innerHTML = '';
      divElemnt.appendChild(labelElement);
      divElemnt.appendChild(selectElement);
      modal_bory.appendChild(divElemnt);//coloca o input name no modal







      

      divElemnt = document.createElement("div");
      divElemnt.classList.add("form-group");
      inputElement = document.createElement("input");
      inputElement.type = 'email';
      inputElement.classList.add("form-control");
      inputElement.id = 'email_usuario';
      inputElement.value = response.email;
      // Adiciona o evento de input para truncar o valor
inputElement.addEventListener("input", function () {
  var input = this;
  var maxLength = 50;
  var valor = input.value;
  input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 
});
      labelElement = document.createElement("label");
      labelElement.textContent  = "Email";
      divElemnt.appendChild(labelElement);
      divElemnt.appendChild(inputElement);
      modal_bory.appendChild(divElemnt);//coloca o input name no modal
      


      divElemnt = document.createElement("div");
      divElemnt.classList.add("form-group");
      inputElement = document.createElement("input");
      inputElement.type = 'tel';
      inputElement.id = 'whazapp_usuario';
      inputElement.classList.add("form-control");
      inputElement.maxLength = 15;
      inputElement.addEventListener("input", handlePhone);
      inputElement.value = response.whatsapp;
      labelElement = document.createElement("label");
      labelElement.textContent  = "Whatsapp";
      divElemnt.appendChild(labelElement);
      divElemnt.appendChild(inputElement);
      modal_bory.appendChild(divElemnt);//coloca o input name no modal
      handlePhone({ target: inputElement });
      
      
      value_funcao();
      mostrarModal();
    }
  });


}
function confirmarModal() {
  var nome = document.getElementById("nome_usuario").value;
      var senha = document.getElementById("senha_usuario").value;
      var funcao = document.getElementById("funcao_usuario").value;
      var email = document.getElementById("email_usuario").value;
      var whazapp = document.getElementById("whazapp_usuario").value;
      $.ajax({
          url: '<?= base_url('public/user_modificar_update') ?>',
          type: "POST",
          dataType: "json", // Indicar que o retorno é em formato JSON
          data: {id: id_g , nome: nome, senha: senha, nivel: funcao, email: email, whazapp: whazapp },
          success: function (response) {
              if (!response.ok) {
                  //response.msg
                  for (const chave in response.msg) {
                      const valor = response.msg[chave];
                      alert_personalizado(chave, valor);
                  }
              }else{
                  alert_certo('Alteração','Usuário Modificado com sucesso.');
                  lista();
              }

          }
      });
 
  fecharModal();
}
lista_temp1 = "";
function value_funcao() {
  $.ajax({
    url: '<?= base_url('public/lista_nivel') ?>',
    type: "POST",
    dataType: "json", // Indicar que o retorno é em formato JSON
    success: function (response) {
      
        // Obter referência ao elemento select
        var funcao = document.getElementById("funcao_usuario");
        if (funcao !== null) {
          if (funcao != lista_temp1) {
        // Armazenar o valor da opção selecionada antes de limpar o select
        var valorSelecionadoAntes = funcao.value;

        // Limpar o select
        funcao.innerHTML = '';

        // Criar um novo elemento option
        var novoOption = document.createElement("option");

        // Definir o valor e texto do novo elemento option
        novoOption.value = '';
        novoOption.textContent = 'Função do novo usuario';

        // Adicionar o novo elemento option ao select
        funcao.appendChild(novoOption);

        response.lista.forEach(element => {



          // Criar um novo elemento option
          var novoOption = document.createElement("option");

          // Definir o valor e texto do novo elemento option
          novoOption.value = element;
          novoOption.textContent = element;

          // Adicionar o novo elemento option ao select
          funcao.appendChild(novoOption);
        });
        var opcoes = funcao.options;
        for (var i = 0; i < opcoes.length; i++) {
          if (opcoes[i].value === valorSelecionadoAntes) {
            opcoes[i].selected = true;
            break;
          }
        }


        lista_temp1 = funcao;
      }
    }}
  });
}
// Executar função ao abrir o site
document.addEventListener('DOMContentLoaded', value_funcao);


    function alert_certo(titulo, bory){
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
    // setInterval(value_funcao, 1000);
</script>