<script>
lista();

let lista_atual = "";

function montarDataTableNivel() {
    if (!(window.jQuery && $('#example1').length)) {
        return;
    }

    if ($.fn.DataTable.isDataTable('#example1')) {
        $('#example1').DataTable().destroy();
    }

    $('#example1').DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        buttons: [],
        language: {
            decimal: '',
            emptyTable: 'Sem dados disponiveis',
            infoEmpty: 'Mostrando de 0 ate 0 de 0 registros',
            infoFiltered: '(filtrado do total de registros)',
            infoPostFix: '',
            thousands: ',',
            lengthMenu: 'MENU',
            loadingRecords: 'Carregando dados...',
            processing: 'Processando...',
            search: 'Buscar:',
            zeroRecords: 'Nao foram encontrados resultados',
            paginate: {
                first: 'Primeiro',
                last: 'Ultimo',
                next: 'Seguinte',
                previous: 'Anterior'
            },
            aria: {
                sortAscending: ': clique para ordenar ascendente',
                sortDescending: ': clique para ordenar descendente'
            }
        }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
}

function lista() {
    var ativos = document.getElementById('checkbox_ativos').checked;
    var desativados = document.getElementById('checkbox_desativado').checked;

    $.ajax({
        url: '<?= base_url('public/nivel_lista') ?>',
        type: 'POST',
        dataType: 'json',
        data: { ativos: ativos, desativados: desativados },
        success: function (response) {
            if (lista_atual !== response.lista) {
                var listaBody = document.getElementById('lista');
                if (listaBody) {
                    listaBody.innerHTML = response.lista;
                }
                montarDataTableNivel();
                lista_atual = response.lista;
            }
        }
    });
}

function desativar(id) {
    $.ajax({
        url: '<?= base_url('public/nivel_lista_desativar') ?>',
        type: 'POST',
        dataType: 'json',
        data: { id: id },
        success: function () {
            lista();
        }
    });
}

function ativar(id) {
    $.ajax({
        url: '<?= base_url('public/nivel_lista_ativar') ?>',
        type: 'POST',
        dataType: 'json',
        data: { id: id },
        success: function () {
            lista();
        }
    });
}

function getNivelScope(preferModal) {
    var modal = document.getElementById('modal');
    var modalBory = document.getElementById('modal_bory');
    var usarModal = preferModal !== false;

    if (usarModal && modal && modal.style.display === 'block' && modalBory) {
        return modalBory.querySelector('.nivel-form-scope') || modalBory;
    }

    var inputsBody = document.getElementById('inputs_body');
    if (inputsBody) {
        return inputsBody.querySelector('.nivel-form-scope') || inputsBody;
    }

    return document;
}

function getScopeField(scope, id) {
    return scope ? scope.querySelector('#' + id) : null;
}

