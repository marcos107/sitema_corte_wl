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
    'Lista De Corte Cortador',
    'Processos'
  );
  /**
   * Gera uma lista de níveis ativos ou desativados e retorna os dados formatados via AJAX.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo a lista formatada de níveis.
   */
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
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . str_replace('-',  ' - ', Ferramentas::decodificador($value['processos'])) . '</td>
              <td ondblclick="modal_nivel(' . $id_temp . ')">' . Ferramentas::decodificador($value['status']) . '</td>
  
              ';
          if (Ferramentas::decodificador($value['status']) == "ativo") {
            $lista .= "<td><button name='cadastrarar' type='submit' onclick='desativar(" . $id_temp . ")' class='btn btn-outline-danger btn-lg btn-block'> Desativar </button></td>";
          } else {
            $lista .= "<td><button name='cadastrarar' type='submit' onclick='ativar(" . $id_temp . ")' class='btn btn-outline-success btn-lg btn-block'> Ativar </button></td>";
          }

          $lista .= '<td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_nivel(\'' . $id_temp . '\')"> Modificar </button></td></tr>';
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
  /**
   * Desativa um nível específico no banco de dados.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON com o status da operação.
   */
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
  /**
   * Ativa um nível específico no banco de dados.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON com o status da operação.
   */
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
  /**
   * Gera o modal para modificar as informações de um nível específico, retornando as opções de permissões e processos.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o modal gerado e os dados do nível.
   */
  function nivel_modifica_modal()
  {
    if ($this->request->isAJAX()) {
      $id = service('request')->getPost('id');
      session_start();

      $processos_db = new \App\Models\Processos(); // Obtém a tabela de prioridades do banco

      $processos_data = $processos_db->find();

      $array_prcoessos = array();
      foreach ($processos_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $array_prcoessos[] = Ferramentas::decodificador(Ferramentas::array_index($value, ["nome"]));
        }
      }


      if ($id != null) {
        $lista = $_SESSION["lista"][$id];
        $_SESSION["modal_id"] = $lista['id'];
      } else {
        $lista["permissao"] = "";
        $lista["processos"] = "";
        $lista['nome'] = "";
      }
      $checkbox = "";



      $enable = "";
      $check = "";
      if ($lista["permissao"] == "all") {
        $enable = "disabled";
        $check = "checked";
      }

      foreach (self::$array_niveis as $item) {
        if (in_array(str_replace(' ', '_', $item), explode('-', $lista["permissao"]))) {
          $checkbox .= ' <label style="font-weight: normal;" id="nivel_checkbox_label" style="margin: 5px;"><input type="checkbox" value="' . str_replace(' ', '_', $item) . '" id="nivel_checkbox" ' . $enable . ' checked>' . $item . '</label>&nbsp';
        } else {
          $checkbox .= ' <label style="font-weight: normal;" id="nivel_checkbox_label" style="margin: 5px;"><input type="checkbox" value="' . str_replace(' ', '_', $item) . '" id="nivel_checkbox" ' . $enable . '>' . $item . '</label>&nbsp';
        }
      }
      $check_processos = "checked";
      $checkbox_processos = "";

      foreach ($array_prcoessos as $key => $item) {
        if (in_array($item, explode('-', $lista["processos"]))) {
          $checkbox_processos .= '<label style="font-weight: normal;" id="permissao_checkbox_label" style="margin: 5px;"><input type="checkbox" value="' . $item . '" id="permissao_checkbox" checked>' . $item . '</label>&nbsp';
        } else {
          $checkbox_processos .= '<label style="font-weight: normal;" id="permissao_checkbox_label" style="margin: 5px;"><input type="checkbox" value="' . $item . '" id="permissao_checkbox">' . $item . '</label>&nbsp';
          $check_processos = "";
        }
      }

      if ($check_processos == "checked") {
        $checkbox_processos = str_replace("selected", "disabled", $checkbox_processos);
      }



      $check_relatorio = "";
      if (in_array("relatorio", explode('-', $lista["processos"]))) {
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
        <label>Permissões</label><br/> <input type="checkbox" class="" id="checkbox_todos" onclick="marcar_todos_nivel(this)" ' . $check . '><label for="scales">&nbsp; Selecionar todos.</label>
        <br/>
        ' .  $checkbox . '
            </div>

            <div class="form-group">
            <label>Processos</label><br/> <input type="checkbox" class="" id="checkbox_todos_processos" onclick="marcar_todos_processos(this)" ' . $check_processos . '><label for="scales">&nbsp; Selecionar todos.</label>
           <br/>
            ' . $checkbox_processos . '
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


      $data = ['modal' => $modal, 'conteudo' => $conteudo[0], "1" => $array_prcoessos, "2" => $lista["processos"], '3' => explode('-', $lista["processos"])];
      return $this->response->setJSON($data);
    }
  }

  /**
   * Modifica as informações de um nível no banco de dados com base nos dados fornecidos via AJAX.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o status da operação e eventuais mensagens de erro.
   */
  function nivel_modificar()
  {
    if ($this->request->isAJAX()) {


      $msg = array();
      $ok = false;
      $violacao = array();
      $nivel = service('request')->getPost('nivel');
      $permissao = service('request')->getPost('permissao');
      $relatorio = service('request')->getPost('relatorio');
      $processos = service('request')->getPost('processos');

      $processos_db = new \App\Models\Processos(); // Obtém a tabela de prioridades do banco

      $processos_data = $processos_db->find();

      if (strlen($nivel) > 30) {
        $msg['Nível'] = "Nome do nível excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "nivel_modificar nivel excedeu o tamanho máximo";
      }

      if (strlen($nivel) < 2) {
        $msg['Nível'] = "Nome do nível não possui o tamanho mínimo de 2 caracter";
      } else {
        if (Ferramentas::codificador($nivel) == '') {
          $msg['Nível'] = "Nome do nível possui caracteres não permitidos";
          $violacao[] = "nivelnivel_modificarcadastrar nivel possui caracteres não permitidos";
        }
      }

      if (strlen($permissao) < 2) {
        $msg['Permissao'] = "Nenhuma Permissão escolhida.";
      } else if ($permissao != "all") {

        // Verificar se todos os valores existem no array global
        foreach (explode('-', str_replace('_', ' ', $permissao)) as $valor) {
          if (!in_array($valor, self::$array_niveis)) {
            $msg['Permissao'] = "Permissão não encontrada.";
            $violacao[] = "nivel_modificar permissão não encontrada.";
          }
        }
      }

      $array_prcoessos = array();
      foreach ($processos_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $array_prcoessos[] = Ferramentas::decodificador(Ferramentas::array_index($value, ["nome"]));
        }
      }

      if ($processos != null)
        if ($processos != "all") {
          // Verificar se todos os valores existem no array global
          foreach (explode('-', $processos) as $valor) {
            if (!in_array($valor, $array_prcoessos)) {
              $msg["Processos"] = "Processo não encontrada.";
              $violacao[] = "nivel_cadastrar processo não encontrada.";
            }
          }
        } else {
          $processos = implode("-", $array_prcoessos);
        }

      session_start();
      if (count($msg) == 0 and count($violacao) == 0) {

        $db = new \App\Models\Nivel();
        $processos_salva = array();

        if ($processos != null)
          $processos_salva = explode('-', $processos);



        if ($relatorio == "true") {
          $processos_salva[] = "relatorio";
        }

        $nivel_data = $db->find();
        $id = $_SESSION["modal_id"];
        $nome = Ferramentas::array_pesquisa($nivel_data, 'id', $id);
        if (count(Ferramentas::array_pesquisa_mult($nivel_data, ['nome', 'permissao', 'processos'], [Ferramentas::codificador($nivel), Ferramentas::codificador($permissao), Ferramentas::codificador(implode('-', $processos_salva))])) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

          $date = [
            'nome' => Ferramentas::codificador($nivel),
            'permissao' => Ferramentas::codificador($permissao),
            'processos' => Ferramentas::codificador(implode('-', $processos_salva))
          ];
          $db->update($id, $date);
          $ok = true;
        } else {
          if ($nome['nome'] != Ferramentas::codificador($nivel)) {
            $msg["Nível"] = 'Nome do Nível já existente';
            $violacao[] = "nivel_modificar nivel já existente";
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
  /**
   * Gera o modal para cadastrar um novo nível, exibindo as opções de permissões.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o modal gerado.
   */
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
  /**
   * Cadastra um novo nível no banco de dados com base nos dados fornecidos via AJAX.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo o status da operação e eventuais mensagens de erro.
   */
  function nivel_cadastrar()
  {
    if ($this->request->isAJAX()) {
      $msg = array();
      $ok = false;
      $violacao = array();
      $nivel = service('request')->getPost('nivel');
      $permissao = service('request')->getPost('permissao');
      $relatorio = service('request')->getPost('relatorio');
      $processos = service('request')->getPost('processos');

      $processos_db = new \App\Models\Processos(); // Obtém a tabela de prioridades do banco

      $processos_data = $processos_db->find();


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

      if (strlen($permissao) == null) {
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
      $array_prcoessos = array();
      foreach ($processos_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $array_prcoessos[] = Ferramentas::decodificador(Ferramentas::array_index($value, ["nome"]));
        }
      }

      if ($processos != "all") {
        // Verificar se todos os valores existem no array global
        foreach (explode('-', $processos) as $valor) {
          if (!in_array($valor, $array_prcoessos)) {
            $msg['Permissao'] = "Processo não encontrada.";
            $violacao[] = "nivel_cadastrar processo não encontrada.";
          }
        }
      } else {
        $processos = implode("-", $array_prcoessos);
      }


      session_start();
      if (count($msg) == 0 and count($violacao) == 0) {

        $db = new \App\Models\Nivel();
        if ($processos != null)
          $processos_salva = explode('-', $processos);
        else
          $processos_salva = array();

        if ($relatorio == "true") {
          $processos_salva[] = "relatorio";
        }


        $nivel_data = $db->find();

        if (count(Ferramentas::array_pesquisa($nivel_data, 'nome', Ferramentas::codificador($nivel))) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

          $date = [
            'nome' => Ferramentas::codificador($nivel),
            'permissao' => Ferramentas::codificador($permissao),
            'processos' => Ferramentas::codificador(implode('-', $processos_salva)),
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

      $data = ['ok' => $ok, 'msg' => $msg, '1' => $array_prcoessos];
      return $this->response->setJSON($data);
    }
  }
  /**
   * Gera as opções de níveis ativos para serem exibidas em um select HTML.
   *
   * @return \CodeIgniter\HTTP\Response Retorna uma resposta JSON contendo as opções de níveis.
   */
  function nivel_option()
  {
    if ($this->request->isAJAX()) {
      $db = new \App\Models\Nivel();
      $nivel_data = $db->find();
      $array = [];
      $option = "<option value=''>Novo Nível</option>";
      session_start();
      foreach ($nivel_data as $key => $value) {
        if ($value["status"] != 'ativo')
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
      if ($value["status"] != 'ativo')
        continue;

      $lista[] = Ferramentas::decodificador($value['nome']);
    }
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }
}
