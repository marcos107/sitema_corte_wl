<?= $this->include('partials/wl-layout-open') ?>

<div id="modal" class="modal-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_titulo"></h5>
                <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_bory"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()"></button>
            </div>
        </div>
    </div>
</div>

<div id="cadastro1" class="card wl-card">
    <div class="card-header border-0">
        <h5 class="card-title mb-0"><?= esc($titulo) ?></h5>
    </div>

    <div class="card-body" id="inputs_body">
        <?php
        $id_group = 0;
        foreach ($array_input_typ as $kay => $value) {
            switch ($value) {
                case 'select':
                    echo '<div class="form-group mb-3" id="group_' . $id_group . '">
                        <label>' . $array_input_titulo[$kay] . '</label>
                        <select id="' . $array_input_id[$kay] . '" class="form-select">' . $array_input_placeholder[$kay] . '</select>
                    </div>';
                    break;

                case 'tel':
                    echo '<div class="form-group mb-3" id="group_' . $id_group . '">
                        <label>' . $array_input_titulo[$kay] . '</label>
                        <input maxlength="15" onkeyup="handlePhone(event)" type="' . $value . '" class="form-control" id="' . $array_input_id[$kay] . '" placeholder="' . $array_input_placeholder[$kay] . '">
                    </div>';
                    break;

                default:
                    echo '<div class="form-group mb-3" id="group_' . $id_group . '">
                        <label>' . $array_input_titulo[$kay] . '</label>
                        <input type="' . $value . '" class="form-control" id="' . $array_input_id[$kay] . '" placeholder="' . $array_input_placeholder[$kay] . '">
                    </div>';
                    break;
            }
            $id_group++;
        }
        ?>
    </div>

    <div class="card-footer bg-transparent border-0 pt-0">
        <button name="cadastarar" type="submit" onclick="cadastrar()" id="cadastrar_btn" class="btn btn-primary btn-lg w-100">
            <?= esc($button_execut_nome) ?>
        </button>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
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

function mudar_button() {
    const elemento1 = document.getElementById('cadastro');
    const elemento2 = document.getElementById('lista');
    const elemento11 = document.getElementById('cadastro1');
    const elemento21 = document.getElementById('lista1');

    if (!elemento1 || !elemento2 || !elemento11 || !elemento21) {
        return;
    }

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
    modal.style.display = 'block';
    document.body.classList.add('no-scroll');
}

function fecharModal() {
    const modal = document.getElementById('modal');
    modal.style.display = 'none';
    document.getElementById('modal_bory').innerHTML = '';
    document.body.classList.remove('no-scroll');
}

window.onclick = function (event) {
    const modal = document.getElementById('modal');
    if (event.target === modal) {
        modal.style.display = 'none';
        document.body.classList.remove('no-scroll');
    }
};
</script>

<?php if ($ajax != '') {
    echo view($ajax);
} ?>

<?= $this->include('partials/wl-layout-end') ?>
