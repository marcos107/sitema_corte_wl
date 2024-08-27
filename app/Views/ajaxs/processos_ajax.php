<script>
    document.getElementById('extencao_novo').setAttribute('multiple', 'multiple');
    document.getElementById('extencao_novo').setAttribute('size', '7');
    extensao();




    lista_temp = '';
    function lista() {
        var ativos = document.getElementById('checkbox_ativos').checked;
        var desativados = document.getElementById('checkbox_desativado').checked;
        $.ajax({
            url: '<?= base_url('public/processos') ?>',
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












    function extensao() {
        $.ajax({
            url: '<?= base_url('public/lista_filtro') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {

                // Remove a última vírgula (se houver) e cria um array a partir da string
                const opcoes = response.lista.replace(/,$/, '').split(',');

                // Seleciona o elemento select pelo ID
                const select = document.getElementById('extencao_novo');

                // Itera sobre o array de opções e adiciona cada uma como um option ao select
                opcoes.forEach(function (opcao) {
                    const option = document.createElement('option');
                    option.value = opcao.trim();  // Valor da option
                    option.text = opcao.trim();   // Texto visível na option
                    select.appendChild(option);   // Adiciona a option ao select
                });


            }
        });
    }


    document.getElementById('diretorio_novo').setAttribute('oninput', 'filtrarCampo()');
    document.getElementById('diretorio_novo').setAttribute('maxlength', '100');
    function filtrarCampo() {
        const input = document.getElementById('diretorio_novo');
        // Remove todos os caracteres que não sejam letras ou números
        let valorFiltrado = input.value.replace(/[^a-zA-Z0-9 _]/g, '');
        // Substitui espaços por _
        valorFiltrado = valorFiltrado.replace(/ /g, '_');
        // Converte tudo para maiúsculo
        input.value = valorFiltrado.toUpperCase();
    }



    function cadastrar() {

        let nome = document.getElementById('nome_processos_novo').value;
        let diretorio = document.getElementById('diretorio_novo').value;
        let select = document.getElementById('extencao_novo');
        let values = '';

        var selectedValues = [];
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                selectedValues.push(select.options[i].value);
            }
        }
        extencao = selectedValues.join('-');




        $.ajax({
            url: '<?= base_url('public/processos_cadastrar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nome: nome, diretorio: diretorio, extencao: extencao },
            success: function (response) {

                if (!response.ok) {
                    //response.msg

                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                } else {
                    lista();
                    alert_certo('Cadastrado', 'Prioridade cadastrado com sucesso.');

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

    function modal_modificar(id = null) {
        id = id.replace('modal_', '');
        id_g = id;
        $.ajax({
            url: '<?= base_url('public/processos_modifica_modal') ?>',
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
        let nome = document.getElementById('nome_processos_novo_modal').value;
        let diretorio = document.getElementById('diretorio_novo_modal').value;
        let select = document.getElementById('extencao_novo_modal');
        let values = '';

        var selectedValues = [];
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                selectedValues.push(select.options[i].value);
            }
        }
        extencao = selectedValues.join('-');




        $.ajax({
            url: '<?= base_url('public/processos_modificar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nome: nome, diretorio: diretorio, extencao: extencao },
            success: function (response) {

                if (!response.ok) {
                    //response.msg

                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                } else {
                    lista();
                    fecharModal();
                    alert_certo('Cadastrado', 'Processos cadastrado com sucesso.');

                }

            }
        });

    }


</script>