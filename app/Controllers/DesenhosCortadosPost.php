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

    $data_recolocar = $Recolocar_desenho->where('desenhos_id',  $_SESSION["lista_recolocar"][$id])
      ->find();

    if (count($data_recolocar) != 0) {
      if ($status == 'aprovado') {
       //return $this->response->setJSON(  Ferramentas::re_colcoar_desenho(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'desenhos_id'])));
        $data = [
          'usuario_id_confirmado' => $_SESSION['usuario_permissao'],
          'status' => 'aprovado',
          'quantidade' => intval(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'quantidade']))+1
        
        ];
        $Recolocar_desenho->update(intval(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'id'])),$data);


        Ferramentas::re_colcoar_desenho(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'desenhos_id']));



      } else {
        $data = [
          'usuario_id_confirmado' => $_SESSION['usuario_permissao'],
          'status' => 'negado'
        ];
        $Recolocar_desenho->update(intval(Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'id'])),$data);
      }
    }




    return $this->response->setJSON(['ok' => true,$data_recolocar]);
  }

  function solicitar_recolocar_desenho()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $id = service('request')->getPost('id');

      $Recolocar_desenho = new \App\Models\Recolocar_desenho();


      $data_recolocar = $Recolocar_desenho
      ->where('desenhos_id',  $_SESSION["lista"][$id])->find();

      if (count($data_recolocar) == 0) {

        $data = [
          'desenhos_id' => $_SESSION["lista"][$id],
          'usuario_id_pedido' => $_SESSION['usuario'],
          'status' => 'pendente'

        ];

        $Recolocar_desenho->insert($data);
      } else {
        if (Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'status']) != 'pendente') {
          $data = [
            'desenhos_id' => $_SESSION["lista"][$id],
            'usuario_id_pedido' => $_SESSION['usuario'],
            'status' => 'pendente',
            'quantidade' => Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'quantidade']),
            'recolocar_desenho_id_anterior' => Ferramentas::array_index($data_recolocar, [(count($data_recolocar)-1), 'id'])
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

      $lista = "";
      $id_temp = 0;
      $lista_ids = array();
      $lista_completa = array();






      // Recupera dados da solicitação AJAX
      $dataInicial = service('request')->getPost('data1');
      $dataFinal = service('request')->getPost('data');
      $processo = service('request')->getPost('processo');



      $processoModel = new \App\Models\Processos();
      $proc = $processoModel
        ->where('nome', $processo)
        ->where('status', 'ativo')   
        ->first();

      $recolcoarModel = new \App\Models\Recolocar_desenho();
      $recolcoar = $recolcoarModel
        ->where('status', 'pendente')   
        ->first();
    



      if ($proc) {
        
        $start = (new \DateTime($dataInicial))
      ->setTime(0, 0, 0)
          
          ->format('Y-m-d H:i:s');
        $end   = (new \DateTime($dataFinal))
          
            ->setTime(23, 59, 59)
          ->format('Y-m-d H:i:s');


          $desenhosModel = new \App\Models\Desenhos();
          $desenhos_data = $desenhosModel
              ->select("
                  desenhos.*,
                  prioridade.nome     AS prioridade_nome,
                  prioridade.cor      AS prioridade_cor,
                  empresa.nome        AS empresa_nome,
                  empreendimentos.nome AS empreendimento_nome,
                  finalidade.nome     AS finalidade_nome,
                  usuarios.nome       AS usuario_nome,
                  corte.data_end      AS corte_data_end,
                  recolocar_desenho.status      AS recolocar_desenho_status
              ")
              ->join('prioridade',      'prioridade.id = desenhos.prioridade_id',        'left')
              ->join('empresa',         'empresa.id = desenhos.empresa_id',              'left')
              ->join('empreendimentos','empreendimentos.id = desenhos.empreendimentos_id','left')
              ->join('finalidade',      'finalidade.id = desenhos.finalidade_id',        'left')
              ->join('usuarios',        'usuarios.id = desenhos.usuario_id_desenhista',  'left')
              ->join('corte',           'corte.id = desenhos.corte_id',                  'left')
              ->join('recolocar_desenho','recolocar_desenho.desenhos_id = desenhos.id',  'left')
              ->where('corte.data_end >=', $start)
              ->where('corte.data_end <=', $end)
              ->where('desenhos.status <>',             'pendente')
              ->where('desenhos.status <>',             'cortando')
              ->where('desenhos.processos_id',          $proc['id'])
              ->findAll();

  
              //return $this->response->setJSON($desenhos_data);
      foreach ($desenhos_data as $key => $value) { //cria a lista

        $tags = explode('/', ($value['diretorio']));
        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);

        $tags = implode(" - ", $tags);

          //<button name="cadastarar" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button>
          // Cria a linha da tabela para desenhos com status 'cortado'
          $lista .= '
          <tr>
          <td  bgcolor="' . $value['prioridade_cor'] . '"><span class="marca_texto">' . $value['prioridade_nome']  . '</span></td>
          <td>' . $value['usuario_nome'] . '</td>
          <td>' . Ferramentas::remove_id_file($value['nome']) . '</td>
          <td>' . $value['empresa_nome'] . '</td>
          <td>' . $value['empreendimento_nome'] . '</td>
          <td>' . $value['finalidade_nome'] . '</td>
          <td>' . $tags . '</td>
          <td>' . $value['status'] . '</td>
          <td>' . Ferramentas::formatarDataHora($value['corte_data_end']) . '</td>
          <td>' . Ferramentas::formatarDataHora($value['data_add']) . '</td>';
        
          if ($value['recolocar_desenho_status'] != "pendente") {
            
            $lista .= '<td><button onclick="ver_dxf(\'' . $id_temp . '\')" class="btn btn-outline-info">︾</button><button name="cadastarar" onclick="solicitar_recolocar_desenho(\'' . $id_temp . '\')" type="submit" class="btn btn-outline-primary "> Solicitar <br> Recolocar </button></td>';
            
          } else {
            $lista .= '<td><button name="cadastarar"  type="submit" class="btn btn-outline-warning " disabled> Aguarde <br> Avaliação </button></td>';
          }



          $lista .= '<td></td>
      </tr>
      ';
          // Prepara dados do usuário para armazenamento em arrays
          $value['cor'] = $value['prioridade_cor'];
          $value['finalidade'] = $value['finalidade_nome'];
          $value['empresa'] = $value['empresa_nome'];
          $value['empreendimento'] = $value['empreendimento_nome'];
          $value['data_hora_add'] = $value['data_add'];
          $value['prioridade'] = $value['prioridade_nome'];
          $lista_ids[$id_temp] = $value['id'];
          $value['id'] = $id_temp;
          $lista_completa[$id_temp] = $value;
          $id_temp++;
        
      }

      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;
      $pendente = (is_array($recolcoar) && count($recolcoar) > 0);
      // Resposta do AJAX que retorna a lista e as datas
      $data = [
        "lista" => $lista,
        "pendente" => $pendente ? 'true' : 'false',


      ];

      return $this->response->setJSON($data);
    }
    $pendente = (is_array($recolcoar) && count($recolcoar) > 0);

    return $this->response->setJSON([
        'lista'    => $processo,
        'pendente' => $pendente ? 'true' : 'false',
    ]);

  }}



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



    
        $value = $desenhos->where('id', $recolcoar['desenhos_id'])
        ->find();
     
        if(count($value) == 0){
          continue;
        }
        $value = $value[0];
         // return $this->response->setJSON(['1' => $value]);
         $value['diretorio'] = str_replace('\\','',$value['diretorio']);
        $tags = explode('/', Ferramentas::decodificador($value['diretorio']));

        // Remover os índices de 0 a 5
        $tags = array_slice($tags, 6);

        // Remover o último elemento
        unset($tags[count($tags) - 1]);
        $tags = implode(" - ", $tags);
        $prioridade_desenho = Ferramentas::array_pesquisa($prioridade_data, 'id', $value['prioridade_id']);
        //if (Ferramentas::decodificador($value['status']) == 'pronto') {
        {
           
          $caminhoLocal = Ferramentas::wlStoragePath((string) ($value['diretorio'] ?? ''));
          $caminho = Ferramentas::wlNasPath($caminhoLocal);

          if (!file_exists($caminhoLocal)) {
            $value['status'] = "cortado_notfile";

            $desenhos->update($value['id'], $value);
          }
       
          $dataEspecifica_corte = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_end']));

          //<button name="cadastarar" type="submit" class="btn btn-outline-primary "> adicionar <br> novamente </button>
          // Cria a linha da tabela para desenhos com status 'cortado'
          
          $lista .= '
      <tr id="Linha_'.$id_temp.'">

      <td class="quebrar"  bgcolor="' . Ferramentas::decodificador($prioridade_desenho['cor']) . '" style="max-width: 6%;">
        <span class="marca_texto">' . Ferramentas::decodificador($prioridade_desenho['nome']) . '</span>
      </td>

      <td class="quebrar"  style="max-width: 12%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['usuario_id_desenhista']), ['nome']) . '</td>

      <td class="quebrar"  style="width: 14%;">' . Ferramentas::remove_id_file(substr(Ferramentas::decodificador($value['nome']), 19)) . '</td>

      <td class="quebrar"  style="max-width: 6%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ["nome"]) . '</td>

      <td class="quebrar"  style="max-width: 12%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimentos_id']), ["nome"]) . '</td>

      <td class="quebrar" style="max-width: 12%;">' . Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade_id']), ["nome"]) . '</td>

      <td class="quebrar"  style="max-width: 8%;">' . $tags . '</td>

      <td  class="quebrar" style="max-width: 6%;">' . Ferramentas::decodificador($value['status']) . '</td>

      <td class="quebrar"  style="width: 100px;">' . $dataEspecifica_corte . '</td>

      <td  class="quebrar" style="width: 100px;">' . Ferramentas::decodificador($value['data_add']) . '</td>

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
          $value['finalidade'] = Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade_id']), ["nome"]);
          $value['empresa'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa_id']), ["nome"]);
          $value['empreendimento'] = Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimentos_id']), ["nome"]);
          $value['data_hora_add'] = Ferramentas::decodificador($value['data_add']);
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
