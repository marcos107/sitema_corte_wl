<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | DataTables</title>
  <!--
  <script src="corte.js"></script>
 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="style/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="style/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="style/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="style/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
    integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
    integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
    integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <!-- Theme style -->
  <link rel="stylesheet" href="style/dist/css/adminlte.min.css">
</head>
<style>
  .capitalized {
    text-transform: capitalize;
  }
</style>

<body class="hold-transition sidebar-mini">
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
        <img src="style/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
          style="opacity: .8">
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


            <li class="nav-item menu-is-opening menu-open">
              <a href="#" class="nav-link">
                <i class="fa-solid fa-pen-nib"></i>
                <p>
                  Desenhos
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview" style="display: block;">
                <li class="nav-item">
                  <a href="../forms/general.html" class="nav-link">
                  &nbsp
                  <i class="fa-solid fa-plus"></i>
                  &nbsp
                    <p>Adicionar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link active">
                  &nbsp
                  <i class="fa-solid fa-address-book"></i>
                  &nbsp
                    <p>Meus Desenhos</p>

                  </a>


                </li>

              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="fa-solid fa-wrench"></i>
                <p>
                  Config dos Ajustes
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="../forms/general.html" class="nav-link">
                  &nbsp
                  <i class="fa-solid fa-box-archive"></i>
                  &nbsp
                    <p>Tipo De Arquivo</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                  &nbsp
                  <i class="fa-solid fa-arrow-up-wide-short"></i>
                  &nbsp
                    <p>Prioridade</p>
                  </a>
                </li>


                <li class="nav-item">
                  <a href="#" class="nav-link">
                  &nbsp
                  <i class="fa-solid fa-people-group"></i>
                  &nbsp
                    <p>Empresa/Cliente</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="#" class="nav-link">
                  &nbsp
                  <i class="fa-solid fa-compass-drafting"></i>
                  &nbsp
                    <p>Empreendimento</p>
                  </a>
                </li>

              </ul>
            </li>
            

            
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="fa-solid fa-users-gear"></i>
                <p>
                  Usuarios
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="../forms/general.html" class="nav-link">
                  &nbsp
                  <i class="fa-solid fa-user-plus"></i>
                  &nbsp
                    <p>Cadastrar</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link ">
                  &nbsp
                  <i class="fa-solid fa-user-pen"></i>
                  &nbsp
                    <p>Modificar</p>

                  </a>


                </li>

              </ul>
            </li>

            <li class="nav-item">
              <a href="../config/login/logout.php" class="nav-link">
                <i class="fa-solid fa-door-open"></i>
                <p>
                  Sair
                </p>
              </a>
            </li>


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
              <h3 class="card-title">Lista de corte</h3>
            </div>

            <!-- /.card-header -->
            <div class="card-body">


              <!-- Modal -->
              <div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal_titulo"></h5>
                      <button type="button" class="close" onclick="closeModal()">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body" id="modal_bory">

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" id="botao_fechar_modal"
                        onclick="closeModal()">Cancelar</button>
                      <button type="button" class="btn btn-primary" id="botao_confirmar_modal"
                        onclick="closeModal()">Confirmar corte</button>
                    </div>
                  </div>
                </div>
              </div>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Numero</th>
                    <th>Desenhista</th>
                    <th>Nome Desenho</th>
                    <th>Empresa</th>
                    <th>Empreendimento</th>
                    <th>Finalidade</th>
                    <th>Prioridade</th>
                    <th>Data</th>
                    <th>Cortar</th>
                    <th>Confirmar Corte</th>
                  </tr>
                </thead>
                <tbody id="lista_corte">
                  <?php echo $lista; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Numero</th>
                    <th>Desenhista</th>
                    <th>Nome Desenho</th>
                    <th>Empresa</th>
                    <th>Empreendimento</th>
                    <th>Finalidade</th>
                    <th>Prioridade</th>
                    <th>Data</th>
                    <th>Cortar</th>
                    <th>Confirmar Corte</th>
                  </tr>
                </tfoot>
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
    <script src="style/plugins/jquery/jquery.min.js"></script>
    <!-- DataTables  & style/plugins -->
    <script src="style/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="style/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="style/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="style/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="style/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="style/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="style/plugins/jszip/jszip.min.js"></script>
    <script src="style/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="style/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="style/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="style/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="style/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="style/dist/js/adminlte.min.js"></script>

    <!-- AdminLTE for demo purposes
<script src="style/dist/js/demo.js"></script>
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


    </script>
  </div>
</body>

</html>
<div id="meusScripts">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</div>