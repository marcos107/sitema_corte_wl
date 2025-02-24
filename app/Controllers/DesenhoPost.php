<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class DesenhoPost extends Ferramentas
{
  /**
   * Função desenho_update()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por atualizar a prioridade de um ou mais desenhos, registrando as alterações em um log de alterações.
   *
   * Retorna um JSON indicando se a operação de atualização da prioridade foi bem-sucedida e, em caso de falha, fornece informações sobre a violação.
   */
  function desenho_update()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();


      // Obtém o array de IDs e a nova prioridade da solicitação
      $array = service('request')->getPost('array');
      $prioridade_nova = service('request')->getPost('prioridade');
      $ordem = service('request')->getPost('ordem');

      $ok = false; // Variável para indicar se a operação foi bem-sucedida
      $violacao = array(); // Array para armazenar informações de violações

      if ($prioridade_nova != '') {
        // Se a nova prioridade não estiver vazia, prossiga com a atualização
        $prioridade = new \App\Models\Prioridade(); // Obtém a tabela de prioridades do banco
        $ok = true; // Define que a operação está OK

        $prioridade_data = $prioridade->find();
        $id_prio = Ferramentas::array_pesquisa($prioridade_data, 'nome', Ferramentas::codificador($prioridade_nova))['id'];

        $desenhos = new \App\Models\Desenhos(); // Obtém a tabela de desenhos do banco
        foreach ($array as $key => $value) {
          // Para cada ID no array de IDs, registre as alterações em um log
          $ids = $_SESSION["lista"][$value];
          $alteracao = new \App\Models\Alteracoes();
          $lista_temp = new \App\Models\Historico_desenhos();

          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => $ids,
            "antes" => $_SESSION["lista_completa"][$value]['prioridade'],
            "depois" => $id_prio,
            "item" => "desenho",
            "info_mais" => "prioridade",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);

          $data = [
            "prioridade" => $id_prio

          ];
          $desenhos->update($ids, $data);
          Ferramentas::re_ordenar_oredem_desenho($ids, $id_prio, $ordem);
          $data = [
            'id_desenhos' => $ids,
            'data_hora_mod' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'status' => Ferramentas::codificador('mudança de prioridade')
          ];
          $lista_temp->insert($data);
        }
      } else {
        $violacao[] = "desenho_update prioridade não existe";
      }

      if (count($violacao) != 0) {
        //$violacao = Ferramentas::array_codificar($violacao);
        // Cria uma instância do modelo Violacao
        $db = new \App\Models\Violacao();

        // Para cada violação no array de violações, registra a violação em um banco de dados de violações
        foreach ($violacao as $key => $value) {

          $data = [
            "individuo" => $_SESSION["usuario"],
            "causa" => $value,
            "data" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];

          // Para cada violação no array de violações, registra a violação em um banco de dados de violações
          $db->insert($data);
        }
      }

      $date = [
        "ok" => $ok

      ];

      return $this->response->setJSON($date);
    }
  }


  /**
   * Função desenho_modal()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por retornar a lista completa de usuários, armazenada em uma sessão, para ser utilizada em um modal ou em outro contexto na interface do usuário.
   *
   * Retorna um JSON contendo a lista completa de usuários armazenada na sessão.
   */
  function desenho_modal()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();
      $id = service('request')->getPost('id');

      $db = new \App\Models\Desenhos();
      $desenhos_data = $db
        ->whereIn('status', [
          Ferramentas::codificador('pendente'),
          Ferramentas::codificador('cortando')
        ])
        ->find();
      $agrupados = [];


      foreach ($desenhos_data as $desenho) {
        $prioridade = $desenho['prioridade'];
        $agrupados[$prioridade][] = $desenho;
      }
      foreach ($agrupados as $prioridade => &$grupo) {
        usort($grupo, function ($a, $b) {
          // Converte 'ordem' para inteiro para garantir a comparação numérica
          return intval($a['ordem']) <=> intval($b['ordem']);
        });
        // Após a ordenação, o último elemento possui o maior valor de 'ordem'
        $ultimoItem = end($grupo);
        // Substitui o grupo pelo número da maior ordem
        $grupo = intval($ultimoItem['ordem']);
      }
      unset($grupo); // Desfaz a referência



      if ($id == "") {
        $data = [
          "lista" => $_SESSION["lista_completa"],
          "agrupados" => $agrupados

        ];
      } else {
        $data = [
          "lista" => [Ferramentas::array_pesquisa($_SESSION["lista_completa"], 'id', $id)],
          "agrupados" => $agrupados

        ];
      }

      // Resposta do AJAX que retorna a lista completa de usuários
      return $this->response->setJSON($data);
    }
  }

  /**
   * Função nome_desenho()
   *
   * Esta função recebe, via POST, um ID temporário e utiliza esse ID para retornar o nome completo de um desenho
   * que está em uma lista. 
   *
   * A função retorna um JSON contendo a lista completa de usuários que está armazenada na sessão.
   */
  function nome_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();

      $id = service('request')->getPost('id'); //id recebido via post

      $lista = $_SESSION["lista_completa"][$id]; //busca qual desenhos é da lsita usando o id e retornando as informações desse desenho

      $data = ['ok' => true, 'nome' => Ferramentas::decodificador($lista['nome'])]; // envia ok true indicando que deu certo e retrirar apenas o nome das informações do desenho
      return $this->response->setJSON($data);
    }
  }


  /**
   * Move-o um desenho especificado para o diretório de lixo.
   *
   * Esta função é responsável por apagar um desenho especificado ou movê-lo para o diretório de lixo.
   *
   * @return 
   */
  function apagar_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id_temp = service('request')->getPost('id');

      // Obtém informações sobre o desenho a partir da sessão.
      $lista = $_SESSION["lista_completa"][$id_temp];
      $id = $_SESSION["lista"][$id_temp];
      $caminho = $lista['caminho'];
      $ultima_barra_invertida = strrpos($caminho, 'i061n');

      // Dividir a string em duas partes
      $caminho_diretorio = substr($caminho, 0, $ultima_barra_invertida);
      $nome_arquivo = substr($caminho, $ultima_barra_invertida);

      // Criar o array resultante
      $array_resultante = [$caminho_diretorio, $nome_arquivo];

      $caminho = str_replace(["ci083ni061n", "wli074ndesenhos", "i061n", "i074n"], ["c:/", "wl_desenhos", "/", "_"], $array_resultante[0]) . '/' . Ferramentas::decodificador($array_resultante[1]);
      $caminho = str_replace("//", "/", $caminho);
      $nome = Ferramentas::decodificador($lista['nome']);
      $caminho_antigo = $caminho;
      // Verifica se o desenho não existe mais.
      if (!file_exists($caminho)) {
        // Atualiza o status do desenho para 'apagado' no banco de dados.
        $db = new \App\Models\Desenhos();
        $db->update($id, ['status' => 'apagado']);

        return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Desenho apagado com sucesso ', 'mensagem_false' => 'Desenho não existe', '1' => $caminho, '2' => $nome]);
      }

      // Define o novo caminho do desenho para o diretório de lixo.
      $caminho = str_replace(['c:/wl/wl_desenhos/'], [''], $caminho);
      $caminho = str_replace($nome, '', $caminho);
      $caminho = 'C:/wl/lixo/wl/wl_desenhos/' . $caminho . '/';
      $caminho = str_replace('//', '/', $caminho);
      // Normaliza o caminho removendo espaços extras e corrigindo barras
      $caminho = str_replace('\\', '/', trim($caminho));

      // Verifica se há múltiplas ocorrências de "C:/"
      if (preg_match_all('/[a-zA-Z]:\//', $caminho, $matches, PREG_OFFSET_CAPTURE)) {
        if (count($matches[0]) > 1) {
          // Pega a segunda ocorrência da raiz e corta o restante anterior
          $inicioSegundaRaiz = $matches[0][1][1]; // Índice da segunda raiz
          $caminho = substr($caminho, $inicioSegundaRaiz);
        }
      }
      // Cria o diretório de lixo, se não existir.
      $problema = Ferramentas::criet_diretorio($caminho);

      if (count($problema) == 0) {
        // Gera um nome de arquivo único para evitar conflitos.
        do {

          $nome = Ferramentas::get_name_file($nome, false) . '_' . date('d_m_Y_H_i_s_') . rand(0, 100) . '.' . Ferramentas::get_type_file($nome);
        } while (file_exists($caminho . $nome));

        // Move o desenho para o diretório de lixo.
        if (rename($caminho_antigo, $caminho . $nome)) {
          // Registra informações sobre o desenho no banco de dados de lixo.
          $data = [
            'id_desenho' => $id,
            'individuo' => $_SESSION['usuario'],
            'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'caminho' => Ferramentas::codificador($caminho),
            'nome_desenho' => Ferramentas::codificador($nome)
          ];
          $db = new \App\Models\Lixo_desenhos();
          $db->insert($data);

          // Atualiza o status do desenho para 'apagado' no banco de dados.
          $db = new \App\Models\Desenhos();
          $db->update($id, ['status' => 'apagado']);
          return $this->response->setJSON(['ok' => 'true', 'mensagem' => 'Desenho apagado com sucesso ', 'dir_antigo' => $caminho_antigo, 'dir_novo' => $caminho . $nome]);
        } else {
          return $this->response->setJSON(['ok' => 'false', 'mensagem' => 'Desenho apagado com sucesso ', 'mensagem_false' => 'Trasferencia para lixeira']);
        }
      }
      return $this->response->setJSON(['ok' => 'false', 'caminho' => $problema]);
    }
  }






  /**
   * Define um novo nome para um desenho que será substituído.
   *
   * Esta função é responsável por definir um novo nome para um desenho que será substituído e armazenar esse nome na sessão para uso posterior.
   *
   * @return 
   */
  function desenho_novo_nome()
  {
    if ($this->request->isAJAX()) {
      session_start();

      // Obtém o novo nome do desenho a ser definido.
      $nome_novo = Ferramentas::norma_lizar_str(service('request')->getPost('nome'));

      // Armazena o novo nome na sessão para uso posterior.
      $_SESSION["novo_nome_arquivo"] = $nome_novo;
      return $this->response->setJSON(['mensagem' => $nome_novo]);
    }
  }








  /**
   * Recoloca um desenho duplicado no banco de dados.
   *
   * Esta função lida com a recolocação de um desenho duplicado no banco de dados. Ela cria uma cópia do desenho original com um novo nome e insere os dados duplicados no banco de dados.
   *
   * @return 
   */
  function recolocar_desenho()
  {
    if ($this->request->isAJAX()) {

      $id = service('request')->getPost('id');

      $caminho = Ferramentas::re_colcoar_desenho($id);


      $data = ['ok' => true, 'nome' => $caminho];
      return $this->response->setJSON($data);
    }
  }
}
