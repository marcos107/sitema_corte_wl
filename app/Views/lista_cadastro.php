<?= $this->include('partials/wl-layout-open') ?>

<style>
#cadastro { display: none; }
#lista_btn { display: block; }
#cadastro1 { display: none; }
#lista1 { display: block; }

#modal.modal-1 {
    background-color: rgba(15, 23, 42, .58);
}

#modal .modal-dialog {
    width: min(94vw, 980px);
    max-width: none;
    margin: 1rem auto;
}

#modal .modal-content {
    max-height: calc(100vh - 2.2rem);
    display: flex;
    flex-direction: column;
    background-color: #ffffff;
    color: #0f172a;
    border: 0;
    border-radius: .85rem;
    overflow: hidden;
    box-shadow: 0 28px 65px rgba(15, 23, 42, .28);
}

#modal .modal-header {
    padding: .9rem 1.1rem;
    border-bottom: 1px solid #dbe5f1;
}

#modal .modal-body {
    padding: .85rem 1rem 1rem;
    overflow: auto;
    max-height: calc(100vh - 11.5rem);
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 72%);
}

#modal .modal-footer {
    padding: .8rem 1rem;
    border-top: 1px solid #dbe5f1;
}

#modal .modal-header,
#modal .modal-body,
#modal .modal-footer {
    background-color: #ffffff;
}

#modal .custom-select,
#modal .form-select,
#modal select,
#modal input[type="text"],
#modal input[type="password"],
#modal input[type="email"],
#modal input[type="number"],
#modal input[type="file"] {
    width: 100%;
    min-height: 38px;
    border: 1px solid #cbd5e1;
    border-radius: .5rem;
    padding: .35rem .55rem;
    font-size: .9rem;
    line-height: 1.2;
    background-color: #ffffff;
    color: #0f172a;
    box-shadow: none;
}

#modal select[multiple],
#modal .custom-select[multiple],
#modal .form-select[multiple] {
    min-height: 110px;
    padding-top: .45rem;
    padding-bottom: .45rem;
}

#modal .custom-select:focus,
#modal .form-select:focus,
#modal select:focus,
#modal input[type="text"]:focus,
#modal input[type="password"]:focus,
#modal input[type="email"]:focus,
#modal input[type="number"]:focus,
#modal input[type="file"]:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .2);
}

.wl-page-title {
    display: none;
}

.wl-page-header {
    padding: 0 0 1rem;
}

.wl-page-header .breadcrumb {
    margin-bottom: 0;
}

.wl-page-header .btn {
    white-space: nowrap;
}

.wl-cadastro-card .card-header {
    padding-bottom: 0;
}

.wl-cadastro-card .card-body {
    padding-top: 1rem;
}

.wl-cadastro-card .custom-select,
.wl-cadastro-card .form-select,
.wl-cadastro-card select {
    width: 100%;
    min-height: 38px;
    border: 1px solid #cbd5e1;
    border-radius: .5rem;
    padding: .35rem .55rem;
    font-size: .9rem;
    line-height: 1.2;
    background-color: #ffffff;
    color: #0f172a;
    box-shadow: none;
}

.wl-filter-group {
    border: 1px dashed var(--tb-border-color);
    border-radius: .65rem;
    padding: .75rem .85rem;
    background: rgba(248, 250, 252, .75);
}

@media (max-width: 576px) {
    .wl-page-header {
        padding-bottom: .75rem;
    }

    #modal .modal-dialog {
        width: 98vw;
        margin: .6rem auto;
    }

    .wl-filter-group {
        padding: .7rem;
    }
}
</style>

<div class="page-title-box d-sm-flex align-items-center justify-content-between wl-page-header">
    <div>
        <h4 class="mb-sm-0"><?= esc($titulo_lista ?? $functionType_lista ?? 'Cadastro') ?></h4>
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Cadastros</a></li>
            <li class="breadcrumb-item active"><?= esc($titulo_lista ?? $titulo_cadastro ?? 'Lista') ?></li>
        </ol>
    </div>
    <div class="wl-toggle-group mb-0">
        <div id="cadastro">
            <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" onclick="mudar_button()">
                <i class="ri-list-check-2-line"></i>
                <span><?= esc($functionType_lista) ?></span>
            </button>
        </div>
        <div id="lista_btn">
            <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" onclick="mudar_button()">
                <i class="ri-add-circle-line"></i>
                <span><?= esc($functionType_cadastro) ?></span>
            </button>
        </div>
    </div>
