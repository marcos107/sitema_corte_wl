<!doctype html>
<html lang="pt-br" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | WL Maquetaria</title>
    <link rel="shortcut icon" href="<?= base_url('public/wl.ico') ?>">

    <script src="<?= base_url('public/assets/template/js/layout.js') ?>"></script>
    <link href="<?= base_url('public/assets/template/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('public/assets/template/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('public/assets/template/css/app.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('public/assets/template/css/custom.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('public/assets/font.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('public/assets/all.min.css') ?>" rel="stylesheet" type="text/css" />

    <style>
        .wl-auth {
            min-height: 100vh;
            background: linear-gradient(130deg, #f1f5f9 0%, #dbeafe 100%);
        }

        .wl-auth-card {
            border: 0;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
        }

        .wl-auth-side {
            background-image: linear-gradient(rgba(13, 110, 253, 0.82), rgba(30, 64, 175, 0.86)), url('<?= base_url('public/assets/template/images/auth-one-bg.jpg') ?>');
            background-size: cover;
            background-position: center;
            color: #fff;
        }

        .wl-auth-toggle {
            cursor: pointer;
        }

        .wl-hide {
            display: none !important;
        }
    </style>
</head>
<body>
    <section class="wl-auth d-flex align-items-center justify-content-center py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-7 col-xl-6">
                    <div class="card wl-auth-card overflow-hidden">
                        <div class="row g-0">
                            <div class="col-12 bg-white">
                                <div class="p-4 p-md-5">
                                    <div class="mb-4 text-center text-lg-start">
                                        <h4 class="text-primary fw-semibold mb-1">WL Maquetaria</h4>
                                        <p class="text-muted mb-0">Sistema de corte</p>
                                    </div>

                                    <div id="error" class="alert alert-danger py-2 wl-hide"></div>

                                    <div id="login_panel">
                                        <h5 class="mb-3">Entrar</h5>
                                        <div class="mb-3">
                                            <label for="nome" class="form-label">Usuario</label>
                                            <input id="nome" type="text" class="form-control" placeholder="Digite seu usuario">
                                        </div>
                                        <div class="mb-3">
                                            <label for="senha" class="form-label">Senha</label>
                                            <input id="senha" type="password" class="form-control" placeholder="Digite sua senha">
                                        </div>
                                        <button onclick="login()" class="btn btn-primary w-100">Login</button>

                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-link p-0 wl-auth-toggle" onclick="mostrarRecuperacao()">Redefinir senha</button>
                                        </div>
                                    </div>

                                    <div id="recover_panel" class="wl-hide">
                                        <h5 class="mb-3">Redefinir senha</h5>
                                        <div class="mb-3">
                                            <label for="redefineir" class="form-label">Usuario</label>
                                            <input id="redefineir" type="text" class="form-control" placeholder="Digite seu usuario">
                                        </div>
                                        <button onclick="recuperar_senha()" class="btn btn-outline-primary w-100">Enviar codigo</button>

                                        <div id="recover_confirm_group" class="wl-hide mt-3">
                                            <div class="mb-3">
                                                <label for="codigo_redefinir" class="form-label">Codigo recebido por e-mail</label>
                                                <input id="codigo_redefinir" type="text" class="form-control" maxlength="6" placeholder="Digite o codigo">
                                            </div>
                                            <div class="mb-3">
                                                <label for="nova_senha_redefinir" class="form-label">Nova senha</label>
                                                <input id="nova_senha_redefinir" type="password" class="form-control" placeholder="Digite a nova senha">
                                            </div>
                                            <button onclick="confirmar_recuperacao_senha()" class="btn btn-primary w-100">Confirmar redefinicao</button>
                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-link p-0 wl-auth-toggle" onclick="mostrarLogin()">Voltar para login</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="<?= base_url('public/assets/style/plugins/jquery/jquery.min.js') ?>"></script>
    <script>
        function mostrarMensagem(mensagem, tipo) {
            const el = document.getElementById('error');
            if (!mensagem) {
                el.classList.add('wl-hide');
                el.innerText = '';
                return;
            }

            el.classList.remove('alert-danger', 'alert-success');
            el.classList.add(tipo === 'success' ? 'alert-success' : 'alert-danger');
            el.innerText = mensagem;
            el.classList.remove('wl-hide');
        }

        function mostrarErro(mensagem) {
            mostrarMensagem(mensagem, 'error');
        }

        function mostrarSucesso(mensagem) {
            mostrarMensagem(mensagem, 'success');
        }

        function mostrarRecuperacao() {
            mostrarErro('');
            document.getElementById('login_panel').classList.add('wl-hide');
            document.getElementById('recover_panel').classList.remove('wl-hide');
            document.getElementById('recover_confirm_group').classList.add('wl-hide');
        }

        function mostrarLogin() {
            mostrarErro('');
            document.getElementById('recover_panel').classList.add('wl-hide');
            document.getElementById('login_panel').classList.remove('wl-hide');
            document.getElementById('recover_confirm_group').classList.add('wl-hide');
        }

        function recuperar_senha() {
            const nome = document.getElementById('redefineir').value.trim();
            if (nome === '') {
                mostrarErro('Preencha o nome');
                return;
            }

            $.ajax({
                url: '<?= base_url('public/recuperar_senha') ?>',
                type: 'POST',
                dataType: 'json',
                data: { nome: nome },
                success: function (response) {
                    if (response && response.ok) {
                        mostrarSucesso(response.mensagem || 'Codigo enviado.');
                        document.getElementById('recover_confirm_group').classList.remove('wl-hide');
                    } else {
                        var mensagem = (response && response.mensagem) ? response.mensagem : 'Nao foi possivel enviar o codigo.';
                        if (response && response.debug && response.debug.erro) {
                            mensagem += '\n\nDEBUG: ' + response.debug.erro;
                            if (response.debug.classe) {
                                mensagem += '\nClasse: ' + response.debug.classe;
                            }
                            if (response.debug.arquivo && response.debug.linha) {
                                mensagem += '\nLocal: ' + response.debug.arquivo + ':' + response.debug.linha;
                            }
                        }
                        mostrarErro(mensagem);
                    }
                },
                error: function () {
                    mostrarErro('Erro ao enviar solicitacao de redefinicao.');
                }
            });
        }

        function confirmar_recuperacao_senha() {
            const nome = document.getElementById('redefineir').value.trim();
            const codigo = document.getElementById('codigo_redefinir').value.trim();
            const novaSenha = document.getElementById('nova_senha_redefinir').value;

            if (nome === '' || codigo === '' || novaSenha === '') {
                mostrarErro('Preencha usuario, codigo e nova senha.');
                return;
            }

            $.ajax({
                url: '<?= base_url('public/recuperar_senha_confirmar') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    nome: nome,
                    codigo: codigo,
                    nova_senha: novaSenha
                },
                success: function (response) {
                    if (response && response.ok) {
                        mostrarSucesso(response.mensagem || 'Senha redefinida com sucesso.');
                        setTimeout(function () {
                            document.getElementById('codigo_redefinir').value = '';
                            document.getElementById('nova_senha_redefinir').value = '';
                            mostrarLogin();
                        }, 900);
                        return;
                    }

                    mostrarErro((response && response.mensagem) ? response.mensagem : 'Nao foi possivel redefinir a senha.');
                },
                error: function () {
                    mostrarErro('Erro ao confirmar redefinicao de senha.');
                }
            });
        }

        function login() {
            const nome = document.getElementById('nome').value;
            const senha = document.getElementById('senha').value;

            if (nome === '' || senha === '') {
                mostrarErro('Preencha senha e nome');
                return;
            }

            $.ajax({
                url: '<?= base_url('public/login') ?>',
                type: 'POST',
                dataType: 'json',
                data: { nome: nome, senha: senha },
                success: function (response) {
                    if (response.location) {
                        window.location.href = response.location;
                        return;
                    }

                    if (response.mensagem != null) {
                        mostrarErro(response.mensagem);
                    }
                },
                error: function () {
                    mostrarErro('Erro ao autenticar.');
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !document.getElementById('login_panel').classList.contains('wl-hide')) {
                login();
            }
        });
    </script>
</body>
</html>
