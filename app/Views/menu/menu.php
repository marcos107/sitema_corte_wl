<?php if(array_intersect(["Adicionar","Meus_Desenhos","Lista_De_Corte","Lista_De_Corte ADM","Subpasta","Desenhos_cortados"],$permissao) or in_array("all",$permissao)){?>
<li id="desenhos_top" class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-pen-nib"></i>
    <p>Desenhos<i class="fas fa-angle-left right"></i></p>
  </a>
  <ul class="nav nav-treeview" id="desenhos_bory">
  <?php if(in_array("Adicionar",$permissao) or in_array("all",$permissao)){?>

    <li class="nav-item"><a href="<?=site_url("public/desenho_adicionar")?>" id="adicionar" class="nav-link">&nbsp<i class="fa-solid fa-plus"></i>&nbsp<p>
          Adicionar</p></a></li>
      <?php } if(in_array("Meus_Desenhos",$permissao) or in_array("all",$permissao)){?>

    <li class="nav-item"><a href="<?=site_url("public/desenho_meus")?>" id="desenhos" class="nav-link">&nbsp<i class="fa-solid fa-address-book"></i>&nbsp<p>Meus
          Desenhos</p></a></li>
          <?php } if(in_array("Lista_De_Corte",$permissao) or in_array("all",$permissao)){?>

    <li class="nav-item"><a href="<?=site_url("public/lista_corte")?>" id="lista_corte" class="nav-link">&nbsp<i class="fa-solid fa-scissors"></i>&nbsp<p>Lista
          De Corte</p></a></li>
          <?php } if(in_array("Lista_De_Corte ADM",$permissao) or in_array("all",$permissao)){?>

    <li class="nav-item"><a href="<?=site_url("public/lista_corte_adm")?>" id="lista_corte_adm" class="nav-link">&nbsp<i class="fa-solid fa-scissors"></i>&nbsp<p>Lista
          De Corte ADM</p></a></li>
          <?php } if(in_array("Subpasta",$permissao) or in_array("all",$permissao)){?>

    <li class="nav-item"><a href="<?=site_url("public/subpasta")?>" id="tags" class="nav-link">&nbsp<i class="fa-solid fa-folder-tree"></i>&nbsp<p>Subpastas
          </p></a></li>
          <?php } if(in_array("Desenhos_cortados",$permissao) or in_array("all",$permissao)){?>

    <li class="nav-item"><a href="<?=site_url("public/desenhos_cortados")?>" id="desenhos_cortados" class="nav-link">&nbsp<i class="fa-solid fa-database"></i>&nbsp<p>Desenhos Cortados
          </p></a></li>
          <?php }?>

  </ul>
</li>
<?php }?>


<?php if(array_intersect(["Prioridade","Fialidade","Empresa","Empreendimento","Nível","Relátorio","Usuario"],$permissao) or in_array("all",$permissao)){?>
<li id="config_dos_ajustes_top" class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-wrench"></i>
    <p>Configurações<i class="fas fa-angle-left right"></i></p>
  </a>
  <ul class="nav nav-treeview" id="config_dos_ajustes_bory">
    <li class="nav-item"><a href="<?=site_url("public/tipo_de_arquivo")?>" id="tipo_de_arquivo" class="nav-link">&nbsp<i
          class="fa-solid fa-box-archive"></i>&nbsp<p>Tipo De Arquivo</p></a></li>
          <?php if(in_array("Prioridade",$permissao) or in_array("all",$permissao)){?>
          <li class="nav-item"><a href="<?=site_url("public/prioridade")?>" id="prioridade" class="nav-link">&nbsp<i class="fa-solid fa-arrow-up-wide-short"></i>&nbsp<p>
          Prioridade</p></a></li>

          <?php } if(in_array("Fialidade",$permissao) or in_array("all",$permissao)){?>
          <li class="nav-item"><a href="<?= site_url("public/finalidade") ?>" id="finalidade" class="nav-link">&nbsp<i class="fa-solid fa-bullseye"></i></i>&nbsp<p>
          Finalidade</p></a></li>

          <?php } if(in_array("Empresa",$permissao) or in_array("all",$permissao)){?>
          <li class="nav-item"><a href="<?=site_url("public/empresa")?>" id="empresa_cliente" class="nav-link">&nbsp<i class="fa-solid fa-people-group"></i>&nbsp<p>
          Empresa/Cliente</p></a></li>

          <?php } if(in_array("Empreendimento",$permissao) or in_array("all",$permissao)){?>
    <li class="nav-item"><a href="<?=site_url("public/empreendimento")?>" id="empreendimento" class="nav-link">&nbsp<i class="fa-solid fa-compass-drafting"></i>&nbsp<p>
          Empreendimento</p></a></li>

          <?php } if(in_array("Nível",$permissao) or in_array("all",$permissao)){?>
    <li class="nav-item"><a href="<?=site_url("public/nivel")?>" id="nivel" class="nav-link">&nbsp<i class="fa-solid fa-layer-group"></i>&nbsp<p>
          Nível</p></a></li>
          <?php } if(in_array("Processos",$permissao) or in_array("all",$permissao)){?>
    <li class="nav-item"><a href="<?=site_url("public/processos")?>" id="processos" class="nav-link">&nbsp<i class="fa-solid fa-microchip"></i>&nbsp<p>
    Processos</p></a></li>


          <?php } if(in_array("Usuario",$permissao) or in_array("all",$permissao)){?>
    <li class="nav-item"><a href="<?=site_url("public/usuario")?>" id="usuario" class="nav-link">&nbsp<i class="fa-solid fa-user-pen"></i>&nbsp<p>Usuário</p>
      </a></li>
      <?php }?>
  </ul>
</li>
<?php }?>

<?php if(in_array("Relátorio",$permissao) or in_array("all",$permissao)){?>
     <li id="Relatorio analítico" class="nav-item"><a href="<?=site_url("public/relatorios")?>" id="relatorio" class="nav-link">&nbsp<i class="fa-solid fa-arrow-up-wide-short"></i>&nbsp<p>
      Relatório</p></a></li>
<?php } ?>

<?php if(in_array("Lista_De_Corte_Cortador",$permissao) or in_array("all",$permissao)){?>
<li class="nav-item"><a href="<?=site_url("public/lista_afazeres")?>" id="lista_afazeres" class="nav-link">&nbsp<i class="fa-solid fa-scissors"></i>&nbsp<p>Lista
De Afazeres</p></a></li>
<?php }?>


<li class="nav-item"><a href="<?=site_url("public/logout")?>" class="nav-link"><i class="fa-solid fa-door-open"></i>
    <p>Sair</p>
  </a></li>