<?php

namespace App\Controllers;
use App\Controllers\Ferramentas;

class Desenhista extends BaseController
{
  /**
   * Ação index
   *
   * Esta função é chamada quando uma solicitação é feita para a ação "index" deste controlador.
   * Ela redireciona para a função "desenho_adicionar".
   */
  function index()
  {
    $this->desenho_adicionar();
  }
  
  /**
   * Renderiza a lista de corte de desenhos para o perfil "desenhista".
   *
   * Controla o acesso para garantir que apenas "desenhistas" possam visualizar.
   *
   * Parâmetros:
   * - Nenhum é necessário, pois os dados são obtidos da sessão do usuário.
   *
   * Variáveis utilizadas:
   * - $array_view: Dados a serem passados para a view.
   * - $menu_box: Define o menu de navegação como "desenhos".
   * - $menu_select: Define a opção de menu selecionada como "lista_corte".
   * - $template: Modelo a ser renderizado como "lista".
   */
  function lista_corte()
  {
    // Verifica se o usuário é um "desenhista"
    Login::verifica_login('desenhista');

    // Inicialização do array $array_view e configuração específica da página
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
      'ajax' => 'ajaxs/desenhos/lista_corte_ajax'
    ];
    $menu_box = "desenhos";
    $menu_select = "lista_corte";
    $template = "lista";

    $array_view['titulo'] = "Lista de Corte";
    $array_view['array_titulo_lista'] = array("Prioridade","Desenhista", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade", "status", "Data de Envio");
    $array_view['menu'] = $this->menu($menu_box, $menu_select);

    // Renderiza a visualização "lista" com os dados do array $array_view
    echo view($template, $array_view);
  }


  /**
   * Renderiza a página de adição de desenhos para o perfil "desenhista".
   *
   * Controla o acesso para garantir que apenas "desenhistas" possam adicionar desenhos.
   *
   * Parâmetros:
   * - Nenhum é necessário, pois os dados são obtidos da sessão do usuário.
   *
   * Variáveis utilizadas:
   * - $array_view: Dados a serem passados para a view.
   * - $menu_box: Define o menu de navegação como "desenhos".
   * - $menu_select: Define a opção de menu selecionada como "adicionar".
   * - $template: Modelo a ser renderizado como "desenhista/add_desenho".
   */
  function desenho_adicionar()
  {
    // Verifica se o usuário é um "desenhista"
    Login::verifica_login('desenhista');

    // Inicialização do array $array_view e configuração específica da página
    $array_view = array(
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
      'selecao_lista' => true,
      'array_titulo_lista' => '',
      'functiontype_cadastro' => '',
      'titulo_cadastro' => '',
      'titulo_lista' => '',
      'functiontype_lista' => '',
      'ajax' => 'ajaxs/desenhos/desenho_adicionar_ajax.php',
      'filtro' => ''
    );
    $menu_box = "desenhos";
    $menu_select = "adicionar";
    $template = "desenhista/add_desenho";

    // Define o título da página e o menu de navegação
    $array_view['titulo'] = "Adicionar Desenho";
    $array_view['menu'] = $this->menu($menu_box, $menu_select);

    // Renderiza a visualização "add_desenho" com os dados do array $array_view
    echo view($template, $array_view);

  }


