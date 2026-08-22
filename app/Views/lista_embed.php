<!doctype html>
<html lang="pt-br">
<head>
    <?= $this->include('partials/wl-head') ?>
    <style>
    body {
        margin: 0;
        background: #f3f6fb;
    }

    .wl-embed-page {
        padding: .75rem;
    }

    #modal.modal-1 {
        background-color: rgba(15, 23, 42, .58);
    }

    #modal .modal-dialog {
        width: min(96vw, 1700px);
        max-width: none;
        margin: 1rem auto;
    }

    #modal .modal-content {
        max-height: calc(100vh - 2.5rem);
        display: flex;
        flex-direction: column;
        border: 0;
        border-radius: .9rem;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 28px 65px rgba(15, 23, 42, .28);
    }

    #modal .modal-body {
        padding: .85rem 1rem 1rem;
        overflow: auto;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 72%);
    }

    #modal .modal-header,
    #modal .modal-body,
    #modal .modal-footer {
        background-color: #ffffff;
    }

    #modal .modal-header {
        padding: .9rem 1.1rem;
        border-bottom: 1px solid #dbe5f1;
    }

    #modal .modal-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: .01em;
    }

    #modal .modal-footer {
        padding: .8rem 1rem;
        border-top: 1px solid #dbe5f1;
    }

    #modal .modal-footer .btn {
        min-width: 110px;
        border-radius: .55rem;
        font-weight: 600;
    }

    #modal .wl-modal-table-wrap {
        border: 1px solid #dbe5f1;
        border-radius: .75rem;
        background: #ffffff;
        max-height: calc(100vh - 13rem);
        overflow: auto;
    }

    #modal .wl-modal-table-wrap table {
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        min-width: 1180px;
        margin-bottom: 0;
    }

    #modal .wl-modal-table-wrap table th,
    #modal .wl-modal-table-wrap table td {
        border-color: #e2e8f0 !important;
        padding: .45rem .55rem;
        vertical-align: middle;
        background-color: #ffffff;
    }

    #modal .wl-modal-table-wrap table tr:first-child th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f1f5f9;
        color: #334155;
        font-size: .74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        white-space: nowrap;
    }

    #modal .wl-modal-table-wrap table tr:not(:first-child) > th {
        font-size: .86rem;
        font-weight: 500;
        color: #0f172a;
        text-transform: none;
        letter-spacing: normal;
    }

    #modal .custom-select,
    #modal select,
    #modal input[type="text"],
    #modal input[type="file"] {
        width: 100%;
        min-height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: .5rem;
        padding: .3rem .5rem;
        font-size: .85rem;
        background-color: #ffffff;
    }

    #modal .wl-prioridade-cell {
        font-weight: 700;
    }

    #modal #modal_apagar_container {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-right: auto;
    }

    #modal #modal_apagar_checkbox {
        width: 18px;
        height: 18px;
    }

    @media (max-width: 768px) {
        #modal .modal-dialog {
            width: 99vw;
            margin: .6rem auto;
        }

        #modal .modal-content {
            max-height: calc(100vh - 1.2rem);
        }

        #modal .modal-body {
            padding: .65rem;
        }

        #modal .wl-modal-table-wrap {
            max-height: calc(100vh - 12.5rem);
        }

        #modal .wl-modal-table-wrap table {
            min-width: 1040px;
        }
    }
    </style>