function escapeHtmlNivel(valor) {
    return String(valor == null ? '' : valor)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function normalizarTokenNivelTelaInicial(valor) {
    valor = String(valor == null ? '' : valor).trim().toLowerCase();
    if (!valor) {
        return '';
    }

    valor = valor.replace(/[^a-z0-9]+/g, '_');
    valor = valor.replace(/^_+|_+$/g, '');

    var aliasesEspeciais = {
        'n_vel': 'nivel',
        'relat_rio': 'relatorio',
        'rel_torio': 'relatorio'
    };

    if (Object.prototype.hasOwnProperty.call(aliasesEspeciais, valor)) {
        return aliasesEspeciais[valor];
    }

    return valor;
}

function obterDefinicoesTelaInicial(scope) {
    var telaInicialInput = getScopeField(scope, 'tela_inicial');
    if (!telaInicialInput) {
        return [];
    }

    var raw = telaInicialInput.getAttribute('data-tela-inicial-definicoes') || '[]';
    try {
        var definicoes = JSON.parse(raw);
        return Array.isArray(definicoes) ? definicoes : [];
    } catch (error) {
        return [];
    }
}

function obterPermissoesSelecionadasTelaInicial(scope) {
    var checkboxTodos = getScopeField(scope, 'checkbox_todos');
    if (checkboxTodos && checkboxTodos.checked) {
        return ['all'];
    }

    return getCheckboxesNivel(scope)
        .map(normalizarTokenNivelTelaInicial)
        .filter(function (valor) { return valor !== ''; });
}

function telaInicialPermitidaNoNivel(definicao, permissoes) {
    if (!definicao || !definicao.key) {
        return false;
    }

    if (permissoes.indexOf('all') !== -1) {
        return true;
    }

    var aliases = Array.isArray(definicao.aliases) ? definicao.aliases : [];
    return aliases.some(function (alias) {
        return permissoes.indexOf(normalizarTokenNivelTelaInicial(alias)) !== -1;
    });
}

function atualizarOpcoesTelaInicialNivel(scope) {
    var telaInicialInput = getScopeField(scope, 'tela_inicial');
    if (!telaInicialInput) {
        return;
    }

    var valorAtual = telaInicialInput.value || '';
    var permissoes = obterPermissoesSelecionadasTelaInicial(scope);
    var definicoes = obterDefinicoesTelaInicial(scope);
    var html = '<option value="">Automatica</option>';

    definicoes.forEach(function (definicao) {
        if (!telaInicialPermitidaNoNivel(definicao, permissoes)) {
            return;
        }

        html += '<option value="' + escapeHtmlNivel(definicao.key || '') + '">' + escapeHtmlNivel(definicao.label || definicao.key || '') + '</option>';
    });

    telaInicialInput.innerHTML = html;

    var permitidoAinda = Array.from(telaInicialInput.options).some(function (option) {
        return option.value === valorAtual;
    });

    telaInicialInput.value = permitidoAinda ? valorAtual : '';
    telaInicialInput.disabled = permissoes.length === 0;
}

function inicializarTelaInicialNivel(scope) {
    if (!scope || !scope.querySelector) {
        return;
    }

    var telaInicialInput = getScopeField(scope, 'tela_inicial');
    if (!telaInicialInput) {
        return;
    }

    var checkboxTodos = getScopeField(scope, 'checkbox_todos');
    if (checkboxTodos && checkboxTodos.dataset.telaInicialBound !== '1') {
        checkboxTodos.addEventListener('change', function () {
            atualizarOpcoesTelaInicialNivel(scope);
        });
        checkboxTodos.dataset.telaInicialBound = '1';
    }

    var checkboxesPermissao = scope.querySelectorAll('.nivel-checkbox, input[id="nivel_checkbox"]');
    checkboxesPermissao.forEach(function (checkbox) {
        if (checkbox.dataset.telaInicialBound === '1') {
            return;
        }

        checkbox.addEventListener('change', function () {
            atualizarOpcoesTelaInicialNivel(scope);
        });
        checkbox.dataset.telaInicialBound = '1';
    });

    atualizarOpcoesTelaInicialNivel(scope);
}

function getCheckboxesNivel(scopeParam) {
    var scope = scopeParam || getNivelScope(true);
    var marcados = scope.querySelectorAll('.nivel-checkbox:checked');

    if (!marcados.length) {
        marcados = scope.querySelectorAll('input[id="nivel_checkbox"]:checked');
    }

    return Array.from(marcados).map(function (checkbox) {
        return checkbox.value;
    });
}

function getCheckboxesProcessos(scopeParam) {
    var scope = scopeParam || getNivelScope(true);
    var marcados = scope.querySelectorAll('.processo-checkbox:checked');

    if (!marcados.length) {
        marcados = scope.querySelectorAll('input[id="permissao_checkbox"]:checked');
    }

    return Array.from(marcados).map(function (checkbox) {
        return checkbox.value;
    });
}

function marcar_todos_nivel(checkboxBtn) {
    var scope = checkboxBtn.closest('.nivel-form-scope') || getNivelScope(true);
    var checkboxes = scope.querySelectorAll('.nivel-checkbox, input[id="nivel_checkbox"]');

    checkboxes.forEach(function (checkbox) {
        checkbox.disabled = checkboxBtn.checked;
    });

    atualizarOpcoesTelaInicialNivel(scope);
}

function marcar_todos_processos(checkboxBtn) {
    var scope = checkboxBtn.closest('.nivel-form-scope') || getNivelScope(true);
    var checkboxes = scope.querySelectorAll('.processo-checkbox, input[id="permissao_checkbox"]');

    checkboxes.forEach(function (checkbox) {
        if (checkboxBtn.checked) {
            checkbox.checked = false;
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    });
}

function modal_nivel(id) {
    var possuiId = id !== null && id !== undefined && id !== '';
    var idNormalizado = typeof id === 'string' ? id.replace('modal_', '') : id;

    $.ajax({
        url: '<?= base_url('public/nivel_modifica_modal') ?>',
        type: 'POST',
        dataType: 'json',
        data: { id: possuiId ? idNormalizado : null },
        success: function (response) {
            if (possuiId) {
                var modalTitulo = document.getElementById('modal_titulo');
                var modalBory = document.getElementById('modal_bory');
                var botaoConfirmar = document.getElementById('botao_confirmar_modal');

                if (modalTitulo) {
                    modalTitulo.textContent = response.titulo || 'Modificar Nivel';
                }

                if (modalBory) {
                    modalBory.innerHTML = response.conteudo || '';
                    inicializarTelaInicialNivel(modalBory.querySelector('.nivel-form-scope') || modalBory);
                }

                if (botaoConfirmar) {
                    botaoConfirmar.innerHTML = 'Confirmar';
                    botaoConfirmar.setAttribute('onclick', 'confirmarModal()');
                }

                mostrarModal();
            } else {
                var inputsBody = document.getElementById('inputs_body');
                if (inputsBody) {
                    inputsBody.innerHTML = response.conteudo || '';
                    inicializarTelaInicialNivel(inputsBody.querySelector('.nivel-form-scope') || inputsBody);
                }
            }
        }
    });
}

function confirmarModal() {
    var scope = getNivelScope(true);
    var checkboxTodos = getScopeField(scope, 'checkbox_todos');
    var checkboxTodosProcessos = getScopeField(scope, 'checkbox_todos_processos');
    var relatorioInput = getScopeField(scope, 'checkbox_relatorio');
    var nivelInput = getScopeField(scope, 'nivel_novo');
    var telaInicialInput = getScopeField(scope, 'tela_inicial');
    var nivelAdicionalInput = getScopeField(scope, 'nivel_adicional_id');

    if (!nivelInput) {
        alert_personalizado('Erro', 'Nao foi possivel localizar os campos do modal.');
        return;
    }

    var permissao = checkboxTodos && checkboxTodos.checked ? 'all' : getCheckboxesNivel(scope).join('-');
    var processos = checkboxTodosProcessos && checkboxTodosProcessos.checked ? 'all' : getCheckboxesProcessos(scope).join('-');
    var relatorio = relatorioInput ? relatorioInput.checked : false;

    $.ajax({
        url: '<?= base_url('public/nivel_modificar') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            nivel: nivelInput.value,
            permissao: permissao,
            relatorio: relatorio,
            processos: processos,
            tela_inicial: telaInicialInput ? telaInicialInput.value : '',
            nivel_adicional_id: nivelAdicionalInput ? nivelAdicionalInput.value : ''
        },
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
                alert_certo('Cadastrado', 'nivel modificado com sucesso.');
                nivelInput.value = '';
                fecharModal();
                lista();
            }
        }
    });
}

