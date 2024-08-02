<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;


class NivelPost extends Ferramentas
{
    //as paginas que possui no sistema e colocar no Login.php tambem
    private static $array_niveis = array(
        'Adicionar',
        'Meus Desenhos',
        'Lista De Corte',
        'Lista De Corte ADM',
        'Subpasta',
        'Desenhos cortados',
        'Tipo De Arquivo',
        'Prioridade',
        'Fialidade',
        'Empresa',
        'Empreendimento',
        'Nível',
        'Usuario',
        'Relátorio',
        'Lista De Corte Cortador'
      );

    function nivel_lista()
    {
      if ($this->request->isAJAX()) {
        $ativo = service('request')->getPost('ativos');
        $desativado = service('request')->getPost('desativados');
  
  
        session_start();
  
        $id_temp = -1;
  
        $db_nivel = new \App\Models\Nivel();
  
        $nivel_data = $db_nivel->find();
  
        $lista = "";
        $lista_array = array();
        foreach ($nivel_data as $key => $value) {
          if ((Ferramentas::decodificador($value['status']) == "ativo" and $ativo == "true") or (Ferramentas::decodificador($value['status']) == "desativado" and $desativado == "true")) {
            $id_temp += 1;
  
            $lista .= '<tr>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . Ferramentas::decodificador($value['nome']) . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . str_replace(['_', '-'], [' ', ' - '], Ferramentas::decodificador($value['permissao'])) . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . str_replace(['_', '-'], [' ', ' - '], Ferramentas::decodificador($value['processos'])) . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . Ferramentas::decodificador($value['status']) . '</td>
  
              ';
            if (Ferramentas::decodificador($value['status']) == "ativo") {
              $lista .= "<td><button name='cadastrarar' type='submit' onclick='desativar(" . $id_temp . ")' class='btn btn-outline-danger btn-lg btn-block'> Desativar </button></td>";
            } else {
              $lista .= "<td><button name='cadastrarar' type='submit' onclick='ativar(" . $id_temp . ")' class='btn btn-outline-success btn-lg btn-block'> Ativar </button></td>";
            }
  
            $lista .= "</tr>";
            $lista_array[$id_temp] = [
              'processos' => Ferramentas::decodificador($value['processos']),
              'permissao' => Ferramentas::decodificador($value['permissao']),
              'nome' => Ferramentas::decodificador($value['nome']),
              'status' => Ferramentas::decodificador($value['status']),
              'id' => $value['id']
            ];
          }
        }
        $_SESSION["lista"] = $lista_array;
  
        $data = ['lista' => $lista];
        return $this->response->setJSON($data);
      }
    }

    function nivel_lista_desativar()
    {
      if ($this->request->isAJAX()) {
        session_start();
        $id = service('request')->getPost('id');
        $lista = $_SESSION["lista"];
  
  
        $db = new \App\Models\Nivel();
  
        $value['status'] = "desativado";
  
        $db->update($lista[$id]['id'], $value);
  
        $data = ['lista' => 'true'];
        return $this->response->setJSON($data);
      }
    }

    function nivel_lista_ativar()
    {
      if ($this->request->isAJAX()) {
        session_start();
        $id = service('request')->getPost('id');
        $lista = $_SESSION["lista"];
  
  
        $db = new \App\Models\Nivel();
  
        $value['status'] = "ativo";
  
        $db->update($lista[$id]['id'], $value);
  
        $data = ['lista' => 'true'];
        return $this->response->setJSON($data);
      }
    }

    function nivel_modifica_modal()
    {
      if ($this->request->isAJAX()) {
        $id = service('request')->getPost('id');
        session_start();
  
        if($id!=null){
        $lista = $_SESSION["lista"][$id];
        $_SESSION["modal_id"] = $lista['id'];
        }else{
          $lista["permissao"] = "";
          $lista["processos"] = "";
          $lista['nome'] = "";
        }
        $option = "";
  
        foreach (self::$array_niveis as $item) {
          if (in_array(str_replace(' ', '_', $item), explode('-', $lista["permissao"]))) {
            $option .= '<option value="' . str_replace(' ', '_', $item) . '" selected>' . $item . '</option>';
          } else {
            $option .= '<option value="' . str_replace(' ', '_', $item) . '">' . $item . '</option>';
          }
        }

        $enable = "";
        $check = "";
        if ($lista["permissao"] == "all") {
          $enable = "disabled";
          $check = "checked";
        }

        $check_relatorio = "";
        if (in_array("relatorio",explode('-',$lista["processos"]))) {
          $check_relatorio = "checked";
        }
        $conteudo = [
          0 => '<div class="form-group">
        <label>Nome</label>
        <input type="text" class="form-control" id="nivel_novo" placeholder="Novo Nível" value="' . $lista["nome"] . '">
      </div>
      <div class="form-group">
        <input type="checkbox" class="" id="checkbox_relatorio" ' . $check_relatorio . '><label for="scales">&nbsp; Aparecer nos relatório.</label>
      </div>

      <div class="form-group">
        <label>Permissões</label><br/> <input type="checkbox" class="" id="checkbox_todos" onclick="selecionar_todos()" ' . $check . '><label for="scales">&nbsp; Selecionar todos.</label>
        <select multiple="multiple" class="form-control" id="permissao_novo" ' . $enable . '>' . $option . ' </select>
            </div>'
        ];
        
        $modal = '<div id="modal" class="modal-1" style="display: block;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modal_titulo">Modificar Setor: ' . $lista['nome'] . '</h5>
              <button type="button" class="close" onclick="fecharModal()">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body" id="modal_bory"><div class="form-group">
        ' . $conteudo[0] . '
    
    
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">Cancelar</button>
              <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="confirmarModal()">Confirmar</button>
            </div></div></div>
          </div>
        </div>
      </div>';

        
        $data = ['modal' => $modal,'conteudo' => $conteudo[0]];
        return $this->response->setJSON($data);
      }
    }