</div>

<div id="modal" class="modal-1">
    <div class="modal-dialog modal-dialog-centered" id="modal_sizer" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_titulo"></h5>
                <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_bory"></div>
            <div class="modal-footer" id="modal_rodape">
                <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div id="cadastro1" class="card wl-card wl-cadastro-card">
    <div class="card-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1"><?= esc($titulo_cadastro) ?></h5>
                <p class="text-muted mb-0">Adicionar / Enviar Arquivo</p>
            </div>
            <span class="badge bg-primary-subtle text-primary fw-semibold">
                <i class="ri-upload-cloud-2-line align-bottom me-1"></i>Cadastro
            </span>
        </div>
    </div>
    <div class="card-body" id="inputs_body">
        <div class="row g-3">
            <?php
            foreach ($array_input_typ as $kay => $value) {
                switch ($value) {
                    case 'select':
                        echo '<div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label" for="' . $array_input_id[$kay] . '">' . $array_input_titulo[$kay] . '</label>
                                <select id="' . $array_input_id[$kay] . '" class="form-select">' . $array_input_placeholder[$kay] . '</select>
                            </div>
                        </div>';
                        break;
                    case 'checkbox':
                        echo '<div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label d-block" for="' . $array_input_id[$kay] . '">' . $array_input_titulo[$kay] . '</label>
                                <div class="form-check form-check-primary mb-0">
                                    <input class="form-check-input" type="checkbox" id="' . $array_input_id[$kay] . '">
                                </div>
                            </div>
                        </div>';
                        break;
                    case 'tel':
                        echo '<div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label" for="' . $array_input_id[$kay] . '">' . $array_input_titulo[$kay] . '</label>
                                <input maxlength="15" class="form-control" onkeyup="handlePhone(event)" type="' . $value . '" id="' . $array_input_id[$kay] . '" placeholder="' . $array_input_placeholder[$kay] . '">
                            </div>
                        </div>';
                        break;
                    default:
                        echo '<div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-label" for="' . $array_input_id[$kay] . '">' . $array_input_titulo[$kay] . '</label>
                                <input type="' . $value . '" class="form-control" id="' . $array_input_id[$kay] . '" placeholder="' . $array_input_placeholder[$kay] . '">
                            </div>
                        </div>';
                        break;
                }
            }
            ?>
        </div>
    </div>
    <div class="card-footer bg-transparent border-0 pt-0">
        <div class="d-grid gap-2 d-md-flex">
            <button name="cadastarar" type="button" onclick="cadastrar()" id="cadastrar_btn" class="btn btn-primary btn-lg flex-fill">
                <?= esc($button_execut_nome) ?>
            </button>
            <button type="button" class="btn btn-soft-secondary btn-lg" onclick="mudar_button()">
                <i class="ri-eye-line align-bottom me-1"></i>Ver Lista
            </button>
        </div>
    </div>
</div>

<div id="lista1" class="card wl-card wl-cadastro-card">
    <div class="card-header border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h5 class="card-title mb-1"><?= esc($titulo_lista) ?></h5>
                <p class="text-muted mb-0">Arquivos cadastrados</p>
            </div>
            <span class="badge bg-info-subtle text-info fw-semibold">
                <i class="ri-file-list-3-line align-bottom me-1"></i>Listagem
            </span>
        </div>
    </div>
    <div class="card-body">
        <?php if ($selecao_lista) { ?>
            <fieldset class="wl-filter-group mb-3">
                <legend class="text-muted text-uppercase fw-semibold small mb-2">Mostrar</legend>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check form-check-primary mb-0">
                        <input class="form-check-input" type="checkbox" id="checkbox_ativos" onclick="lista()" checked>
                        <label class="form-check-label" for="checkbox_ativos">Ativos</label>
                    </div>
                    <div class="form-check form-check-primary mb-0">
                        <input class="form-check-input" type="checkbox" id="checkbox_desativado" onclick="lista()">
                        <label class="form-check-label" for="checkbox_desativado">Desativados</label>
                    </div>
                </div>
            </fieldset>
        <?php } ?>

        <?php
        if (($ajax ?? '') === 'ajaxs/nivel_ajax.php') {
            $array_titulo_lista = array("Nome", "Permissoes", "Processos", "Tela Inicial", "Nivel adicional", "Relatorio", "Status", "", "");
        }

        $titulo_lista_cols = '';
        for ($i = 0; $i < count($array_titulo_lista); $i++) {
            $titulo_lista_cols .= '<th>' . $array_titulo_lista[$i] . '</th>';
        }
        ?>

        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped table-hover table-nowrap align-middle mb-0">
                <thead>
                    <tr><?= $titulo_lista_cols ?></tr>
                </thead>
                <tbody id="lista">
                    <?= $lista ?>
                </tbody>
                <tfoot>
                    <tr><?= $titulo_lista_cols ?></tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
