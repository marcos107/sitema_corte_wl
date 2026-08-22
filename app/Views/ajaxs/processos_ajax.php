<script>
    document.getElementById('extencao_novo').setAttribute('multiple', 'multiple');
    document.getElementById('extencao_novo').setAttribute('size', '7');
    extensao();




    lista_temp = '';
    function lista(forcarAtualizacao) {
        var ativos = document.getElementById('checkbox_ativos').checked;
        var desativados = document.getElementById('checkbox_desativado').checked;
        var forcar = !!forcarAtualizacao;
        $.ajax({
            url: '<?= base_url('public/processos') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { ativos: ativos, desativados: desativados },
            success: function (response) {
                if (forcar || response.lista != lista_temp) {
                    if ($.fn.DataTable.isDataTable('#example1')) {
                        $('#example1').DataTable().destroy();
                    }





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
    function filtrarCampoPorId(campoId) {
        const input = document.getElementById(campoId);
        if (!input) {
            return;
        }
        let valorFiltrado = input.value.replace(/[^a-zA-Z0-9 _]/g, '');
        valorFiltrado = valorFiltrado.replace(/ /g, '_');
        input.value = valorFiltrado.toUpperCase();
    }

    function filtrarCampo() {
        filtrarCampoPorId('diretorio_novo');
    }

    function filtrarCampoModal() {
        filtrarCampoPorId('diretorio_novo_modal');
    }



    function cadastrar() {

        let nome = document.getElementById('nome_processos_novo').value;
        let diretorio = document.getElementById('diretorio_novo').value;
        let select = document.getElementById('extencao_novo');
        let multivalorado = document.getElementById('multivalorado_novo').checked ;
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
            data: { nome: nome, diretorio: diretorio, extencao: extencao ,multivalorado: multivalorado},
            success: function (response) {

                if (!response.ok) {
                    //response.msg

                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                } else {
                    lista();
                    alert_certo('Cadastrado', 'Processo cadastrado com sucesso.');

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
        const possuiId = id !== null && id !== undefined && id !== '';
        const idNormalizado = possuiId ? String(id).replace('modal_', '') : null;

        if (possuiId) {
            id_g = idNormalizado;
        }

        $.ajax({
            url: '<?= base_url('public/processos_modifica_modal') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { id: idNormalizado },
            success: function (response) {
                if (possuiId) {
                    var modalTitulo = document.getElementById('modal_titulo');
                    var modalBory = document.getElementById('modal_bory');
                    var botaoConfirmar = document.getElementById('botao_confirmar_modal');

                    if (modalTitulo) {
                        modalTitulo.textContent = 'Modificar Processo';
                    }

                    if (modalBory) {
                        modalBory.innerHTML = response.conteudo || '';
                    }

                    if (botaoConfirmar) {
                        botaoConfirmar.innerHTML = 'Confirmar';
                        botaoConfirmar.setAttribute('onclick', 'confirmarModal()');
                    }

                    var diretorioModal = document.getElementById('diretorio_novo_modal');
                    if (diretorioModal) {
                        diretorioModal.setAttribute('oninput', 'filtrarCampoModal()');
                        diretorioModal.setAttribute('maxlength', '100');
                    }

                    var extensaoModal = document.getElementById('extencao_novo_modal');
                    if (extensaoModal) {
                        extensaoModal.setAttribute('multiple', 'multiple');
                        extensaoModal.setAttribute('size', '7');
                    }

                    var finalidadesDependenciaModal = document.getElementById('dependencia_finalidades_opcionais');
                    if (finalidadesDependenciaModal) {
                        finalidadesDependenciaModal.setAttribute('multiple', 'multiple');
                        finalidadesDependenciaModal.setAttribute('size', '5');
                    }

                    mostrarModal();
                } else {
                    var inputsBody = document.getElementById('inputs_body');
                    if (inputsBody) {
                        inputsBody.innerHTML = response.conteudo || '';
                    }
                }
            }
        });
    }




    function confirmarModal() {
        let nome = document.getElementById('nome_processos_novo_modal').value;
        let diretorio = document.getElementById('diretorio_novo_modal').value;
        let select = document.getElementById('extencao_novo_modal');
        let dependencia =  document.getElementById('processo_dependencia').value;
        let dependenciaObrigatoriaEl = document.getElementById('dependencia_obrigatoria');
        let dependenciaObrigatoria = (!dependenciaObrigatoriaEl || dependenciaObrigatoriaEl.checked) ? '1' : '0';
        let selectFinalidadesDependencia = document.getElementById('dependencia_finalidades_opcionais');
        let dependenciaFinalidades = '';
        let values = '';

        var selectedValues = [];
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                selectedValues.push(select.options[i].value);
            }
        }
        extencao = selectedValues.join('-');

        var selectedFinalidadesDependencia = [];
        if (selectFinalidadesDependencia) {
            for (var j = 0; j < selectFinalidadesDependencia.options.length; j++) {
                if (selectFinalidadesDependencia.options[j].selected) {
                    selectedFinalidadesDependencia.push(selectFinalidadesDependencia.options[j].value);
                }
            }
            dependenciaFinalidades = selectedFinalidadesDependencia.join('-');
        }



        $.ajax({
            url: '<?= base_url('public/processos_modificar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nome: nome, diretorio: diretorio, extencao: extencao , dependencia: dependencia, dependencia_obrigatoria: dependenciaObrigatoria, dependencia_finalidades_opcionais: dependenciaFinalidades},
            success: function (response) {

                if (!response.ok) {
                    const mensagens = response && response.msg ? response.msg : null;
                    let exibiuMensagem = false;
                    if (mensagens && typeof mensagens === 'object') {
                        for (const chave in mensagens) {
                            if (!Object.prototype.hasOwnProperty.call(mensagens, chave)) {
                                continue;
                            }
                            const valor = mensagens[chave];
                            alert_personalizado(chave, valor);
                            exibiuMensagem = true;
                        }
                    } else if (typeof mensagens === 'string' && mensagens.trim() !== '') {
                        alert_personalizado('Alteracao', mensagens);
                        exibiuMensagem = true;
                    }
                    if (!exibiuMensagem) {
                        alert_personalizado('Alteracao', 'Nenhum item foi modificado.');
                    }
                    return;
                } else {
                    fecharModal();
                    lista(true);
                    alert_certo('Atualizado', 'Processo atualizado com sucesso.');

                }

            }
        });

    }


</script>
