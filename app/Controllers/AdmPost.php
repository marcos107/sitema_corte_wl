<?php
namespace App\Controllers;


use App\Controllers\Ferramentas;

use Mpdf\Mpdf;

class AdmPost extends Ferramentas
{


  /**
   * Função lista_funcao()
   *
   * Esta função é responsável por buscar informações sobre funções no banco de dados e retorná-las em formato JSON.
   *
   * Retorna um JSON contendo uma lista de nomes de funções obtidos do banco de dados.
   */
  function lista_funcao()
  {
    $funcao = new \App\Models\Funcao(); // Inicializa o modelo de Função para acessar o banco de dados

    $funcao_data = $funcao->find(); // Busca dados sobre funções no banco de dados
    $lista = array();

    // Cria uma lista de nomes de funções decodificadas
    foreach ($funcao_data as $key => $value) { //cria a lista 
      $lista[] = Ferramentas::decodificador($value['nome']);
    }
    $data = ['lista' => $lista]; // Prepara os dados para serem retornados em formato JSON
    return $this->response->setJSON($data);
  }



  /**
   * Função troca_status()
   *
   * Esta função é responsável por alterar o status de um objeto no banco de dados para "ativo" ou "desativado".
   *
   * @param string $table O nome da tabela do banco de dados onde a alteração deve ser realizada.
   * @param string $status O novo status a ser definido ("ativo" ou "desativado").
   *
   * Retorna um JSON indicando se a operação foi bem-sucedida ou não.
   */
  function troca_status($table = null, $status = NULL)
  {
    if ($status == "desativado" || $status == "ativo") { // Verifica se a variável status está correta
      if ($this->request->isAJAX()) {
        session_start();
        $id = service('request')->getPost('id'); // Obtém o ID falso fornecido via AJAX
        $lista = $_SESSION["lista"]; // Obtém a lista de IDs

        if (Ferramentas::array_index($lista, [$id]) != "") { // Verifica se o ID existe na lista
          $item = '';
          switch ($table) { // Determina qual tabela do banco de dados deve ser atualizada
            case 'user':
              $db = new \App\Models\Usuarios();
              $item = "user";
              break;
            case 'empreendimentos':
              $db = new \App\Models\Empreendimentos();
              $item = "empreendimentos";
              break;
            case 'empresa':
              $db = new \App\Models\Empresa();
              $item = "empresa";
              break;
            case 'finalidade':
              $db = new \App\Models\Finalidade();
              $item = "finalidade";
              break;
            case 'prioridade':
              $db = new \App\Models\Prioridade();
              $item = "prioridade";
              break;
            case 'filtros':
              $db = new \App\Models\Filtros();
              $item = "filtros";
              break;
            case 'tag':
              $db = new \App\Models\Tag();
              $item = "tag";
              break;
            default:
              $data = [
                //caso não exista retorna que deu errado
                "ok" => false,
              ];
              return $this->response->setJSON($data);
              break;
          }
          $alteracao = new \App\Models\Alteracoes();

          // Registra a alteração no histórico de alterações
          $data = [
            "individuo" => $_SESSION["usuario"],
            "id_item" => Ferramentas::array_index($lista, [$id]),
            "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($db->find(), 'id', Ferramentas::array_index($lista, [$id])), ['status']),
            "depois" => $status,
            "item" => $item,
            "info_mais" => "status",
            "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

          ];
          $alteracao->insert($data);

          // Atualiza o status no banco de dados
          $db->update(Ferramentas::array_index($lista, [$id]), ['status' => $status]); //faz o update no banco e troca o id falso pelo verdadeiro
          $data = [
            //retorna que deu certo para o ajax
            "ok" => true,
          ];
        } else {
          $data = [
            //se o não ouver nada na lista retorna que deu errado
            "ok" => false,
          ];
        }
        return $this->response->setJSON($data);
      }
    }
  }






}