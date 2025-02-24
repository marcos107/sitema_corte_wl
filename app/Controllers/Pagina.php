<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;


class Pagina extends BaseController
{


    /**
     * Controla a exibição da lista de corte no contexto de administração.
     *
     * Esta função é responsável por exibir a lista de corte, que pode incluir informações relacionadas a prioridade, nome do arquivo, empresa/cliente, empreendimento, finalidade, status e data de envio. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista de corte.
     */
    function lista_corte_adm()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Lista De Corte ADM');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da lista de corte.
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
            'ajax' => 'ajaxs/lista_corte_adm_ajax'
        ];

        // Informações do menu.
        $menu_box = "desenhos";
        $menu_select = "lista_corte_adm";
        $template = "lista";

        // Configuração da página da lista de corte.
        $array_view['titulo'] = "Lista de Corte";
        $array_view['array_titulo_lista'] = array("Prioridade","Ordem","Processos", "Desenhista", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade", "Subpastas", "status","Inicio Corte", "Data de Envio", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página da lista de corte.
        echo view($template, $array_view);
    }


    /**
     * Controla a exibição da página para adicionar um novo desenho.
     *
     * Esta função é responsável por exibir a página na qual os usuários podem adicionar um novo desenho ao sistema. Ela verifica se o usuário está autenticado como administrador antes de exibir a página.
     */
    function desenho_adicionar()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Adicionar');

        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da página de adição de desenho.
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
            'ajax' => 'ajaxs/desenho_adicionar_ajax.php',
            'filtro' => ''
        );

        // Informações do menu.
        $menu_box = "desenhos";
        $menu_select = "adicionar";
        $template = "add_desenho";

        // Configuração da página de adição de desenho.
        $array_view['titulo'] = "Adicionar Desenho";
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página de adição de desenho.
        echo view($template, $array_view);

    }


    /**
     * Controla a exibição da lista de desenhos pertencentes ao usuário atual.
     *
     * Esta função é responsável por exibir a lista de desenhos que pertencem ao usuário atual. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista.
     */
    function desenho_meus()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Meus Desenhos');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da lista de desenhos do usuário.
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
            'ajax' => 'ajaxs/desenho_meus_ajax',
            'hora_lista' => true
        );

        // Informações do menu.
        $menu_box = "desenhos";
        $menu_select = "desenhos";
        $template = "lista";

        // Configuração da página de lista de desenhos do usuário.
        $array_view['titulo'] = "Lista dos Meus Desenhos";
        $array_view['array_titulo_lista'] = array("Prioridade", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade", "Subpastas", "Status", "Data de Envio", "", "");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a lista de desenhos do usuário.
        echo view($template, $array_view);

    }

    /**
     * Controla a exibição da lista de desenhos.
     *
     * Esta função é responsável por exibir a lista de desenhos. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista.
     */
    function desenhos_cortados()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Desenhos cortados');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da lista de desenhos.
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
            'ajax' => 'ajaxs/desenhos_cortados_ajax',
            'hora_lista' => true
        );

        // Informações do menu.
        $menu_box = "desenhos";
        $menu_select = "desenhos_cortados";
        $template = "lista";

        // Configuração da página de lista de desenhos.
        $array_view['titulo'] = "Lista de Todos os desenhos";
        $array_view['array_titulo_lista'] = array("Prioridade", "Desenhista", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade","Subpastas", "Status","Data de Corte", "Data de Envio", "", "");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a lista de desenho.
        echo view($template, $array_view);

    }

    /**
     * Controla a exibição da lista de empresas/clientes e a adição de novos registros.
     *
     * Esta função é responsável por exibir a lista de empresas/clientes e fornecer a capacidade de adicionar novos registros. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista e a opção de adicionar novos registros.
     */
    function empresa()
    {

        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Empresa');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }


        // Dados da visualização da lista de empresas/clientes e adição de novos registros.
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
            'ajax' => 'ajaxs/empresa_ajax'
        );

        // Informações do menu.
        $menu_box = "config_dos_ajustes";
        $menu_select = "empresa_cliente";
        $template = "lista_cadastro";

        // Configuração da página de lista de empresas/clientes.
        $array_view['functionType_lista'] = "Lista Empresa/Cliente";
        $array_view['functionType_cadastro'] = "Cadastrar Empresa/Cliente";
        $array_view['titulo_cadastro'] = "Adicionar novo Empresa/Cliente";
        $array_view['titulo_lista'] = "Lista Empresa/Cliente";
        $array_view['array_input_titulo'] = array("Empresa/Cliente");
        $array_view['array_input_placeholder'] = array("Nova Empresa/Cliente");
        $array_view['array_input_typ'] = array("text");
        $array_view['array_input_id'] = array("empresa_cliente_novo");
        $array_view['button_execut_nome'] = "Adicionar";
        $array_view['array_titulo_lista'] = array("Empresa/Cliente", "Status", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página de lista de empresas/clientes.
        echo view($template, $array_view);
    }


    /**
     * Controla a exibição da lista de empreendimentos e a adição de novos registros.
     *
     * Esta função é responsável por exibir a lista de empreendimentos e fornecer a capacidade de adicionar novos registros. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista e a opção de adicionar novos registros.
     */
    function empreendimento()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Empreendimento');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

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
            'ajax' => 'ajaxs/empreendimento_ajax'
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
        $array_view['array_titulo_lista'] = array("Empreendimento", "Empresa/Cliente", "Status", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página de lista de empreendimentos.
        echo view($template, $array_view);

    }

    /**
     * Controla a exibição da lista de finalidades e a adição de novos registros.
     *
     * Esta função é responsável por exibir a lista de finalidades e fornecer a capacidade de adicionar novos registros de finalidades. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista e a opção de adicionar novos registros de finalidades.
     */
    function finalidade()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Fialidade');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da lista de finalidades e adição de novos registros.
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
            'ajax' => 'ajaxs/finalidade_ajax'
        );

        // Informações do menu.
        $menu_box = "config_dos_ajustes";
        $menu_select = "finalidade";
        $template = "lista_cadastro";

        // Configuração da página de lista de finalidades.
        $array_view['array_input_typ'] = array("text");
        $array_view['array_input_titulo'] = array("Finalidade");
        $array_view['titulo_cadastro'] = "Adicionar nova Finalidade";
        $array_view['titulo_lista'] = "Lista Finalidade";
        $array_view['functionType_cadastro'] = "Cadastrar Finalidade";
        $array_view['functionType_lista'] = "Lista Finalidade";
        $array_view['array_titulo_lista'] = array("Finalidade", "Status", "","");
        $array_view['button_execut_nome'] = "Adicionar";
        $array_view['array_input_id'] = array("nome_Finalidade_novo");
        $array_view['array_input_placeholder'] = array("Nome da Finalidade");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página de lista de finalidades.
        echo view($template, $array_view);
    }


    /**
     * Controla a exibição da lista de prioridades e a adição de novos registros.
     *
     * Esta função é responsável por exibir a lista de prioridades e fornecer a capacidade de adicionar novos registros de prioridades. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista e a opção de adicionar novos registros de prioridades.
     */
    function prioridade()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Prioridade');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da lista de prioridades e adição de novos registros.
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
            'ajax' => 'ajaxs/prioridade_ajax'
        );

        // Informações do menu.
        $menu_box = "config_dos_ajustes";
        $menu_select = "prioridade";
        $template = "lista_cadastro";

        // Configuração da página de lista de prioridades.
        $array_view['array_titulo_lista'] = array("Prioridade", "Ordem", "Cor", "Status", "","");
        $array_view['button_execut_nome'] = "Adicionar";
        $array_view['array_input_id'] = array("nome_prioridade_nova", "nova_ordem", "nova_cor");
        $array_view['array_input_placeholder'] = array("Nova Prioridade", "Nova Ordem", "");
        $array_view['array_input_typ'] = array("text", "number", "color");
        $array_view['functionType_lista'] = "Lista Prioridade";
        $array_view['functionType_cadastro'] = "Cadastrar Prioridade";
        $array_view['titulo_cadastro'] = "Adicionar nova prioridade";
        $array_view['titulo_lista'] = "Lista Prioridade";
        $array_view['array_input_titulo'] = array("Prioridade", "Ordem", "Cor");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página de lista de prioridades.
        echo view($template, $array_view);

    }


    /**
     * Controla a exibição da lista de tipos de arquivos e a adição de novos registros.
     *
     * Esta função é responsável por exibir a lista de tipos de arquivos e fornecer a capacidade de adicionar novos registros de tipos de arquivos. Ela verifica se o usuário está autenticado como administrador antes de exibir a lista e a opção de adicionar novos registros de tipos de arquivos.
     */
    function extencao()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Tipo De Arquivo');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados da visualização da lista de tipos de arquivos e adição de novos registros.
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
            'ajax' => 'ajaxs/extencao_ajax'
        );

        // Informações do menu.
        $menu_box = "config_dos_ajustes";
        $menu_select = "tipo_de_arquivo";
        $template = "lista_cadastro";

        // Configuração da página de lista de tipos de arquivos.
        $array_view['functionType_lista'] = "Lista Tipos de Arquivos";
        $array_view['functionType_cadastro'] = "Cadastrar Tipos de Arquivos";
        $array_view['titulo_cadastro'] = "Adicionar novo tipo de arquivo";
        $array_view['titulo_lista'] = "Lista Tipos de Arquivos";
        $array_view['array_input_titulo'] = array("Extensão do Arquivo");
        $array_view['array_input_typ'] = array("text");
        $array_view['array_input_placeholder'] = array("Extensão");
        $array_view['array_input_id'] = array("nome_extensao_novo");
        $array_view['button_execut_nome'] = "Adicionar";
        $array_view['array_titulo_lista'] = array("Extensão do Arquivo", "Status", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a página de lista de tipos de arquivos.
        echo view($template, $array_view);


    }



    function usuario()
    {
        // Verifica se o usuário está autenticado como administrador.
        //Login::verifica_permissao('Subpasta');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados do formulário de criação de tags e da lista de tags.
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
            'ajax' => 'ajaxs/usuario_ajax'
        );

        // Informações do menu e template.
        $menu_box = "config_dos_ajustes";
        $menu_select = "usuario";
        $template = "lista_cadastro";
        $text_rep = array(' do novo usuario');
        $select_option = "<option value='' disabled selected>Nível do novo usuario</option>";


        // Configuração do formulário de criação de tags.
        $array_view['functionType_lista'] = "Lista Usuário";
        $array_view['functionType_cadastro'] = "Cadastrar Usuário";
        $array_view['array_input_id'] = array("nome_usuario", "senha_usuario", "funcao_usuario", "email_usuario", "whazapp_usuario");
        $array_view['array_input_placeholder'] = array("Nome" . $text_rep[0], "Senha" . $text_rep[0], $select_option, "Email" . $text_rep[0], "Whazapp" . $text_rep[0]);
        $array_view['array_input_typ'] = array("text", "password", "select", "email", "tel");
        $array_view['array_input_titulo'] = array("Nome", "Senha", "Nível", "Email", "whatsapp");
        $array_view['titulo_cadastro'] = "Cadastro de Usuario";
        $array_view['button_execut_nome'] = "Cadastrar";



        // Configuração da lista de tags existentes.
        $array_view['titulo_lista'] = "Lista de Subpastas";
        $array_view['array_titulo_lista'] = array("Nome", "Senha", "Nível", "Email", "whatsapp", "Status", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);


        // Exibe o formulário de criação de tags e a lista de tags.
        echo view($template, $array_view);

    }

    /**
     * Controla a exibição do formulário de criação de tags e a lista de tags existentes.
     *
     * Esta função é responsável por exibir um formulário para criar novas tags e listar as tags existentes. Ela verifica se o usuário está autenticado como administrador antes de exibir as informações.
     */
    function subpasta()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Subpasta');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Dados do formulário de criação de tags e da lista de tags.
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
            'ajax' => 'ajaxs/subpasta_ajax'
        );

        // Informações do menu e template.
        $menu_box = "desenhos";
        $menu_select = "tags";
        $template = "lista_cadastro";


        // Configuração do formulário de criação de tags.
        $array_view['functionType_lista'] = "Lista Subpastas";
        $array_view['functionType_cadastro'] = "Cadastrar Subpastas";
        $array_view['titulo_cadastro'] = "Cadastrar Subpastas";
        $array_view['array_input_titulo'] = array("Empresa","Empreendimento","Finalidade","Subpasta");
        $array_view['array_input_typ'] = array("select","select","select","text");
        $array_view['array_input_placeholder'] = array("","","","Nova Subpasta");
        $array_view['array_input_id'] = array("empresa_tag_novo","empreendimento_tag_novo","finalidade_tag_novo","nome_tag_novo");
        $array_view['button_execut_nome'] = "Adicionar";



        // Configuração da lista de tags existentes.
        $array_view['titulo_lista'] = "Lista de Subpastas";
        $array_view['array_titulo_lista'] = array("Nome","Empreendimento","Finalidade", "Status", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);


        // Exibe o formulário de criação de tags e a lista de tags.
        echo view($template, $array_view);

    }


    function relatorios()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Relátorio');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }


        // Dados da visualização da lista de desenhos do usuário.
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
            'ajax' => 'ajaxs/relatorios_ajax.php'

        );

        // Informações do menu.
        $menu_box = "";


        $menu_select = "relatorio";
        $template = "cadastro";

        // Configuração do formulário de cadastro.
        $array_view['array_input_id'] = array("data_inicial\" required \"", "data_final\" required disabled  \"", "rad_1", "checkbox_ativo");
        $array_view['array_input_placeholder'] = array("Data inicial para aconsulta", "Data final para aconsulta", "", "");
        $array_view['array_input_typ'] = array("date", "date", "radio", "checkbox");
        $array_view['array_input_titulo'] = array("Data de inicio", "Data de final", "Tipo de Relatório", "Visualizar usuários");
        $array_view['titulo'] = "Gerar Relatório";
        $array_view['button_execut_nome'] = "Gerar PDF";
        $array_view['menu'] = $this->menu($menu_box, $menu_select);
        // Exibe a lista de desenhos do usuário.
        echo view($template, $array_view);
    }



    /**
     * Exibe a lista de desenhos aguardando corte.
     *
     * Esta função é responsável por exibir uma lista de desenhos que estão aguardando corte.
     * Ela verifica o login do usuário, configura variáveis de visualização e, em seguida,
     * carrega um modelo de visualização para exibir a lista de desenhos aguardando corte.
     *
     * 
     */
    function lista_tarefas()
    {
        // Verifica se o usuário está logado como 'cortador'
        Login::verifica_permissao('Lista_De_Corte_Cortador');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

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
            'ajax' => 'ajaxs/lista_tarefas_ajax'
        ];
        $menu_select = "lista_tarefas";
        $template = "lista";

        $array_view['titulo'] = "Lista de Corte";
        $array_view['array_titulo_lista'] = array("Prioridade","Ordem", "Desenhista", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade","Subpastas", "Data de Envio", "Cortar", "Confirmar Corte");
        $array_view['menu'] = $this->menu('', $menu_select);

        // Exibe a visualização usando o modelo especificado
        echo view($template, $array_view);
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
        Login::verifica_permissao('Lista De Corte');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }


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
            'ajax' => 'ajaxs/lista_corte_ajax'
        ];
        $menu_box = "desenhos";
        $menu_select = "lista_corte";
        $template = "lista";

        $array_view['titulo'] = "Lista de Corte";
        $array_view['array_titulo_lista'] = array("Prioridade","Ordem","Processos", "Desenhista", "Nome do arquivo", "Empresa/Cliente", "Empreendimento", "Finalidade","Subpastas", "Status","Inicio Corte", "Data de Envio");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Renderiza a visualização "lista" com os dados do array $array_view
        echo view($template, $array_view);
    }


    function nivel()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Nível');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }


        // Dados da visualização da lista de desenhos do usuário.
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
            'ajax' => 'ajaxs/nivel_ajax.php'

        );

        // Informações do menu.
        $menu_box = "config_dos_ajustes";


        $menu_select = "nivel";
        $template = "lista_cadastro";
        // Configuração do formulário de criação de tags.
        $array_view['functionType_lista'] = "Lista Nível";
        $array_view['functionType_cadastro'] = "Cadastrar Nível";
        $array_view['titulo_cadastro'] = "Cadastrar Nível";
        $array_view['array_input_titulo'] = array("Nível");
        $array_view['array_input_typ'] = array("text");
        $array_view['array_input_placeholder'] = array("Novo Nível");
        $array_view['array_input_id'] = array("nome_tag_novo");
        $array_view['button_execut_nome'] = "Adicionar";

        // Configuração da página de lista de desenhos do usuário.
      
        $array_view['array_titulo_lista'] = array("Nome", "Permissões", "Processos","Status", "","");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a lista de desenhos do usuário.
        echo view($template, $array_view);
    }


    function processos()
    {
        // Verifica se o usuário está autenticado como administrador.
        Login::verifica_permissao('Processos');
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }


        // Dados da visualização da lista de desenhos do usuário.
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
            'ajax' => 'ajaxs/processos_ajax.php'

        );

        // Informações do menu.
        $menu_box = "config_dos_ajustes";


        $menu_select = "processos";
        $template = "lista_cadastro";
        // Configuração do formulário de criação de tags.
        $array_view['functionType_lista'] = "Lista de Processos";
        $array_view['functionType_cadastro'] = "Cadastrar Processos";
        $array_view['titulo_cadastro'] = "Cadastrar Processos";
        $array_view['array_input_titulo'] = array("Processos","Pasta","Tipo de Arquivo");
        $array_view['array_input_typ'] = array("text","text","select");
        $array_view['array_input_placeholder'] = array("Novo Processos","Nome da Pasta","Tipo de Arquivo");
        $array_view['array_input_id'] = array("nome_processos_novo","diretorio_novo","extencao_novo");
        $array_view['button_execut_nome'] = "Adicionar";

        // Configuração da página de lista de desenhos do usuário.
      
        $array_view['array_titulo_lista'] = array("Nome", "Diretório", "Tipo de Arquivo","Status", "");
        $array_view['menu'] = $this->menu($menu_box, $menu_select);

        // Exibe a lista de desenhos do usuário.
        echo view($template, $array_view);
    }

    /**
     * Gera um menu a partir de uma visão (template).
     *
     * Esta função é utilizada para gerar um menu a partir de uma visão (template). Ela destaca o item de menu selecionado com base nos parâmetros fornecidos.
     *
     * @param string $menu_box    O nome da caixa de menu (topo do menu).
     * @param string $menu_select O nome do item de menu selecionado.
     * A representação do menu gerada a partir da visão (template).
     */
    private function menu($menu_box = "", $menu_select = "", $menu_box2 = "")
    {
        // Carrega a visão do menu.
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $menu = view('menu/menu', array('permissao' => $_SESSION['permissao']));

        // Realiza substituições para destacar o item de menu selecionado.
        $menu = str_replace('id="' . $menu_box . '_top" class="nav-item"', 'id="' . $menu_box . '_top" class="nav-item menu-is-opening menu-open"', $menu);
        $menu = str_replace('id="' . $menu_box . '_bory"', 'id="' . $menu_box . '_bory" style="display: block;"', $menu);
        $menu = str_replace('id="' . $menu_box2 . '_top" class="nav-item"', 'id="' . $menu_box2 . '_top" class="nav-item menu-is-opening menu-open"', $menu);
        $menu = str_replace('id="' . $menu_box2 . '_bory"', 'id="' . $menu_box2 . '_bory" style="display: block;"', $menu);
        $menu = str_replace('id="' . $menu_select . '" class="nav-link"', 'id="' . $menu_select . '" class="nav-link active"', $menu);

        // Retorna a representação do menu gerada.
        return $menu;
    }



}