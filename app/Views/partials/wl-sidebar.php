<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="<?= site_url('public/lista_tarefas') ?>" class="logo logo-dark">
            <span class="logo-sm">
                <span class="text-white fw-bold">WL</span>
            </span>
            <span class="logo-lg text-white fw-bold">WL</span>
        </a>
        <a href="<?= site_url('public/lista_tarefas') ?>" class="logo logo-light">
            <span class="logo-sm">
                <span class="text-white fw-bold">WL</span>
            </span>
            <span class="logo-lg text-white fw-bold">WL Maquetaria</span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Menu</span></li>
                <?= isset($menu) ? $menu : '' ?>
            </ul>
        </div>
    </div>
</div>
<div class="vertical-overlay"></div>
