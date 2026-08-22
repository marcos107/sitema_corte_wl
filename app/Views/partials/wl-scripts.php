<script src="<?= base_url('public/assets/style/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('public/assets/template/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('public/assets/template/libs/simplebar/simplebar.min.js') ?>"></script>
<script src="<?= base_url('public/assets/template/js/pages/plugins/lord-icon-2.1.0.js') ?>"></script>
<script src="<?= base_url('public/assets/template/js/plugins.js') ?>"></script>
<script src="<?= base_url('public/assets/template/js/app.js') ?>"></script>

<script src="<?= base_url('public/assets/style/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('public/assets/style/plugins/toastr/toastr.min.js') ?>"></script>
<script src="<?= base_url('public/assets/all.min.js') ?>"></script>
<script src="<?= base_url('public/assets/js/wl-table-sort.js?v=20260307_01') ?>"></script>

<script>
(function () {
    // Compatibilidade: várias telas ainda usam $(document).Toasts('create', ...)
    // (API do AdminLTE). No template atual, usamos Toastr.
    // Este shim evita quebra e garante feedback visual para cadastro/modificação.
    if (window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.Toasts !== 'function') {
        window.jQuery.fn.Toasts = function (action, options) {
            if (action !== 'create') {
                return this;
            }

            var opts = options || {};
            var cssClass = String(opts.class || '').toLowerCase();
            var titulo = String(opts.title || '');
            var corpo = String(opts.body || '');
            var delay = Number(opts.delay || 5000);

            var tipo = 'info';
            if (cssClass.indexOf('danger') !== -1) {
                tipo = 'error';
            } else if (cssClass.indexOf('success') !== -1) {
                tipo = 'success';
            } else if (cssClass.indexOf('warning') !== -1) {
                tipo = 'warning';
            }

            if (window.toastr && typeof window.toastr[tipo] === 'function') {
                window.toastr.options = window.toastr.options || {};
                window.toastr.options.timeOut = delay;
                window.toastr.options.closeButton = true;
                window.toastr.options.progressBar = true;
                window.toastr[tipo](corpo, titulo);
                return this;
            }

            if (window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    icon: tipo === 'error' ? 'error' : (tipo === 'success' ? 'success' : (tipo === 'warning' ? 'warning' : 'info')),
                    title: titulo,
                    text: corpo
                });
                return this;
            }

            window.alert((titulo ? titulo + ': ' : '') + corpo);
            return this;
        };
    }

    document.addEventListener('click', function (event) {
        var pushToggle = event.target.closest('[data-widget="pushmenu"]');
        if (pushToggle) {
            event.preventDefault();
            var topToggle = document.getElementById('topnav-hamburger-icon');
            if (topToggle) {
                topToggle.click();
            }
        }
    });
})();
</script>
