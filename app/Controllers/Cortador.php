<?php

namespace App\Controllers;
use App\Controllers\Ferramentas;
class Cortador extends BaseController
{

  /**
   * Exibe a lista de desenhos aguardando corte.
   *
   * Esta função é responsável por exibir uma lista de desenhos que estão aguardando corte.
   * Ela verifica o login do usuário, configura variáveis de visualização e, em seguida,
   * carrega um modelo de visualização para exibir a lista de desenhos aguardando corte.
   *
   * 
   */
  function lista_corte()
  {
    // Verifica se o usuário está logado como 'cortador'
    Login::verifica_login('cortador');

    // Configura variáveis de visualização
    $array_view = [
      'button_execut_nome' => '',
      'array_input_id' => '',
      'array_input_placeholder' => '',
      'array_input_typ' => '',
      'array_input_titulo' => '',
      'titulo' => '',
      'functionType' => '',
      'nomeUsuario' => Ferramentas::decodificador($_SESSION['usuario_nome']),
      'menu' => '',
      'lista' => '',
      'selecao_lista' => false,
      'array_titulo_lista' => '',
      'functiontype_cadastro' => '',
      'titulo_cadastro' => '',
      'titulo_lista' => '',
      'functiontype_lista' => '',
      'ajax' => 'ajaxs/corte/lista_corte_ajax'
    ];
    $menu_box = "desenhos";
    $menu_select = "lista_corte";
    $template = "lista";

    $array_view['titulo'] = "Lista de Corte";
    $array_view['array_titulo_lista'] = array("Prioridade","Desenhista", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade", "Data de Envio", "Cortar", "Confirmar Corte");
    $array_view['menu'] = $this->menu($menu_box, $menu_select);

    // Exibe a visualização usando o modelo especificado
    echo view($template, $array_view);
  }


  /**
   * Gera o menu de navegação da interface.
   *
   * Esta função é responsável por gerar o menu de navegação da interface com base nas configurações
   * fornecidas. Ela recebe o nome do menu principal e a seleção atual e ajusta as classes do menu
   * para destacar a seleção atual.
   *
   * @param string $menu_box Nome do menu principal.
   * @param string $menu_select Nome da seleção atual no menu.
   *
   * @return string O código HTML do menu de navegação gerado.
   */
  private function menu($menu_box = "", $menu_select = "")
  {
    // Carrega o modelo de visualização do menu
    $menu = view('corte/menu');

    // Atualiza as classes do menu para destacar a seleção atual
    $menu = str_replace('id="' . $menu_box . '_top" class="nav-item"', 'id="' . $menu_box . '_top" class="nav-item menu-is-opening menu-open"', $menu);
    $menu = str_replace('id="' . $menu_box . '_bory"', 'id="' . $menu_box . '_bory" style="display: block;"', $menu);
    $menu = str_replace('id="' . $menu_select . '" class="nav-link"', 'id="' . $menu_select . '" class="nav-link active"', $menu);

    // Retorna o código HTML do menu de navegação gerado
    return $menu;
  }
}