<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class ListaCortePost extends Ferramentas
{

    /**
     * Cancela o processo de corte de um desenho.
     *
     * Esta função é acionada por uma requisição AJAX para cancelar o corte de um desenho.
     * Ela atualiza o status do corte para 'cancelado' e redefine o status do desenho como 'corte'.
     *
     */
    function cancelar_corte()
    {
        if ($this->request->isAJAX()) {
            $id = service('request')->getPost('id');
            session_start();

            // Cria uma instância do modelo de dados 'Corte'
            $corte = new \App\Models\Corte();

            // Busca os dados de corte no banco de dados
            $corte_data = $corte->find();

            // Obtém o ID do corte que corresponde ao desenho em 'inicio'
            $id_corte = Ferramentas::array_index(Ferramentas::array_pesquisa_mult($corte_data, ['id_desenho', 'status'], [$_SESSION["lista"][$id], 'inicio']), ['id']);

            // Define os dados a serem atualizados para cancelar o corte
            $update = [
                'cortador_fim' => $_SESSION["usuario"],
                'data_fim' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                'status' => 'cancelado'
            ];

            // Atualiza o registro de corte com o status 'cancelado'
            $corte->update($id_corte, $update);

            // Cria uma instância do modelo de dados 'Desenhos'
            $desenho = new \App\Models\Desenhos();

            // Define os dados a serem atualizados para definir o status como 'corte'
            $updat = [
                'status' => 'corte'
            ];

            // Atualiza o registro de desenho para o status 'corte'
            $desenho->update($_SESSION["lista"][$id], $updat);

            // Prepara uma resposta com sucesso
            $data = [
                "ok" => true
            ];

            // Retorna a resposta no formato JSON
            return $this->response->setJSON($data);

        }
    }


    /**
     * Função lista_corte_adm()
     *
     * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os usuários com status de 'corte' ou 'cortando' em uma tabela.
     *
     * Retorna um JSON contendo a lista de usuários com status de 'corte' ou 'cortando'.
     */
    function lista_corte_adm() //rece um post via ajax pedindo para listar os usuarios
    {
        if ($this->request->isAJAX()) {
            session_start();
            // Inicialização de objetos para acessar tabelas do banco de dados
            $desenhos = new \App\Models\Desenhos();
            $prioridade = new \App\Models\Prioridade();
            $finalidade = new \App\Models\Finalidade();
            $empresa = new \App\Models\Empresa();
            $empreendimento = new \App\Models\Empreendimentos();
            $usuario = new \App\Models\Usuarios();
            // Recupera dados das tabelas do banco de dados
            $prioridade_data = $prioridade->find();
            $finalidade_data = $finalidade->find();
            $empresa_data = $empresa->find();
            $empreendimento_data = $empreendimento->find();
            $desenhos_data = $desenhos->find();
            $usuario_data = $usuario->find();

            $check = service('request')->getPost('check'); // Obtém a informação POST fornecida via AJAX para listar usuários ativos


            $lista = "";
            $id_temp = 0;
            $lista_ids = array();
            $lista_completa = array();
            $alteracao = new \App\Models\Alteracoes();
            $alteracao_data = $alteracao->where('item', 'som_corte')
                ->orderBy('id', 'DESC')
                ->first();

            if ($check != null) {


                if ($alteracao_data) {
                    if ($check != $alteracao_data["depois"]) {

                        $data = [
                            "individuo" => $_SESSION["usuario"],
                            "id_item" => $alteracao_data["id"],
                            "antes" => $alteracao_data["depois"],
                            "depois" => $check,
                            "item" => "som_corte",
                            "info_mais" => "se vai sari som para o cortador",
                            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

                        ];
                        $alteracao->insert($data);
                    }

                } else {
                    $data = [
                        "individuo" => $_SESSION["usuario"],
                        "id_item" => "",
                        "antes" => "",
                        "depois" => $check,
                        "item" => "som_corte",
                        "info_mais" => "se vai sari som para o cortador",
                        "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

                    ];
                    $alteracao->insert($data);
                }
            } else {
                $check = $alteracao_data["depois"];
            }


            // Itera sobre os dados de desenhos para criar a lista
            foreach ($desenhos_data as $key => $value) {
                $tags = explode('/', Ferramentas::decodificador($value['caminho']));
                // Remover os índices de 0 a 5
                $tags = array_slice($tags, 6);

                // Remover o último elemento
                unset($tags[count($tags) - 1]);
                $tags = implode(" - ", $tags);
                if (Ferramentas::decodificador($value['status']) == "corte" || Ferramentas::decodificador($value['status']) == 'cortando') {
                    $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

                    // Monta a linha da tabela com os dados do usuário
                    if (Ferramentas::decodificador($value['status']) == 'corte') {
                        $lista .= '<tr><td onclick="prio_modal(' . $id_temp . ')" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>';
                    } else {
                        $lista .= '<tr><td bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>';
                    }
                    $lista .= '
      

       
          <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
       
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td>
       <td>' . $tags . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::decodificador($value['status'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add'])) . '</td>
      ';
                    if (Ferramentas::decodificador($value['status']) == 'corte') {
                        $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="apagar(' . $id_temp . ')"> Apagar </button></td> </tr>';
                    } else if (Ferramentas::decodificador($value['status']) == 'cortando') {
                        $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="cancelar_corte(' . $id_temp . ')"> Cancelar corte </button></td> </tr>';
                    } else {
                        $lista .= '<td></td></tr>';
                    }
                    // Prepara dados do usuário para armazenamento em arrays
                    $value['nome'] = Ferramentas::decodificador($value['nome']);
                    $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
                    $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
                    $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
                    $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
                    $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
                    $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);

                    $lista_ids[$id_temp] = $value['id'];
                    $value['id'] = $id_temp;
                    $lista_completa[$id_temp] = $value;
                    $id_temp++;
                }
            }

            // Inicializa a sessão e armazena as listas

            $_SESSION["lista"] = $lista_ids;
            $_SESSION["lista_completa"] = $lista_completa;
            $_SESSION["lista_primordial"] = $lista_completa;

            // Resposta do AJAX que retorna a lista de usuários
            $data = [
                "lista" => $lista,
                'check' => $check
            ];

            return $this->response->setJSON($data);
        }
    }

        /**
    * Confirma o corte de um desenho e renomeia o arquivo cortado.
    *
    * Esta função é responsável por confirmar o corte de um desenho, renomear o arquivo cortado, atualizar o status do desenho e do processo de corte no banco de dados.

    * return Um array com informações sobre o resultado do processo:
    * - 'ok' (bool): Indica se o corte foi bem-sucedido (true) ou não (false).
    * - 'mensagem' (string): Uma mensagem descritiva do resultado.
    * - 'caminho_novo' (string): O caminho para o arquivo cortado renomeado.
    * - 'caminho_antigo' (string): O caminho para o arquivo original.
    */
    function confirmar_corte()
    {
        if ($this->request->isAJAX()) {
            session_start();
            // $_SESSION['confirmar_corte_proc'] = isset ($_SESSION['confirmar_corte_proc']) ? $_SESSION['confirmar_corte_proc'] : FALSE;
            // if ($_SESSION['confirmar_corte_proc']) {
            //     return;
            //   } else {
            //     $_SESSION['confirmar_corte_proc'] = TRUE;
            //   }
            // Obtém o ID do desenho a ser cortado a partir dos dados da requisição AJAX
            $id = service('request')->getPost('id');
            $id = $_SESSION["lista"][$id];
            $data = [];
            $desenho = new \App\Models\Desenhos();

            // Busca os dados do desenho no banco de dados
            $desenho_data = $desenho->find();

            // Obtém o status do desenho
            $status = Ferramentas::array_index(Ferramentas::array_pesquisa($desenho_data, 'id', $id), ['status']);

            if (Ferramentas::decodificador($status) == "corte" || Ferramentas::decodificador($status) == "cortando") {

                // Se o status for "corte" ou "cortando", continua o processamento
                $caminho = Ferramentas::array_index(Ferramentas::array_pesquisa($desenho_data, 'id', $id), ['caminho']);
                $nome = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($desenho_data, 'id', $id), ['nome']));
           
                $ultima_barra_invertida = strrpos($caminho, 'i061n');
          
                // Dividir a string em duas partes
                $caminho_diretorio = substr($caminho, 0, $ultima_barra_invertida);
                $nome_arquivo = substr($caminho, $ultima_barra_invertida);
          
                // Criar o array resultante
                $array_resultante = [$caminho_diretorio, $nome_arquivo];
          
                $caminho = str_replace(["ci083ni061n", "wli074ndesenhos", "i061n"], ["c:/", "wl_desenhos", "/"], $array_resultante[0]) . '/' . Ferramentas::decodificador($array_resultante[1]);
                $caminho = str_replace("//", "/", $caminho);

                // Obtém a extensão do arquivo a partir do nome
                $extencao = '.' . Ferramentas::get_type_file($nome);

                // Remove a extensão do nome do arquivo e atualiza o caminho
                $caminho = str_replace($nome, '', $caminho);
                $nome = str_replace('.' . Ferramentas::get_type_file($nome), '', $nome);


                // Gera um novo nome de arquivo único
                do {
                    $radom = rand(1000, 9999);
                    $novo_nome = 'cortado_' . date('d_m_Y_') . Ferramentas::remove_id_file($nome) . '_' . $radom . "_" . $extencao;
                } while (file_exists($caminho . $novo_nome));

                if (!file_exists($caminho . $nome . $extencao)) {
                    // Se o arquivo original não existe, retorna uma resposta com erro
                    $data = [
                        "ok" => false,
                        'mensagem' => 'arquivo não exsite.',
                        'local' => $caminho . '  ' . $nome . $extencao
                    ];
                    //return $this->response->setJSON($data);
                }

                if (!isset($data["mensagem"])) {
                    // Se não houver mensagens de erro, tenta renomear o arquivo
                    // Move o arquivo original com o novo nome
                    if (!rename($caminho . $nome . $extencao, $caminho . $novo_nome)) {
                        $data = [
                            "ok" => false,
                            'mensagem' => 'não conseguiu trasferir o arquivo.'
                        ];
                        // return $this->response->setJSON($data);
                    }
                }


                // Atualiza os registros no banco de dados
                $corte = new \App\Models\Corte();
                $corte_data = $corte->find();

                // Atualiza os dados do corte
                $update = [
                    'cortador_fim' => $_SESSION["usuario"],
                    'data_fim' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                    'status' => 'finalizado',
                    'nome' => Ferramentas::codificador($novo_nome)
                ];

                // Pesquise os dados de corte
                $registro_encontrado = Ferramentas::array_index(Ferramentas::array_pesquisa_mult($corte_data, ['id_desenho', 'status'], [$id, 'inicio']), ['id']);

                if ($registro_encontrado) {

                    $corte->update($registro_encontrado, $update);
                } else {
                    // Insere um novo registro se não encontrado
                    $insert_data = array_merge($update, [
                        'id_desenho' => $id,
                        'cortador' => $_SESSION["usuario"],
                        'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                        'ip' => Ferramentas::codificador($_SERVER['REMOTE_ADDR']),

                    ]);
                    $corte->insert($insert_data);
                }



                // Atualiza os dados do desenho
                $updat = [
                    'cortador' => $_SESSION["usuario"],
                    'status' => 'cortado',
                    // 'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                    'nome' => Ferramentas::codificador($novo_nome),
                    'caminho' => Ferramentas::codificador($caminho . $novo_nome)

                ];
                $desenho->update($id, $updat);

                if (!isset($data["mensagem"])) {
                    // Se não houver mensagens de erro, retorna uma resposta bem-sucedida
                    $data = [
                        "ok" => true,
                        '1' => $caminho . '    ' . $novo_nome,
                        '2' => $caminho . '    ' . $nome . '.' . $extencao
                    ];
                }
                $_SESSION['confirmar_corte_proc'] = false;


                return $this->response->setJSON($data);

                // deu certo
            } else {
                // Se o status não for "corte" ou "cortando", retorna uma resposta com erro
                $data = [
                    "ok" => false
                ];
                $_SESSION['confirmar_corte_proc'] = false;

                return $this->response->setJSON($data);
                //desenho ja cortado 
            }


        }
    }


    /**
     * Atualiza a prioridade dos desenhos com base em regras específicas.
     *
     * Esta função calcula e atualiza a prioridade dos desenhos com base em critérios como status, data de envio e ordem de prioridade.
     *
     * Parâmetros:
     * - Nenhum parâmetro é necessário, pois a função obtém dados do banco de dados.
     *
     * Retorna:
     * - Um array contendo informações sobre as atualizações de prioridade realizadas.
     */
    function atualiza_prio()
    {
        // Inicialização de modelos de banco de dados
        $desenhos = new \App\Models\Desenhos();
        $prioridade = new \App\Models\Prioridade();
        $lista_temp = new \App\Models\Historico_desenhos;

        // Obtém dados relevantes
        $lista_temp_data = $lista_temp->find();
        $prioridade_data = $prioridade->find();
        $desenhos_data = $desenhos->find();
        $diasPassados = array();

        // Loop através dos desenhos
        foreach ($desenhos_data as $key => $value) { // Criação de uma lista de desenhos para análise.

            // Verifica se o status do desenho é "corte".
            if (Ferramentas::decodificador($value['status']) == "corte") {
                // Obter o timestamp da data atual.
                $dataAtualTimestamp = time();

                // Inicialização de variáveis para o cálculo da prioridade.
                $antigaData_mod = 0;

                // Obtém a data de modificação anterior do desenho.
                $arrayPesquisaResult = Ferramentas::array_pesquisa_mult_all($lista_temp_data, ['id_desenhos'], [$value['id']]);

                // Obtenha o último elemento do array resultante
                $endResult = end($arrayPesquisaResult);

                // Extraia o valor desejado
                $dias_anterior = substr(Ferramentas::decodificador(Ferramentas::array_index($endResult, ['data_hora_mod'])), 0, 10);
                $po = $dias_anterior;
                // Verifica se não há data anterior registrada.
                if ($dias_anterior == "") {
                    $antigaData_mod = -1;
                } else {
                    // Calcula a diferença de dias entre a data anterior e a data atual.
                    $dias_anterior = strtotime(str_replace('/', '-', $dias_anterior));
                    $diferenca = $dataAtualTimestamp - $dias_anterior;
                    $antigaData_mod = intval(floor($diferenca / (60 * 60 * 24)) / 3);
                }

                // Obtém a data de envio original do desenho.
                $antigaData = substr(Ferramentas::decodificador($value['data_hora_add']), 0, 10); // Data antiga no formato dd/mm/yyyy

                // Converte a data antiga para um formato compatível (timestamp).
                $antigaDataTimestamp = strtotime(str_replace('/', '-', $antigaData));



                // Calcula a diferença em segundos entre a data atual e a data antiga.
                $diferencaSegundos = $dataAtualTimestamp - $antigaDataTimestamp;

                // Converte a diferença de segundos em dias.
                $dias = intval(floor($diferencaSegundos / (60 * 60 * 24)) / 3);

                // Obtém a prioridade do desenho.
                $prio = intval(Ferramentas::array_index(Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']), ['ordem']));

                // Constrói uma informação de log das atualizações.
                //$diasPassados[] = [$prio . ' ' . $value['id'] . ' ' . $dias . ' ' . $antigaData_mod . " " . $po, end(Ferramentas::array_pesquisa_mult_all($lista_temp_data, ['id_desenhos'], [$value['id']]))];
                $diasPassados[] = [];
                if ($antigaData_mod != -1) {
                    if ($antigaData_mod != 0) {
                        if ($prio <= $antigaData_mod) {
                            // Atualiza a prioridade para o valor de prioridade máxima.
                            $updat = [
                                'prioridade' => Ferramentas::array_pesquisa($prioridade_data, 'ordem', '1')['id']
                            ];
                            $desenhos->update($value['id'], $updat);
                        } else if ($antigaData_mod != 0) {
                            $num = 0;
                            do {
                                $nova_prio = $prio - $antigaData_mod - $num;
                                $num++;
                            } while (Ferramentas::array_pesquisa($prioridade_data, 'ordem', $nova_prio)['status'] != 'ativo');

                            // Atualiza a prioridade com base no novo cálculo.
                            $updat = [
                                'prioridade' => Ferramentas::array_pesquisa($prioridade_data, 'ordem', $nova_prio)['id']
                            ];
                            $desenhos->update($value['id'], $updat);

                            // Registra a mudança de prioridade no histórico.
                            $data = [
                                'id_desenhos' => $value['id'],
                                'data_hora_mod' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                                'status' => Ferramentas::codificador('mudança de prioridade')
                            ];
                            $lista_temp->insert($data);

                        }
                    }
                } else {
                    // Registra a mudança de prioridade no histórico.
                    $data = [
                        'id_desenhos' => $value['id'],
                        'data_hora_mod' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                        'status' => Ferramentas::codificador('mudança de prioridade')
                    ];
                    $lista_temp->insert($data);
                }
            }

        }
        return $diasPassados;
    }




    /**
     * Lista desenhos disponíveis para corte e gerencia suas interações.
     *
     * Esta função gera uma lista de desenhos disponíveis para corte com base em critérios específicos, como status e prioridade. Ela também gerencia interações, como o processo de corte.
     *
     * Parâmetros:
     * - Nenhum parâmetro é necessário, pois a função obtém dados do banco de dados.
     *
     * Retorna:
     * - Um array contendo informações sobre a lista de desenhos para exibição e mensagens de atualização de prioridade.
     */
    function lista_corte_cortador()
    {
        // Verifica se a solicitação é uma chamada AJAX
        if ($this->request->isAJAX()) {
            $status = "corte";
            // Atualiza a prioridade dos desenhos com base em critérios específicos.
            $oi = self::atualiza_prio();

            // Inicializa modelos de banco de dados
            $desenhos = new \App\Models\Desenhos();
            $prioridade = new \App\Models\Prioridade();
            $finalidade = new \App\Models\Finalidade();
            $empresa = new \App\Models\Empresa();
            $empreendimento = new \App\Models\Empreendimentos();
            $corte = new \App\Models\Corte();
            $usuario = new \App\Models\Usuarios();

            // Obtém dados do banco de dados
            $prioridade_data = $prioridade->find();
            $finalidade_data = $finalidade->find();
            $empresa_data = $empresa->find();
            $empreendimento_data = $empreendimento->find();
            $desenhos_data = $desenhos->find();
            $corte_data = $corte->find();
            $usuario_data = $usuario->find();
            $lista = ""; // Inicializa a lista HTML
            $lista1 = ''; // Inicializa outra lista HTML
            $id_temp = 0; // Inicializa um identificador temporário
            $lista_ids = array(); // Inicializa um array para IDs de desenhos
            $lista_completa = array(); // Inicializa um array para informações completas de desenhos



            $corte_data = Ferramentas::array_pesquisa_mult($corte_data, ['ip', 'status'], [Ferramentas::codificador($_SERVER['REMOTE_ADDR']), 'inicio']);

            if (count($corte_data) != 0) {
                foreach ($desenhos_data as $key => $value) {
                    // Verifica se o desenho está em estado "corte" ou "cortando"
                    if (Ferramentas::decodificador($value['status']) == "corte" || Ferramentas::decodificador($value['status']) == "cortando") {
                        $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

                        // Verifica se o desenho não está em processo de corte
                        if ($corte_data['id_desenho'] != $value['id']) {
                            // Constrói as entradas da lista para desenhos em espera
                            if (Ferramentas::decodificador($value['status']) == "cortando") {
                                $lista .= '
          <tr>
        
           <td onclick="prio_modal()" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
           <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark  btn-lg btn-block"> Cortando... </button></td>
           <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark  btn-lg btn-block"> Confirmar Corte </button></td>
          </tr>
          ';

                            } else {
                                // Constrói as entradas da lista para desenhos em espera
                                $lista .= '
          <tr>
        
           <td onclick="prio_modal()" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
           <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark  btn-lg btn-block"> Cortar </button></td>
           <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark  btn-lg btn-block"> Confirmar Corte </button></td>
          </tr>
          ';
                            }
                        } else {
                            // Constrói as entradas da lista para desenhos em andamento


                            $ultima_barra_invertida = strrpos($value['caminho'], 'i061n');

                            // Dividir a string em duas partes
                            $caminho_diretorio = substr($value['caminho'], 0, $ultima_barra_invertida);
                            $nome_arquivo = substr($value['caminho'], $ultima_barra_invertida);

                            // Criar o array resultante
                            $array_resultante = [$caminho_diretorio, $nome_arquivo];

                            $caminho = str_replace(["ci083ni061n", "wli074ndesenhos", "i061n"], ["c:/", "wl_desenhos", "/"], $array_resultante[0]) . '/' . Ferramentas::decodificador($array_resultante[1]);
                            str_replace("//", "/", $caminho);
                            $status = "cortando";
                            $lista1 .= '
                            <tr>
                          
                             <td onclick="prio_modal()" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
                             <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>

                             <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
                             <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
                            <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
                                <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td><td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
                             <td><button name="cadastarar" type="submit" onclick="cortando(\'' . str_replace(["c:/wl/wl_desenhos/", "/"], ["i:/", "\\\\"], $caminho) . '\')" class="btn btn-outline-info btn-lg btn-block"> Cortando... </button></td>
                             <td><button name="cadastarar" type="submit" onclick="confirmar(\'' . $id_temp . '\',\'' . str_replace("'", "\'", Ferramentas::decodificador($value['nome'])) . '\')" class="btn btn-outline-success btn-lg btn-block"> Confirmar Corte </button></td>
                            </tr>
                            ';

                            // Prepara informações detalhadas sobre o desenho em andamento
                            $value['nome'] = Ferramentas::decodificador($value['nome']);
                            $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
                            $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
                            $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
                            $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
                            $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
                            $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
                            $lista_ids[$id_temp] = $value['id'];
                            $value['id'] = $id_temp;
                            $lista_completa[$id_temp] = $value;
                            $id_temp++;
                        }
                    }

                }










            } else {
                foreach ($desenhos_data as $key => $value) {
                    // Verifica se o desenho está em estado "corte"
                    if (Ferramentas::decodificador($value['status']) == "corte") {
                        $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

                        // Constrói as entradas da lista para desenhos em espera
                        $lista .= '
      <tr>
    
       
       <td onclick="prio_modal()" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>

       <td>' . Ferramentas::decodificador(Ferramentas::decodificador($value['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td><td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" type="submit" onclick="cortar(\'' . $id_temp . '\')" class="btn btn-outline-info btn-lg btn-block"> Cortar </button></td>
       <td><button name="cadastarar" type="submit" onclick="confirmar(\'' . $id_temp . '\',\'' . Ferramentas::decodificador($value['nome']) . '\')" class="btn btn-outline-success btn-lg btn-block"> Confirmar Corte </button></td>
      </tr>
      ';

                        // Prepara informações detalhadas sobre o desenho em espera
                        $value['nome'] = Ferramentas::decodificador($value['nome']);
                        $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
                        $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
                        $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
                        $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
                        $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
                        $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
                        $lista_ids[$id_temp] = $value['id'];
                        $value['id'] = $id_temp;
                        $lista_completa[$id_temp] = $value;
                        $id_temp++;
                    } else if (Ferramentas::decodificador($value['status']) == "cortando") {
                        // Constrói as entradas da lista para desenhos em andamento

                        $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);
                        $lista .= '
      <tr>
    
       <td onclick="prio_modal()" bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>

       <td>' . Ferramentas::decodificador(Ferramentas::decodificador($value['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
           <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td><td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark  btn-lg btn-block"> Cortando... </button></td>
       <td><button name="cadastarar" type="submit" disabled class="btn btn-outline-dark  btn-lg btn-block"> Confirmar Corte </button></td>
         </tr>
      ';
                    }
                }
            }

            // Define variáveis de sessão para manter informações entre solicitações AJAX
            session_start();
            $_SESSION["lista"] = $lista_ids;
            $_SESSION["lista_completa"] = $lista_completa;

            $alteracao = new \App\Models\Alteracoes();
            $alteracao_data = $alteracao->where('item', 'som_corte')
                ->orderBy('id', 'DESC')
                ->first();
            $som = "false";
            if ($alteracao_data) {
                $som = $alteracao_data["depois"];
            }

            // Resposta AJAX que inclui a lista gerada e mensagens de atualização de prioridade
            $data = [
                "lista" => $lista1 . $lista,
                "status" => $status,
                "som" => $som
            ];

            return $this->response->setJSON($data);
        }
    }

    /**
     * Inicia o processo de corte de um desenho e fornece o caminho do arquivo.
     *
     * Esta função é usada para iniciar o processo de corte de um desenho específico. Ela atualiza o status do desenho para "cortando" e registra informações sobre o início do processo de corte no banco de dados.
     *
     * Parâmetros:
     * - $id (POST): O ID do desenho que será iniciado para corte.
     *
     * Retorna:
     * - Um array contendo o caminho do arquivo que está sendo cortado.
     */
    function caminho_desenho()
    {
        if ($this->request->isAJAX()) {
            // Obtém o ID do desenho a ser cortado
            $id = service('request')->getPost('id');
            session_start();
            // $_SESSION['confirmar_corte_proc'] = isset ($_SESSION['confirmar_corte_proc']) ? $_SESSION['confirmar_corte_proc'] : FALSE;
            // if ($_SESSION['confirmar_corte_proc']) {
            //     return;
            // } 
            $array = Ferramentas::array_pesquisa($_SESSION["lista_completa"], 'id', $id);

            $corte = new \App\Models\Corte();
            $desenho = new \App\Models\Desenhos();
            // Salva o inicio do corte
            $input = [
                'id_desenho' => $_SESSION["lista"][$id],
                'cortador' => $_SESSION["usuario"],
                'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                'ip' => Ferramentas::codificador($_SERVER['REMOTE_ADDR']),
                'status' => 'inicio'
            ];
            $corte->insert($input);
            // Inicia o processo de corte do desenho no banco de dados
            $updat = [
                'status' => 'cortando'
            ];
            $desenho->update($_SESSION["lista"][$id], $updat);

            $ultima_barra_invertida = strrpos($array['caminho'], 'i061n');

            // Dividir a string em duas partes
            $caminho_diretorio = substr($array['caminho'], 0, $ultima_barra_invertida);
            $nome_arquivo = substr($array['caminho'], $ultima_barra_invertida);

            // Criar o array resultante
            $array_resultante = [$caminho_diretorio, $nome_arquivo];

            $caminho = str_replace(["ci083ni061n", "wli074ndesenhos", "i061n"], ["c:/", "wl_desenhos", "/"], $array_resultante[0]) . '/' . Ferramentas::decodificador($array_resultante[1]);
            str_replace("//", "/", $caminho);
            // Retorna o caminho do arquivo que está sendo cortado
            $data = [
                "caminho" => preg_replace('/\\\\+/', '\\\\', str_replace(["c:/wl/wl_desenhos/", "/"], ["i:/", "\\\\"], $caminho))
            ];
            return $this->response->setJSON($data);

        }
    }

      /**
   * Lista desenhos com status de "corte" ou "cortando".
   *
   * Esta função retorna uma lista de desenhos que possuem status "corte" ou "cortando".
   *
   * @return 
   */
  function lista_corte_desenhista() //rece um post via ajax pedindo para listar os usuarios
  {
    if ($this->request->isAJAX()) {
      $desenhos = new \App\Models\Desenhos(); // Instancia o modelo de dados para desenhos.

      $prioridade = new \App\Models\Prioridade(); // Instancia o modelo de dados para prioridades.

      $finalidade = new \App\Models\Finalidade(); // Instancia o modelo de dados para finalidades.

      $empresa = new \App\Models\Empresa(); // Instancia o modelo de dados para empresas.

      $empreendimento = new \App\Models\Empreendimentos(); // Instancia o modelo de dados para empreendimentos.
      $usuario = new \App\Models\Usuarios();

      $prioridade_data = $prioridade->find(); // Recupera dados de prioridades do banco de dados.
      $finalidade_data = $finalidade->find(); // Recupera dados de finalidades do banco de dados.
      $empresa_data = $empresa->find(); // Recupera dados de empresas do banco de dados.
      $empreendimento_data = $empreendimento->find(); // Recupera dados de empreendimentos do banco de dados.
      $desenhos_data = $desenhos->find(); // Recupera dados de desenhos do banco de dados.
      $usuario_data = $usuario->find();

      $lista = "";

      foreach ($desenhos_data as $key => $value) {
        // Verifica se o status do desenho é "corte" ou "cortando".
        if (Ferramentas::decodificador($value['status']) == "corte" || Ferramentas::decodificador($value['status']) == 'cortando') {
          // Obtém a prioridade do desenho.
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

          // Constrói a linha da tabela com informações do desenho.
          $lista .= '
      <tr>

       
       <td bgcolor="' . Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor'])) . '"><span class="marca_texto">' . Ferramentas::array_index($prioridade_desenho, ['nome']) . '</span></td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])) . '</td>
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
      </tr>
      ';

        }
      }

      $data = [
        "lista" => $lista
      ];

      return $this->response->setJSON($data);
    }
  }
}