<?php if (empty($ajax)) { ?>
(function () {
    if (window.jQuery && $('#example1').length) {
        $('#example1').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            buttons: ['colvis'],
            order: [],
            ordering: true,
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
                }
            }
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
})();
<?php } ?>

const handlePhone = (event) => {
    let input = event.target;
    input.value = phoneMask(input.value);
};

const phoneMask = (value) => {
    if (!value) return '';
    value = value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d)(\d{4})$/, '$1-$2');
    return value;
};

const modalRodapePadrao = document.getElementById('modal_rodape')
    ? document.getElementById('modal_rodape').innerHTML
    : '';

function resetarModalEstadoPadrao() {
    const modalSizer = document.getElementById('modal_sizer');
    if (modalSizer) {
        modalSizer.classList.remove('modal-sm', 'modal-lg', 'modal-xl', 'modal-xxl');
    }

    const modalRodape = document.getElementById('modal_rodape');
    if (modalRodape && modalRodapePadrao) {
        modalRodape.innerHTML = modalRodapePadrao;
    }

    const botaoAlternativo = document.getElementById('botao_confirmar_modal1')
        || document.getElementById('botao_confirmar_modal_apagar');
    if (botaoAlternativo) {
        botaoAlternativo.id = 'botao_confirmar_modal';
    }

    const botaoConfirmar = document.getElementById('botao_confirmar_modal');
    if (botaoConfirmar) {
        botaoConfirmar.disabled = false;
        botaoConfirmar.setAttribute('onclick', 'confirmarModal()');
    }
}

function mudar_button() {
    const elemento1 = document.getElementById('cadastro');
    const elemento2 = document.getElementById('lista_btn');
    const elemento11 = document.getElementById('cadastro1');
    const elemento21 = document.getElementById('lista1');

    if (elemento1.style.display === 'block') {
        elemento1.style.display = 'none';
        elemento2.style.display = 'block';
        elemento11.style.display = 'none';
        elemento21.style.display = 'block';
    } else {
        elemento1.style.display = 'block';
        elemento2.style.display = 'none';
        elemento11.style.display = 'block';
        elemento21.style.display = 'none';
    }
}

function simularF11() {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
        document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
        document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
        document.documentElement.msRequestFullscreen();
    }

    sessionStorage.setItem('telaCheia', 'true');
}

window.onload = function () {
    const telaCheia = sessionStorage.getItem('telaCheia');
    if (telaCheia === 'true') {
        simularF11();
    }
};

function mostrarModal() {
    const modal = document.getElementById('modal');
    if (!modal) {
        return;
    }
    modal.style.display = 'block';
    document.body.classList.add('no-scroll');
}

function fecharModal() {
    const modal = document.getElementById('modal');
    if (!modal) {
        return;
    }
    resetarModalEstadoPadrao();
    modal.style.display = 'none';
    const modalBory = document.getElementById('modal_bory');
    if (modalBory) {
        modalBory.innerHTML = '';
    }
    document.body.classList.remove('no-scroll');
}

window.onclick = function (event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        fecharModal();
    }
};
</script>

<?php if ($ajax != '') {
    echo view($ajax);
} ?>

<?= $this->include('partials/wl-layout-end') ?>
