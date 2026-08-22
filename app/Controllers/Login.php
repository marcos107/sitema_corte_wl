<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use App\Libraries\NivelTelaInicial;
use Config\App;


class Login extends BaseController
{
    /**
     * AÃƒÂ§ÃƒÂ£o para exibir a pÃƒÂ¡gina de login.
     *
     * Esta funÃƒÂ§ÃƒÂ£o ÃƒÂ© responsÃƒÂ¡vel por exibir a pÃƒÂ¡gina de login. Normalmente, ela renderiza uma view ou template
     * que contÃƒÂ©m o formulÃƒÂ¡rio de login.
     *
     * @return void
     */
    function index()
    {
        if ($this->hostEhLoopback() && $this->request->isSecure()) {
            return redirect()->to($this->urlAtualHttpLocal());
        }

        // Renderiza a view 'login', que geralmente contém o formulário de login.
        echo view('login');
    }

    private function carregarDadosNivelUsuario(array $user): array
    {
        $nivelId = (int) ($user['nivel_id'] ?? 0);
        $nivelModel = new \App\Models\Nivel();
        $nivel = $nivelId > 0 ? ($nivelModel->find($nivelId) ?: []) : [];
        $contextoAcesso = $nivelModel->montarContextoAcesso($nivelId);

        $permissoes = $contextoAcesso['permissoes'] ?? [];
        $processosNomes = $contextoAcesso['processos_nomes'] ?? [];

        return [
            'nivel' => is_array($nivel) ? $nivel : [],
            'permissoes' => $permissoes,
            'processos_nomes' => $processosNomes,
            'processos_por_contexto' => $contextoAcesso['processos_por_contexto'] ?? [],
            'processos_ids_por_contexto' => $contextoAcesso['processos_ids_por_contexto'] ?? [],
            'origem_contexto' => $contextoAcesso['origem_contexto'] ?? [],
            'nivel_ids_contexto' => $contextoAcesso['nivel_ids'] ?? ($nivelId > 0 ? [$nivelId] : []),
        ];
    }

