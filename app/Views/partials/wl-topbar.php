<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <div class="ms-3 d-none d-md-block">
                    <h5 class="mb-0 fw-semibold">WL Maquetaria</h5>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-dark rounded-circle" data-toggle="fullscreen" title="Tela cheia">
                        <i class='bx bx-fullscreen fs-20'></i>
                    </button>
                </div>

                <div class="dropdown header-item">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center gap-2">
                            <img class="rounded-circle header-profile-user" src="<?= base_url('public/assets/template/images/users/avatar-1.jpg') ?>" alt="Avatar">
                            <span class="d-none d-md-inline-block fw-medium text-capitalize"><?= isset($nomeUsuario) ? esc($nomeUsuario) : 'Usuario' ?></span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header text-capitalize"><?= isset($nomeUsuario) ? esc($nomeUsuario) : 'Usuario' ?></h6>
                        <a class="dropdown-item" href="<?= site_url('public/logout') ?>">
                            <i class="bx bx-log-out text-muted fs-17 align-middle me-1"></i>
                            <span class="align-middle">Sair</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
