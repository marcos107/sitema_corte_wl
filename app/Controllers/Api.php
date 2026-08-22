<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;
use Config\ResponseTrait;

class Api extends Ferramentas
{


    function comecar_conversa()
    {
        // Verificar se a requisição é POST e o conteúdo é JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtém o IP do cliente
            $ip_cliente = $_SERVER['REMOTE_ADDR'];

            // Verificar se existe um proxy
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
            }
            $mensagem = random_bytes(32) . random_bytes(12) . random_bytes(32) . random_bytes(16);

            $tokens = new \App\Models\Tokens();
            $tokens_data = $tokens->find();
            $codes = Ferramentas::array_pesquisa_mult_all($tokens_data, ['status'], ['ativo']);


            $string = array();
            foreach ($codes as $key => $value) {
                $string[] = time() - strtotime(str_replace('/', '-', Ferramentas::decodificador($value['data_hora_add'])));
                if ((time() - strtotime(str_replace('/', '-', Ferramentas::decodificador($value['data_hora_add'])))) > 300) {
                    $data = [
                        "status" => "encerrado"
                    ];
                    $tokens->update($value['id'], $data);
                } else if (explode("-", $value['code'])[1] == $ip_cliente) {
                    // return $this->response->setJSON([("erro:5309")]);
                }
            }



            $data = [
                "code" => base64_encode($mensagem) . "-" . $ip_cliente,
                "status" => "ativo",
                "local" => "appCorte",
                "data_hora_add" => Ferramentas::codificador(date('d/m/Y H:i'))

            ];
            $tokens->insert($data);




            $key_validacao = random_bytes(32);
            $key_ping = random_bytes(12);  // 12 bytes = 96 bits (Nonce para a versão IETF)
            $aes_secret_key = random_bytes(32); // 32 bytes = 256 bits
            $aes_iv = random_bytes(16);  // 16 bytes = 128 bits (IV)





            // Criptografia com AES-256-CBC
            $encrypted_with_aes = openssl_encrypt(base64_encode($mensagem), 'aes-256-cbc', $aes_secret_key, OPENSSL_RAW_DATA, $aes_iv);

            // Concatenar $str.$aes_secret_key . $aes_iv diretamente
            $combined_str = $encrypted_with_aes . $aes_secret_key . $aes_iv;

