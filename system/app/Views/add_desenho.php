<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title><?= $titulo ?></title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>public/wl.ico" >
  <!--
  <script src="corte.js"></script>
 -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  <!-- icons -->
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
  href="<?php echo base_url(); ?>public/assets/font.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/assets/style/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet"
    href="<?php echo base_url(); ?>public/assets/style/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet"
    href="<?php echo base_url(); ?>public/assets/style/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet"
    href="<?php echo base_url(); ?>public/assets/style/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <script src="<?php echo base_url(); ?>public/assets/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>

  <script src="<?php echo base_url(); ?>public/assets/popper.min.js"
    integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
    crossorigin="anonymous"></script>
  <script src="<?php echo base_url(); ?>public/assets/bootstrap.min.js"
    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/assets/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/assets/style/dist/css/adminlte.min.css">
</head>
<style>
  .capitalized {
    text-transform: capitalize;
  }
  .container-mesma-linha {
      display: flex;
    }
  .modal-1 {
    display: none;
    position: fixed;
    z-index: 1039;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
  }
  .no-scroll {
    overflow: hidden;
  }
</style>

<body class="hold-transition sidebar-mini">





<div id="modal" class="modal-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_titulo"></h5>
                    <button type="button" class="close" onclick="fecharModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_bory">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="botao_fechar_modal"
                        onclick="fecharModal()">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="botao_confirmar_modal"
                        onclick="confirmarModal()"></button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal_cadastrar" class="modal-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal_cadastrar_titulo"></h5>
          <button type="button" class="close" onclick="fecharModal('modal_cadastrar')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modal_cadastrar_bory">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="botao_fechar_modal_cadastrar"
            onclick="fecharModal('modal_cadastrar')">Cancelar</button>
          <button type="button" class="btn btn-primary" id="botao_confirmar_modal_cadastrar" onclick="cadastrar()"></button>
        </div>
      </div>
    </div>
  </div>



  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <!--
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>-->
      </ul>
      <ul class="navbar-nav ml-auto">

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="" class="brand-link">
        <img src="<?php echo base_url(); ?>public/assets/style/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
          class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Wl Maquetaria</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- nome do Usuario -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">

          </div>

          <div class="info">


            <a href="" class="d-block capitalized"><i class="fa-solid fa-user" color="#fff"></i>
              <?php echo $nomeUsuario ?>
            </a>



          </div>
        </div>

        <!-- Pesquisa -->
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php echo $menu; ?>




          </ul>
        </nav>

        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <section class="content">
        <div class="modal fade" id="modal-default">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Default Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>One fine body&hellip;</p>
              </div>
              <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <section>
          <!-- Content Header (Page header) -->
          <section class="content-header">
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6">
                  <h1>
                    <?php echo $functionType; ?>
                  </h1>
                </div>

              </div>
            </div><!-- /.container-fluid -->
          </section>

          <!-- Main content -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <?= $titulo ?>
              </h3>
            </div>

            <!-- /.card-header -->
            <div class="card-body">

              <div class="row">


              <input type="file" name="file" id="desenhos_add" class="inputfile" accept="<?=$filtro;?>" data-multiple-caption="{count} files selected" multiple />


              <br><br><br>
              <button name="cadastarar" type="submit" onclick="adicionar()" class="btn btn-block btn-outline-primary btn-lg">
                                        Adicionar                                    </button>
              </div>
             
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
      </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <div id="meusScripts">
    <script src="<?php echo base_url(); ?>public/assets/style/plugins/jquery/jquery.min.js"></script>
    <!-- DataTables  & <?php echo base_url(); ?>public/assets/style/plugins -->
    <script src="<?php echo base_url(); ?>public/assets/style/plugins/datatables/jquery.dataTables.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo base_url(); ?>public/assets/style/plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>public/assets/style/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>public/assets/style/plugins/pdfmake/vfs_fonts.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script
      src="<?php echo base_url(); ?>public/assets/style/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url(); ?>public/assets/style/dist/js/adminlte.min.js"></script>


    <!-- Bootstrap 4 -->
    <script
                src="<?php echo base_url(); ?>public/assets/style/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
            <!-- SweetAlert2 -->
            <script src="<?php echo base_url(); ?>public/assets/style/plugins/sweetalert2/sweetalert2.min.js"></script>
            <!-- Toastr -->
            <script src="<?php echo base_url(); ?>public/assets/style/plugins/toastr/toastr.min.js"></script>
    <!-- AdminLTE for demo purposes
<script src="<?php echo base_url(); ?>public/assets/style/dist/js/demo.js"></script>
 Page specific script -->
    <script>
      $(function () {
        $("#example1").DataTable({

          "responsive": true, "lengthChange": false, "autoWidth": false,
          "buttons": ["colvis"],
          "ordering": false,
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



                function mostrarModal(class_var = "modal") {
                    const modal = document.getElementById(class_var);
                    modal.style.display = "block";

                    // Adiciona a classe para desabilitar a rolagem
                    document.body.classList.add("no-scroll");
                }

                function fecharModal(class_var = "modal") {
                    const modal = document.getElementById(class_var);
                    modal.style.display = "none";
                    document.getElementById(class_var+'_bory').innerHTML = '';
                    // Remove a classe para habilitar a rolagem
                    document.body.classList.remove("no-scroll");
                }

                // Fechar o modal se o usuário clicar fora da área do modal
                window.onclick = function (event) {
                    const modal = document.getElementById("modal");
                    if (event.target === modal) {
                        modal.style.display = "none";
                        document.body.classList.remove("no-scroll"); // Certifique-se de remover a classe aqui também
                    }
                };

    </script>
  </div>
</body>
<?php if ($ajax != '') {
  echo view($ajax);
} ?>

</html>
<div id="meusScripts">
  <script src="<?php echo base_url(); ?>public/assets/all.min.js"></script>
</div>