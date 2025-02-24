<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;


class Login extends BaseController
{
    /**
     * Ação para exibir a página de login.
     *
     * Esta função é responsável por exibir a página de login. Normalmente, ela renderiza uma view ou template
     * que contém o formulário de login.
     *
     * @return void
     */
    function index()
    {
        // Renderiza a view 'login', que geralmente contém o formulário de login.
        echo view('login');
    }

    /**
     * Ação de login do usuário.
     *
     * Esta função é responsável por lidar com a ação de login do usuário. Ela recebe os dados de nome e senha
     * fornecidos via requisição AJAX, realiza o processo de autenticação, cria uma sessão para o usuário autenticado
     * e redireciona para as páginas apropriadas com base na função do usuário.
     *
     * @return object
     */
    function login()
    {

        // Verifica se a requisição é AJAX.
        if ($this->request->isAJAX()) {

            // Obtém os valores de nome e senha do POST da requisição.
            $nome = service('request')->getPost('nome');
            $senha = service('request')->getPost('senha');

            // Codifica os valores de nome e senha utilizando a função de codificação Ferramentas::codificador.
            $nome = Ferramentas::codificador($nome);
            $senha = Ferramentas::codificador($senha);

            // Cria uma instância do modelo de Usuários.
            $db = new \App\Models\Usuarios();

            // Realiza uma consulta no banco de dados para encontrar dados do usuário.
            $db_data = $db->find();

            // Executa uma pesquisa no array de dados para encontrar um usuário com nome e senha correspondentes.
            $user = Ferramentas::array_pesquisa_mult($db_data, ['nome', 'senha'], [$nome, $senha]);

            // Verifica se o usuário foi encontrado.
            if (count($user) != 0) {

                // Verifica se o status do usuário é 'ativo'.
                if ($user['status'] == 'ativo') {
                    $array_niveis = array(
                        'Adicionar' => 'desenho_adicionar',
                        'Meus_Desenhos' => 'desenho_meus',
                        'Lista_De_Corte' => 'lista_corte',
                        'Lista_De_Corte_ADM' => 'lista_corte_adm',
                        'Subpasta' => 'subpasta',
                        'Desenhos_cortados' => 'desenhos_cortados',
                        'Tipo_De_Arquivo' => 'tipo_de_arquivo',
                        'Prioridade' => 'prioridade',
                        'Fialidade' => 'finalidade',
                        'Empresa' => 'empresa',
                        'Empreendimento' => 'empreendimento',
                        'Nível' => 'nivel',
                        'Usuario' => 'user_cadastrar',
                        'Relátorio' => 'relatorios',
                        'Lista_De_Corte_Cortador' => 'lista_tarefas',
                        'Processos' => 'processos'
                    );
                    
                    // Inicia a sessão.
                    
                    
                    session_start();

                    if (!filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP)) {
                        $allowed_ids = [21, 15, 1, 29, 8, 31]; // Array com os IDs permitidos
                        
                        if (!in_array($user['id'], $allowed_ids)) {
                            $this->logout();
                            return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
                        }
                    }
                    
                    // Cria uma instância do modelo de Função.
                    $db = new \App\Models\Nivel();

                    // Realiza uma consulta no banco de dados para encontrar dados de funções.
                    $db_data = $db->find();

                    // Define variáveis de sessão para o nome do usuário, ID do usuário e função do usuário.
                    $_SESSION['usuario_nome'] = $user['nome'];


                    $_SESSION['usuario'] = $user['id'];

                    // Obtém o nome da função do usuário e define na variável de sessão 'funcao'.
                   $_SESSION['funcao'] = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $user['nivel']), ['nome']));
                   $_SESSION['permissao'] = explode('-',  Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $user['nivel']), ['permissao'])));
                   $_SESSION['processos'] = explode('-',  Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $user['nivel']), ['processos'])));
                    // Com base na função do usuário, redireciona para páginas apropriadas.
                    if (in_array('all', $_SESSION['permissao']))
                        echo json_encode(['location' => base_url() . 'public/desenho_adicionar']);
                    else
                        echo json_encode(['location' => base_url() . 'public/' . Ferramentas::array_index($array_niveis, [Ferramentas::array_index(($_SESSION['permissao']), [0])])]);

                    die();
                } else {
                    return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
                }
            } else {
                return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
            }
        }
        return $this->response->setJSON(['ok' => 'false']);
    }

  function login_verificar_permissao(){

        // Verifica se a requisição é AJAX.
        if ($this->request->isAJAX()) {

            // Obtém os valores de nome e senha do POST da requisição.
            $nome = service('request')->getPost('nome');
            $senha = service('request')->getPost('senha');

            // Codifica os valores de nome e senha utilizando a função de codificação Ferramentas::codificador.
            $nome = Ferramentas::codificador($nome);
            $senha = Ferramentas::codificador($senha);

            // Cria uma instância do modelo de Usuários.
            $db = new \App\Models\Usuarios();

            // Realiza uma consulta no banco de dados para encontrar dados do usuário.
            $db_data = $db->find();

            // Executa uma pesquisa no array de dados para encontrar um usuário com nome e senha correspondentes.
            $user = Ferramentas::array_pesquisa_mult($db_data, ['nome', 'senha'], [$nome, $senha]);

            // Verifica se o usuário foi encontrado.
            if (count($user) != 0) {

                // Verifica se o status do usuário é 'ativo'.
                if ($user['status'] == 'ativo') {
                    $array_niveis = array(
                        'Adicionar' => 'desenho_adicionar',
                        'Meus_Desenhos' => 'desenho_meus',
                        'Lista_De_Corte' => 'lista_corte',
                        'Lista_De_Corte_ADM' => 'lista_corte_adm',
                        'Subpasta' => 'subpasta',
                        'Desenhos_cortados' => 'desenhos_cortados',
                        'Tipo_De_Arquivo' => 'tipo_de_arquivo',
                        'Prioridade' => 'prioridade',
                        'Fialidade' => 'finalidade',
                        'Empresa' => 'empresa',
                        'Empreendimento' => 'empreendimento',
                        'Nível' => 'nivel',
                        'Usuario' => 'user_cadastrar',
                        'Relátorio' => 'relatorios',
                        'Lista_De_Corte_Cortador' => 'lista_tarefas',
                        'Processos' => 'processos'
                    );
                    
                    // Inicia a sessão.
                    session_start();
                    // if(!filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP)){
                    //     if($user['id'] != 21 and $user['id'] != 15 and $user['id'] != 1){
                    //     $this->logout();
                    //     return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
                    //     }
                    // }
                    // Cria uma instância do modelo de Função.
                    $db = new \App\Models\Nivel();

                    // Realiza uma consulta no banco de dados para encontrar dados de funções.
                    $db_data = $db->find();

                    // Define variáveis de sessão para o nome do usuário, ID do usuário e função do usuário.

                   $permitido = explode('-',  Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($db_data, 'id', $user['nivel']), ['permissao'])));
                    // Com base na função do usuário, redireciona para páginas apropriadas.
                    if (in_array('all', $permitido) || in_array('Lista_De_Corte_ADM',$permitido) || in_array('lista_corte_adm',$permitido))
                    $_SESSION['usuario_permissao'] = $user['id'];
                    else{
                    $_SESSION['usuario_permissao'] = "";
                    return $this->response->setJSON(['ok' => 'false','mensagem' => 'Sem permissão precisa ser coordenador ou superior.']);
                    }
                    return $this->response->setJSON(['ok' => 'true']);
                } else {
                    return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
                }
            } else {
                return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Senha ou Nome errados']);
            }
        }
        return $this->response->setJSON(['ok' => 'false']);
    }




    /**
     * Realiza a ação de logout do usuário.
     *
     * Esta função encerra a sessão do usuário, redireciona para a página de login 
     * e encerra a execução do script.
     *
     * @return void
     */
    public static function logout()
    {
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Destroi a sessão do usuário.
        session_destroy();

        // Redireciona o usuário para a página de login (ou outra página desejada).
        header('Location: ' . base_url() . 'public/'); // Você pode personalizar a URL de redirecionamento aqui.

        // Encerra a execução do script.
        die();
    }

    /**
     * Verifica o status do login e a função do usuário.
     *
     * @param string $funcao A função que deve ser verificada para o usuário.
     * @return void
     *
     * Esta função verifica se o usuário está autenticado na sessão, se a função do usuário
     * corresponde à função fornecida e realiza ação de logout se alguma das verificações falhar.
     */
    public static function verifica_login(string $funcao)
    {
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $ok = true;

        // Verifica se o usuário está autenticado na sessão.
        if (Ferramentas::array_index($_SESSION, ['usuario']) == "") {
            $ok = false;
        }

        // Verifica se a função do usuário corresponde à função fornecida.
        if (Ferramentas::array_index($_SESSION, ['funcao']) == "") {
            $ok = false;
        } else if ($funcao != $_SESSION['funcao']) {
            $ok = false;
        }

        // Se alguma das verificações falhar, realiza uma ação de logout.
        if (!$ok) {
            self::logout();
        }
    }

    public static function verifica_permissao($permitido)
    {
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $ok = true;

        // Verifica se a função do usuário está entre as funções permitidas.
        if (empty($_SESSION['permissao']) || (!in_array($permitido, $_SESSION['permissao']) and (!in_array("all", $_SESSION['permissao']) and !in_array(str_replace(' ','_',$permitido), $_SESSION['permissao']))) ) {
            $ok = false;
        }


        // Se alguma das verificações falhar, realiza uma ação de logout.
        if (!$ok) {
           self::logout();
        }
    }

}