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
      

      // Recupera dados das tabelas do banco de dados
      $prioridade_data = $prioridade->find();
      $finalidade_data = $finalidade->find();
      $empresa_data = $empresa->find();
      $empreendimento_data = $empreendimento->find();
      $desenhos_data = $desenhos->find();
      $usuario_data = $usuario->find();
      $cortado_data = $cortado->find();
      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();

      // Recupera dados da solicitação AJAX
      $dataInicial = service('request')->getPost('data');
      $dataFinal = service('request')->getPost('data1');

      // Converte datas em timestamps para comparação
      $dataInicialTimestamp = strtotime($dataInicial);
      $dataFinalTimestamp = strtotime($dataFinal);

      // Garante que a data inicial seja menor que a data final
      if ($dataInicialTimestamp > $dataFinalTimestamp) {
        list($dataInicialTimestamp, $dataFinalTimestamp) = array($dataFinalTimestamp, $dataInicialTimestamp);
      }

      $dias = array();

      // Gera uma lista de datas entre a data inicial e a data final
      while ($dataInicialTimestamp <= $dataFinalTimestamp) {
        $dias[] = date("d/m/Y", $dataInicialTimestamp);
        $dataInicialTimestamp = strtotime("+1 day", $dataInicialTimestamp);
      }




      foreach ($desenhos_data as $key => $value) { //cria a lista
        $_SESSION["lista_primordial"][$id_temp] = $value;



        // Verifica se a data de adição está dentro do intervalo de datas especificado
        $encontrou = false;
        foreach ($dias as $data) {
          if (strpos(Ferramentas::decodificador($value['data_hora_add']), $data) !== false) {
            $encontrou = true;
            break; // Se encontrou, não precisa continuar verificando
          }
        }


        if ($encontrou) { // pega apenas os desenhos do desenhista que esta vendo
          $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade']);
          if (Ferramentas::decodificador($value['status']) == 'cortado') {
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
       <td>' . Ferramentas::decodificador($value['status']) . '</td>
       <td>' .$dataEspecifica_corte.'</td>
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
      }

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;


      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista,
        "data" => $dias,
        "1"=> $caminho

      ];

      return $this->response->setJSON($data);
    }
  }

}