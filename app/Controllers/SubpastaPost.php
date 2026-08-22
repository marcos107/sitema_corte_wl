<?php

namespace App\Controllers;

use App\Controllers\EmpresaPost;
use App\Controllers\FinalidadePost;
use App\Controllers\Ferramentas;
use Config\App;

class SubpastaPost extends EmpresaPost
{
  private function resolverSelecaoId(string $valor, string $tokenSession, string $listaSession): int
  {
    $tokens = $_SESSION[$tokenSession] ?? [];
    if (isset($tokens[$valor])) {
      return (int) $tokens[$valor];
    }

    $listaLegada = $_SESSION[$listaSession] ?? [];
    $id = array_search($valor, $listaLegada, true);
    return $id === false ? 0 : (int) $id;
  }

  /**
   * Lista tags de acordo com seu status (ativo/desativado).
   *
   * Esta função retorna uma lista de tags com base no status (ativo ou desativado) fornecido via AJAX.
   *
   * @return 
   */
  function desenho_tag()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $tag = new \App\Models\Subpasta(); // Instancia o modelo de dados para tags.

      $empreendimento = new \App\Models\Empreendimentos(); // Instancia o modelo de dados para empreendimentos.
      $finalidade = new \App\Models\Finalidade(); // Instancia o modelo de dados para finalidades.


      $tag_data = $tag->find(); // Recupera dados de tags do banco de dados.
      $ativos = service('request')->getPost('ativos'); // Verifica se é para listar tags ativas.
      $desativados = service('request')->getPost('desativados'); // Verifica se é para listar tags desativadas.
      $lista = "";
      $lista_ids = array();
      $id_temp = 0;
      $lista_completa = array();






      foreach ($tag_data as $key => $value) { //cria a lista
        $empreendimento_nome = Ferramentas::array_index($empreendimento->where('id', $value['empreendimentos_id'])->first(), ['nome']);
        $finalidade_nome = Ferramentas::array_index($finalidade->where('id', $value['finalidade_id'])->first(), ['nome']);
        $meio = '        
        <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $value['nome'] . '</p></td>
        <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $empreendimento_nome . '</p></td>
        <td><p ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . $finalidade_nome . '</p></td>
        <td ondblclick="modal_modificar(\'modal_' . $id_temp . '\')">' . ucfirst($value['status']) . '</td>';

        if (($ativos == 'true' && Ferramentas::decodificador($value['status']) == 'ativo')) { //verifica se é para mostrar os com estus ativo
          // Se a tag é ativa e deve ser listada, gera uma linha da tabela com opção "Desativar".
          $lista .= '
        <tr>
         ' . $meio . '
         <td><button name="cadastarar" type="submit" onclick="desativar(\'' . $id_temp . '\')" class="btn btn-outline-danger btn-lg btn-block"> Desativar </button></td>
         <td><button name="cadastarar" type="submit" class="btn btn-outline-warning btn-lg btn-block" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
        </tr>
        ';
        } else if (($desativados == 'true' && Ferramentas::decodificador($value['status']) == 'desativado')) { //verifica se é para mostrar os com estus desativado
          // Se a tag é desativada e deve ser listada, gera uma linha da tabela com opção "Ativar".
          $lista .= '<tr>' . $meio . '
         <td><button name="cadastarar" type="submit" onclick="ativar(\'' . $id_temp . '\')" class="btn btn-outline-success btn-lg btn-block"> Ativar </button></td>
         <td><button name="cadastarar" type="submit" class="btn btn-outline-warning" onclick="modal_modificar(\'modal_' . $id_temp . '\')"> Modificar </button></td>
         </tr>
        ';
        }
        $value["empreendimento"] = $empreendimento_nome;
        $value["finalidade"] = $finalidade_nome;
        $lista_ids[$id_temp] = $value['id'];
        $lista_completa[$id_temp] = $value;
        $id_temp++;
      }
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
   * Função para cadastrar uma nova tag.
   *
   * Esta função é usada para cadastrar uma nova tag com base em dados fornecidos via AJAX.
   *
   * @return 
   */
  function desenho_tag_cadastro()
  {
    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para mensagens de erro.
      $ok = false; // Inicializa uma variável de status para falso.
      $violacao = array(); // Inicializa um array para violações.
      session_start();
      $tag = service('request')->getPost('tag'); // Obtém o nome da tag enviado via POST.

      $empreendimento = service('request')->getPost('empreendimento');
      $finalidade = service('request')->getPost('finalidade');

      $empreendimentoId = $this->resolverSelecaoId(
        (string) $empreendimento,
        'subpasta_empreendimento_tokens',
        'lista_empreendimento'
      );
      $finalidadeId = $this->resolverSelecaoId(
        (string) $finalidade,
        'subpasta_finalidade_tokens',
        'lista_finalidade'
      );

      $tag = Ferramentas::norma_lizar_str($tag);

      if (strlen($tag) > 30) {
        // Verifica se o nome da tag excedeu o tamanho máximo de 17 caracteres.
        $msg['Subpasta'] = "Nome da Subpasta excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "desenho_tag_cadastro Nome da Subpasta excedeu o tamanho máximo de 30 caracter";
      }





      if ($empreendimentoId <= 0) {
        $msg['Empreendimento'] = "Nome da empreendimento não cadastrado";
        $violacao[] = "desenho_tag_cadastro Nome da empreendimento não cadastrado";
      } else {
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Empreendimento'] = "Empreendimento possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro empreendimento possui caracteres não permitidos";
        }
      }