            // 2ª rodada: Criptografia com ChaCha20-Poly1305 (IETF) usando a string concatenada
            $chacha_encrypted = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
                base64_encode($combined_str),          // Concatenar mensagem criptografada com AES, chave e IV
                '',                     // AAD (Additional Authenticated Data) - opcional
                $key_ping,              // Nonce (12 bytes)
                $key_validacao          // Chave secreta (32 bytes)
            );

            // 32 bits aleatórios (4 bytes)
            $random_bits_32 = random_bytes(4); // Adiciona 32 bits aleatórios para confundir

            // 64 bits aleatórios (8 bytes) para mais confusão
            $random_bits_64 = random_bytes(8);

            // Inserir os 64 bits aleatórios no início da string para segurança
            $combined_with_64_bits = $random_bits_64 . $chacha_encrypted;

            // Adicionar $key_validacao e $key_ping concatenados ao final da string
            $final_str = $combined_with_64_bits . $key_ping . $key_validacao;

            // Codificar tudo em base64


            // Retornar uma resposta JSON
            return $this->response->setJSON([base64_encode($final_str)]);
        }

        // Se a requisição não for POST ou os dados estiverem vazios
        return $this->response->setJSON(['status' => $_SERVER['REQUEST_METHOD'], 'mensagem' => 'Requisição inválida']);
    }


    function descriptografar_conversa($encrypted_data_base64)
    {
        // Decodificar o Base64
        $encrypted_data = base64_decode($encrypted_data_base64);

        // Extrair os 64 bits (8 bytes) de dados aleatórios do início da string
        $random_bits_64 = substr($encrypted_data, 0, 8);

        // Separar a parte criptografada com ChaCha20-Poly1305 (sem os 64 bits e sem as chaves no final)
        $chacha_encrypted = substr($encrypted_data, 8, -44);  // Remove 8 bytes iniciais e os últimos 44 bytes (32 da chave + 12 do nonce)

        // Extrair as chaves $key_ping (12 bytes) e $key_validacao (32 bytes) do final da string
        $key_ping = substr($encrypted_data, -44, 12);
        $key_validacao = substr($encrypted_data, -32);

        // Descriptografar com ChaCha20-Poly1305
        $combined_str = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $chacha_encrypted,   // Dados criptografados com ChaCha
            '',                  // AAD (Additional Authenticated Data) - opcional
            $key_ping,           // Nonce (12 bytes)
            $key_validacao       // Chave secreta (32 bytes)
        );

        if ($combined_str === false) {
            return 'Erro na descriptografia com ChaCha20';
        }

        // Decodificar a string concatenada da primeira criptografia (AES) a partir do base64
        $combined_str_decoded = base64_decode($combined_str);

        // Extrair a parte criptografada com AES, chave AES e IV
        $encrypted_with_aes = substr($combined_str_decoded, 0, -48); // Remover os últimos 48 bytes (32 da chave AES + 16 do IV)
        $aes_secret_key = substr($combined_str_decoded, -48, 32);    // Extrair chave AES
        $aes_iv = substr($combined_str_decoded, -16);                // Extrair IV

        // Descriptografar com AES-256-CBC
        $decrypted_data = openssl_decrypt($encrypted_with_aes, 'aes-256-cbc', $aes_secret_key, OPENSSL_RAW_DATA, $aes_iv);

        if ($decrypted_data === false) {
            return 'Erro na descriptografia com AES-256-CBC';
        }

        // Retornar os dados descriptografados
        return $decrypted_data;
    }


    function login_api()
    {
        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Decodificar o JSON de forma segura, verificando se a decodificação foi bem-sucedida
        $decodedData = json_decode($requestData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            //      return $this->response->setJSON(['error' => 'JSON inválido ou malformado.'], 400);
        }

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }

        // Obtém os valores de nome e senha do POST da requisição.
        $nome = explode(":", $requestData)[0];
        $senha = explode(":", $requestData)[1];

        // Codifica os valores de nome e senha utilizando a função de codificação Ferramentas::codificador.
        $nome = Ferramentas::codificador($nome);
        $senha = Ferramentas::codificador($senha);

        // Cria uma instância do modelo de Usuários.
        $db = new \App\Models\Usuarios();

        // Realiza uma consulta no banco de dados para encontrar dados do usuário.
        $db_data = $db->find();

        // Executa uma pesquisa no array de dados para encontrar um usuário com nome e senha correspondentes.
        $user = Ferramentas::array_pesquisa_mult($db_data, ['nome', 'senha'], [$nome, $senha]);
        if (count($user) == 0) {
            return $this->response->setJSON(['erro' => '5377'], 400);
        }


        // // Gerar ID de conversa usando random_bytes
        // try {
        $id_conversa = bin2hex(random_bytes(32)) . bin2hex(random_bytes(12)) . bin2hex(random_bytes(32)) . bin2hex(random_bytes(16));
        // } catch (Exception $e) {
        //     return $this->response->setJSON(['error' => 'Erro ao gerar o ID de conversa.'], 500);
        // }

        // // Buscar dados dos tokens
        $tokens = new \App\Models\Tokens();
        $tokens_data = $tokens->find();
        $codes = Ferramentas::array_pesquisa_mult_all($tokens_data, ['status'], ['ativo']);

        $ok = false;
        foreach ($codes as $key => $value) {
            $tokenParts = explode("-", $value['code']);
            if (count($tokenParts) === 2 && $tokenParts[1] == $ip_cliente) {
                $ok = true;
                $id_conversa = $tokenParts[0];
                $data = [
                    "data_hora_user" => Ferramentas::codificador(date('d/m/Y H:i'))
                ];
                $tokens->update($value['id'], $data);
            }
        }

        // Inserir novo token, se a operação foi bem-sucedida
        if (!$ok) {

            $data = [
                "code" =>  $id_conversa . '-' . $ip_cliente,
                "individuo" => $user['id'],
                "status" => "ativo",
                "local" => "appCorte",
                "data_hora_user" => Ferramentas::codificador(date('d/m/Y H:i')),
                "data_hora_add" => Ferramentas::codificador(date('d/m/Y H:i'))
            ];
            $tokens->insert($data);
            return $this->response->setJSON(['id' => $id_conversa]);
        } else {
            return $this->response->setJSON(['id' => $id_conversa]);
        }

        // Caso não tenha sido encontrado um código correspondente
        return $this->response->setJSON(['erro' => '5377'], 400);
    }

    public function login_cancelar_api()
    {
        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Decodificar o JSON de forma segura, verificando se a decodificação foi bem-sucedida
        $decodedData = json_decode($requestData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            //      return $this->response->setJSON(['error' => 'JSON inválido ou malformado.'], 400);
        }

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }

        // Obtém os valores de nome e senha do POST da requisição.
        $nome = explode(":", $requestData)[0];
        $senha = explode(":", $requestData)[1];

        // Codifica os valores de nome e senha utilizando a função de codificação Ferramentas::codificador.
        $nome = Ferramentas::codificador($nome);
        $senha = Ferramentas::codificador($senha);

        // Cria uma instância do modelo de Usuários.
        $db = new \App\Models\Usuarios();
        $nivel = new \App\Models\Nivel();

        // Realiza uma consulta no banco de dados para encontrar dados do usuário.
        $db_data = $db->find();
        $db_nivel = $nivel->find();

        // Executa uma pesquisa no array de dados para encontrar um usuário com nome e senha correspondentes.
        $user = Ferramentas::array_pesquisa_mult($db_data, ['nome', 'senha'], [$nome, $senha]);
        if (count($user) == 0) {
            return $this->response->setJSON(['erro' => '5377'], 400);
        }
        $permissao = Ferramentas::array_index(Ferramentas::array_pesquisa($db_nivel, 'id', $user["nivel"]), ["permissao"]);


        if ($permissao != "all" and strpos($permissao, "ADM") == false) {
            return $this->response->setJSON(['erro' => '5378'], 400);
        }

        // Cria uma instância do modelo de dados 'Corte'
        $corte = new \App\Models\Corte();

        // Busca os dados de corte no banco de dados
        $corte_data = $corte->find();

        // Obtém o ID do corte que corresponde ao desenho em 'inicio'
        $id_corte = Ferramentas::array_index(Ferramentas::array_pesquisa_mult($corte_data, ['id_desenho', 'status'], [explode(":", $requestData)[2], 'inicio']), ['id']);

        // Define os dados a serem atualizados para cancelar o pendente
        $update = [
            'cortador_fim' => $user['id'],
            'data_fim' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'status' => 'cancelado'
        ];

        // Atualiza o registro de corte com o status 'cancelado'
        $corte->update($id_corte, $update);

        // Cria uma instância do modelo de dados 'Desenhos'
        $desenho = new \App\Models\Desenhos();

        // Define os dados a serem atualizados para definir o status como 'corte'
        $updat = [
            'status' => 'pendente'
        ];

        // Atualiza o registro de desenho para o status 'corte'
        $desenho->update(explode(":", $requestData)[2], $updat);

        return $this->response->setJSON(['ok' => '5378'], 400);
    }



    function descriptografar($dado, $keyValidacao, $keyPing, $aesSecretKey, $aesIv)
    {


        // Decodificar a string base64 para obter os bytes combinados
        $desencode = base64_decode($dado);


        $key_validacao =  ($keyValidacao);
        $key_ping = ($keyPing);
        // return $desencode;

        // 1ª etapa: Descriptografia com ChaCha20-Poly1305 (IETF)
        $desencode_chacha = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $desencode,      // Mensagem criptografada com ChaCha20
            '',                   // AAD (opcional)
            $key_ping,          // Nonce
            $key_validacao      // Chave secreta
        );

        $aes_iv = ($aesIv);
        $aes_secret_key = ($aesSecretKey);
        $desencode_chacha = ($desencode_chacha);

        // Retornar a mensagem original
        $mensagem = openssl_decrypt($desencode_chacha, 'aes-256-cbc', $aes_secret_key, OPENSSL_RAW_DATA, $aes_iv);

        return $mensagem;
    }

    function lista_tarefas_api()
    {
        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }

        // Obtém os valores de nome e senha do POST da requisição.

        // // Buscar dados dos tokens
        $tokens = new \App\Models\Tokens();
        $tokens_data = $tokens->find();
        $codes = Ferramentas::array_pesquisa_mult_all($tokens_data, ['status'], ['ativo']);

        $ok = false;
        foreach ($codes as $key => $value) {
            $tokenParts = explode("-", $value['code']);
            if (count($tokenParts) === 2 && $tokenParts[1] == $ip_cliente && $tokenParts[0] == $requestData) {
                $ok = true;

                $data = [
                    "data_hora_user" => Ferramentas::codificador(date('d/m/Y H:i'))
                ];
                $tokens->update($value['id'], $data);
            }
        }

        // Inserir novo token, se a operação foi bem-sucedida
        if (true) {
            $lista = "";
            // Verifica se a solicitação é uma chamada AJAX

            $status = "pendente";
            $processo = "CORTE_LASER";


            // Inicializa modelos de banco de dados
            $desenhos = new \App\Models\Desenhos();
            $prioridade = new \App\Models\Prioridade();
            $finalidade = new \App\Models\Finalidade();
            $empresa = new \App\Models\Empresa();
            $empreendimento = new \App\Models\Empreendimentos();
            $corte = new \App\Models\Corte();
            $usuario = new \App\Models\Usuarios();
            $processos = new \App\Models\Processos();

            // Obtém dados do banco de dados
            $prioridade_data = $prioridade->find();
            $finalidade_data = $finalidade->find();
            $empresa_data = $empresa->find();
            $empreendimento_data = $empreendimento->find();
            $desenhos_data = $desenhos->find();

            $corte_data = $corte->find();
            $usuario_data = $usuario->find();
            $processos_data = $processos->find();

            $lista = ""; // Inicializa a lista HTML
            $lista1 = ''; // Inicializa outra lista HTML
            $id_temp = 0; // Inicializa um identificador temporário
            $lista_ids = array(); // Inicializa um array para IDs de desenhos
            $lista_completa = array(); // Inicializa um array para informações completas de desenhos



            while (true) {
                // Busca um registro em $corte_data que atenda aos critérios
                $resposta = $corte
                    ->where('ip', Ferramentas::codificador($_SERVER['REMOTE_ADDR']))
                    ->where('status', 'inicio')
                    ->first();

                // Se não há mais registros correspondentes, encerre o loop
                if (!$resposta) {
                    $corte_data = [];
                    break;
                }

                // Busca o status do desenho correspondente diretamente no banco
                $desenho = $desenhos->find($resposta['id_desenho']);

                // Verifica se o status do desenho não é "cortando"
                if ($desenho && $desenho['status'] !== 'cortando') {
                    // Atualiza o status no banco de dados
                    $corte->update($resposta['id'], ['status' => 'finalizado_revisao']);
                } else {
                    $corte_data = $resposta;
                    break;
                }
            }


            if (count($corte_data) == 0) {
                foreach ($desenhos_data as $key => $value) {
                    if (Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($processos_data, 'id', $value['processos_id']), ["nome"])) == $processo) {

                        // Verifica se o desenho está em estado "corte" ou "cortando"
                        if ((Ferramentas::decodificador($value['status']) == "pendente" || Ferramentas::decodificador($value['status']) == "cortando")) {
                            $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

                            $tags = explode('/', Ferramentas::decodificador($value['caminho']));


                            // Remover os índices de 0 a 5
                            $tags = array_slice($tags, 6);

                            // Remover o último elemento
                            unset($tags[count($tags) - 1]);
                            $tags = implode(" - ", $tags);
                            // Prepara informações detalhadas sobre o desenho em andamento
                            $value['subpasta'] = $tags;
                            $value['nome'] = Ferramentas::decodificador($value['nome']);
                            $value['cor'] = Ferramentas::decodificador(Ferramentas::array_index($prioridade_desenho, ['cor']));
                            $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
                            $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
                            $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
                            $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
                            $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
                            $value['desenhista'] = Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome']);
                            $lista_ids[$id_temp] = $value['id'];
                            $value['proprietario'] = "No";

                            $value['teste']  = Ferramentas::codificador($_SERVER['REMOTE_ADDR']);
                            //$value['id'] = $id_temp;
                            $lista_completa['p' . $id_temp] = $value;
                            $id_temp++;
                        }
                    }
                }
            } else {
                foreach ($desenhos_data as $key => $value) {
                    if (Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($processos_data, 'id', $value['processos_id']), ["nome"])) == $processo) {
                        if ((Ferramentas::decodificador($value['status']) == "pendente" || Ferramentas::decodificador($value['status']) == "cortando")) {
                            $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);

                            $tags = explode('/', Ferramentas::decodificador($value['caminho']));


                            // Remover os índices de 0 a 5
                            $tags = array_slice($tags, 6);

                            // Remover o último elemento
                            unset($tags[count($tags) - 1]);
                            $tags = implode(" - ", $tags);
                            // Prepara informações detalhadas sobre o desenho em andamento


                            if ($corte_data["id_desenho"] == $value["id"]) {
                                $value['proprietario'] = "yes";
                            } else {
                                $value['proprietario'] = "no" . $corte_data["id_desenho"];
                            }


                            // Prepara informações detalhadas sobre o desenho em espera
                            $value['subpasta'] = $tags;
                            $value['nome'] = Ferramentas::decodificador($value['nome']);
                            $value['cor'] = Ferramentas::decodificador($prioridade_desenho['cor']);
                            $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]);
                            $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]);
                            $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]);
                            $value['data_hora_add'] = Ferramentas::decodificador($value['data_hora_add']);
                            $value['prioridade'] = Ferramentas::decodificador($prioridade_desenho['nome']);
                            $lista_ids[$id_temp] = $value['id'];
                            //$value['id'] = $id_temp;
                            $lista_completa['p' . $id_temp] = $value;
                            $id_temp++;
                        }
                    }
                }
            }

            // Define variáveis de sessão para manter informações entre solicitações AJAX

            $alteracao = new \App\Models\Alteracoes();
            $som = $alteracao->latestDetailValueByItem('som_corte', 'se vai sari som para o cortador', 'false');


            return $this->response->setJSON(['lista' => $lista_completa]);
        }

        // Caso não tenha sido encontrado um código correspondente
        return $this->response->setJSON(['erro' => '5377'], 400);
    }



    public function download_lista_tarefas_api()
    {
        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }

        // Obtém os valores de nome e senha do POST da requisição.

        // // Buscar dados dos tokens
        $tokens = new \App\Models\Tokens();
        $tokens_data = $tokens->find();
        $codes = Ferramentas::array_pesquisa_mult_all($tokens_data, ['status'], ['ativo']);

        $ok = false;
        foreach ($codes as $key => $value) {
            $tokenParts = explode("-", $value['code']);
            if (count($tokenParts) === 2 && $tokenParts[1] == $ip_cliente && $tokenParts[0] == $requestData) {
                $ok = true;

                $data = [
                    "data_hora_user" => Ferramentas::codificador(date('d/m/Y H:i'))
                ];
                $tokens->update($value['id'], $data);
            }
        }

        // Inserir novo token, se a operação foi bem-sucedida
        if (true) {

            // Verifica se a solicitação é uma chamada AJAX




            // Inicializa modelos de banco de dados
            $desenhos = new \App\Models\Desenhos();

            // Obtém dados do banco de dados

            $desenhos_data = $desenhos->find();



            // Lista de arquivos que deseja incluir no ZIP

            $files = Ferramentas::decodificador(Ferramentas::array_pesquisa_mult($desenhos_data, ['id'], [$requestData])['caminho']);
            $name = Ferramentas::decodificador(Ferramentas::array_pesquisa_mult($desenhos_data, ['id'], [$requestData])['nome']);
            while (substr($files, -1) !== '/') {
                $files = substr($files, 0, -1); // Remove o último caractere
            }

            $files .= $name;



















            // Caminho temporário para o arquivo ZIP
            $zipFilePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'arquivos.zip';

            // Criação do arquivo ZIP
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                return $this->response->setJSON(['erro' => '5372'], 400);
            }
            if (file_exists($files)) {
                $fileName = basename($files);
                $zip->addFile($files, $fileName);
            }


            // Fecha o arquivo ZIP
            $zip->close();

            // Verifica se o arquivo ZIP foi criado e retorna para o download
            if (file_exists($zipFilePath)) {
                return $this->response->download($zipFilePath, null)->setFileName('arquivos.zip');
            } else {
                return $this->response->setJSON(['erro' => '5371'], 400);
            }
        }
    }

    public function confirmar_tarefa_api()
    {

        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }
        // // Buscar dados dos tokens
        $tokens = new \App\Models\Tokens();
        $tokens_data = $tokens->find();
        $codes = Ferramentas::array_pesquisa_mult_all($tokens_data, ['status'], ['ativo']);
        $usuario_id = "";
        $ok = false;
        foreach ($codes as $key => $value) {
            $tokenParts = explode("-", $value['code']);
            if (count($tokenParts) === 2 && $tokenParts[1] == $ip_cliente && $tokenParts[0] == explode('-', $requestData)[0]) {
                $ok = true;
                $usuario_id = $value['individuo'];
                $data = [
                    "data_hora_user" => Ferramentas::codificador(date('d/m/Y H:i'))
                ];
                $tokens->update($value['id'], $data);
            }
        }

        // Inserir novo token, se a operação foi bem-sucedida
        if (true) {
            // Obtém o ID do desenho a ser cortado a partir dos dados da requisição AJAX
            $id = explode('-', $requestData)[1];
            $data = [];
            $desenho = new \App\Models\Desenhos();

            // Busca os dados do desenho no banco de dados
            $desenho_data = $desenho->find();

            // Obtém o status do desenho
            $status = Ferramentas::array_index(Ferramentas::array_pesquisa($desenho_data, 'id', $id), ['status']);

            if (Ferramentas::decodificador($status) == "pendente" || Ferramentas::decodificador($status) == "cortando") {

                // Se o status for "pendente" ou "cortando", continua o processamento
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
                $caminho = trim(str_replace($nome, '', $caminho));
                $nome = trim(str_replace('.' . Ferramentas::get_type_file($nome), '', $nome));


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
                    'cortador_fim' => $usuario_id,
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
                        'cortador' => $usuario_id,
                        'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
                        'ip' => Ferramentas::codificador($_SERVER['REMOTE_ADDR']),

                    ]);
                    $corte->insert($insert_data);
                }



                // Atualiza os dados do desenho
                $updat = [
                    'cortador' => $usuario_id,
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



                return $this->response->setJSON($data);

                // deu certo
            } else {
                // Se o status não for "corte" ou "cortando", retorna uma resposta com erro
                $data = [
                    "ok" => false
                ];

                return $this->response->setJSON($data);
                //desenho ja cortado 
            }
        }
    }

    public function iniciar_tarefa_api()
    {
        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }
        // // Buscar dados dos tokens
        $tokens = new \App\Models\Tokens();
        $tokens_data = $tokens->find();
        $codes = Ferramentas::array_pesquisa_mult_all($tokens_data, ['status'], ['ativo']);
        $usuario_id = "";
        $ok = false;
        foreach ($codes as $key => $value) {
            $tokenParts = explode("-", $value['code']);
            if (count($tokenParts) === 2 && $tokenParts[1] == $ip_cliente && $tokenParts[0] == explode('-', $requestData)[0]) {
                $ok = true;
                $usuario_id = $value['individuo'];
                $data = [
                    "data_hora_user" => Ferramentas::codificador(date('d/m/Y H:i'))
                ];
                $tokens->update($value['id'], $data);
            }
        }




        $corte = new \App\Models\Corte();
        $desenho = new \App\Models\Desenhos();
        // Salva o inicio do corte
        $input = [
            'id_desenho' => explode('-', $requestData)[1],
            'cortador' => $usuario_id,
            'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'ip' => Ferramentas::codificador($_SERVER['REMOTE_ADDR']),
            'status' => 'inicio'
        ];
        $corte->insert($input);
        // Inicia o processo de corte do desenho no banco de dados
        $updat = [
            'status' => 'cortando'
        ];
        $desenho->update(explode('-', $requestData)[1], $updat);


        return $this->response->setJSON(['erro' => '5377'], 400);
    }

    public function login_config_api()
    {
        // Pegar o corpo bruto da requisição (neste caso, uma string JSON)
        $requestData = $this->request->getBody();

        // Decodificar o JSON de forma segura, verificando se a decodificação foi bem-sucedida
        $decodedData = json_decode($requestData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            //      return $this->response->setJSON(['error' => 'JSON inválido ou malformado.'], 400);
        }

        // Obtém o IP do cliente
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        // Verificar se existe um proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
        }

        // Obtém os valores de nome e senha do POST da requisição.
        $nome = explode(":", $requestData)[0];
        $senha = explode(":", $requestData)[1];

        // Codifica os valores de nome e senha utilizando a função de codificação Ferramentas::codificador.
        $nome = Ferramentas::codificador($nome);
        $senha = Ferramentas::codificador($senha);

        // Cria uma instância do modelo de Usuários.
        $db = new \App\Models\Usuarios();
        $nivel = new \App\Models\Nivel();

        // Realiza uma consulta no banco de dados para encontrar dados do usuário.
        $db_data = $db->find();
        $db_nivel = $nivel->find();

        // Executa uma pesquisa no array de dados para encontrar um usuário com nome e senha correspondentes.
        $user = Ferramentas::array_pesquisa_mult($db_data, ['nome', 'senha'], [$nome, $senha]);
        if (count($user) == 0) {
            return $this->response->setJSON(['erro' => '5377'], 400);
        }
        $permissao = Ferramentas::array_index(Ferramentas::array_pesquisa($db_nivel, 'id', $user["nivel"]), ["permissao"]);


        if ($permissao != "all") {
            return $this->response->setJSON(['erro' => '5379'], 400);
        }


        return $this->response->setJSON(['ok' => '5378'], 400);
    }











    public function empreendimento_api()
    { {
            // $id = service('request')->getPost('id');
            $empreendimentoModel = new \App\Models\Empreendimentos();
            $empreendimentos = $empreendimentoModel
                ->select('empreendimentos.nome,
                          empreendimentos.status,
                          empresa.nome as empresa')
                ->join('empresa', 'empresa.id = empreendimentos.empresa_id')
                ->where('empreendimentos.status', 'ativo')
                ->findAll();


            return $this->response->setJSON(['ok' => 'true', 'empreendimentos' => Ferramentas::array_decodificador($empreendimentos)], 400);
        }
    }

    public function projetistas_api()
    { {
            // $id = service('request')->getPost('id');
            $usuarioModel = new \App\Models\Usuarios();
            $usuarios = $usuarioModel
                ->select('usuarios.nome')
                ->where('usuarios.status', 'ativo')
                ->where('usuarios.nivel', '3')
                ->findAll();


            return $this->response->setJSON(['ok' => 'true', 'projetistas' => Ferramentas::array_decodificador($usuarios)], 400);
        }
    }


    public function cortador_api()
    { {
            // $id = service('request')->getPost('id');
            $usuarioModel = new \App\Models\Usuarios();
            $usuarios = $usuarioModel
                ->select('usuarios.nome')
                ->where('usuarios.status', 'ativo')
                ->where('usuarios.nivel', '2')
                ->findAll();


            return $this->response->setJSON(['ok' => 'true', 'projetistas' => Ferramentas::array_decodificador($usuarios)], 400);
        }
    }

    public function empreendimento_arquivos_api()
    { {
        $nome = service('request')->getPost('nome');

        if (!$nome) {
            return $this->response->setJSON(['erro' => 'Nome do empreendimento não fornecido.']);
        }

        $empreendimentoModel = new \App\Models\Empreendimentos();
        $desenhosModel = new \App\Models\Desenhos();
        $finalidadeModel = new \App\Models\Finalidade();

        $empreendimento = $empreendimentoModel
            ->select('id')
            ->where('nome', $nome)
            ->first();

        if (!$empreendimento) {
            return $this->response->setJSON(['erro' => 'Empreendimento não encontrado.']);
        }

        $idEmpreendimento = $empreendimento['id'];

        $finalidadesUsadas = $desenhosModel
            ->select('finalidade')
            ->where('empreendimento', $idEmpreendimento)
            ->groupBy('finalidade')
            ->findAll();

        $dadosFinalidades = [];

        foreach ($finalidadesUsadas as $item) {
            $finalidadeId = $item['finalidade'];

            $finalidade = $finalidadeModel
                ->select('nome')
                ->where('id', $finalidadeId)
                ->first();

            $nomeFinalidade = $finalidade ? $finalidade['nome'] : 'Desconhecida';

            $total = $desenhosModel
                ->where('empreendimento', $idEmpreendimento)
                ->where('finalidade', $finalidadeId)
                ->countAllResults();

            $cortados = $desenhosModel
                ->where('empreendimento', $idEmpreendimento)
                ->where('finalidade', $finalidadeId)
                ->where('status', 'cortado')
                ->countAllResults();

            $cortando = $desenhosModel
                ->where('empreendimento', $idEmpreendimento)
                ->where('finalidade', $finalidadeId)
                ->where('status', 'cortando')
                ->countAllResults();

            $dadosFinalidades[] = [   
                'finalidade_nome' => $nomeFinalidade,
                'total' => $total,
                'cortados' => $cortados,
                'cortando' => $cortando
            ];
        }        


            return $this->response->setJSON(['ok' => 'true', 'finalidade' => Ferramentas::array_decodificador($dadosFinalidades)], 400);
        }
    }

    public function finalizar_empreendimento_api()
    {
        $nome = service('request')->getPost('nome');

        if (!$nome) {
            return $this->response->setJSON(['erro' => 'Nome do empreendimento não fornecido.']);
        }

        $empreendimentoModel = new \App\Models\Empreendimentos();

        $empreendimento = $empreendimentoModel
            ->where('nome', $nome)
            ->first();

        if (!$empreendimento) {
            return $this->response->setJSON(['erro' => 'Empreendimento não encontrado.']);
        }

        // Atualiza o campo "finalizado" para true (1)
        $empreendimentoModel
            ->where('id', $empreendimento['id'])
            ->set(['status' => "desativado"])
            ->update();

        return $this->response->setJSON([
            'mensagem' => 'Empreendimento finalizado com sucesso.',
            'empreendimento' => $nome
        ]);
    }
}