function cadastrar() {
    var scope = getNivelScope(false);
    var checkboxTodos = getScopeField(scope, 'checkbox_todos');
    var checkboxTodosProcessos = getScopeField(scope, 'checkbox_todos_processos');
    var relatorioInput = getScopeField(scope, 'checkbox_relatorio');
    var nivelInput = getScopeField(scope, 'nivel_novo');
    var telaInicialInput = getScopeField(scope, 'tela_inicial');
    var nivelAdicionalInput = getScopeField(scope, 'nivel_adicional_id');

    if (!nivelInput) {
        alert_personalizado('Erro', 'Nao foi possivel localizar os campos de cadastro.');
        return;
    }

    var permissao = checkboxTodos && checkboxTodos.checked ? 'all' : getCheckboxesNivel(scope).join('-');
    var processos = checkboxTodosProcessos && checkboxTodosProcessos.checked ? 'all' : getCheckboxesProcessos(scope).join('-');
    var relatorio = relatorioInput ? relatorioInput.checked : false;

    $.ajax({
        url: '<?= base_url('public/nivel_cadastrar') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            nivel: nivelInput.value,
            permissao: permissao,
            relatorio: relatorio,
            processos: processos,
            tela_inicial: telaInicialInput ? telaInicialInput.value : '',
            nivel_adicional_id: nivelAdicionalInput ? nivelAdicionalInput.value : ''
        },
        success: function (response) {
            if (!response.ok) {
                for (const chave in response.msg) {
                    const valor = response.msg[chave];
                    alert_personalizado(chave, valor);
                }
            } else {
                alert_certo('Cadastrado', 'nivel cadastrado com sucesso.');
                nivelInput.value = '';
                modal_nivel();
                lista();
            }
        }
    });
}

function add() {
    $.ajax({
        url: '<?= base_url('public/nivel_cadastrar_modal') ?>',
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            var div = document.getElementById('div');
            if (div) {
                div.innerHTML = response.modal;
            }
            mostrarModal();
            lista();
        }
    });
}

function selecionar_todos() {}

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

modal_nivel();
</script>
