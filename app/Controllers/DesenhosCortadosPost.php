<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class DesenhosCortadosPost extends Ferramentas
{


  function recolocar_desenho()
  {
    session_start();
    $id = service('request')->getPost('id');
    $status = service('request')->getPost('status');

    $Recolocar_desenho = new \App\Models\Recolocar_desenho();

    $data_recolocar = $Recolocar_desenho->where('desenho',  $_SESSION["lista_recolocar"][$id])
      ->find();

    if (count($data_recolocar) != 0) {
      if ($status == 'aprovado') {
        $data = [
          'responsavel' => $_SESSION['usuario_permissao'],
          'data_fim' => Ferramentas::codificador(date('d/m/Y H:i:s')),
          'status' => 'aprovado',
          'quantidade' => intval(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'quantidade']))+1
        
        ];
        $Recolocar_desenho->update(intval(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'id'])),$data);


        Ferramentas::re_colcoar_desenho(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'desenho']));



      } else {
        $data = [
          'responsavel' => $_SESSION['usuario_permissao'],
          'data_fim' => Ferramentas::codificador(date('d/m/Y H:i:s')),
          'status' => 'negado'
        ];
        $Recolocar_desenho->update(intval(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'id'])),$data);
      }
    }




    return $this->response->setJSON(['ok' => $data_recolocar]);
  }

  function solicitar_recolocar_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id = service('request')->getPost('id');

      $Recolocar_desenho = new \App\Models\Recolocar_desenho();


      $data_recolocar = $Recolocar_desenho->where('desenho',  $_SESSION["lista"][$id])
        ->find();

      if (count($data_recolocar) == 0) {

        $data = [
          'desenho' => $_SESSION["lista"][$id],
          'individuo' => $_SESSION['usuario'],
          'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
          'status' => 'pendente'

        ];

        $Recolocar_desenho->insert($data);
      } else {
        if (Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'status']) != 'pendente') {
          $data = [
            'desenho' => $_SESSION["lista"][$id],
            'individuo' => $_SESSION['usuario'],
            'data_add' => Ferramentas::codificador(date('d/m/Y H:i:s')),
            'status' => 'pendente',
            'quantidade' => Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'quantidade']),
            'id_anterior' => Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'id'])
          ];
          $Recolocar_desenho->insert($data);
        }
      }
      return $this->response->setJSON(['ok' => true]);
    }
  }





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
      $Recolocar_desenho = new \App\Models\Recolocar_desenho();

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

      $data_recolocar = $Recolocar_desenho->where('status', 'pendente')
        ->find();


      foreach ($cortado_data as $key => $value1) { //cria a lista

        //$_SESSION["lista_primordial"][$id_temp] = $value;


        //  $oi = Ferramentas::decodificador($value1["data_fim"]);
        // Verifica se a data de adição está dentro do intervalo de datas especificado

        $data_recolocar = $Recolocar_desenho->where('desenho',  $value1['id_desenho'])
          ->find();



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
       <td>' . Ferramentas::decodificador($value['data_hora_add']) . '</td>';

          if (count($data_recolocar) == 0 or Ferramentas::array_index($data_recolocar,[(count($data_recolocar)-1),'status']) != "pendente") {
            
            $lista .= '<td><button name="cadastarar" onclick="solicitar_recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> Solicitar <br> Recolocar </button></td>';
            
          } else {
            $lista .= '<td><button name="cadastarar"  type="submit" class="btn btn-outline-warning " disabled> Aguarde <br> Avaliação </button></td>';
          }



          $lista .= '<td></td>
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
        "lista" => $lista,
        "pendente" => (count($data_recolocar) != 0) ? 'true' : 'false'


      ];

      return $this->response->setJSON($data);
    }
  }


  function lista_recolcoar()
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
      $Recolocar_desenho = new \App\Models\Recolocar_desenho();

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




      $data_recolocar = $Recolocar_desenho->where('status', 'pendente')
        ->find();


      foreach ($data_recolocar as $key => $recolcoar) { //cria a lista



        // pega apenas os desenhos do desenhista que esta vendo
        $value = Ferramentas::array_pesquisa($desenhos_data, 'id', $recolcoar['desenho']);

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
      <tr id="Linha_'.$id_temp.'">

  <td class="quebrar"  bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '" style="max-width: 6%;">
    <span class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span>
  </td>

  <td class="quebrar"  style="max-width: 12%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome']) . '</td>

  <td class="quebrar"  style="width: 14%;">' . Ferramentas::remove_id_file(substr(Ferramentas::decodificador($value['nome']), 19)) . '</td>

  <td class="quebrar"  style="max-width: 6%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ["nome"]) . '</td>

  <td class="quebrar"  style="max-width: 12%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ["nome"]) . '</td>

  <td class="quebrar" style="max-width: 12%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ["nome"]) . '</td>

  <td class="quebrar"  style="max-width: 8%;">' . $tags . '</td>

  <td  class="quebrar" style="max-width: 6%;">' . Ferramentas::decodificador($value['status']) . '</td>

  <td class="quebrar"  style="width: 100px;">' . $dataEspecifica_corte . '</td>

  <td  class="quebrar" style="width: 100px;">' . Ferramentas::decodificador($value['data_hora_add']) . '</td>

  <td class="quebrar"  style="width: 105px;">
    <button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\',\'aprovado\')" type="submit" class="btn btn-outline-primary"> Aprovar </button>
  </td>

  <td class="quebrar"  style="width: 95px;">
    <button name="cadastarar" onclick="recolocar_desenho(\'' . $id_temp . '\',\'negado\')" type="submit" class="btn btn-outline-danger"> Negar </button>
  </td>

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

      $_SESSION["lista_recolocar"] = $lista_ids;
      $_SESSION["lista_completa_recolocar"] = $lista_completa;


      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista,
        "pendente" => (count($data_recolocar) != 0) ? 'true' : 'false'


      ];

      return $this->response->setJSON($data);
    }
  }
}