  /**
   * Renderiza a lista dos desenhos do usuário (perfil "desenhista").
   *
   * Controla o acesso para garantir que apenas "desenhistas" possam visualizar seus próprios desenhos.
   *
   * Parâmetros:
   * - Nenhum é necessário, pois os dados são obtidos da sessão do usuário.
   *
   * Variáveis utilizadas:
   * - $array_view: Dados a serem passados para a view.
   * - $menu_box: Define o menu de navegação como "desenhos".
   * - $menu_select: Define a opção de menu selecionada como "desenhos".
   * - $template: Modelo a ser renderizado como "lista".
   */
  function desenho_meus()
  {
    // Verifica se o usuário é um "desenhista"
    Login::verifica_login('desenhista');

    // Inicialização do array $array_view e configuração específica da página
    $array_view = array(
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
      'ajax' => 'ajaxs/adm/desenho_meus_ajax',
      'hora_lista' => true
    );
    $menu_box = "desenhos";
    $menu_select = "desenhos";
    $template = "lista";

    // Define o título da página, o menu de navegação e a estrutura da lista
    $array_view['titulo'] = "Lista dos Meus Desenhos";
    $array_view['array_titulo_lista'] = array("Prioridade", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade","Subpastas", "Status", "Data de Envio", "", "");
    $array_view['menu'] = $this->menu($menu_box, $menu_select);

    // Renderiza a visualização "lista" com os dados do array $array_view
    echo view($template, $array_view);

  }


  /**
   * Renderiza a lista de tags e permite a criação de novas tags para o perfil "desenhista".
   *
   * Controla o acesso para garantir que apenas "desenhistas" possam visualizar e criar tags.
   *
   * Parâmetros:
   * - Nenhum é necessário, pois os dados são obtidos da sessão do usuário.
   *
   * Variáveis utilizadas:
   * - $array_view: Dados a serem passados para a view.
   * - $menu_box: Define o menu de navegação como "desenhos".
   * - $menu_select: Define a opção de menu selecionada como "tags".
   * - $template: Modelo a ser renderizado como "lista_cadastro".
   */
  function tags()
  {
    // Verifica se o usuário é um "desenhista"
    Login::verifica_login('desenhista');

    // Inicialização do array $array_view e configuração específica da página
    $array_view = array(
      'button_execut_nome' => '',
      'array_input_id' => array(),
      'array_input_placeholder' => array(),
      'array_input_typ' => array(),
      'array_input_titulo' => array(),
      'titulo' => '',
      'functionType' => '',
      'nomeUsuario' => Ferramentas::decodificador($_SESSION['usuario_nome']),
      'menu' => '',
      'lista' => '',
      'selecao_lista' => true,
      'array_titulo_lista' => '',
      'functionType_cadastro' => '',
      'titulo_cadastro' => '',
      'titulo_lista' => '',
      'functionType_lista' => '',
      'ajax' => 'ajaxs/desenhos/tags_ajax'
    );
    $menu_box = "desenhos";
    $menu_select = "tags";
    $template = "lista_cadastro";


    // Configura informações específicas da página, como títulos e elementos de entrada
    $array_view['functionType_lista'] = "Lista Subpastas";
    $array_view['functionType_cadastro'] = "Cadastrar Subpastas";
    $array_view['titulo_cadastro'] = "Cadastrar Subpastas";
    $array_view['array_input_titulo'] = array("Subpasta");
    $array_view['array_input_typ'] = array("text");
    $array_view['array_input_placeholder'] = array("Nova Subpasta");
    $array_view['array_input_id'] = array("nome_tag_novo");
    $array_view['button_execut_nome'] = "Adicionar";



    // Configuração da lista de tags existentes.
    $array_view['titulo_lista'] = "Lista de Subpastas";
    $array_view['array_titulo_lista'] = array("Nome", "Status", "");
    $array_view['menu'] = $this->menu($menu_box, $menu_select);



    // Renderiza a visualização "lista_cadastro" com os dados do array $array_view
    echo view($template, $array_view);

  }
   /**
   * Controla a exibição da lista de empreendimentos e a adição de novos registros.
   *
   * Esta função é responsável por exibir a lista de empreendimentos e fornecer a capacidade de adicionar novos registros. Ela verifica se o usuário está autenticado como desenhista antes de exibir a lista e a opção de adicionar novos registros.
   */
  function config_empreendimento()
  {
    // Verifica se o usuário é um "desenhista"
    Login::verifica_login('desenhista');

    // Dados da visualização da lista de empreendimentos e adição de novos registros.
    $array_view = array(
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
      'selecao_lista' => true,
      'array_titulo_lista' => '',
      'functiontype_cadastro' => '',
      'titulo_cadastro' => '',
      'titulo_lista' => '',
      'functiontype_lista' => '',
      'ajax' => 'ajaxs/adm/config_empreendimento_ajax'
    );

    // Informações do menu.
    $menu_box = "config_dos_ajustes";
    $menu_select = "empreendimento";
    $template = "lista_cadastro";
    $select_option = "<option value='' disabled selected>Empresa/Cliente</option>";


    // Configuração da página de lista de empreendimentos.
    $array_view['array_input_typ'] = array("text", "select");
    $array_view['array_input_titulo'] = array("Empreendimento", "Empresa/Cliente");
    $array_view['functionType_cadastro'] = "Cadastrar Empreendimento";
    $array_view['titulo_lista'] = "Lista Empreendimento";
    $array_view['titulo_cadastro'] = "Adicionar novo Empreendimento";
    $array_view['functionType_lista'] = "Lista Empreendimento";
    $array_view['array_input_placeholder'] = array("Novo Empreendimento", $select_option);
    $array_view['array_input_id'] = array("empreendimento_novo", "empresa_cliente_novo");
    $array_view['button_execut_nome'] = "Adicionar";
    $array_view['array_titulo_lista'] = array("Empreendimento", "Empresa/Cliente", "Status", "");
    $array_view['menu'] = $this->menu($menu_box, $menu_select);

    // Exibe a página de lista de empreendimentos.
    echo view($template, $array_view);

  }
  /**
   * Gera o menu de navegação dinâmico com destaque para a opção selecionada.
   *
   * Esta função é usada para criar o menu de navegação dinâmico para o perfil "desenhista".
   *
   * Parâmetros:
   * - $menu_box: Identifica o menu ativo (por padrão, vazio).
   * - $menu_select: Identifica a opção do menu que deve ser destacada como ativa (por padrão, vazio).
   *
   * Retorna:
   * - Uma string que representa o menu de navegação.
   */
  private function menu($menu_box = "", $menu_select = "")
  {
    // Gera o menu de navegação
    $menu = view('desenhista/menu');

    // Realiza substituições para destacar o menu ativo e a opção selecionada
    $menu = str_replace('id="' . $menu_box . '_top" class="nav-item"', 'id="' . $menu_box . '_top" class="nav-item menu-is-opening menu-open"', $menu);
    $menu = str_replace('id="' . $menu_box . '_bory"', 'id="' . $menu_box . '_bory" style="display: block;"', $menu);
    $menu = str_replace('id="' . $menu_select . '" class="nav-link"', 'id="' . $menu_select . '" class="nav-link active"', $menu);
    return $menu;
  }

}