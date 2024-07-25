<li id="desenhos_top" class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-pen-nib"></i>
    <p>Desenhos<i class="fas fa-angle-left right"></i></p>
  </a>
  <ul class="nav nav-treeview" id="desenhos_bory">
    <li class="nav-item"><a href="<?=site_url("public/adm/desenho_adicionar")?>" id="adicionar" class="nav-link">&nbsp<i class="fa-solid fa-plus"></i>&nbsp<p>
          Adicionar</p></a></li>
    <li class="nav-item"><a href="<?=site_url("public/adm/desenho_meus")?>" id="desenhos" class="nav-link">&nbsp<i class="fa-solid fa-address-book"></i>&nbsp<p>Meus
          Desenhos</p></a></li>
    <li class="nav-item"><a href="<?=site_url("public/adm/lista_corte")?>" id="lista_corte" class="nav-link">&nbsp<i class="fa-solid fa-scissors"></i>&nbsp<p>Lista
          De Corte</p></a></li>
    <li class="nav-item"><a href="<?=site_url("public/adm/subpastas")?>" id="tags" class="nav-link">&nbsp<i class="fa-solid fa-folder-tree"></i>&nbsp<p>Subpastas
          </p></a></li>
    <li class="nav-item"><a href="<?=site_url("public/adm/desenhos_cortados")?>" id="desenhos_cortados" class="nav-link">&nbsp<i class="fa-solid fa-database"></i>&nbsp<p>Desenhos cortados
          </p></a></li>
  </ul>
</li>
<li id="config_dos_ajustes_top" class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-wrench"></i>
    <p>Config dos Ajustes<i class="fas fa-angle-left right"></i></p>
  </a>
  <ul class="nav nav-treeview" id="config_dos_ajustes_bory">
    <li class="nav-item"><a href="<?=site_url("public/adm/config_tipo_de_arquivo")?>" id="tipo_de_arquivo" class="nav-link">&nbsp<i
          class="fa-solid fa-box-archive"></i>&nbsp<p>Tipo De Arquivo</p></a></li>
          <li class="nav-item"><a href="<?=site_url("public/adm/config_prioridade")?>" id="prioridade" class="nav-link">&nbsp<i class="fa-solid fa-arrow-up-wide-short"></i>&nbsp<p>
          Prioridade</p></a></li>
          <li class="nav-item"><a href="<?= site_url("public/adm/config_finalidade") ?>" id="finalidade" class="nav-link">&nbsp<i class="fa-solid fa-bullseye"></i></i>&nbsp<p>
          Finalidade</p></a></li>
          <li class="nav-item"><a href="<?=site_url("public/adm/config_empresa_cliente")?>" id="empresa_cliente" class="nav-link">&nbsp<i class="fa-solid fa-people-group"></i>&nbsp<p>
          Empresa/Cliente</p></a></li>
    <li class="nav-item"><a href="<?=site_url("public/adm/config_empreendimento")?>" id="empreendimento" class="nav-link">&nbsp<i class="fa-solid fa-compass-drafting"></i>&nbsp<p>
          Empreendimento</p></a></li>
  </ul>
</li>
<li id="usuarios_top" class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-users-gear"></i>
    <p>Usuarios<i class="fas fa-angle-left right"></i></p>
  </a>
  <ul class="nav nav-treeview" id="usuarios_bory">
    <li class="nav-item"><a href="<?=site_url("public/adm/user_cadastrar")?>" id="cadastrar" class="nav-link">&nbsp<i
          class="fa-solid fa-user-plus"></i>&nbsp<p>Cadastrar</p></a></li>
    <li class="nav-item"><a href="<?=site_url("public/adm/user_modificar")?>" id="modificar" class="nav-link">&nbsp<i class="fa-solid fa-user-pen"></i>&nbsp<p>Modificar</p>
      </a></li>

  </ul>
</li>

<li id="relatorio_top" class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-flag"></i>
<p>Relatórios<i class="fas fa-angle-left right"></i></p>
</a>
<ul class="nav nav-treeview" id="relatorio_bory">




<li id="Relatorio analítico" class="nav-item"><a href="<?=site_url("public/adm/relatorios_analitico")?>" id="relatorios_detalhado" class="nav-link">&nbsp<i class="fa-solid fa-arrow-up-wide-short"></i>&nbsp<p>
Analítico</p></a></li>




</ul>

</li>






<li class="nav-item"><a href="<?=site_url("public/logout")?>" class="nav-link"><i class="fa-solid fa-door-open"></i>
    <p>Sair</p>
  </a></li>