</head>
<body>
    <div class="wl-embed-page">
        <div id="modal" class="modal-1">
            <div class="modal-dialog" id="modal_sizer" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_titulo"></h5>
                        <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal_bory"></div>
                    <div class="modal-footer" id="modal_rodape">
                        <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()"></button>
                    </div>
                </div>
            </div>
        </div>

        <audio id="bell-sound" src="<?= base_url('public/assets/som/bell.mp4') ?>" preload="auto"></audio>

        <div id="list1" class="card wl-card">
            <div class="card-header border-0">
                <h5 class="card-title mb-0"><?= esc($titulo) ?></h5>
            </div>
            <div class="card-body" id="top-lista">
                <?php if ($selecao_lista) { ?>
                    <fieldset class="mb-3">
                        Mostrar:&nbsp;&nbsp;
                        <input type="checkbox" id="checkbox_ativos" onclick="lista()" checked>
                        <label for="checkbox_ativos">&nbsp;Ativos</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="checkbox_desativado" onclick="lista()">
                        <label for="checkbox_desativado">&nbsp;Desativados</label>
                    </fieldset>
                <?php } ?>

                <?php if (isset($hora_lista)) { ?>
                    <fieldset class="mb-3">
                        Intervalo de tempo:&nbsp;&nbsp;
                        <input type="date" id="dataFinal" name="dataFinal" required>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="date" id="dataInicial" name="dataInicial" required>
                    </fieldset>
                <?php } ?>

                <?php
                $titulo_lista = "";
                for ($i = 0; $i < count($array_titulo_lista); $i++) {
                    $titulo_lista .= "<th>" . $array_titulo_lista[$i] . "</th>";
                }
                ?>

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped align-middle mb-0">
                        <thead>
                            <tr><?= $titulo_lista ?></tr>
                        </thead>
                        <tbody id="lista">
                            <?= $lista ?>
                        </tbody>
                        <tfoot>
                            <tr><?= $titulo_lista ?></tr>
                        </tfoot>
                    </table>
                </div>

                <div id="roda_pe"></div>
            </div>
        </div>
    </div>

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
                    },
                    aria: {
                        sortAscending: ': clique para ordenar ascendente',
                        sortDescending: ': clique para ordenar descendente'
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

    const modalRodapePadrao = document.getElementById('modal_rodape') ? document.getElementById('modal_rodape').innerHTML : '';

    function resetarModalEstadoPadrao() {
        const modalSizer = document.getElementById('modal_sizer');
        if (modalSizer) {
            modalSizer.classList.remove('modal-sm', 'modal-lg', 'modal-xl', 'modal-xxl');
        }

        const modalRodape = document.getElementById('modal_rodape');
        if (modalRodape && modalRodapePadrao) {
            modalRodape.innerHTML = modalRodapePadrao;
        }

        const botaoAlternativo = document.getElementById('botao_confirmar_modal1') || document.getElementById('botao_confirmar_modal_apagar');
        if (botaoAlternativo) {
            botaoAlternativo.id = 'botao_confirmar_modal';
        }

        const botaoConfirmar = document.getElementById('botao_confirmar_modal');
        if (botaoConfirmar) {
            botaoConfirmar.disabled = false;
            botaoConfirmar.setAttribute('onclick', 'confirmarModal()');
        }
    }

    function mostrarModal() {
        const modal = document.getElementById('modal');
        modal.style.display = 'block';
        document.body.classList.add('no-scroll');
    }

    function fecharModal() {
        resetarModalEstadoPadrao();
        const modal = document.getElementById('modal');
        modal.style.display = 'none';
        document.getElementById('modal_bory').innerHTML = '';
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

    <?php if (isset($hora_lista)) { ?>
    <script>
    setDataAtual();

    function setDataAtual() {
        let dataInput = document.getElementById('dataInicial');
        let dataAtual = new Date();

        let ano = dataAtual.getFullYear().toString().padStart(4, '0');
        let mes = (dataAtual.getMonth() + 1).toString().padStart(2, '0');
        let dia = dataAtual.getDate().toString().padStart(2, '0');

        let dataFormatada = `${ano}-${mes}-${dia}`;
        dataInput.value = dataFormatada;

        dataInput = document.getElementById('dataFinal');
        dataAtual.setDate(dataAtual.getDate() - 3);
        ano = dataAtual.getFullYear().toString().padStart(4, '0');
        mes = (dataAtual.getMonth() + 1).toString().padStart(2, '0');
        dia = dataAtual.getDate().toString().padStart(2, '0');

        dataFormatada = `${ano}-${mes}-${dia}`;
        dataInput.value = dataFormatada;
    }
    </script>
    <?php } ?>
</body>
</html>
