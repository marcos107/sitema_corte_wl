<script>
    var processos_select = [];
    var processo_nome = '';
    var tipo_input = '';
    var flowStep = 1;
    var arquivosSelecionados = [];

    function alert_certo(titulo, bory) {
        if (window.toastr) {
            toastr.success(bory, titulo);
            return;
        }

        if (window.Swal) {
            Swal.fire({ icon: 'success', title: titulo, text: bory });
            return;
        }

        alert(titulo + ': ' + bory);
    }

    function alert_personalizado(titulo, bory) {
        if (window.toastr) {
            toastr.error(bory, titulo, { timeOut: 13000, closeButton: true, progressBar: true });
            return;
        }

        if (window.Swal) {
            Swal.fire({ icon: 'error', title: titulo, html: bory });
            return;
        }

        alert(titulo + ': ' + bory);
    }

    function get_radio() {
        var radios = document.getElementsByName('processo');

        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return radios[i];
            }
        }

        return null;
    }

    function chaveArquivo(file) {
        return [file.name, file.size, file.lastModified, file.type].join('||');
    }

    function formatarTamanhoArquivo(bytes) {
        if (bytes < 1024) {
            return bytes + ' B';
        }

        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function sincronizarInputArquivos() {
        var fileInput = document.getElementById('desenhos_add');
        if (!fileInput || typeof DataTransfer === 'undefined') {
            return;
        }

        var dt = new DataTransfer();
        for (var i = 0; i < arquivosSelecionados.length; i++) {
            dt.items.add(arquivosSelecionados[i]);
        }

        fileInput.files = dt.files;
    }

    function adicionarArquivosNaSelecao(fileList) {
        if (!fileList || fileList.length === 0) {
            return;
        }

        var chaves = {};
        for (var i = 0; i < arquivosSelecionados.length; i++) {
            chaves[chaveArquivo(arquivosSelecionados[i])] = true;
        }

        for (var j = 0; j < fileList.length; j++) {
            var arquivo = fileList[j];
            var chave = chaveArquivo(arquivo);
            if (!chaves[chave]) {
                arquivosSelecionados.push(arquivo);
                chaves[chave] = true;
            }
        }

        sincronizarInputArquivos();
        atualizarListaArquivos();
    }

    function removerArquivoSelecionado(index) {
        if (index < 0 || index >= arquivosSelecionados.length) {
            return;
        }

        arquivosSelecionados.splice(index, 1);
        sincronizarInputArquivos();
        atualizarListaArquivos();
    }

    function limparArquivosSelecionados() {
        arquivosSelecionados = [];
        sincronizarInputArquivos();
    }

    function bindUploadInteractions() {
        var fileInput = document.getElementById('desenhos_add');
        var dropzone = document.getElementById('upload_dropzone');
        var chooseBtn = document.getElementById('upload_choose_btn');

        if (chooseBtn && fileInput) {
            chooseBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                adicionarArquivosNaSelecao(fileInput.files);
            });
        }

        if (dropzone && fileInput) {
            dropzone.addEventListener('click', function(event) {
                if (event.target && event.target.id === 'upload_choose_btn') {
                    return;
                }

                fileInput.click();
            });

            dropzone.addEventListener('dragover', function(event) {
                event.preventDefault();
                dropzone.classList.add('is-dragging');
            });

            dropzone.addEventListener('dragleave', function(event) {
                if (!dropzone.contains(event.relatedTarget)) {
                    dropzone.classList.remove('is-dragging');
                }
            });

            dropzone.addEventListener('drop', function(event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragging');

                if (!event.dataTransfer || !event.dataTransfer.files || event.dataTransfer.files.length === 0) {
                    return;
                }

                adicionarArquivosNaSelecao(event.dataTransfer.files);
            });
        }
    }

    function atualizarEtapaVisual() {
        var step1 = document.getElementById('step_process');
        var step2 = document.getElementById('step_upload');
        var chip1 = document.getElementById('step_chip_1');
        var chip2 = document.getElementById('step_chip_2');
        var flowTitle = document.getElementById('flow_title');
        var flowSubtitle = document.getElementById('flow_subtitle');
        var backBtn = document.getElementById('flow_back_btn');
        var primaryBtn = document.getElementById('flow_primary_btn');

        if (!step1 || !step2 || !chip1 || !chip2 || !flowTitle || !flowSubtitle || !backBtn || !primaryBtn) {
            return;
        }

        if (flowStep === 1) {
            step1.classList.remove('d-none');
            step2.classList.add('d-none');
            chip1.classList.add('is-active');
            chip2.classList.remove('is-active');
            flowTitle.textContent = 'Escolha o processo';
            flowSubtitle.textContent = 'Primeiro, selecione o processo de destino dos desenhos.';
            backBtn.disabled = true;
            primaryBtn.textContent = 'Continuar';
        } else {
            step1.classList.add('d-none');
            step2.classList.remove('d-none');
            chip1.classList.remove('is-active');
            chip2.classList.add('is-active');
            flowTitle.textContent = 'Envie os arquivos';
            flowSubtitle.textContent = 'Agora, adicione os arquivos para preparar os metadados.';
            backBtn.disabled = false;
            primaryBtn.textContent = 'Enviar e configurar';
        }
    }

    function atualizarResumoProcesso() {
        var processoLabel = document.getElementById('summary_processo');

        if (processoLabel) {
            processoLabel.textContent = processo_nome ? processo_nome : 'Nenhum processo selecionado';
        }
    }

    function atualizarResumoUpload() {
        var countLabel = document.getElementById('summary_count');
        var total = arquivosSelecionados.length;

        if (countLabel) {
            countLabel.textContent = total + ' arquivo(s)';
        }
    }

    function atualizarListaArquivos() {
        var filesList = document.getElementById('files_list');

        if (!filesList) {
            return;
        }

        filesList.innerHTML = '';

        if (arquivosSelecionados.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'wl-upload-empty';
            empty.textContent = 'Nenhum arquivo selecionado ainda.';
            filesList.appendChild(empty);
            atualizarResumoUpload();
            return;
        }

        for (var i = 0; i < arquivosSelecionados.length; i++) {
            var file = arquivosSelecionados[i];
            var item = document.createElement('div');
            item.className = 'wl-upload-item';

            var meta = document.createElement('div');
            meta.className = 'wl-upload-meta';

            var name = document.createElement('span');
            name.className = 'wl-upload-name';
            name.textContent = file.name;

            var size = document.createElement('small');
            size.className = 'text-muted';
            size.textContent = formatarTamanhoArquivo(file.size);

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'wl-upload-remove';
            removeBtn.title = 'Remover arquivo';
            removeBtn.setAttribute('aria-label', 'Remover arquivo ' + file.name);
            removeBtn.textContent = 'X';

            (function(fileIndex) {
                removeBtn.addEventListener('click', function() {
                    removerArquivoSelecionado(fileIndex);
                });
            })(i);

            meta.appendChild(name);
            meta.appendChild(size);
            item.appendChild(meta);
            item.appendChild(removeBtn);
            filesList.appendChild(item);
        }

        atualizarResumoUpload();
    }

    function anexarTabelaNoModal(modalBody, tabela) {
        if (!modalBody || !tabela) {
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'wl-modal-table-wrap';
        wrapper.appendChild(tabela);
        modalBody.appendChild(wrapper);
    }

    function sincronizarCartoesProcesso() {
        var radios = document.querySelectorAll('input[name="processo"]');
        radios.forEach(function(radio) {
            var card = radio.closest('.wl-process-card');
            if (!card) return;

            if (radio.checked) {
                card.classList.add('is-selected');
            } else {
                card.classList.remove('is-selected');
            }
        });
    }

    function getFiltroByNome(nome) {
        for (var i = 0; i < processos_select.length; i++) {
            if (processos_select[i].nome === nome) {
                var desenho = document.getElementById('desenhos_add');
                if (desenho) {
                    desenho.accept = processos_select[i].filtro;
                }
                return processos_select[i].filtro;
            }
        }

        return '';
    }

    function sincronizarProcessoSelecionado() {
        var selecionado = get_radio();
        if (!selecionado) {
            processo_nome = '';
            tipo_input = '';
            atualizarResumoProcesso();
            sincronizarCartoesProcesso();
            return;
        }

        processo_nome = selecionado.value;
        tipo_input = selecionado.dataset.input ? selecionado.dataset.input : ((selecionado.id || '').split('_')[1] || '');

        getFiltroByNome(processo_nome);
        atualizarResumoProcesso();
        sincronizarCartoesProcesso();
    }

    function renderProcessos() {
        var radioContainer = document.getElementById('processos_radio');
        if (!radioContainer) {
            return;
        }

        radioContainer.innerHTML = '';

        if (!processos_select || processos_select.length === 0) {
            var empty = document.createElement('div');
            empty.className = 'wl-upload-empty';
            empty.textContent = 'Nenhum processo encontrado.';
            radioContainer.appendChild(empty);
            return;
        }

        var hasChecked = false;

        processos_select.forEach(function(processo, index) {
            var id = 'processo_' + processo.input + '_' + index;

            var label = document.createElement('label');
            label.className = 'wl-process-card';

            var radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'processo';
            radio.id = id;
            radio.value = processo.nome;
            radio.dataset.input = processo.input;
            radio.dataset.filtro = processo.filtro;

            if (processo_nome) {
                radio.checked = (processo.nome === processo_nome);
            } else if (!hasChecked) {
                radio.checked = true;
                hasChecked = true;
            }

            radio.addEventListener('change', function() {
                sincronizarProcessoSelecionado();
            });

            var name = document.createElement('span');
            name.className = 'wl-process-name';
            name.textContent = processo.nome;

            label.appendChild(radio);
            label.appendChild(name);

            radioContainer.appendChild(label);
        });

        if (!get_radio()) {
            var firstOption = radioContainer.querySelector('input[name="processo"]');
            if (firstOption) {
                firstOption.checked = true;
            }
        }

        sincronizarProcessoSelecionado();
    }

    function processo_lista() {
        $.ajax({
            url: '<?= base_url('public/processos_lista') ?>',
            type: 'POST',
            dataType: 'json',
            data: { contexto_tela: 'desenho_adicionar' },
            async: false,
            success: function(response) {
                processos_select = response.lista || [];
                renderProcessos();
            }
        });
    }

    function inicio_tela() {
        flowStep = 1;
        limparArquivosSelecionados();
        atualizarListaArquivos();
        atualizarEtapaVisual();
        sincronizarProcessoSelecionado();
    }

    function irParaEtapa(etapa) {
        if (etapa === 1) {
            inicio_tela();
            return;
        }

        if (etapa === 2) {
            if (!get_radio()) {
                alert_personalizado('Desenho', 'Selecione um processo para continuar.');
                return;
            }

            sincronizarProcessoSelecionado();
            flowStep = 2;
            atualizarEtapaVisual();
            atualizarListaArquivos();
        }
    }

    function resetarFluxoUpload() {
        limparArquivosSelecionados();
        atualizarListaArquivos();
        flowStep = 1;
        atualizarEtapaVisual();
        processo_lista();
    }

    function adicionar() {
        if (flowStep === 1) {
            irParaEtapa(2);
            return;
        }

        var files = arquivosSelecionados;

        if (files.length > 0) {
            $.ajax({
                url: '<?= base_url('public/criar_pasta_temp') ?>',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.ok == 'true') {
                        for (var i = 0; i < files.length; i++) {
                            var file = files[i];

                            if (file) {
                                var formData = new FormData();
                                formData.append('file', file);

                                $.ajax({
                                    url: '<?= site_url('public/desenho_adicionar_temp') ?>',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: formData,
                                    processData: false,
                                    async: false,
                                    contentType: false,
                                    success: function(response) {
                                    },
                                    error: function() {
                                        alert_personalizado('Desenho', 'Erro ao enviar o arquivo.');
                                    }
                                });
                            }
                        }

                        if (tipo_input == 'mult') {
                            desenho_modal();
                        } else {
                            desenho_modal_ind();
                        }
                    } else {
                        alert_personalizado('Desenho', 'Erro ao criar pasta temporaria.');
                    }
                }
            });
        } else {
            alert_personalizado('Desenho', 'Selecione ao menos um arquivo antes de continuar.');
        }
    }

    bindUploadInteractions();
    processo_lista();
    atualizarEtapaVisual();
    atualizarListaArquivos();
    function desenho_modal_ind() {
        $.ajax({
            url: '<?= site_url('public/desenho_adicionar_modal') ?>',
            type: 'POST',
            dataType: 'json',

            data: {
                nome_processos: processo_nome
            },
            success: function(response) {
                desenhos = response.desenhos;
                lista_array = response;
                var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
                botao_confirmar_modal.innerHTML = "Confirmar";
                var modal_titulo = document.getElementById('modal_titulo');
                var modal_bory = document.getElementById('modal_bory');
                modal_bory.innerHTML = '';
                modal_titulo.textContent = "Adicionar desenho";
                tabel_bory = document.createElement("table");

                tabel_bory.setAttribute('border', '1');



                tr = document.createElement('tr');
                th = document.createElement('th');
                th.innerHTML = 'Nome';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Prioridade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Emepresa/Cliente';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empreendimento';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Finalidade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-01';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-02';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-03';
                tr.appendChild(th);
                tr.classList.add()
                tabel_bory.appendChild(tr);
                ////////////////////////////////////// começo 
                tr = document.createElement('tr');
                th = document.createElement('th');

                th.style.fontWeight = 'normal';
                th.innerHTML = '*';
                tr.appendChild(th);
                //selecte prioridade
                selectElement = document.createElement("select");
                selectElement.id = 'prioridade_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte empresa

                selectElement = document.createElement("select");
                selectElement.id = 'empresa_cliente_novo_todos';
                selectElement.addEventListener("change", function() {
                    var selectedValue = this.value; // Valor da opção selecionada
                    var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                    // Chame a função que deseja executar quando uma opção é selecionada
                    value_empreendimento_c(selectedValue, selectedIndex1, desenhos.length);

                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("empresa_cliente_novo_" + j)) {
                            document.getElementById("empresa_cliente_novo_" + j).selectedIndex = selectedIndex1;
                        }
                    }

                });
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                th.appendChild(selectElement);
                tr.appendChild(th);


                //coloca o input name no modal




                //selecte empreendimento
                selectElement = document.createElement("select");
                selectElement.id = 'empreendimento_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                var novoOption = document.createElement("option");
                selectElement.disabled = true;
                novoOption.value = '';
                novoOption.textContent = 'Empreendimento';
                selectElement.appendChild(novoOption);

                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte finalidade
                selectElement = document.createElement("select");
                selectElement.id = 'finalidade_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag1_novo_todos';
                selectElement.disabled = true;
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag1_botao_todos" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag2_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag2_botao_todos" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);


                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag3_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag3_botao_todos" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                tabel_bory.appendChild(tr);





                for (i = 0; i < desenhos.length; i++) {

                    tr = document.createElement('tr');
                    tr.id = "desenho_" + i;
                    if (i % 2 == 0) {
                        tr.classList.add('odd');
                    } else {
                        tr.classList.add('even');
                    }


                    //nome
                    th = document.createElement('th');
                    th.style.fontWeight = 'normal';
                    th.style.fontSize = '16px';
                    th.innerHTML = desenhos[i];


                    tr.appendChild(th);
                    if (i == 0) {
                        //     th = document.createElement('th');
                        //     th.colSpan = 8;
                        //     th.style.textAlign = 'center'; // opcional: alinhamento
                        //     th.textContent = 'Descrição do Projeto';
                        //     tr.appendChild(th);
                        // } else if (i == 1) {
                        // célula fundida
                        const td = document.createElement('td');
                        td.colSpan = 8;
                        td.rowSpan = desenhos.length;
                        td.style.position = 'relative'; // torna o td contêiner posicionado
                        td.style.padding = '0'; // remove qualquer padding interno

                        // cria o textarea
                        const textarea = document.createElement('textarea');
                        textarea.id = 'descricao_desenho';
                        textarea.placeholder = 'Descrição do projeto...';

                        // faz o textarea preencher exatamente TODO o td
                        textarea.style.position = 'absolute';
                        textarea.style.top = '0';
                        textarea.style.left = '0';
                        textarea.style.right = '0';
                        textarea.style.bottom = '0';
                        textarea.style.width = '100%'; // redundante, mas reforça
                        textarea.style.height = '100%'; // idem
                        textarea.style.boxSizing = 'border-box'; // inclui borda e padding na contagem
                        textarea.style.resize = 'none'; // opcional: desabilita arrastar
                        textarea.style.overflow = 'auto'; // rolagem interna quando necessário

                        td.appendChild(textarea);
                        tr.appendChild(td);
                    }
                    tabel_bory.appendChild(tr);










                }

                tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
                anexarTabelaNoModal(modal_bory, tabel_bory);
                selects();
                mostrarModal();

            },
            error: function() {
                alert_personalizado('Desenho', 'Erro ao enviar o arquivo.');

            }
        });

    }


    desenhos = [];
    lista_array = [];

    function desenho_modal() {
        $.ajax({
            url: '<?= site_url('public/desenho_adicionar_modal') ?>',
            type: 'POST',
            dataType: 'json',

            data: {
                nome_processos: processo_nome
            },
            success: function(response) {
                desenhos = response.desenhos;
                lista_array = response;
                var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
                botao_confirmar_modal.innerHTML = "Confirmar";
                var modal_titulo = document.getElementById('modal_titulo');
                var modal_bory = document.getElementById('modal_bory');
                modal_bory.innerHTML = '';
                modal_titulo.textContent = "Adicionar desenho";
                tabel_bory = document.createElement("table");

                tabel_bory.setAttribute('border', '1');



                tr = document.createElement('tr');
                th = document.createElement('th');
                th.innerHTML = 'Nome';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Prioridade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Emepresa/Cliente';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empreendimento';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Finalidade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-01';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-02';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-03';
                tr.appendChild(th);
                tr.classList.add()
                tabel_bory.appendChild(tr);
                ////////////////////////////////////// começo 
                tr = document.createElement('tr');
                th = document.createElement('th');

                th.style.fontWeight = 'normal';
                th.innerHTML = '*';
                tr.appendChild(th);
                //selecte prioridade
                selectElement = document.createElement("select");
                selectElement.id = 'prioridade_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte empresa

                selectElement = document.createElement("select");
                selectElement.id = 'empresa_cliente_novo_todos';
                selectElement.addEventListener("change", function() {
                    var selectedValue = this.value; // Valor da opção selecionada
                    var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                    // Chame a função que deseja executar quando uma opção é selecionada
                    value_empreendimento_c(selectedValue, selectedIndex1, desenhos.length);

                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("empresa_cliente_novo_" + j)) {
                            document.getElementById("empresa_cliente_novo_" + j).selectedIndex = selectedIndex1;
                        }
                    }

                });
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                th.appendChild(selectElement);
                tr.appendChild(th);


                //coloca o input name no modal




                //selecte empreendimento
                selectElement = document.createElement("select");
                selectElement.id = 'empreendimento_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                var novoOption = document.createElement("option");
                selectElement.disabled = true;
                novoOption.value = '';
                novoOption.textContent = 'Empreendimento';
                selectElement.appendChild(novoOption);

                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte finalidade
                selectElement = document.createElement("select");
                selectElement.id = 'finalidade_novo_todos';
                selectElement.classList.add("custom-select");
                selectElement.addEventListener("change", function() {
                    value_tags(true);
                    value_tags_c(true);
                });


                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th); //coloca o input name no modal



                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag1_novo_todos';
                selectElement.disabled = true;
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag1_botao_todos" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag2_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag2_botao_todos" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);


                tr.appendChild(th); //coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag3_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button id="tag3_botao_todos" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_todos\',\'finalidade_novo_todos\')">+</button>';

                th.appendChild(div);

                tr.appendChild(th); //coloca o input name no modal

                tabel_bory.appendChild(tr);





                for (i = 0; i < desenhos.length; i++) {

                    tr = document.createElement('tr');
                    tr.id = "desenho_" + i;
                    if (i % 2 == 0) {
                        tr.classList.add('odd');
                    } else {
                        tr.classList.add('even');
                    }


                    //nome
                    th = document.createElement('th');
                    th.style.fontWeight = 'normal';
                    th.style.fontSize = '16px';
                    th.innerHTML = desenhos[i];


                    tr.appendChild(th);








                    //selecte prioridade
                    selectElement = document.createElement("select");
                    selectElement.id = 'prioridade_novo_' + i;
                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');
                    th.appendChild(selectElement);
                    tr.appendChild(th); //coloca o input name no modal


                    //selecte empresa
                    selectElement = document.createElement("select");
                    selectElement.id = 'empresa_cliente_novo_' + i;
                    selectElement.classList.add("custom-select");

                    th = document.createElement('th');
                    th.appendChild(selectElement);
                    tr.appendChild(th);


                    //coloca o input name no modal




                    //selecte empreendimento
                    selectElement = document.createElement("select");
                    selectElement.id = 'empreendimento_novo_' + i;
                    selectElement.classList.add("custom-select");
                    selectElement.addEventListener("change", function() {
                        value_tags(true);
                        value_tags_c(true);
                    });

                    var novoOption = document.createElement("option");
                    selectElement.disabled = true;
                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';
                    selectElement.appendChild(novoOption);

                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th); //coloca o input name no modal



                    //selecte finalidade
                    selectElement = document.createElement("select");
                    selectElement.id = 'finalidade_novo_' + i;
                    selectElement.classList.add("custom-select");
                    selectElement.addEventListener("change", function() {
                        value_tags(true);
                        value_tags_c(true);
                    });


                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th); //coloca o input name no modal




                    th = document.createElement('th');
                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag1_novo_' + i;

                    selectElement.classList.add("custom-select");

                    div = document.createElement('div');
                    div.classList.add("container-mesma-linha");
                    div.appendChild(selectElement);
                    div.innerHTML += '<button id="tag1_botao_' + +i + '" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_' + i + '\',\'finalidade_novo_' + i + '\')">+</button>';

                    th.appendChild(div);


                    tr.appendChild(th); //coloca o input name no modal
                    th = document.createElement('th');
                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag2_novo_' + i;
                    selectElement.classList.add("custom-select");



                    div = document.createElement('div');
                    div.classList.add("container-mesma-linha");
                    div.appendChild(selectElement);
                    div.innerHTML += '<button id="tag2_botao_' + +i + '" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_' + i + '\',\'finalidade_novo_' + i + '\')">+</button>';

                    th.appendChild(div);


                    tr.appendChild(th); //coloca o input name no modal
                    th = document.createElement('th');
                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag3_novo_' + i;
                    selectElement.classList.add("custom-select");



                    div = document.createElement('div');
                    div.classList.add("container-mesma-linha");
                    div.appendChild(selectElement);
                    div.innerHTML += '<button id="tag3_botao_' + +i + '" name="cadastarar" type="button" class="btn btn-outline-primary" onclick="adicinar_subpasta(\'empreendimento_novo_' + i + '\',\'finalidade_novo_' + i + '\')">+</button>';

                    th.appendChild(div);


                    tr.appendChild(th); //coloca o input name no modal

                    tabel_bory.appendChild(tr);










                }

                tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
                anexarTabelaNoModal(modal_bory, tabel_bory);
                selects();
                mostrarModal();

            },
            error: function() {
                alert_personalizado('Desenho', 'Erro ao enviar o arquivo.');

            }
        });

    }

    function selects() {
        empreendimento_select();
        value_prioridade(true);
        value_tags(true);
        value_finalidade(true);
        value_empresa(true);

        value_tags_c(true);
        value_empresa_c(true);
        value_finalidade_c(true);
        value_prioridade_c(true);

    }

    function tag_ordem(selectedValue, selectedIndex, coluna, id) {
        if (coluna == '1' && selectedIndex == 0) {
            select = document.getElementById("tag1_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag1_botao_" + id).disabled = true;
            select = document.getElementById("tag2_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag2_botao_" + id).disabled = true;
            select = document.getElementById("tag3_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag3_botao_" + id).disabled = true;
        } else if (coluna == '2' && selectedIndex == 0) {
            select = document.getElementById("tag3_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            document.getElementById("tag3_botao_" + id).disabled = true;
        }

        console.log("A");
        if (document.getElementById("finalidade_novo_" + id).selectedIndex != 0 && document.getElementById("empreendimento_novo_" + id).selectedIndex != 0) {
            select = document.getElementById("tag1_novo_" + id);
            select.disabled = false;
            select.options.disabled = false;
            document.getElementById("tag1_botao_" + id).disabled = false;
        }

        if (coluna == '1' && selectedIndex != 0) {
            select = document.getElementById("tag2_novo_" + id);
            select.disabled = false;
            select.options.disabled = false;
            document.getElementById("tag2_botao_" + id).disabled = false;
        }

        if (coluna == '2' && selectedIndex != 0) {
            select = document.getElementById("tag3_novo_" + id);
            select.disabled = false;
            document.getElementById("tag3_botao_" + id).disabled = false;
        }
        select3 = document.getElementById("tag3_novo_" + id);

        select2 = document.getElementById("tag2_novo_" + id);

        select1 = document.getElementById("tag1_novo_" + id);
        for (let index = 0; index < select1.length; index++) {
            select2.options[index].disabled = false;
            select3.options[index].disabled = false;
            select1.options[index].disabled = false;
        }
        if (select1.selectedIndex != 0) {
            select2.options[select1.selectedIndex].disabled = true;
            select3.options[select1.selectedIndex].disabled = true;
        }
        if (select2.selectedIndex != 0) {
            select1.options[select2.selectedIndex].disabled = true;
            select3.options[select2.selectedIndex].disabled = true;
        }
        if (select3.selectedIndex != 0) {
            select1.options[select3.selectedIndex].disabled = true;
            select2.options[select3.selectedIndex].disabled = true;
        }



    }












    function inverterCor(hex) {
        // Verificar se a cor é válida (começa com # seguido por 6 caracteres hexadecimais)
        var regex = /^#[0-9A-F]{6}$/i;
        if (!regex.test(hex)) {
            throw new Error("Cor inválida. Use notação hexadecimal de 6 dígitos, começando com '#'.");
        }

        // Extrair os componentes de cor
        var red = parseInt(hex.substr(1, 2), 16);
        var green = parseInt(hex.substr(3, 2), 16);
        var blue = parseInt(hex.substr(5, 2), 16);

        // Inverter os componentes de cor
        red = 255 - red;
        green = 255 - green;
        blue = 255 - blue;

        // Converter os valores invertidos de volta para notação hexadecimal
        var invertedHex = "#" + ((1 << 24) | (red << 16) | (green << 8) | blue).toString(16).slice(1);

        return invertedHex;
    }



    lista_temp2 = "";

    function value_prioridade(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/prioridade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                if (document.getElementById("prioridade_novo_0") != null && (response.toString() != lista_temp2 || efeturar)) {
                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("prioridade_novo_" + j)) {


                            // Obter referência ao elemento select
                            var funcao = document.getElementById("prioridade_novo_" + j);
                            // Armazenar o valor da opção selecionada antes de limpar o select
                            var valorSelecionadoAntes = funcao.value;

                            // Limpar o select
                            funcao.innerHTML = '';

                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = '';
                            novoOption.textContent = 'Prioridade';

                            // Adicionar o novo elemento option ao select
                            funcao.appendChild(novoOption);

                            response.lista.forEach(element => {



                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = element.prioridade;
                                novoOption.textContent = element.prioridade;
                                novoOption.style.backgroundColor = element.cor;
                                novoOption.style.color = inverterCor(element.cor);
                                funcao.appendChild(novoOption);
                            });
                            var opcoes = funcao.options;
                            for (var i = 0; i < opcoes.length; i++) {
                                if (opcoes[i].value === valorSelecionadoAntes) {
                                    opcoes[i].selected = true;
                                    break;
                                }
                            }

                        }
                    }
                    lista_temp2 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_prioridade);

    // Repetir função a cada segundo 
    //setInterval(value_prioridade, 15000);


    lista_temp3 = "";

    function value_tags(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenho_tag_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {

                if (document.getElementById("tag1_novo_0") != null && (response.toString() != lista_temp3 || efeturar)) {
                    for (let j = 0; j < desenhos.length; j++) {
                        valorSelecionadoAntes = '';
                        for (let h = 1; h < 4; h++) {
                            console.log(h);
                            if (document.getElementById("tag" + h + "_novo_" + j)) {
                                // Obter referência ao elemento select
                                var funcao = document.getElementById("tag" + h + "_novo_" + j);
                                // Armazenar o valor da opção selecionada antes de limpar o select

                                if (valorSelecionadoAntes == '') {
                                    funcao.disabled = true;
                                } // Limpar o select
                                valorSelecionadoAntes = funcao.value;
                                funcao.innerHTML = '';

                                funcao.addEventListener("change", function() {
                                    var selectedValue = this.value; // Valor da opção selecionada
                                    var selectedIndex = this.selectedIndex; // Índice da opção selecionada

                                    // Chame a função que deseja executar quando uma opção é selecionada
                                    tag_ordem(selectedValue, selectedIndex, h, j);
                                });

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = '';
                                novoOption.textContent = 'Subpasta';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);

                                let empreendimento = document.getElementById("empreendimento_novo_" + j).value;
                                let finalidade = document.getElementById("finalidade_novo_" + j).value;

                                response.tags?.[empreendimento]?.[finalidade]?.forEach(element => {


                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element;
                                    novoOption.textContent = element;

                                    funcao.appendChild(novoOption);
                                });
                                var opcoes = funcao.options;
                                for (var i = 0; i < opcoes.length; i++) {
                                    if (opcoes[i].value === valorSelecionadoAntes) {
                                        opcoes[i].selected = true;
                                        break;
                                    }
                                }

                            }
                        }
                        tag_ordem(document.getElementById("tag1_novo_" + j).value, document.getElementById("tag1_novo_" + j).selectedIndex, 1, j);


                    }
                    lista_temp3 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_tags);

    // Repetir função a cada segundo 
    //setInterval(value_tags, 15000);

    var lista_temp4 = '';

    function value_finalidade(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/finalidade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                if (document.getElementById("finalidade_novo_0") != null || efeturar) {
                    if (response.toString() != lista_temp4 || efeturar) {

                        lista_temp4 = response.toString();
                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("finalidade_novo_" + j)) {


                                // Obter referência ao elemento select
                                var funcao = document.getElementById("finalidade_novo_" + j);
                                // Armazenar o valor da opção selecionada antes de limpar o select
                                var valorSelecionadoAntes = funcao.value;

                                // Limpar o select
                                funcao.innerHTML = '';

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = '';
                                novoOption.textContent = 'Finalidade';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);

                                response.lista.forEach(element => {



                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element.finalidade;
                                    novoOption.textContent = element.finalidade;
                                    funcao.appendChild(novoOption);
                                });
                                var opcoes = funcao.options;
                                for (var i = 0; i < opcoes.length; i++) {
                                    if (opcoes[i].value === valorSelecionadoAntes) {
                                        opcoes[i].selected = true;
                                        break;
                                    }
                                }

                            }
                        }

                    }
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_finalidade);

    // Repetir função a cada segundo 
    //setInterval(value_finalidade, 15000);

    lista_temp5 = "";

    function getSelectedOptionText(selectId) {
        const select = document.getElementById(selectId);
        if (!select || select.selectedIndex < 0 || select.value === '') {
            return '';
        }

        const option = select.options[select.selectedIndex];
        return option ? option.textContent : '';
    }

    function value_empresa(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/empresas_lista') ?>',
            type: "GET",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                if (document.getElementById("empresa_cliente_novo_0") != null && (response.toString() != lista_temp5 || efeturar)) {

                    for (let j = 0; j < desenhos.length; j++) {

                        if (document.getElementById("empresa_cliente_novo_" + j)) {

                            // Obter referência ao elemento select
                            var funcao = document.getElementById("empresa_cliente_novo_" + j);
                            // Armazenar o valor da opção selecionada antes de limpar o select
                            var valorSelecionadoAntes = funcao.value;

                            // Limpar o select
                            funcao.innerHTML = '';

                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = '';
                            novoOption.textContent = 'Empresa';

                            // Adicionar o novo elemento option ao select
                            funcao.appendChild(novoOption);
                            funcao.onchange = function() {
                                var selectedValue = this.value;
                                var selectedIndex = this.selectedIndex; // Índice da opção selecionada

                                // Chame a função que deseja executar quando uma opção é selecionada
                                value_empreendimento(selectedValue, selectedIndex, j);
                            };
                            response.lista.forEach(element => {



                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = element.id;
                                novoOption.textContent = element.nome;
                                funcao.appendChild(novoOption);
                            });
                            var opcoes = funcao.options;
                            for (var i = 0; i < opcoes.length; i++) {
                                if (opcoes[i].value === valorSelecionadoAntes) {
                                    opcoes[i].selected = true;
                                    break;
                                }
                            }

                        }
                    }
                    lista_temp5 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_empresa);

    // Repetir função a cada segundo 
    //setInterval(value_empresa, 15000);
    function value_empreendimento_lista(selectedValue, selectedIndex, id, response) {

        $.ajax({
            url: '<?= base_url('public/empreendimentos_lista') ?>',
            type: "GET",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
                empresaId: selectedValue
            },
            success: function(response) {

                if (document.getElementById("empreendimento_novo_0") != null) {

                    var funcao = document.getElementById("empreendimento_novo_" + id);
                    if (selectedIndex == 0) {
                        funcao.disabled = true;
                    } else {
                        funcao.disabled = false;
                    }



                    // Obter referência ao elemento select

                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option

                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);
                    var novoOption = document.createElement("option");

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.id;
                        novoOption.textContent = element.nome;
                        novoOption.dataset.nome = element.nome;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp6 = response.toString();
                }

            }
        });

    }

    function value_empreendimento(selectedValue, selectedIndex, id) {
        $.ajax({
            url: '<?= base_url('public/empreendimentos_lista') ?>',
            type: "GET",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
                empresaId: selectedValue
            },
            success: function(response) {

                if (document.getElementById("empreendimento_novo_0") != null) {
                    if (document.getElementById("empreendimento_novo_" + id)) {
                        var funcao = document.getElementById("empreendimento_novo_" + id);
                        if (selectedIndex == 0) {
                            funcao.disabled = true;
                        } else {
                            funcao.disabled = false;
                        }



                        // Obter referência ao elemento select

                        // Armazenar o valor da opção selecionada antes de limpar o select
                        var valorSelecionadoAntes = funcao.value;

                        // Limpar o select
                        funcao.innerHTML = '';

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option

                        novoOption.value = '';
                        novoOption.textContent = 'Empreendimento';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        var novoOption = document.createElement("option");

                        response.lista.forEach(element => {



                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element.id;
                            novoOption.textContent = element.nome;
                            novoOption.dataset.nome = element.nome;
                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }
                    }

                    lista_temp6 = response.toString();
                }

            }
        });

    }

    function confirmarModal() {
        if (tipo_input == 'mult') {

            desenhos_enviar = [];
            i = 0;
            for (let j = 0; j < desenhos.length; j++) {
                if (desenhos[j] != null) {
                    data = {
                        empresa_id: document.getElementById("empresa_cliente_novo_" + j).value,
                        empresa: getSelectedOptionText("empresa_cliente_novo_" + j),
                        empreendimento_id: document.getElementById("empreendimento_novo_" + j).value,
                        empreendimento: getSelectedOptionText("empreendimento_novo_" + j),
                        finalidade: document.getElementById("finalidade_novo_" + j).value,
                        prioridade: document.getElementById("prioridade_novo_" + j).value,
                        tag1: document.getElementById("tag1_novo_" + j).value,
                        tag2: document.getElementById("tag2_novo_" + j).value,
                        tag3: document.getElementById("tag3_novo_" + j).value,
                        desenho: desenhos[j]

                    };
                    desenhos_enviar[i] = data;
                    i++;
                }
            }
            $.ajax({
                url: '<?= base_url('public/desenhos_add') ?>',
                type: "POST",
                dataType: "json", // Indicar que o retorno é em formato JSON
                data: {
                    desenhos: desenhos_enviar,
                    nome_processos: processo_nome
                },
                success: function(response) {

                    ok1 = true;
                    cont = 0;
                    cont_rep = 0;
                    mgs_final = [];
                    var index = [];
                    var mgs = response.msg;
                    var ok = response.ok;
                    for (const chave in mgs) {
                        cont_rep = 0;
                        cont1 = 0;

                        const valor = mgs[chave];
                        if (valor != null) {
                            for (const chave1 in mgs) {
                                if (mgs[chave1] == valor && chave1 != chave) {
                                    mgs[chave1] = null;
                                    ok[cont1] = null;
                                    cont_rep++;
                                }
                                cont1++;
                            }

                            if (cont_rep != 0) {
                                mgs_final["O core em " + (cont_rep + 1) + " desenhos"] = valor;
                                index[chave] = cont;
                            } else {
                                mgs_final[chave] = valor;
                                index[chave] = cont;
                            }
                        }



                        cont++;

                    }

                    ok = ok.filter(item => item !== null);

                    ok = Array.from(ok);


                    cont = 0;
                    cont_certo = 0;
                    for (const chave in mgs_final) {
                        if (ok[cont]) {
                            index1 = 0;
                            for (let j = 0; j < desenhos.length; j++) {
                                if (desenhos[j] == chave) {
                                    index1 = j;
                                    break;
                                }
                            }
                            desenhos[index1] = null;

                            var node = document.getElementById("desenho_" + index1);
                            if (node.parentNode) {
                                node.parentNode.removeChild(node);
                            }
                            cont_certo++;

                        } else {
                            const valor = mgs_final[chave];
                            alert_personalizado(chave, valor);

                            ok1 = false;
                        }

                        cont++;
                    }
                    if (cont_certo != 0) {
                        alert_certo("Desenhos adicionados", "Ao total " + cont_certo + " desenhos foram adicionados.");
                    }
                    if (ok1) {
                        fecharModal();
                        resetarFluxoUpload();

                    }
                }
            });
            return;
        } else {
   
            data = {
                empresa_id: document.getElementById("empresa_cliente_novo_todos").value,
                empresa: getSelectedOptionText("empresa_cliente_novo_todos"),
                empreendimento_id: document.getElementById("empreendimento_novo_todos").value,
                empreendimento: getSelectedOptionText("empreendimento_novo_todos"),
                finalidade: document.getElementById("finalidade_novo_todos").value,
                prioridade: document.getElementById("prioridade_novo_todos").value,
                tag1: document.getElementById("tag1_novo_todos").value,
                tag2: document.getElementById("tag2_novo_todos").value,
                tag3: document.getElementById("tag3_novo_todos").value,
                desenho: desenhos,
                descricao: document.getElementById("descricao_desenho").value

            };
            desenhos_enviar = data;


       
        $.ajax({
            url: '<?= base_url('public/desenhos_add_uni') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
                desenhos: desenhos_enviar,
                nome_processos: processo_nome
            },
            success: function(response) {

                ok1 = true;
                cont = 0;
                cont_rep = 0;
                mgs_final = [];
                var index = [];
                var mgs = response.msg;
                var ok = response.ok;
                for (const chave in mgs) {
                    cont_rep = 0;
                    cont1 = 0;

                    const valor = mgs[chave];
                    if (valor != null) {
                        for (const chave1 in mgs) {
                            if (mgs[chave1] == valor && chave1 != chave) {
                                mgs[chave1] = null;
                                ok[cont1] = null;
                                cont_rep++;
                            }
                            cont1++;
                        }

                        if (cont_rep != 0) {
                            mgs_final["O core em " + (cont_rep + 1) + " desenhos"] = valor;
                            index[chave] = cont;
                        } else {
                            mgs_final[chave] = valor;
                            index[chave] = cont;
                        }
                    }



                    cont++;

                }

                ok = ok.filter(item => item !== null);

                ok = Array.from(ok);


                cont = 0;
                cont_certo = 0;
                for (const chave in mgs_final) {
                    if (ok[cont]) {
                        index1 = 0;
                        for (let j = 0; j < desenhos.length; j++) {
                            if (desenhos[j] == chave) {
                                index1 = j;
                                break;
                            }
                        }
                        desenhos[index1] = null;

                        var node = document.getElementById("desenho_" + index1);
                        if (node.parentNode) {
                            node.parentNode.removeChild(node);
                        }
                        cont_certo++;

                    } else {
                        const valor = mgs_final[chave];
                        alert_personalizado(chave, valor);

                        ok1 = false;
                    }

                    cont++;
                }
                if (cont_certo != 0) {
                    alert_certo("Desenhos adicionados", "Ao total " + cont_certo + " desenhos foram adicionados.");
                }
                if (ok1) {
                    fecharModal();
                    resetarFluxoUpload();

                }
            }
        });
 }
    }
    



    //lista cabeçario




    lista_temp_c5 = "";

    function value_empresa_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/empresas_lista') ?>',
            type: "GET",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                if (document.getElementById("empresa_cliente_novo_todos") != null && (response.toString() != lista_temp_c5 || efeturar)) {





                    // Obter referência ao elemento select
                    var funcao = document.getElementById("empresa_cliente_novo_todos");
                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Empresa';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);


                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.id;
                        novoOption.textContent = element.nome;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp_c5 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_empresa_c);

    // Repetir função a cada segundo 
    //setInterval(value_empresa_c, 15000);






    function value_empreendimento_c(selectedValue, selectedIndex, index) {

        $.ajax({
            url: '<?= base_url('public/empreendimentos_lista') ?>',
            type: "GET",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: {
                empresaId: selectedValue
            },
            success: function(response) {

                if (document.getElementById("empreendimento_novo_todos") != null) {

                    var funcao = document.getElementById("empreendimento_novo_todos");
                    if (selectedIndex == 0) {
                        funcao.disabled = true;
                    } else {
                        funcao.disabled = false;
                    }

                    funcao.onchange = function() {
                        var selectedValue1 = this.value; // Valor da opção selecionada
                        var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                        // Chame a função que deseja executar quando uma opção é selecionada

                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("empresa_cliente_novo_" + j)) {
                                if (document.getElementById("empresa_cliente_novo_" + j).value == selectedValue) {
                                    document.getElementById("empreendimento_novo_" + j).value = selectedValue1;
                                }
                            }
                        }

                    };

                    // Obter referência ao elemento select

                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option

                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);
                    var novoOption = document.createElement("option");

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.id;
                        novoOption.textContent = element.nome;
                        novoOption.dataset.nome = element.nome;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }
                    var opcoes1 = opcoes;



                    for (let id = 0; id < index; id++) {
                        if (document.getElementById("empreendimento_novo_0") != null) {
                            if (document.getElementById("empreendimento_novo_" + id) && document.getElementById("empresa_cliente_novo_" + id).value == selectedValue) {
                                var funcao = document.getElementById("empreendimento_novo_" + id);
                                if (selectedIndex == 0) {
                                    funcao.disabled = true;
                                } else {
                                    funcao.disabled = false;
                                }



                                // Obter referência ao elemento select

                                // Armazenar o valor da opção selecionada antes de limpar o select
                                var valorSelecionadoAntes = funcao.value;

                                // Limpar o select
                                funcao.innerHTML = '';

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option

                                novoOption.value = '';
                                novoOption.textContent = 'Empreendimento';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);
                                var novoOption = document.createElement("option");

                                response.lista.forEach(element => {



                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element.id;
                                    novoOption.textContent = element.nome;
                                    novoOption.dataset.nome = element.nome;
                                    funcao.appendChild(novoOption);
                                });
                            }
                            lista_temp6 = response.toString();
                        }
                    }
                }
            }
        });

    }




    var lista_temp_c4 = '';

    function value_finalidade_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/finalidade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                if (document.getElementById("finalidade_novo_todos") != null || efeturar) {
                    if (response.toString() != lista_temp_c4 || efeturar) {

                        lista_temp_c4 = response.toString();




                        // Obter referência ao elemento select
                        var funcao = document.getElementById("finalidade_novo_todos");
                        // Armazenar o valor da opção selecionada antes de limpar o select
                        var valorSelecionadoAntes = funcao.value;

                        // Limpar o select
                        funcao.innerHTML = '';

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = '';
                        novoOption.textContent = 'Finalidade';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        funcao.addEventListener("change", function() {
                            var selectedValue = this.value; // Valor da opção selecionada
                            var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                            // Chame a função que deseja executar quando uma opção é selecionada

                            for (let j = 0; j < desenhos.length; j++) {
                                if (document.getElementById("finalidade_novo_" + j)) {
                                    document.getElementById("finalidade_novo_" + j).selectedIndex = selectedIndex1;
                                }
                            }

                        });
                        response.lista.forEach(element => {



                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element.finalidade;
                            novoOption.textContent = element.finalidade;
                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }



                    }
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_finalidade_c);

    // Repetir função a cada segundo 
    //setInterval(value_finalidade_c, 15000);

    lista_temp_c2 = "";

    function value_prioridade_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/prioridade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                if (document.getElementById("prioridade_novo_todos") != null && (response.toString() != lista_temp_c2 || efeturar)) {




                    // Obter referência ao elemento select
                    var funcao = document.getElementById("prioridade_novo_todos");

                    funcao.addEventListener("change", function() {
                        var selectedValue = this.value; // Valor da opção selecionada
                        var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                        // Chame a função que deseja executar quando uma opção é selecionada

                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("prioridade_novo_" + j)) {
                                document.getElementById("prioridade_novo_" + j).selectedIndex = selectedIndex1;
                            }

                        }

                    });










                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Prioridade';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.prioridade;
                        novoOption.textContent = element.prioridade;
                        novoOption.style.backgroundColor = element.cor;
                        novoOption.style.color = inverterCor(element.cor);
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp_c2 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_prioridade_c);

    // Repetir função a cada segundo 
    //setInterval(value_prioridade_c, 15000);


    lista_temp_c3 = "";

    function value_tags_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenho_tag_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {

                if (document.getElementById("tag1_novo_0") != null && (response.toString() != lista_temp_c3 || efeturar)|| efeturar) {

                    valorSelecionadoAntes = '';
                    for (let h = 1; h < 4; h++) {

                        // Obter referência ao elemento selecttag2_novo_todos
                        var funcao = document.getElementById("tag" + h + "_novo_todos");
                        // Armazenar o valor da opção selecionada antes de limpar o select


                        if (valorSelecionadoAntes == '') {
                            funcao.disabled = true;
                            document.getElementById("tag" + h + "_botao_todos").disabled = true;
                        }

                        if (document.getElementById("empreendimento_novo_todos").selectedIndex > 0 && document.getElementById("finalidade_novo_todos").selectedIndex > 0) {
                            select = document.getElementById("tag1_novo_todos");
                            select.disabled = false;
                            select.options.disabled = false;
                            document.getElementById("tag1_botao_todos").disabled = false;
                        }

                        // Limpar o select
                        valorSelecionadoAntes = funcao.value;
                        funcao.innerHTML = '';
                        funcao.addEventListener("change", function() {
                            var selectedValue = this.value; // Valor da opção selecionada
                            var selectedIndex = this.selectedIndex; // Índice da opção selecionada
                            tag_ordem(selectedValue, selectedIndex, h, 'todos');
                            // Chame a função que deseja executar quando uma opção é selecionada
                            for (let j = 0; j < desenhos.length; j++) {
                                if (document.getElementById("tag" + h + "_novo_" + j)) {
                                    var selectElement_todos = document.getElementById("tag" + h + "_botao_todos");
                                    var selectElement = document.getElementById("tag" + h + "_novo_" + j);
                                    if (selectElement && selectElement.querySelector('option[value="' + selectElement_todos.value + '"]')) {
                                        selectElement.value = selectedValue;
                                        tag_ordem(selectedValue, selectedIndex, h, j);
                                    }

                                }
                            }
                        });

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = '';
                        novoOption.textContent = 'Subpasta';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        let empreendimento = document.getElementById("empreendimento_novo_todos").value;
                        let finalidade = document.getElementById("finalidade_novo_todos").value;

                        response.tags?.[empreendimento]?.[finalidade]?.forEach(element => {


                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element;
                            novoOption.textContent = element;

                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }
                    }


                    lista_temp_c3 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_tags_c);

    // Repetir função a cada segundo
    //setInterval(value_tags_c, 15000);

    modal_bory_geral = '';

    function adicinar_subpasta(empreendimento, finalidade) {
        //Remove o prefixo 'modal_' do ID para obter o ID real





        //Modificar o conteúdo do modal de acordo com a resposta do servidor

        //Altera o texto do botão de confirmação no modal
        var botao_confirmar_modal = document.getElementById('botao_confirmar_modal_cadastrar');
        botao_confirmar_modal.innerHTML = "Confirmar";

        //Obtém referências aos elementos do modal
        var modal_titulo = document.getElementById('modal_cadastrar_titulo');
        var modal_bory = document.getElementById('modal_cadastrar_bory');
        modal_bory_geral = modal_bory.innerHTML;
        //Define o título do modal como "Modificar a tag: Nome da Tag"
        modal_titulo.textContent = "Adicionar Subpasta";

        // Limpa o conteúdo do modal_body
        modal_bory.innerHTML = '';



        // Cria e configura o segundo grupo de form para "Empreendimento"
        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        labelElement = document.createElement("label");
        labelElement.textContent = "Empreendimento";

        inputElement = document.createElement("select");
        inputElement.id = 'empreendimento_tag_novo';
        inputElement.classList.add("form-control");
        inputElement.disabled = true;

        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);

        // Cria e configura o terceiro grupo de form para "Finalidade"
        divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        labelElement = document.createElement("label");
        labelElement.textContent = "Finalidade";

        inputElement = document.createElement("select");
        inputElement.id = 'finalidade_tag_novo';
        inputElement.classList.add("form-control");
        inputElement.disabled = true;

        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);

        // Cria e configura o primeiro grupo de form para "Subpasta"
        var divElemnt = document.createElement("div");
        divElemnt.classList.add("form-group");

        var labelElement = document.createElement("label");
        labelElement.textContent = "Subpasta";

        var inputElement = document.createElement("input");
        inputElement.type = 'text';
        inputElement.id = 'nome_tag_novo';
        inputElement.classList.add("form-control");


        // Adiciona um evento de input ao elemento para limitar o comprimento do valor
        inputElement.addEventListener("input", function() {
            var input = this;
            var maxLength = 17;
            input.value = input.value.slice(0, maxLength); // Trunca o valor para o tamanho máximo
        });

        // Adiciona os elementos ao divElemnt e depois ao modal_body
        divElemnt.appendChild(labelElement);
        divElemnt.appendChild(inputElement);
        modal_bory.appendChild(divElemnt);

        empreendimento_select(document.getElementById(empreendimento).value);
        finalidade_select(document.getElementById(finalidade).value);



        //Exibe o modal
        mostrarModal("modal_cadastrar");





    }


    function finalidade_select(id = null) {
        $.ajax({
            url: '<?= base_url('public/finalidade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                // Limpa as opções atuais do select
                $('#finalidade_tag_novo').empty();

                // Adiciona uma opção padrão
                $('#finalidade_tag_novo').append('<option value="">Finalidade</option>');

                // Itera sobre o array de resposta e adiciona as opções ao select
                $.each(response.lista, function(index, item) {
                    $('#finalidade_tag_novo').append('<option value="' + item.finalidade + '">' + item.finalidade + '</option>');
                });
                if (id != null)
                    document.getElementById("finalidade_tag_novo").value = id;
            },
            error: function(xhr, status, error) {
                console.error("Ocorreu um erro ao carregar os dados: ", error);
            }
        });

    }

    function empreendimento_select(id = null) {
        $.ajax({
            url: '<?= base_url('public/empreendimento_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function(response) {
                // Limpa as opções atuais do select
                $('#empreendimento_tag_novo').empty();

                // Adiciona uma opção padrão
                $('#empreendimento_tag_novo').append('<option value="">Empreendimento</option>');

                // Itera sobre o array de resposta e adiciona as opções ao select
                $.each(response.lista, function(index, item) {
                    $('#empreendimento_tag_novo').append('<option value="' + item.empreendimento + '">' + item.empreendimento + '</option>');
                });
                if (id != null)
                    document.getElementById("empreendimento_tag_novo").value = id;

            },
            error: function(xhr, status, error) {
                console.error("Ocorreu um erro ao carregar os dados: ", error);
            }
        });

    }

    function cadastrar() {
        //Esta função é usada para cadastrar uma nova "tag".

        //Obtém o valor da tag a partir do elemento com o ID "nome_tag_novo".
        var tag = document.getElementById("nome_tag_novo").value;
        var empreendimento = document.getElementById("empreendimento_tag_novo").value;
        var finalidade = document.getElementById("finalidade_tag_novo").value;

        $.ajax({
            url: '<?= base_url('public/desenho_tag_cadastro') ?>',
            type: "POST",
            dataType: "json", //Indicar que o retorno é em formato JSON
            data: {
                tag: tag,
                empreendimento: empreendimento,
                finalidade: finalidade
            },
            success: function(response) {
                //Função a ser executada em caso de sucesso da solicitação AJAX.

                if (!response.ok) {
                    //Se a resposta não indica sucesso, isso significa que ocorreu um erro no cadastramento da tag.

                    //A resposta contém mensagens de erro no formato de um objeto. O loop for percorre essas mensagens.
                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        //Para cada mensagem de erro, exibe um alerta personalizado com a chave (nome do campo) e o valor (mensagem de erro).
                        alert_personalizado(chave, valor);
                    }
                } else {
                    //Se a resposta indica sucesso, exibe um alerta informando que a "tag" foi cadastrada com sucesso.
                    alert_certo('Cadastrado', 'Tag cadastrado com sucesso.');
                    //Limpa o valor do campo de entrada para que o usuário possa inserir outra "tag".
                    document.getElementById("nome_tag_novo").value = '';
                    value_tags_c(true);
                    value_tags(true);
                    fecharModal('modal_cadastrar');
                }

            }
        });
    }
</script>


