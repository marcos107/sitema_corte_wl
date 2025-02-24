<script>

lista();

    lista_atual = "";
    function lista() {
        var ativos = document.getElementById('checkbox_ativos').checked;
        var desativados = document.getElementById('checkbox_desativado').checked;
        $.ajax({
            url: '<?= base_url('public/nivel_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { ativos: ativos, desativados: desativados },
            success: function (response) {
                console.log(response);

                if (lista_atual != response.lista) {

                    $('#example1').DataTable().destroy();
                    var div = $('#minhaDiv');

                    div.load(location.href + ' #minhaDiv');
                    // Selecione o elemento <tbody> pelo seu ID
                    var lista = document.getElementById('lista');
                    // Substitua o conteúdo do elemento <tbody> com o novo HTML
                    lista.innerHTML = response.lista;
                    $(function () {
                        // Recria e configura a tabela DataTable com os novos dados.

                        $("#example1").DataTable({

                            "responsive": true, "lengthChange": false, "autoWidth": false,
                            "buttons": [],
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
                    lista_atual = response.lista;
                }

                // Atualiza lista_temp com a nova lista.





            }
        });

    }
    
    // setInterval(lista, 5000);

    function desativar(id) {
        $.ajax({
            url: '<?= base_url('public/nivel_lista_desativar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { id: id },
            success: function (response) {
                //console.log(response);
                lista();
            }
        });
    }

    function ativar(id) {

        $.ajax({
            url: '<?= base_url('public/nivel_lista_ativar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { id: id },
            success: function (response) {

                lista();
            }
        });
    }

    function modal_nivel(id = null) {
        $.ajax({
            url: '<?= base_url('public/nivel_modifica_modal') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { id: id },
            success: function (response) {

                if(id != null){
                    document.getElementById('modal').innerHTML = response.modal;
                    mostrarModal();
                   
                    lista();
                }else{
                    document.getElementById('inputs_body').innerHTML = response.conteudo;
                }
             

            }
        });
    }

    function confirmarModal() {
      var checkbox = document.getElementById('checkbox_todos');
      var relatorio = document.getElementById('checkbox_relatorio').checked;
      checkbox_processos = document.getElementById('checkbox_todos_processos').checked;

      var permissao = "";
      if (checkbox.checked) {
        permissao =  "all";
      } else {
        permissao = getCheckboxesNivel().join('-');
      }

      var processos = "";
      if (checkbox_processos) {
        processos =  "all";
      } else {
        processos = getCheckboxesProcessos().join('-');
      }

        var nivel = document.getElementById("nivel_novo").value;
        console.log(nivel);
        $.ajax({
            url: '<?= base_url('public/nivel_modificar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nivel: nivel ,permissao: permissao,relatorio: relatorio,processos: processos},
            success: function (response) {
                console.log(response);
                if (!response.ok) {
                    //response.msg

                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                } else {
                    alert_certo('Cadastrado', 'nivel modificado com sucesso.');
                    document.getElementById("nivel_novo").value = '';
                    fecharModal();
                    lista();
                }

            }
        });

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


    function cadastrar() {
      var checkbox = document.getElementById('checkbox_todos');
      var relatorio = document.getElementById('checkbox_relatorio').checked;
      checkbox_processos = document.getElementById('checkbox_todos_processos').checked;

      var permissao = "";
      if (checkbox.checked) {
        permissao =  "all";
      } else {
        permissao = getCheckboxesNivel().join('-');
      }

      var processos = "";
      if (checkbox_processos) {
        processos =  "all";
      } else {
        processos = getCheckboxesProcessos().join('-');
      }

      
        var nivel = document.getElementById("nivel_novo").value;
        console.log(nivel);
        $.ajax({
            url: '<?= base_url('public/nivel_cadastrar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nivel: nivel ,permissao: permissao,relatorio: relatorio,processos: processos},
            success: function (response) {
                console.log(response);
                if (!response.ok) {
                    //response.msg

                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                } else {
                    alert_certo('Cadastrado', 'nivel cadastrado com sucesso.');
                    document.getElementById("nivel_novo").value = '';
                    modal_nivel();
                    lista();
                }

            }
        });
    }

    function add() {
        $.ajax({
            url: '<?= base_url('public/nivel_cadastrar_modal') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                console.log(response);

                document.getElementById('div').innerHTML = response.modal;

                mostrarModal();
                lista();
            }
        });
    }



    function selecionar_todos() {
      

    }




    modal_nivel();



    


    
    function marcar_todos_nivel(checkbox_btn) {
    // Verifica se o checkbox está dentro da div com id 'cadastro1'
    console.log('1');
    var modal_ok = true;
    if (checkbox_btn.closest('#cadastro1')) {
      var modal_ok = false;
    }
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach(function(checkbox) {


// Verifica se o id do checkbox é 'nivel_checkbox'
if (checkbox.id == 'nivel_checkbox') {
  if(checkbox.closest('#cadastro1') && !modal_ok){
    if(checkbox_btn.checked){
      checkbox.disabled = true;
    }else{
      checkbox.disabled = false;
    }

  }else if(modal_ok){
    if(checkbox_btn.checked){
      checkbox.disabled = true;
    }else{
      checkbox.disabled = false;
    }
    
  }

}
});
    
}



function marcar_todos_processos(checkbox_btn) {
    // Verifica se o checkbox está dentro da div com id 'cadastro1'
    console.log('1');
    var modal_ok = true;
    if (checkbox_btn.closest('#cadastro1')) {
      var modal_ok = false;
    }
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach(function(checkbox) {


// Verifica se o id do checkbox é 'nivel_checkbox'
if (checkbox.id == 'permissao_checkbox') {
  if(checkbox.closest('#cadastro1') && !modal_ok){
    if(checkbox_btn.checked){
      checkbox.checked = false;
      checkbox.disabled = true;
    }else{
      checkbox.disabled = false;
    }

  }else if(modal_ok){
    if(checkbox_btn.checked){
      checkbox.checked = false;
      checkbox.disabled = true;
    }else{
      checkbox.disabled = false;
    }
    
  }

}
});
    
}



    function selecionar_todos_nivel() {


    var checkbox_btn = document.getElementById('checkbox_todos');
    // Seleciona todos os checkboxes na página
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    var modal = [];
    var cadastrar = [];
    
    modal_ok = false;
    checkboxes.forEach(function(checkbox) {


        // Verifica se o id do checkbox é 'nivel_checkbox'
        if (checkbox.id == 'nivel_checkbox') {
          if(checkbox.closest('#cadastro1')){
            if(checkbox.checked)
              cadastrar.push(checkbox.value);
          }else{
            modal_ok = true;
            if(checkbox.checked)
              modal.push(checkbox.value);
          }

        }
    });

    if(modal_ok){
      return modal;
    }
    return cadastrar;
    
    // Retorna o array de valores dos checkboxes com o maior z-index

}
    



    processos = "";

    function selecionar_processos_todos(){
    var checkbox = document.getElementById('checkbox_todos_processos');
      var select = document.getElementById('processos_novo');

      if (checkbox.checked) {
        select.disabled = true;
      } else {
        select.disabled = false;
      }
    }

    function getCheckboxesNivel() {
    // Seleciona todos os checkboxes na página
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    var modal = [];
    var cadastrar = [];
    var modal_ok = false;

    checkboxes.forEach(function(checkbox) {


        // Verifica se o id do checkbox é 'nivel_checkbox'
        if (checkbox.id == 'nivel_checkbox') {
          if(checkbox.closest('#cadastro1')){
            if(checkbox.checked)
              cadastrar.push(checkbox.value);
          }else{
            modal_ok = true;
            if(checkbox.checked)
              modal.push(checkbox.value);
          }

        }
    });

    if(modal_ok){
      return modal;
    }
    return cadastrar;
    
    // Retorna o array de valores dos checkboxes com o maior z-index

}

function getCheckboxesProcessos() {
    // Seleciona todos os checkboxes na página
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    var modal = [];
    var cadastrar = [];
    var modal_ok = false;

    checkboxes.forEach(function(checkbox) {


        // Verifica se o id do checkbox é 'nivel_checkbox'
        if (checkbox.id == 'permissao_checkbox') {
          if(checkbox.closest('#cadastro1')){
            if(checkbox.checked)
              cadastrar.push(checkbox.value);
          }else{
            modal_ok = true;
            if(checkbox.checked)
              modal.push(checkbox.value);
          }

        }
    });

    if(modal_ok){
      return modal;
    }
    return cadastrar;
    
    // Retorna o array de valores dos checkboxes com o maior z-index

}



</script>