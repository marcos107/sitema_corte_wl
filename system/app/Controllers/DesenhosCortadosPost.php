<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class DesenhosCortadosPost extends Ferramentas
{
  /**
   * Função desenhos_cortados()
   *
   * Esta função é chamada por meio de uma solicitação AJAX e é responsável por listar os usuários com base em diferentes critérios, como a data de adição e o status. Além disso, a função possibilita ações como adicionar novamente desenhos e exibir informações detalhadas sobre eles.
   *
   * Retorna um JSON contendo a lista de usuários com base nos critérios definidos.
   */
  function desenhos_cortados()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();
      $caminho = "";
      // Instancia tabelas do banco de dados
      $desenhos = new \App\Models\Desenhos();
      $prioridade = new \App\Models\Prioridade();
      $finalidade = new \App\Models\Finalidade();
      $empresa = new \App\Models\Empresa();
      $empreendimento = new \App\Models\Empreendimentos();
      $usuario = new \App\Models\Usuarios();
      $cortado = new \App\Models\Corte();
      $processos = new \App\Models\Processos();


      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $usuario_data = $usuario->find();
      $cortado_data = $cortado->find();
      $processos_data = $processos->find();
      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();

      // Recupera dados da solicitação AJAX
      $dataInicial = service('request')->getPost('data');
      $dataFinal = service('request')->getPost('data1');
      $processo = service('request')->getPost('processo');

        // Converte as datas para timestamps (apenas para geração do array de datas para exibição)
        $dataInicialTimestamp = strtotime($dataInicial);
        $dataFinalTimestamp   = strtotime($dataFinal);
        if ($dataInicialTimestamp > $dataFinalTimestamp) {
            list($dataInicialTimestamp, $dataFinalTimestamp) = array($dataFinalTimestamp, $dataInicialTimestamp);
            $dataInicial = date('Y-m-d', $dataInicialTimestamp);
            $dataFinal   = date('Y-m-d', $dataFinalTimestamp);
        }

 
      
      $cortado_data = $cortado
      ->where("STR_TO_DATE(REPLACE(SUBSTRING_INDEX(data_fim, ' ', 1), 'i061n', '/'), '%d/%m/%Y') >=", $dataInicial)
      ->where("STR_TO_DATE(REPLACE(SUBSTRING_INDEX(data_fim, ' ', 1), 'i061n', '/'), '%d/%m/%Y') <=", $dataFinal)
      ->find();

      foreach ($cortado_data as $key => $value1) { //cria a lista
       
        //$_SESSION["lista_primordial"][$id_temp] = $value;

      
      //  $oi = Ferramentas::decodificador($value1["data_fim"]);
        // Verifica se a data de adição está dentro do intervalo de datas especificado



         // pega apenas os desenhos do desenhista que esta vendo
          $value = Ferramentas::array_pesquisa($desenhos_data, 'id', $value1['id_desenho']);
          if (Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($processos_data, 'id', $value['processos_id']), ["nome"])) != $processo) {
              $id_temp++;
              continue;
            }
            $tags = explode('/', Ferramentas::decodificador($value['caminho']));

            // Remover os índices de 0 a 5
            $tags = array_slice($tags, 6);

            // Remover o último elemento
            unset($tags[count($tags) - 1]);
            $tags = implode(" - ", $tags);
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);
          //if (Ferramentas::decodificador($value['status']) == 'pronto') {
            {
            $caminho = $value['caminho'];
            $ultima_barra_invertida = strrpos($caminho, 'i061n');

            // Dividir a string em duas partes
            $caminho_diretorio = substr($caminho, 0, $ultima_barra_invertida);
            $nome_arquivo = substr($caminho, $ultima_barra_invertida);

            // Criar o array resultante
            $array_resultante = [$caminho_diretorio, $nome_arquivo];

            $caminho = str_replace(["ci083ni061n", "wli074ndesenhos", "i061n"], ["c:/", "wl_desenhos", "/"], $array_resultante[0]) . '/' . Ferramentas::decodificador($array_resultante[1]);
            $caminho = str_replace("//", "/", $caminho);

            if (!file_exists($caminho)) {
              $value['status'] = "cortado_notfile";

              $desenhos->update($value['id'], $value);

            }
            $dataEspecifica_corte = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']));

            //<button name="cadastarar" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button>
            // Cria a linha da tabela para desenhos com status 'cortado'
            $lista .= '
      <tr>

       
       <td  bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '"><span class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span></td>

       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome']) . '</td>


       <td>' . Ferramentas::remove_id_file(substr(Ferramentas::decodificador($value['nome']), 19)) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]) . '</td>
       <td>' . Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]) . '</td>
       <td>' . $tags . '</td>
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' . $dataEspecifica_corte . '</td>
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>
       <td><button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button></td>
       <td></td>
      </tr>
      ';
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

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;


      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista
    
      

      ];

      return $this->response->setJSON($data);
    }
  }

}