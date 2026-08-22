<?php if (array_intersect(["Adicionar", "Meus_Desenhos", "Meus Desenhos", "Lista_De_Corte", "Lista De Corte", "Lista_De_Corte_ADM", "Lista_De_Corte ADM", "Lista De Corte ADM", "Subpasta", "Desenhos_cortados"], $permissao) || in_array("all", $permissao)) { ?>
<li id="desenhos_top" class="nav-item">
    <a id="desenhos_toggle" href="#desenhos_bory" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="desenhos_bory">
        <i class="ri-pencil-ruler-2-line"></i>
        <span>Desenhos</span>
    </a>
    <div class="collapse menu-dropdown" id="desenhos_bory">
        <ul class="nav nav-sm flex-column">
            <?php if (in_array("Adicionar", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/desenho_adicionar') ?>" id="adicionar" class="nav-link">Adicionar</a>
            </li>
            <?php } ?>

            <?php if (array_intersect(["Meus_Desenhos", "Meus Desenhos", "Lista_De_Corte", "Lista De Corte", "Lista_De_Corte_ADM", "Lista_De_Corte ADM", "Lista De Corte ADM", "Desenhos_cortados", "Desenhos cortados"], $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/painel_tarefas') ?>" id="painel_tarefas" class="nav-link">Painel de Tarefas</a>
            </li>
            <?php } ?>

            <?php if (in_array("Subpasta", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/subpasta') ?>" id="tags" class="nav-link">Subpastas</a>
            </li>
            <?php } ?>

        </ul>
    </div>
</li>
<?php } ?>

<?php if (array_intersect(["Prioridade", "Fialidade", "Empresa", "Empreendimento", "Nível", "Relátorio", "Logs", "Usuario"], $permissao) || in_array("all", $permissao)) { ?>
<li id="config_dos_ajustes_top" class="nav-item">
    <a id="config_dos_ajustes_toggle" href="#config_dos_ajustes_bory" class="nav-link menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="config_dos_ajustes_bory">
        <i class="ri-settings-3-line"></i>
        <span>Configuracoes</span>
    </a>
    <div class="collapse menu-dropdown" id="config_dos_ajustes_bory">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="<?= site_url('public/tipo_de_arquivo') ?>" id="tipo_de_arquivo" class="nav-link">Tipo de Arquivo</a>
            </li>

            <?php if (in_array("Prioridade", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/prioridade') ?>" id="prioridade" class="nav-link">Prioridade</a>
            </li>
            <?php } ?>

            <?php if (in_array("Fialidade", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/finalidade') ?>" id="finalidade" class="nav-link">Finalidade</a>
            </li>
            <?php } ?>

            <?php if (in_array("Empresa", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/empresa') ?>" id="empresa_cliente" class="nav-link">Empresa/Cliente</a>
            </li>
            <?php } ?>

            <?php if (in_array("Empreendimento", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/empreendimento') ?>" id="empreendimento" class="nav-link">Empreendimento</a>
            </li>
            <?php } ?>

            <?php if (in_array("Nível", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/nivel') ?>" id="nivel" class="nav-link">Nivel</a>
            </li>
            <?php } ?>

            <?php if (in_array("Processos", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/processos') ?>" id="processos" class="nav-link">Processos</a>
            </li>
            <?php } ?>

            <?php if (in_array("Usuario", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/usuario') ?>" id="usuario" class="nav-link">Usuario</a>
            </li>
            <?php } ?>

            <?php if (in_array("Logs", $permissao) || in_array("all", $permissao)) { ?>
            <li class="nav-item">
                <a href="<?= site_url('public/logs_alteracoes') ?>" id="logs_alteracoes" class="nav-link">Logs</a>
            </li>
            <?php } ?>
        </ul>
    </div>
</li>
<?php } ?>

<?php if (in_array("Relátorio", $permissao) || in_array("all", $permissao)) { ?>
<li id="Relatorio_analitico" class="nav-item">
    <a href="<?= site_url('public/relatorios') ?>" id="relatorio" class="nav-link menu-link">
        <i class="ri-bar-chart-box-line"></i>
        <span>Relatorio</span>
    </a>
</li>
<?php } ?>

<?php if (in_array("Lista_De_Corte_Cortador", $permissao) || in_array("all", $permissao)) { ?>
<li class="nav-item">
    <a href="<?= site_url('public/lista_tarefas') ?>" id="lista_tarefas" class="nav-link menu-link">
        <i class="ri-scissors-cut-line"></i>
        <span>Lista de Tarefas Operador</span>
    </a>
</li>
<?php } ?>

<li class="nav-item">
    <a href="<?= site_url('public/logout') ?>" class="nav-link menu-link">
        <i class="ri-logout-box-r-line"></i>
        <span>Sair</span>
    </a>
</li>