    private function definirSessaoUsuario(
        array $user,
        array $nivel,
        array $permissoes,
        array $processosNomes,
        array $processosPorContexto = [],
        array $processosIdsPorContexto = [],
        array $origemContexto = [],
        array $nivelIdsContexto = []
    ): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['funcao'] = $nivel['nome'] ?? '';
        $_SESSION['nivel_id'] = (int) ($user['nivel_id'] ?? 0);
        $_SESSION['permissao'] = $permissoes;
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario'] = $user['id'];
        $_SESSION['processos'] = $processosNomes;
        $_SESSION['processos_por_contexto'] = $processosPorContexto;
        $_SESSION['processos_ids_por_contexto'] = $processosIdsPorContexto;
        $_SESSION['origem_processos_contexto'] = $origemContexto;
        $_SESSION['nivel_ids_contexto'] = $nivelIdsContexto;
    }

    private function urlTelaInicial(?string $telaInicial): ?string
    {
        $telaInicial = trim((string) $telaInicial);
        if ($telaInicial === '') {
            return null;
        }

        $abaPainel = NivelTelaInicial::abaPainel($telaInicial);
        if ($abaPainel !== null) {
            return $this->urlPublica('public/painel_tarefas?aba=' . rawurlencode($abaPainel));
        }

        $mapa = [
            'desenho_adicionar' => 'desenho_adicionar',
            'subpasta' => 'subpasta',
            'tipo_de_arquivo' => 'tipo_de_arquivo',
            'prioridade' => 'prioridade',
            'finalidade' => 'finalidade',
            'empresa' => 'empresa',
            'empreendimento' => 'empreendimento',
            'nivel' => 'nivel',
            'usuario' => 'usuario',
            'relatorios' => 'relatorios',
            'logs_alteracoes' => 'logs_alteracoes',
            'processos' => 'processos',
        ];

        return isset($mapa[$telaInicial]) ? $this->urlPublica('public/' . $mapa[$telaInicial]) : null;
    }

    private function urlInicialPadrao(array $permissoes): string
    {
        foreach (NivelTelaInicial::definicoes() as $definicao) {
            $key = (string) ($definicao['key'] ?? '');
            if ($key === '' || !NivelTelaInicial::permitida($key, $permissoes)) {
                continue;
            }

            $url = $this->urlTelaInicial($key);
            if ($url !== null) {
                return $url;
            }
        }

        return $this->urlPublica('public/desenho_adicionar');
    }

    private function hostNormalizado(): string
    {
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '') {
            return '';
        }

        return strtolower(trim((string) preg_replace('/:\d+$/', '', $host), '[]'));
    }

    private function hostEhLoopback(): bool
    {
        return in_array($this->hostNormalizado(), ['localhost', '127.0.0.1', '::1'], true);
    }

    private function hostEhLocal(): bool
    {
        $host = $this->hostNormalizado();
        if ($host === '') {
            return false;
        }

        if ($this->hostEhLoopback()) {
            return true;
        }

        return filter_var($host, FILTER_VALIDATE_IP) !== false;
    }

    private function urlPublica(string $path): string
    {
        return base_url($path);
    }

    private function urlAtualHttpLocal(): string
    {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');

        return rtrim(base_url(), '/') . '/' . ltrim($requestUri, '/');
    }

    private function requestEhPost(): bool
    {
        return strtolower((string) $this->request->getMethod()) === 'post';
    }

    /**
     * AÃƒÂ§ÃƒÂ£o de login do usuÃƒÂ¡rio.
     *
     * Esta funÃƒÂ§ÃƒÂ£o ÃƒÂ© responsÃƒÂ¡vel por lidar com a aÃƒÂ§ÃƒÂ£o de login do usuÃƒÂ¡rio. Ela recebe os dados de nome e senha
     * fornecidos via requisiÃƒÂ§ÃƒÂ£o AJAX, realiza o processo de autenticaÃƒÂ§ÃƒÂ£o, cria uma sessÃƒÂ£o para o usuÃƒÂ¡rio autenticado
     * e redireciona para as pÃƒÂ¡ginas apropriadas com base na funÃƒÂ§ÃƒÂ£o do usuÃƒÂ¡rio.
     *
     * @return object
     */
    public function login()
    {
        // SÃƒÂ³ aceita POST
        if (!$this->requestEhPost()) {
            return $this->response->setJSON(['ok' => false]);
        }

        // 1) Recebe e codifica credenciais
        $post = $this->request->getPost(['nome', 'senha']);
        $nome = $post['nome'];
        $senha = $post['senha'];

        // 2) Busca usuÃƒÂ¡rio exato
        $usuariosModel = new \App\Models\Usuarios();
        $user = $usuariosModel
            ->where('nome', $nome)
            ->where('senha', $senha)
            ->first();

        // 3) Verifica existÃƒÂªncia e status
        if (!$user || $user['status'] !== 'ativo') {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Senha ou Nome errados'
            ]);
        }

        // 4) Se nÃƒÂ£o for um host IP, checa acesso_remoto
        if (!$this->hostEhLocal() && (int) ($user['acesso_remoto'] ?? 0) !== 1) {
          //  $this->logout();
            return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Sem permissÃƒÂ£o para acesso remoto']);
        }


        // 5) Carrega relaÃƒÂ§ÃƒÂµes de nÃƒÂ­vel, permissÃƒÂµes e processos
        $contextoNivel = $this->carregarDadosNivelUsuario($user);
        $nivel = $contextoNivel['nivel'];
        $permissoes = $contextoNivel['permissoes'];
        $processosNomes = $contextoNivel['processos_nomes'];
        $processosPorContexto = $contextoNivel['processos_por_contexto'] ?? [];
        $processosIdsPorContexto = $contextoNivel['processos_ids_por_contexto'] ?? [];
        $origemContexto = $contextoNivel['origem_contexto'] ?? [];
        $nivelIdsContexto = $contextoNivel['nivel_ids_contexto'] ?? [];



        // 6) Seta sessÃƒÂ£o 
        $this->definirSessaoUsuario(
            $user,
            $nivel,
            $permissoes,
            $processosNomes,
            $processosPorContexto,
            $processosIdsPorContexto,
            $origemContexto,
            $nivelIdsContexto
        );
        // 7) Mapeamento de rotas por permissÃƒÂ£o
        $map = [
            'desenho_adicionar' => 'desenho_adicionar',
            'desenho_meus' => 'desenho_meus',
            'lista_corte' => 'Lista_De_Corte_Cortador',
            'lista_corte_adm' => 'lista_corte_adm',
            'subpasta' => 'subpasta',
            'Desenhos_cortados' => 'desenhos_cortados',
            'tipo_de_arquivo' => 'tipo_de_arquivo',
            'prioridade' => 'prioridade',
            'finalidade' => 'finalidade',
            'empresa' => 'empresa',
            'empreendimento' => 'empreendimento',
            'nivel' => 'nivel',
            'user_cadastrar' => 'user_cadastrar',
            'relatorios' => 'relatorios',
            'logs_alteracoes' => 'logs_alteracoes',
            'Lista_De_Corte_Cortador' => 'lista_tarefas',
            'processos' => 'processos',
        ];
          
        // 8) Define URL de redirecionamento
        $url = null;
        $telaInicialNivel = (string) ($nivel['tela_inicial'] ?? '');
        if ($telaInicialNivel !== '' && NivelTelaInicial::permitida($telaInicialNivel, $permissoes)) {
            $url = $this->urlTelaInicial($telaInicialNivel);
        }

        if ($url === null) {
            $url = $this->urlInicialPadrao($permissoes);
        }

        return $this->response->setJSON(['location' => $url]);
    }


    function login_verificar_permissao()
    {

        // Verifica se a requisiÃƒÂ§ÃƒÂ£o ÃƒÂ© POST.
        if ($this->requestEhPost()) {
            // 1) Recebe e codifica credenciais
            $post = $this->request->getPost(['nome', 'senha']);
            $nome = $post['nome'];
            $senha = $post['senha'];

            // 2) Busca usuÃƒÂ¡rio exato
            $usuariosModel = new \App\Models\Usuarios();
            $user = $usuariosModel
                ->where('nome', $nome)
                ->where('senha', $senha)
                ->first();

            // 3) Verifica existÃƒÂªncia e status
            if (!$user || $user['status'] !== 'ativo') {
                return $this->response->setJSON([
                    'ok' => false,
                    'mensagem' => 'Senha ou Nome errados'
                ]);
            }

            // 4) Se nÃƒÂ£o for um host IP, checa acesso_remoto
            if (!$this->hostEhLocal() && (int) ($user['acesso_remoto'] ?? 0) !== 1) {
                $this->logout();
                return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
            }


            // 5) Carrega relaÃƒÂ§ÃƒÂµes de nÃƒÂ­vel, permissÃƒÂµes e processos
            $contextoNivel = $this->carregarDadosNivelUsuario($user);
            $permitido = $contextoNivel['permissoes'];



            // 6) Seta sessÃƒÂ£o 
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $processosModel = new \App\Models\Processos();


            $map = [
                'desenho_adicionar' => 'desenho_adicionar',
                'desenho_meus' => 'desenho_meus',
                'lista_corte' => 'lista_corte',
                'lista_corte_adm' => 'lista_corte_adm',
                'subpasta' => 'subpasta',
                'desenhos_cortados' => 'desenhos_cortados',
                'tipo_de_arquivo' => 'tipo_de_arquivo',
                'prioridade' => 'prioridade',
                'finalidade' => 'finalidade',
                'empresa' => 'empresa',
                'empreendimento' => 'empreendimento',
                'nivel' => 'nivel',
                'user_cadastrar' => 'user_cadastrar',
                'relatorios' => 'relatorios',
                'logs_alteracoes' => 'logs_alteracoes',
                'Lista_De_Corte_Cortador' => 'lista_tarefas',
                'processos' => 'processos',
            ];

            // Inicia a sessÃƒÂ£o.

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }



          
            // Cria uma instÃƒÂ¢ncia do modelo de FunÃƒÂ§ÃƒÂ£o.
         

            $permitido = $contextoNivel['permissoes'];
            // Com base na funÃƒÂ§ÃƒÂ£o do usuÃƒÂ¡rio, redireciona para pÃƒÂ¡ginas apropriadas.
            if (in_array('all', $permitido) || in_array('Lista_De_Corte_ADM', $permitido) || in_array('lista_corte_adm', $permitido))
                $_SESSION['usuario_permissao'] = $user['id'];
            else {
                $_SESSION['usuario_permissao'] = "";
                return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Sem permissÃƒÂ£o precisa ser coordenador ou superior.']);
            }
            return $this->response->setJSON(['ok' => 'true']);
        } else {
            return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
        }


        return $this->response->setJSON(['ok' => 'false']);
    }




    /**
     * Realiza a aÃƒÂ§ÃƒÂ£o de logout do usuÃƒÂ¡rio.
     *
     * Esta funÃƒÂ§ÃƒÂ£o encerra a sessÃƒÂ£o do usuÃƒÂ¡rio, redireciona para a pÃƒÂ¡gina de login 
     * e encerra a execuÃƒÂ§ÃƒÂ£o do script.
     *
     * @return void
     */
    public static function logout()
    {

        // Inicia a sessÃƒÂ£o, se ainda nÃƒÂ£o estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Destroi a sessÃƒÂ£o do usuÃƒÂ¡rio.
        session_destroy();

        // Redireciona o usuÃƒÂ¡rio para a pÃƒÂ¡gina de login (ou outra pÃƒÂ¡gina desejada).
        header('Location: ' . base_url() . 'public/'); // VocÃƒÂª pode personalizar a URL de redirecionamento aqui.

        // Encerra a execuÃƒÂ§ÃƒÂ£o do script.
        die();
    }

    /**
     * Verifica o status do login e a funÃƒÂ§ÃƒÂ£o do usuÃƒÂ¡rio.
     *
     * @param string $funcao A funÃƒÂ§ÃƒÂ£o que deve ser verificada para o usuÃƒÂ¡rio.
     * @return void
     *
     * Esta funÃƒÂ§ÃƒÂ£o verifica se o usuÃƒÂ¡rio estÃƒÂ¡ autenticado na sessÃƒÂ£o, se a funÃƒÂ§ÃƒÂ£o do usuÃƒÂ¡rio
     * corresponde ÃƒÂ  funÃƒÂ§ÃƒÂ£o fornecida e realiza aÃƒÂ§ÃƒÂ£o de logout se alguma das verificaÃƒÂ§ÃƒÂµes falhar.
     */
    public static function verifica_login(string $funcao)
    {
        // Inicia a sessÃƒÂ£o, se ainda nÃƒÂ£o estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $ok = true;

        // Verifica se o usuÃƒÂ¡rio estÃƒÂ¡ autenticado na sessÃƒÂ£o.
        if (Ferramentas::array_index($_SESSION, ['usuario']) == "") {
            $ok = false;
        }

        // Verifica se a funÃƒÂ§ÃƒÂ£o do usuÃƒÂ¡rio corresponde ÃƒÂ  funÃƒÂ§ÃƒÂ£o fornecida.
        if (Ferramentas::array_index($_SESSION, ['funcao']) == "") {
            $ok = false;
        } else if ($funcao != $_SESSION['funcao']) {
            $ok = false;
        }

        // Se alguma das verificaÃƒÂ§ÃƒÂµes falhar, realiza uma aÃƒÂ§ÃƒÂ£o de logout.
        if (!$ok) {
            self::logout();
        }
    }

    public static function verifica_permissao($permitido)
    {
        // Inicia a sessÃƒÂ£o, se ainda nÃƒÂ£o estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $ok = true;

        // Verifica se a funÃƒÂ§ÃƒÂ£o do usuÃƒÂ¡rio estÃƒÂ¡ entre as funÃƒÂ§ÃƒÂµes permitidas.
        if (empty($_SESSION['permissao']) || (!in_array($permitido, $_SESSION['permissao']) and (!in_array("all", $_SESSION['permissao']) and !in_array(str_replace(' ', '_', $permitido), $_SESSION['permissao'])))) {
            $ok = false;
        }


        // Se alguma das verificaÃƒÂ§ÃƒÂµes falhar, realiza uma aÃƒÂ§ÃƒÂ£o de logout.
        if (!$ok) {
            self::logout();
        }
    }

    private function chaveRecuperacaoSenha(int $usuarioId): string
    {
        return 'wl_recuperacao_senha_' . $usuarioId;
    }

    private function mascararParteEmail(string $texto, int $inicio = 2, int $fim = 2): string
    {
        $texto = trim($texto);
        $len = strlen($texto);

        if ($len <= 2) {
            return str_repeat('*', $len);
        }

        if ($inicio + $fim >= $len) {
            return substr($texto, 0, 1) . str_repeat('*', max(1, $len - 2)) . substr($texto, -1);
        }

        return substr($texto, 0, $inicio)
            . str_repeat('*', $len - $inicio - $fim)
            . substr($texto, -$fim);
    }

    private function mascararEmail(string $email): string
    {
        $email = trim($email);
        if ($email === '' || strpos($email, '@') === false) {
            return $this->mascararParteEmail($email, 2, 2);
        }

        [$usuario, $dominioCompleto] = explode('@', $email, 2);
        $partesDominio = explode('.', $dominioCompleto);
        $dominioBase = array_shift($partesDominio);
        $sufixoDominio = implode('.', $partesDominio);

        $usuarioMascarado = $this->mascararParteEmail($usuario, 2, 2);
        $dominioMascarado = $this->mascararParteEmail((string) $dominioBase, 2, 2);

        if ($sufixoDominio !== '') {
            return $usuarioMascarado . '@' . $dominioMascarado . '.' . $sufixoDominio;
        }

        return $usuarioMascarado . '@' . $dominioMascarado;
    }

    public function recuperar_senha()
    {
        if (!$this->requestEhPost()) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.'
            ]);
        }

        $nome = trim((string) service('request')->getPost('nome'));
        if ($nome === '') {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Informe o usuario.'
            ]);
        }

        $usuariosModel = new \App\Models\Usuarios();
        $usuario = $usuariosModel
            ->where('nome', $nome)
            ->where('status', 'ativo')
            ->first();

        $mensagemPadrao = 'Se o usuario existir e possuir e-mail vinculado, um codigo sera enviado.';
        if (!$usuario || empty($usuario['email'])) {
            return $this->response->setJSON([
                'ok' => true,
                'mensagem' => $mensagemPadrao
            ]);
        }

        $codigo = (string) random_int(100000, 999999);
        $cache = \Config\Services::cache();
        $cacheKey = $this->chaveRecuperacaoSenha((int) $usuario['id']);
        $cache->save($cacheKey, [
            'hash' => hash('sha256', $codigo),
            'expira_em' => time() + 900
        ], 900);

        $debugHabilitado = defined('ENVIRONMENT') && ENVIRONMENT !== 'production';

        try {
            $enviado = Ferramentas::envia_email([
                'to' => trim((string) $usuario['email']),
                'subject' => 'Codigo de redefinicao de senha',
                'message' => '
                    <h2>Redefinicao de senha</h2>
                    <p>Usuario: <b>' . htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8') . '</b></p>
                    <p>Seu codigo de verificacao e:</p>
                    <p style="font-size: 22px; font-weight: bold; letter-spacing: 4px;">' . $codigo . '</p>
                    <p>Validade: 15 minutos.</p>
                    <hr>
                    <small>Enviado em ' . date('d/m/Y H:i') . '</small>
                '
            ]);

            if (!$enviado) {
                throw new \RuntimeException('envia_email retornou false sem excecao');
            }
        } catch (\Throwable $e) {
            $cache->delete($cacheKey);
            $contextoDebug = [
                'classe' => get_class($e),
                'erro' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'host' => $_SERVER['HTTP_HOST'] ?? '',
                'usuario_id' => (int) $usuario['id'],
                'email_destino' => trim((string) $usuario['email']),
            ];

            log_message(
                'error',
                'Falha ao enviar codigo de recuperacao de senha: {contexto}',
                ['contexto' => json_encode($contextoDebug, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]
            );

            $retorno = [
                'ok' => false,
                'mensagem' => 'Nao foi possivel enviar o codigo agora. Tente novamente em instantes.'
            ];

            if ($debugHabilitado) {
                $retorno['debug'] = [
                    'erro' => $contextoDebug['erro'],
                    'classe' => $contextoDebug['classe'],
                    'arquivo' => basename((string) $contextoDebug['arquivo']),
                    'linha' => $contextoDebug['linha'],
                    'host' => $contextoDebug['host'],
                ];
            }

            return $this->response->setJSON($retorno);
        }

        $emailMascarado = $this->mascararEmail(trim((string) $usuario['email']));

        return $this->response->setJSON([
            'ok' => true,
            'mensagem' => 'Codigo enviado para o e-mail: ' . $emailMascarado,
            'email_mascarado' => $emailMascarado
        ]);
    }

    public function recuperar_senha_confirmar()
    {
        if (!$this->requestEhPost()) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Requisicao invalida.'
            ]);
        }

        $nome = trim((string) service('request')->getPost('nome'));
        $codigo = preg_replace('/\D/', '', (string) service('request')->getPost('codigo'));
        $novaSenha = (string) service('request')->getPost('nova_senha');

        if ($nome === '' || $codigo === '' || $novaSenha === '') {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Informe usuario, codigo e nova senha.'
            ]);
        }

        if (strlen($novaSenha) < 3 || strlen($novaSenha) > 50) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'A nova senha deve ter entre 3 e 50 caracteres.'
            ]);
        }

        if (Ferramentas::codificador($novaSenha) === '') {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'A nova senha possui caracteres nao permitidos.'
            ]);
        }

        $usuariosModel = new \App\Models\Usuarios();
        $usuario = $usuariosModel
            ->where('nome', $nome)
            ->where('status', 'ativo')
            ->first();

        if (!$usuario) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Codigo invalido ou expirado.'
            ]);
        }

        $cache = \Config\Services::cache();
        $cacheKey = $this->chaveRecuperacaoSenha((int) $usuario['id']);
        $dadosRecuperacao = $cache->get($cacheKey);

        if (!is_array($dadosRecuperacao)) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Codigo invalido ou expirado.'
            ]);
        }

        $expiraEm = (int) ($dadosRecuperacao['expira_em'] ?? 0);
        if ($expiraEm < time()) {
            $cache->delete($cacheKey);
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Codigo expirado. Solicite um novo codigo.'
            ]);
        }

        $hashInformado = hash('sha256', $codigo);
        $hashSalvo = (string) ($dadosRecuperacao['hash'] ?? '');
        if ($hashSalvo === '' || !hash_equals($hashSalvo, $hashInformado)) {
            return $this->response->setJSON([
                'ok' => false,
                'mensagem' => 'Codigo invalido ou expirado.'
            ]);
        }

        $usuariosModel->update((int) $usuario['id'], [
            'senha' => $novaSenha
        ]);
        $cache->delete($cacheKey);

        return $this->response->setJSON([
            'ok' => true,
            'mensagem' => 'Senha redefinida com sucesso.'
        ]);
    }



}