    function nivel_modificar()
    {
      if ($this->request->isAJAX()) {
        $_SESSION['desenho_add_proc'] = isset($_SESSION['desenho_add_proc']) ? $_SESSION['desenho_add_proc'] : FALSE;
        if ($_SESSION['desenho_add_proc']) {
          return;
        } else {
          $_SESSION['desenho_add_proc'] = TRUE;
        }
  
        $msg = array();
        $ok = false;
        $violacao = array();
        $nivel = service('request')->getPost('nivel');
        $permissao = service('request')->getPost('permissao');
        $relatorio = service('request')->getPost('relatorio');
  
        if (strlen($nivel) > 30) {
          $msg['Nível'] = "Nome do nível excedeu o tamanho máximo de 30 caracter";
          $violacao[] = "nivel_cadastrar nivel excedeu o tamanho máximo";
        }
  
        if (strlen($nivel) < 2) {
          $msg['Nível'] = "Nome do nível não possui o tamanho mínimo de 2 caracter";
        } else {
          if (Ferramentas::codificador($nivel) == '') {
            $msg['Nível'] = "Nome do nível possui caracteres não permitidos";
            $violacao[] = "nivel_cadastrar nivel possui caracteres não permitidos";
          }
        }
  
        if (strlen($permissao) < 2) {
          $msg['Permissao'] = "Nenhuma Permissão escolhida.";
        } else if ($permissao != "all") {
  
          // Verificar se todos os valores existem no array global
          foreach (explode('-', str_replace('_', ' ', $permissao)) as $valor) {
            if (!in_array($valor, self::$array_niveis)) {
              $msg['Permissao'] = "Permissão não encontrada.";
              $violacao[] = "nivel_cadastrar permissão não encontrada.";
            }
          }
        }
  
        session_start();
        if (count($msg) == 0 and count($violacao) == 0) {
  
          $db = new \App\Models\Nivel();
          $processos = array();
          if($relatorio  == "true"){
            $processos[] = "relatorio";
          }
          
          $nivel_data = $db->find();
          $id = $_SESSION["modal_id"];
          $nome = Ferramentas::array_pesquisa($nivel_data, 'id', $id);
          if (count(Ferramentas::array_pesquisa_mult($nivel_data, ['nome', 'permissao','processos'], [Ferramentas::codificador($nivel), Ferramentas::codificador($permissao), Ferramentas::codificador(implode('-',$processos))])) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
  
            $date = [
              'nome' => Ferramentas::codificador($nivel),
              'permissao' => Ferramentas::codificador($permissao),
              'processos' => Ferramentas::codificador(implode('-',$processos))
            ];
            $db->update($id, $date);
            $ok = true;
          } else {
            if ($nome['nome'] != Ferramentas::codificador($nivel)) {
              $msg["Nível"] = 'Nome do Nível já existente';
              $violacao[] = "nivel_cadastrar nivel já existente";
            } else {
              $msg["Nível"] = 'Nível não houve alteração';
            }
  
          }
  
  
        }
        if (count($violacao) != 0) {
  
          //$violacao = Ferramentas::array_codificar($violacao);
          $db = new \App\Models\Violacao();
          foreach ($violacao as $key => $value) {
  
            $data = [
              "individuo" => $_SESSION["usuario"],
              "causa" => $value,
              "data" => Ferramentas::codificador(date('d/m/Y H:i'))
  
            ];
  
            $db->insert($data);
  
          }
        }
  
        $data = ['ok' => $ok, 'msg' => $msg];
        return $this->response->setJSON($data);
  
  
  
      }
    }

