<?php
namespace App\Controllers;


use App\Controllers\Ferramentas;


class ProcessosPost extends Ferramentas
{


 /**
 * Cadastra um novo processo no banco de dados, validando os dados fornecidos via requisição AJAX.
 * 
 * Esta função realiza as seguintes operações:
 * - Validação dos campos 'nome', 'diretório' e 'extensão' de acordo com os critérios de tamanho e caracteres permitidos.
 * - Verificação da existência de tipos de arquivo na lista de extensões permitidas.
 * - Verificação da existência prévia do processo no banco de dados para evitar duplicações.
 * - Inserção de violações no banco de dados caso alguma validação falhe.
 * - Caso todas as validações sejam aprovadas, o processo é cadastrado com suas informações no banco de dados.
 * 
 */
  function processos_cadastrar()
  { 
      if ($this->request->isAJAX()) {
        $msg = array();
        $ok = false;
        $violacao = array();
        $nome = service('request')->getPost('nome');
        $diretorio = service('request')->getPost('diretorio');
        $extencao = service('request')->getPost('extencao');

        session_start();
        if (strlen($nome) > 100) {
          $msg['Nome'] = "Nome excedeu o tamanho máximo de 100 caracter";
          $violacao[] = "processos_cadastrar nome excedeu o tamanho máximo";
        }

        if (strlen($nome) < 2) {
          $msg['Nome'] = "Nome não possui o tamanho mínimo de 2 caracter";
        } else {
          if (Ferramentas::codificador($nome) == '') {
            $msg['Nome'] = "Nome possui caracteres não permitidos";
            $violacao[] = "processos_cadastrar nome possui caracteres não permitidos";
          }
        }

        if (strlen($diretorio) > 100) {
          $msg['Nome da Pasta'] = "Nome da Pasta excedeu o tamanho máximo de 100 caracter";
          $violacao[] = "processos_cadastrar diretorio excedeu o tamanho máximo";
        }

        if (strlen($diretorio) < 2) {
          $msg['Nome da Pasta'] = "Nome da Pasta não possui o tamanho mínimo de 2 caracter";
        } else {
          if (Ferramentas::codificador($diretorio) == '') {
            $msg['Nome da Pasta'] = "Nome da Pasta possui caracteres não permitidos";
            $violacao[] = "processos_cadastrar diretorio possui caracteres não permitidos";
          }
        }


        foreach (explode('-', str_replace('_', ' ', $extencao)) as $key => $value) {
          if (!in_array($value, explode(',',  $_SESSION["lista_extencao"]))) {
            $msg['Tipo de Arquico'] = "Tipo de Arquico não existe: " . $value;
            $violacao[] = "processos_cadastrar tipo de arquivo nao existe";
          }
        }
        if($extencao == ''){
          $msg['Tipo de Arquico'] = "Tipo de Arquico não existe selecionados";
        }


        if (count($msg) == 0 and count($violacao) == 0) {


          $prioridade = new \App\Models\Filtros(); //pega do banco a tabela

          $prioridade_data = $prioridade->find();
          $filtros_array = array();
          foreach (explode('-', $extencao) as $key1 => $value1) {
            foreach ($prioridade_data as $key => $value) { //cria a lista 
              if (Ferramentas::decodificador($value['nome']) == Ferramentas::decodificador(substr($value1, 1))) {
                $filtros_array[] = $value['id'];
              }
            }
          }


          $db = new \App\Models\Processos();
     


          $processos_data = $db->find();

          if (count(Ferramentas::array_pesquisa_mult($processos_data, ['nome', 'diretorio'], [Ferramentas::codificador($nome), Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) == 0 and
              count(Ferramentas::array_pesquisa_mult($processos_data, ['diretorio'], [Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) == 0) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

            $date = [
              'nome' => Ferramentas::codificador($nome),
              'diretorio' => (Ferramentas::norma_lizar_str($diretorio)),
              'filtros_id' => implode('-',$filtros_array),
              'data_hora_add' => Ferramentas::codificador(date('d/m/Y H:i')),
              'responsavel' => $_SESSION["usuario"],
              'status' => 'ativo'
            ];
            $db->insert($date);
            $ok = true;
          } else {
            $msg["Processo"] = 'Processo já existente';
            $violacao[] = "processos_cadastrar nivel já existente";
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
   * Gera uma lista de processos com base em seu status (ativos ou desativados) e retorna os dados formatados para exibição em uma tabela via AJAX.
   * 
   * Esta função realiza as seguintes operações:
   * - Inicializa a sessão para armazenar a lista de IDs e dados completos dos processos.
   * - Obtém dados de processos e filtros do banco de dados e organiza a lista com base nos status ativos ou desativados.
   * - Para cada processo, verifica se ele deve ser exibido como ativo ou desativado, formatando as informações em uma tabela HTML.
   * - Armazena os IDs e os detalhes completos dos processos em variáveis de sessão para uso posterior.
   * 
   */
  function processos(){
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();
      $processos = new \App\Models\Processos(); // Obtém a tabela de finalidades do banco


      $processos_data = $processos->find();
      $ativos = service('request')->getPost('ativos'); // Obtém a informação POST fornecida via AJAX para listar finalidades ativas
      $desativados = service('request')->getPost('desativados'); // Obtém a informação POST fornecida via AJAX para listar finalidades desativadas
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();
        $filtro = new \App\Models\Filtros;
        $filtro_data = $filtro->find();
      foreach ($processos_data as $key => $value) {
        $filtro = new \App\Models\Filtros;
        $filtro_data = $filtro->find();
        $filtros = array();
        foreach (explode("-",$value['filtros_id']) as $key => $value1) {
          if(Ferramentas::array_index (Ferramentas::array_pesquisa_mult($filtro_data, ['id'], [$value1]),['nome']) != ''){
          $filtros[] = '.'. Ferramentas::array_pesquisa_mult($filtro_data, ['id'], [$value1])['nome'];
        }}


        // Cria a lista com base nas finalidades ativas ou desativadas, dependendo da solicitação
        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['diretorio']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador(implode("-",$filtros)) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          $lista .= '
      <tr>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['nome']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador($value['diretorio']) . '</p></td>
       <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . Ferramentas::decodificador(implode("-",$filtros)) . '</p></td>
       <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst(Ferramentas::decodificador($value['status'])) . '</td>
       <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
       <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
      </tr>
      ';
        }


        $lista_ids[$id_temp] = $value['id'];
        $lista_completa[$id_temp] = $value;
        $id_temp++;
      }

      // Armazena os IDs e detalhes da lista na sessão para uso posterior
      $_SESSION["lista"] = $lista_ids;
      $_SESSION["lista_completa"] = $lista_completa;



      //retorna a lista para o ajax
      $data = [
        "lista" => $lista,


      ];

      return $this->response->setJSON($data);
    }
  }




  /**
 * Gera uma lista de processos ativos com base nas permissões do usuário e retorna os dados formatados via AJAX.
 * 
 * Esta função realiza as seguintes operações:
 * - Inicializa a sessão para acessar e armazenar os dados da lista de processos.
 * - Obtém os processos ativos do banco de dados e filtra os que podem ser exibidos, com base nas permissões do usuário, incluindo permissões específicas ou globais ('Processos' ou 'all').
 * - Para cada processo válido, os filtros associados são buscados e a lista é organizada em um array.
 * - Armazena a lista completa dos processos na sessão para uso posterior.
 * 
 */
  function processos_lista()
  {
    if ($this->request->isAJAX()) {
      // Inicializa a sessão para acessar os dados da lista armazenados nela
      session_start();

      $processos = new \App\Models\Processos(); // Obtém a tabela de prioridades do banco

      $processos_data = $processos->find();
      $lista = array();

      $lista_session = array();

      foreach ($processos_data as $key => $value) {
        // Cria a lista com base nas prioridades ativas
        

        if ($value['status'] == 'ativo' &&( in_array($value['nome'],$_SESSION['processos']) or in_array('Processos',$_SESSION['permissao']) or in_array('all',$_SESSION['permissao']) )) {
          $filtro = new \App\Models\Filtros;
          $filtro_data = $filtro->find();
          $filtros = array();
          
          foreach (explode("-",$value['filtros_id']) as $key => $value1) {
            if(Ferramentas::array_index (Ferramentas::array_pesquisa_mult($filtro_data, ['id'], [$value1]),['nome']) != ''){
            $filtros[] = '.'. Ferramentas::array_pesquisa_mult($filtro_data, ['id'], [$value1])['nome'];
          }}



          $temp['nome'] = Ferramentas::decodificador($value['nome']);
          $temp['filtro'] = implode(",",$filtros);

          $lista[] = $temp;

          $temp['id'] = Ferramentas::decodificador($value['id']);
          $temp['diretorio'] = Ferramentas::decodificador($value['diretorio']);
          $lista_session[] = $temp;
        }

      }
      $_SESSION['processos_lista']['lista'] = $lista_session;
      //retorna a lista para o ajax
      $data = [
        "lista" => $lista,
        "1" => $_SESSION['permissao']


      ];

      return $this->response->setJSON($data);
    }
  }




  /**
   * Gera e retorna um modal para modificar informações de um processo específico, baseado nos dados fornecidos via AJAX.
   * 
   * Esta função realiza as seguintes operações:
   * - Inicializa a sessão para acessar a lista de processos armazenados.
   * - Obtém os filtros ativos do banco de dados e organiza as opções de filtro para serem exibidas no modal.
   * - Se um ID de processo for fornecido, as informações do processo são carregadas e o modal é preenchido com seus dados. Caso contrário, o modal é inicializado vazio.
   * - Constrói o HTML do modal contendo campos de nome, diretório e filtros relacionados ao processo.
   * 
   */
  function processos_modifica_modal()
  {
    if ($this->request->isAJAX()) {
      $id = service('request')->getPost('id');
      session_start();

      $filtro_db = new \App\Models\Filtros(); // Obtém a tabela de prioridades do banco

      $filtro_data = $filtro_db->find();

      $array_filtro = array();
      foreach ($filtro_data as $value) {
        if (Ferramentas::array_index($value, ["status"]) == 'ativo') {
          $array_filtro[] = Ferramentas::decodificador(Ferramentas::array_index($value, ["nome"]));
        }
      }


      if ($id != null) {
        $lista = $_SESSION["lista_completa"][$id];
        $_SESSION["modal_id"] = $lista['id'];
      } else {
        $lista["diretorio"] = "";
        $lista['nome'] = "";
      }



      $enable_filtros = "disabled";
      $option_filtros = "";
      $filtro = new \App\Models\Filtros;
      $filtro_data = $filtro->find();
      $filtros = array();
      foreach (explode("-",$lista['filtros_id']) as $key => $value1) {
        if(Ferramentas::array_index (Ferramentas::array_pesquisa_mult($filtro_data, ['id'], [$value1]),['nome']) != ''){
        $filtros[] =  Ferramentas::decodificador(Ferramentas::array_pesquisa_mult($filtro_data, ['id'], [$value1])['nome']);
      }}

      foreach ($array_filtro as $key => $item) {
        if (in_array($item, $filtros)) {
          $option_filtros .= '<option value="' . $item . '" selected>.'. $item . '</option>';
        } else {
          $option_filtros .= '<option value="' . $item . '">.'. $item . '</option>';
          $check_filtros = "";
          $enable_filtros = "";

        }
      }




      $conteudo = [
        0 => '<div class="form-group">
        <label>Nome</label>
        <input type="text" class="form-control" id="nome_processos_novo_modal" placeholder="Novo Processo" value="' . $lista["nome"] . '">
      </div>
      <div class="form-group">
      <label>Pasta</label>
        <input type="text" class="form-control" id="diretorio_novo_modal" placeholder="Nome da Pasta" value="' . $lista["diretorio"] . '">
      </div>

            <div class="form-group">
            <label>Processos</label><br/> 
            <select multiple="multiple" class="form-control" id="extencao_novo_modal" ' . $enable_filtros . '>' . $option_filtros . ' </select>
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


      $data = ['modal' => $modal, 'conteudo' => $conteudo[0]];
      return $this->response->setJSON($data);
    }
  }



  /**
   * Modifica as informações de um processo existente no banco de dados com base nos dados fornecidos via AJAX.
   * 
   * Esta função realiza as seguintes operações:
   * - Valida os campos 'nome', 'diretório' e 'extensão' de acordo com os critérios de tamanho e caracteres permitidos.
   * - Verifica a existência dos tipos de arquivo selecionados nos filtros disponíveis.
   * - Se todas as validações forem aprovadas, o processo é atualizado no banco de dados.
   * - Caso o processo já exista com os mesmos dados ou não haja alterações, uma mensagem apropriada é retornada.
   * - Registra violações, se houver, no banco de dados, armazenando a causa e o usuário responsável.
   * 
   */
  function processos_modificar()
  { 
      if ($this->request->isAJAX()) {
        $msg = array();
        $ok = false;
        $violacao = array();
        $nome = service('request')->getPost('nome');
        $diretorio = service('request')->getPost('diretorio');
        $extencao = service('request')->getPost('extencao');

        session_start();
        if (strlen($nome) > 100) {
          $msg['Nome'] = "Nome excedeu o tamanho máximo de 100 caracter";
          $violacao[] = "processos_modificar nome excedeu o tamanho máximo";
        }

        if (strlen($nome) < 2) {
          $msg['Nome'] = "Nome não possui o tamanho mínimo de 2 caracter";
        } else {
          if (Ferramentas::codificador($nome) == '') {
            $msg['Nome'] = "Nome possui caracteres não permitidos";
            $violacao[] = "processos_modificar nome possui caracteres não permitidos";
          }
        }

        if (strlen($diretorio) > 100) {
          $msg['Nome da Pasta'] = "Nome da Pasta excedeu o tamanho máximo de 100 caracter";
          $violacao[] = "processos_modificar diretorio excedeu o tamanho máximo";
        }

        if (strlen($diretorio) < 2) {
          $msg['Nome da Pasta'] = "Nome da Pasta não possui o tamanho mínimo de 2 caracter";
        } else {
          if (Ferramentas::codificador($diretorio) == '') {
            $msg['Nome da Pasta'] = "Nome da Pasta possui caracteres não permitidos";
            $violacao[] = "processos_modificar diretorio possui caracteres não permitidos";
          }
        }
        $filtro = new \App\Models\Filtros;
        $filtro_data = $filtro->find();
        $filtros = array();
        foreach ($filtro_data as $key => $value1) {
          if($value1['status'] == 'ativo'){
          $filtros[] =  Ferramentas::decodificador($value1['nome']);
        }}
  

        foreach (explode('-', str_replace('_', ' ', $extencao)) as $key => $value) {
          if (!in_array($value, $filtros)) {
            $msg['Tipo de Arquico'] = "Tipo de Arquico não existe: " . $value;
            $violacao[] = "processos_modificar tipo de arquivo nao existe";
          }
        }
        if($extencao == ''){
          $msg['Tipo de Arquico'] = "Tipo de Arquico não existe selecionados";
        }


        if (count($msg) == 0 and count($violacao) == 0) {


          $prioridade = new \App\Models\Filtros(); //pega do banco a tabela

          $prioridade_data = $prioridade->find();
          $filtros_array = array();
          foreach (explode('-', $extencao) as $key1 => $value1) {
            foreach ($prioridade_data as $key => $value) { //cria a lista 
              if (Ferramentas::decodificador($value['nome']) == Ferramentas::decodificador($value1)) {
                $filtros_array[] = $value['id'];
              }
            }
          }


          $db = new \App\Models\Processos();
          $processos_data = $db->find();

          $id = $_SESSION["modal_id"];

          if (count(Ferramentas::array_pesquisa_mult($processos_data, ['nome', 'diretorio','filtros_id'], [Ferramentas::codificador($nome), Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio)),implode('-',$filtros_array)])) == 0 and ((count(Ferramentas::array_pesquisa_mult($processos_data, ['diretorio'], [Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) == 0 or count(Ferramentas::array_pesquisa_mult($processos_data, ['id','diretorio'], [$id,Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio))])) != 0) and 
          (count(Ferramentas::array_pesquisa_mult($processos_data, ['nome'], [Ferramentas::codificador($nome)])) == 0 or count(Ferramentas::array_pesquisa_mult($processos_data, ['id','nome'], [$id,Ferramentas::codificador($nome)])) != 0))) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 

            $date = [
              'nome' => Ferramentas::codificador($nome),
              'diretorio' => (Ferramentas::norma_lizar_str($diretorio)),
              'filtros_id' => implode('-',$filtros_array),
            ];
            $db->update($id ,$date);
            $ok = true;
          } else {
            if(count(Ferramentas::array_pesquisa_mult($processos_data, ['id','nome', 'diretorio','filtros_id'], [$id,Ferramentas::codificador($nome), Ferramentas::codificador(Ferramentas::norma_lizar_str($diretorio)),implode('-',$filtros_array)])) == 0){
            $msg["Processo"] = 'Processo já existente';
            $violacao[] = "processos_modificar prcoesso já existente";
            }else{
              $msg["Processo"] = 'Não houve alteração';
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

}