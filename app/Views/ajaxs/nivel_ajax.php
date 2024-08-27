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
      var select = document.getElementById('permissao_novo');
      var select_processos = document.getElementById('processos_novo');
      checkbox_processos = document.getElementById('checkbox_todos_processos').checked;
      var permissao = "";
      if (checkbox.checked) {
        permissao =  "all";
      } else {
        var selectedValues = [];
        for (var i = 0; i < select.options.length; i++) {
          if (select.options[i].selected) {
            selectedValues.push(select.options[i].value);
          }
        }
        permissao = selectedValues.join('-');
      }


      var processos = "";
      if (checkbox_processos) {
        processos =  "all";
      } else {
        var selectedValues = [];
        for (var i = 0; i < select_processos.options.length; i++) {
          if (select_processos.options[i].selected) {
            selectedValues.push(select_processos.options[i].value);
          }
        }
        processos = selectedValues.join('-');
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
      var select = document.getElementById('permissao_novo');
      var relatorio = document.getElementById('checkbox_relatorio').checked;
      var select_processos = document.getElementById('processos_novo');
      checkbox_processos = document.getElementById('checkbox_todos_processos').checked;
      var permissao = "";
      if (checkbox.checked) {
        permissao =  "all";
      } else {
        var selectedValues = [];
        for (var i = 0; i < select.options.length; i++) {
          if (select.options[i].selected) {
            selectedValues.push(select.options[i].value);
          }
        }
        permissao = selectedValues.join('-');
      }

      var processos = "";
      if (checkbox_processos) {
        processos =  "all";
      } else {
        var selectedValues = [];
        for (var i = 0; i < select_processos.options.length; i++) {
          if (select_processos.options[i].selected) {
            selectedValues.push(select_processos.options[i].value);
          }
        }
        processos = selectedValues.join('-');
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
      var checkbox = document.getElementById('checkbox_todos');
      var select = document.getElementById('permissao_novo');

      if (checkbox.checked) {
        select.disabled = true;
      } else {
        select.disabled = false;
      }
    }

    modal_nivel();
    

    



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
</script>