    function nivel_cadastrar_modal()
    {
      if ($this->request->isAJAX()) {
  
  
        $option = "";
        foreach (self::$array_niveis as $item) {
          $option .= '<option value="' . str_replace(' ', '_', $item) . '">' . $item . '</option>';
        }
  
        $conteudo = [
          0 => '<div class="form-group">
        <label>Nome</label>
        <input type="text" class="form-control" id="nivel_novo" placeholder="Novo Nível">
      </div>
      <div class="form-group">
        <label>Permissões</label><br/> <input type="checkbox" class="" id="checkbox_todos" onclick="selecionar_todos()"><label for="scales">&nbsp; Selecionar todos</label>
        <select multiple="multiple" class="form-control" id="permissao_novo">' . $option . ' </select>
            </div>'
        ];
  
        $modal = Ferramentas::modal("Cadastrar Função", $conteudo[0], '', 'cadastrar()');
        $data = ['modal' => $modal];
        return $this->response->setJSON($data);
      }
    }

    function nivel_cadastrar()
    {
      if ($this->request->isAJAX()) {
        $msg = array();
        $ok = false;
        $violacao = array();
        $nivel = service('request')->getPost('nivel');
        $permissao = service('request')->getPost('permissao');
        $relatorio = service('request')->getPost('relatorio');

  
        if (strlen($nivel) > 30) {
          $msg['Nível'] = "Nome do nível excedeu o tamanho máximo de 30 caracter";
          $violacao[] = "nivel_cadastrar nivel excedeu o tamanho máximo";
        }
  
        if (strlen($nivel) < 2) {
          $msg['Nível'] = "Nome do nível não possui o tamanho mínimo de 2 caracter";
        } else {
          if (Ferramentas::codificador($nivel) == '') {
            $msg['Nível'] = "Nome do nível possui caracteres não permitidos";
            $violacao[] = "nivel_cadastrar nivel possui caracteres não permitidos";
          }
        }
  
        if (strlen($permissao) < 2) {
          $msg['Permissao'] = "Nenhuma Permissão escolhida.";
        } else if ($permissao != "all") {
  
          // Verificar se todos os valores existem no array global
          foreach (explode('-', str_replace('_', ' ', $permissao)) as $valor) {
            if (!in_array($valor, self::$array_niveis)) {
              $msg['Permissao'] = "Permissão não encontrada.";
              $violacao[] = "nivel_cadastrar permissão não encontrada.";
            }
          }
        }
  
        session_start();
        if (count($msg) == 0 and count($violacao) == 0) {
  
          $db = new \App\Models\Nivel();
          $processos = array();
          if($relatorio  == "true"){
            $processos[] = "relatorio";
          }
          
          $nivel_data = $db->find();
  
          if (count(Ferramentas::array_pesquisa($nivel_data, 'nome', Ferramentas::codificador($nivel))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
  
            $date = [
              'nome' => Ferramentas::codificador($nivel),
              'permissao' => Ferramentas::codificador($permissao),
              'processos' => Ferramentas::codificador(implode('-',$processos)),
              'status' => 'ativo'
            ];
            $db->insert($date);
            $ok = true;
          } else {
            $msg["Nível"] = 'Nome do nível já existente';
            $violacao[] = "nivel_cadastrar nivel já existente";
          }
  
  
        }
        if (count($violacao) != 0) {
  
          //$violacao = Ferramentas::array_codificar($violacao);
          $db = new \App\Models\Violacao();
          foreach ($violacao as $key => $value) {
  
            $data = [
              "individuo" => $_SESSION["usuario"],
              "causa" => $value,
              "data" => Ferramentas::codificador(date('d/m/Y H:i'))
  
            ];
  
            $db->insert($data);
  
          }
        }
  
        $data = ['ok' => $ok, 'msg' => $msg];
        return $this->response->setJSON($data);
  
  
  
      }
    }

    function nivel_option()
    {
      if ($this->request->isAJAX()) {
        $db = new \App\Models\Nivel();
        $nivel_data = $db->find();
        $array = [];
        $option = "<option value=''>Novo Nível</option>";
        session_start();
        foreach ($nivel_data as $key => $value) {
          if($value["status"] != 'ativo')
            continue;

          $array[Ferramentas::decodificador($value["nome"])] = $value["id"];
  
          $option .= "<option value='" . Ferramentas::decodificador($value["nome"]) . "'>" . Ferramentas::decodificador($value["nome"]) . "</option>";
        }
        $_SESSION['nivel_option'] = $array;
        $data = ['option' => $option];
        return $this->response->setJSON($data);
  
      }
    }


    /**
   * Função lista_nivel()
   *
   * Esta função é responsável por buscar informações sobre funções no banco de dados e retorná-las em formato JSON.
   *
   * Retorna um JSON contendo uma lista de nomes de funções obtidos do banco de dados.
   */
  function lista_nivel()
  {
    $funcao = new \App\Models\Nivel(); // Inicializa o modelo de Função para acessar o banco de dados

    $funcao_data = $funcao->find(); // Busca dados sobre funções no banco de dados
    $lista = array();

    // Cria uma lista de nomes de funções decodificadas
    foreach ($funcao_data as $key => $value) { //cria a lista 
      if($value["status"] != 'ativo')
      continue;
    
      $lista[] = Ferramentas::decodificador($value['nome']);
    }
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }
}