      if ($finalidadeId <= 0) {
        $msg['Finalidade'] = "Nome da finalidade não cadastrado";
        $violacao[] = "desenho_tag_cadastro Nome da finalidade não cadastrado";
      } else {
        if (Ferramentas::codificador($finalidade) == '') {
          $msg['Finalidade'] = "Finalidade possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro finalidade possui caracteres não permitidos";
        }
      }

      if (strlen($tag) < 1) {
        // Verifica se o nome da tag possui o tamanho mínimo de 1 caractere.
        $msg['Subpasta'] = "Nome da Subpasta não possui o tamanho mínimo de 1 caracter";
      } else {
        if (Ferramentas::codificador($tag) == '') {
          // Verifica se o nome da tag possui caracteres não permitidos.
          $msg['Subpasta'] = "Nome da Subpasta possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro Subpasta possui caracteres não permitidos";
        }
      }


      if (count($msg) == 0) {
        $db = new \App\Models\Subpasta();




        $existe = $db
          ->where('nome',             $tag)
          ->where('finalidade_id', $finalidadeId)
          ->where('empreendimentos_id', $empreendimentoId)
          ->first();
        if (!$existe) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 
          // Verifica se a tag com o mesmo nome já existe no banco de dados.
          // Se não existir, insere uma nova tag.
          $date = [
            'nome' => $tag,
            "finalidade_id" => $finalidadeId,
            "empreendimentos_id" => $empreendimentoId,
            'status' => 'ativo',
            'usuario_id' => $_SESSION['usuario']
          ];

          $db->insert($date);
          $ok = true;
        } else {
          $msg["Subpasta"] = 'Nome da Subpasta já existente';
          $violacao[] = "desenho_tag_cadastro Subpasta já existente";
        }
      }
      if (count($violacao) != 0) {
        $db = new \App\Models\Violacao();
        foreach ($violacao as $key => $value) {
          // Registra violações no banco de dados, se houver.
          $data = [
            "usuario_id" => $_SESSION["usuario"],
            "causa" => $value,

          ];

          $db->insert($data);
        }
      }
      $data = ['ok' => $ok, 'msg' => $msg];
      return $this->response->setJSON($data);
    }
  }

  /**
   * Função para exibir as configurações de uma tag em um modal.
   *
   * Esta função é usada para exibir as configurações de uma tag em um modal com base em dados fornecidos via AJAX.
   *
   * @return 
   */
  function tag_modal()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $ok = false;
      $id = service('request')->getPost('id'); //pega a informação post que foi fornecida via ajax se é para pegar os usuarios ativos

      // Obtém informações da tag da lista de tags armazenada na sessão.
      $lista = $_SESSION["lista_completa"][$id];

      // A função atualmente não verifica se um desenho está associado a essa tag.
      // Portanto, a variável $ok está definida como false.

      $data = [
        "nome" => Ferramentas::decodificador($lista['nome']),
        // Obtém o nome da tag e a decodifica.
        "desenho" => $ok,
        // Define se um desenho está associado (neste caso, sempre falso).
        "status" => Ferramentas::decodificador($lista['status']), // Obtém o status da tag e a decodifica.
        "finalidade" => $lista['finalidade'],
        "empreendimento" => $lista['empreendimento']


      ];
      return $this->response->setJSON($data);
    }
  }

  /**
   * Função para atualizar configurações de uma tag.
   *
   * Esta função é usada para atualizar as configurações de uma tag com base em dados fornecidos via AJAX.
   *
   * @return 
   */
  function tag_update()
  {
    if ($this->request->isAJAX()) {
      $msg = array(); // Inicializa um array para mensagens de erro.
      $ok = false; // Inicializa uma variável de status para falso.
      $violacao = array(); // Inicializa um array para violações.
      session_start();
      $tag = service('request')->getPost('tag'); // Obtém o nome da tag enviado via POST.
      $empreendimento = service('request')->getPost('empreendimento');
      $finalidade = service('request')->getPost('finalidade');

      // Converte caracteres especiais para suas versões simples usando iconv
      $tag = iconv('UTF-8', 'ASCII//TRANSLIT', $tag);

      // Remove tudo que não seja letra, número ou espaço
      $tag = preg_replace('/[^A-Za-z0-9 ]/', '', $tag);

      // Converte a string para maiúsculas
      $tag = strtoupper($tag);


      if (strlen($tag) > 30) {
        // Verifica se o nome da tag excedeu o tamanho máximo de 17 caracteres.
        $msg['Subpasta'] = "Nome da Subpasta excedeu o tamanho máximo de 30 caracter";
        $violacao[] = "desenho_tag_cadastro Nome da Subpasta excedeu o tamanho máximo de 30 caracter";
      }





      if (!array_search($empreendimento, $_SESSION["lista_empreendimento"])) {
        $msg['Empreendimento'] = "Nome da empreendimento não cadastrado";
        $violacao[] = "desenho_tag_cadastro Nome da empreendimento não cadastrado";
      } else {
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Empreendimento'] = "Empreendimento possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro empreendimento possui caracteres não permitidos";
        }
      }





      if (!array_search($finalidade, $_SESSION["lista_finalidade"])) {
        $msg['Finalidade'] = "Nome da finalidade não cadastrado";
        $violacao[] = "desenho_tag_cadastro Nome da finalidade não cadastrado";
      } else {
        if (Ferramentas::codificador($empreendimento) == '') {
          $msg['Finalidade'] = "Finalidade possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro finalidade possui caracteres não permitidos";
        }
      }



      if (strlen($tag) < 1) {
        // Verifica se o nome da tag possui o tamanho mínimo de 1 caractere.
        $msg['Subpasta'] = "Nome da Subpasta não possui o tamanho mínimo de 1 caracter.";
      } else {
        if (Ferramentas::codificador($tag) == '') {
          // Verifica se o nome da tag possui caracteres não permitidos.
          $msg['Subpasta'] = "Nome da Subpasta possui caracteres não permitidos";
          $violacao[] = "desenho_tag_cadastro Subpasta possui caracteres não permitidos";
        }
      }




      if (count($msg) == 0) {
        $db = new \App\Models\Subpasta();

        $id1 = service('request')->getPost('id'); // Obtém o ID da tag enviado via POST.
        $id = $_SESSION['lista'][$id1]; // Obtém o ID da tag a partir de uma lista.
        $tag_data = $db->find();

        // Verifica se o nome da tag não é duplicado e se houve alterações.
        if ((count(Ferramentas::array_pesquisa_mult($tag_data, ['nome', 'finalidade_id', 'empreendimento_id'], [Ferramentas::codificador($tag), array_search($finalidade, $_SESSION["lista_finalidade"]), array_search($empreendimento, $_SESSION["lista_empreendimento"])])) == 0)) { // verifica se o id do mepreendimento com o mesmo nome é igual ao id 


          $alteracao = new \App\Models\Alteracoes();

          $alteracao->insertWithDetails(
            [
              "usuario_id" => $_SESSION["usuario"],
              "id_item" => $id,
              "item" => "subpasta",

            ],
            [
              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'id', $id), ['nome']),
                "valor_depois" => Ferramentas::norma_lizar_str($tag),
                "campo" => "nome"
              ],


              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'id', $id), ['finalidade_id']),
                "valor_depois" => array_search($finalidade, $_SESSION["lista_finalidade"]),
                "campo" => "finalidade_id"
              ],

              [
                "valor_antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($tag_data, 'id', $id), ['empreendimento_id']),
                "valor_depois" =>  array_search($empreendimento, $_SESSION["lista_empreendimento"]),
                "campo" => "finalidade_id"
              ]
            ]
          );




          $date = [
            'nome' => Ferramentas::norma_lizar_str($tag),
            "finalidade_id" => array_search($finalidade, $_SESSION["lista_finalidade"]),
            "empreendimentos_id" => array_search($empreendimento, $_SESSION["lista_empreendimento"]),
          ];

          $db->update($id, $date);

          $ok = true;
        } else if (count(Ferramentas::array_pesquisa_mult($tag_data, ['id', 'nome', 'finalidade_id', 'empreendimento_id'], [$id, Ferramentas::codificador($tag), array_search($finalidade, $_SESSION["lista_finalidade"]), array_search($empreendimento, $_SESSION["lista_empreendimento"])])) != 0) {
          $msg["Modificar"] = 'Nenhum item foi modificado.';
        } else {
          $msg["Subpasta"] = 'O nome da subpasta já existe neste empreendimento para essa finalidade.';
          $violacao[] = "desenho_tag_cadastro Subpasta já existente";
        }
      }
    }
    if (count($violacao) != 0) {
      $db = new \App\Models\Violacao();
      foreach ($violacao as $key => $value) {
        // Registra violações no banco de dados, se houver.
        $data = [
          "usuario_id" => $_SESSION["usuario"],
          "causa" => $value,

        ];

        $db->insert($data);
      }
    }
    $data = ['ok' => $ok, 'msg' => $msg];
    return $this->response->setJSON($data);
  }

  /**
   * Função para listar as tags ativas.
   *
   * Esta função é usada para listar todas as tags ativas armazenadas no banco de dados.
   *
   * @return 
   */
  function desenho_tag_lista()
  {
    if ($this->request->isAJAX()) {
      session_start();
      $tags = array(); // Inicializa um array para armazenar as tags.
      $tags_empreendimento_finalidade = array();
      $Subpasta = new \App\Models\Subpasta(); // Instancia o modelo de dados de tags.

      $Subpasta_data = $Subpasta
        ->select('subpasta.nome,
                  empreendimentos.nome AS empreendimento_nome,
                  finalidade.nome AS finalidade_nome')
        ->join('empreendimentos', 'empreendimentos.id = subpasta.empreendimentos_id', 'left')
        ->join('finalidade', 'finalidade.id = subpasta.finalidade_id', 'left')
        ->where('subpasta.status', 'ativo')
        ->findAll(); // Obtém todas as tags do banco de dados.




      foreach ($Subpasta_data as $key => $value) { // Itera sobre as tags no banco de dados.
        $nomeSubpasta = Ferramentas::decodificador($value['nome'] ?? '');
        $nomeEmpreendimento = Ferramentas::decodificador($value['empreendimento_nome'] ?? '');
        $nomeFinalidade = Ferramentas::decodificador($value['finalidade_nome'] ?? '');

        if ($nomeSubpasta === '') {
          continue;
        }

        $tags[] = $nomeSubpasta; // Adiciona o nome da tag decodificado ao array de tags.

        if ($nomeEmpreendimento !== '' && $nomeFinalidade !== '') {
          $tags_empreendimento_finalidade[$nomeEmpreendimento][$nomeFinalidade][] = $nomeSubpasta;
        }
      }
      $tags = array_values(array_unique($tags));
      usort($tags, function ($a, $b) {
        return strnatcasecmp($a, $b);
      });

      foreach ($tags_empreendimento_finalidade as &$finalidades) {
        foreach ($finalidades as &$subpastas) {
          $subpastas = array_values(array_unique($subpastas));
          usort($subpastas, function ($a, $b) {
            return strnatcasecmp($a, $b);
          });
        }
        unset($subpastas);
      }
      unset($finalidades);
      // Prepara os dados de resposta em formato JSON, incluindo a lista de tags ativas.
      $data = [
        'lista' => $tags,
        'tags' => $tags_empreendimento_finalidade
      ];
      return $this->response->setJSON($data);
    }
  }
}
