<?php

namespace Config;

// Cria uma nova instância da classe RouteCollection.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Configurações do Roteador
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// O Roteamento Automático (Legacy) é perigoso. É fácil criar aplicativos vulneráveis
// onde filtros de controlador ou proteção CSRF são contornados.
// Se você não deseja definir todas as rotas, use o Roteamento Automático (Melhorado).
// Defina `$autoRoutesImproved` como verdadeiro em `app/Config/Feature.php` e defina o seguinte como verdadeiro.
$routes->setAutoRoute(false);
$routes->get('/', 'Login::index'); // Rota padrão para a página inicial
$routes->get('/logout', 'Login::logout'); // Rota para fazer logout


// Rotas para ações do "desenhista"
$routes->get('/desenhista', 'Desenhista::index');
$routes->get('/desenhista/desenho_adicionar', 'Desenhista::desenho_adicionar');
$routes->get('/desenhista/desenho_meus', 'Desenhista::desenho_meus');
$routes->get('/desenhista/lista_corte', 'Desenhista::lista_corte');
$routes->get('/desenhista/subpastas', 'Desenhista::tags');
$routes->get('/desenhista/config_empreendimento', 'Desenhista::config_empreendimento');

// Definição de rotas POST para ações do "desenhista"
$routes->post('/desenhista/lita_filtro', 'AdmPost::lita_filtro');
$routes->post('/desenhista/desenho_adicionar_temp', 'DesenhistaPost::desenho_adicionar_temp');
$routes->post('/desenhista/desenho_adicionar_modal', 'DesenhistaPost::desenho_adicionar_modal');
$routes->post('/desenhista/config_prioridade_lista', 'AdmPost::config_prioridade_lista');
$routes->post('/desenhista/desenho_tag_lista', 'DesenhistaPost::desenho_tag_lista');
$routes->post('/desenhista/config_finalidade_lista', 'AdmPost::config_finalidade_lista');
$routes->post('/desenhista/config_empresa_lista', 'AdmPost::config_empresa_lista');
$routes->post('/desenhista/config_empreendimento_lista', 'AdmPost::config_empreendimento_lista');
$routes->post('/desenhista/desenhos_add', 'DesenhistaPost::desenhos_add');
$routes->post('/desenhista/criar_temp', 'DesenhistaPost::criar_temp');
$routes->post('/desenhista/lista_corte', 'DesenhistaPost::lista_corte');
$routes->post('/desenhista/desenho_tag_cadastro', 'DesenhistaPost::desenho_tag_cadastro');
$routes->post('/desenhista/config_tag_modal', 'DesenhistaPost::config_tag_modal');
$routes->post('/desenhista/config_tag_update', 'DesenhistaPost::config_tag_update');
$routes->post('/desenhista/troca_status/(:any)/(:any)', 'AdmPost::troca_status/$1/$2');
$routes->post('/desenhista/desenho_tag', 'DesenhistaPost::desenho_tag');


// Rotas para ações do "adm" (administrador)
$routes->get('/adm/desenhos_add', 'DesenhistaPost::desenhos_add');
$routes->get('/adm/desenho_tag_lista', 'DesenhistaPost::desenho_tag_lista');
$routes->get('/adm', 'Adm::index');
$routes->get('/adm/desenho_adicionar', 'Adm::desenho_adicionar');
$routes->get('/adm/desenho_meus', 'Adm::desenho_meus');
$routes->get('/adm/lista_corte', 'Adm::lista_corte');
$routes->get('/adm/config_tipo_de_arquivo', 'Adm::config_tipo_de_arquivo');
$routes->get('/adm/config_prioridade', 'Adm::config_prioridade');
$routes->get('/adm/config_finalidade', 'Adm::config_finalidade');
$routes->get('/adm/config_empresa_cliente', 'Adm::config_empresa_cliente');
$routes->get('/adm/config_empreendimento', 'Adm::config_empreendimento');
$routes->get('/adm/user_cadastrar', 'Adm::user_cadastrar');
$routes->get('/adm/user_modificar', 'Adm::user_modificar');
$routes->get('/adm/lista_corte1', 'AdmPost::lista_corte');
$routes->get('/adm/subpastas', 'adm::tags');
$routes->get('/adm/desenhos_cortados', 'adm::desenhos_cortados');
$routes->get('/adm/relatorios_analitico', 'adm::relatorios_analitico');



// Definição de rotas POST para ações do "adm"
$routes->post('/adm/subistituir_desenho', 'DesenhistaPost::subistituir_desenho');
$routes->post('/adm/subistituir_desenho_modal', 'DesenhistaPost::subistituir_desenho_modal');
$routes->post('/adm/apagar_desenho', 'DesenhistaPost::apagar_desenho');
$routes->post('/adm/desenho_novo_nome', 'DesenhistaPost::desenho_novo_nome');
$routes->post('/adm/recolocar_desenho', 'DesenhistaPost::recolocar_desenho');
$routes->post('/adm/nome_desenho', 'DesenhistaPost::nome_desenho');
$routes->post('/adm/lista_corte', 'AdmPost::lista_corte');
$routes->post('/adm/desenho_meus', 'AdmPost::desenho_meus');
$routes->post('/adm/desenhos_cortados', 'AdmPost::desenhos_cortados');
$routes->post('/adm/config_tipo_de_arquivo', 'AdmPost::config_tipo_de_arquivo');
$routes->post('/adm/config_prioridade', 'AdmPost::config_prioridade');
$routes->post('/adm/config_finalidade', 'AdmPost::config_finalidade');
$routes->post('/adm/config_empresa_cliente', 'AdmPost::config_empresa_cliente');
$routes->post('/adm/user_cadastrar', 'AdmPost::user_cadastrar');
$routes->post('/adm/config_empreendimento', 'AdmPost::config_empreendimento');
$routes->post('/adm/config_empreendimento_modal', 'AdmPost::config_empreendimento_modal');
$routes->post('/adm/config_empreendimento_cadastrar', 'AdmPost::config_empreendimento_cadastrar');
$routes->post('/adm/config_prioridade_lista', 'AdmPost::config_prioridade_lista');
$routes->post('/adm/desenho_meus_modal', 'AdmPost::desenho_meus_modal');
$routes->post('/adm/desenho_update', 'AdmPost::desenho_update');
$routes->post('/adm/desenho_modal', 'AdmPost::desenho_modal');
$routes->post('/adm/user_modificar', 'AdmPost::user_modificar');
$routes->post('/adm/lita_funcao', 'AdmPost::lita_funcao');
$routes->post('/adm/lita_empresa', 'AdmPost::lita_empresa');
$routes->post('/adm/config_empresa_cadastrar', 'AdmPost::config_empresa_cadastrar');
$routes->post('/adm/config_finalidade_cadastrar', 'AdmPost::config_finalidade_cadastrar');
$routes->post('/adm/config_prioridade_cadastrar', 'AdmPost::config_prioridade_cadastrar');
$routes->post('/adm/ordem_max', 'AdmPost::ordem_max');
$routes->post('/adm/config_tipo_de_arquivo_cadastrar', 'AdmPost::config_tipo_de_arquivo_cadastrar');
$routes->post('/adm/user_modificar_modal', 'AdmPost::user_modificar_modal');
$routes->post('/adm/user_modificar_update', 'AdmPost::user_modificar_update');
$routes->post('/adm/config_empreendimento_update', 'AdmPost::config_empreendimento_update');
$routes->post('/adm/config_empresa_modal', 'AdmPost::config_empresa_modal');
$routes->post('/adm/config_empresa_update', 'AdmPost::config_empresa_update');
$routes->post('/adm/config_finalidade_modal', 'AdmPost::config_finalidade_modal');
$routes->post('/adm/config_finalidade_update', 'AdmPost::config_finalidade_update');
$routes->post('/adm/config_tipo_de_arquivo_update', 'AdmPost::config_tipo_de_arquivo_update');
$routes->post('/adm/config_tipo_de_arquivo_modal', 'AdmPost::config_tipo_de_arquivo_modal');
$routes->post('/adm/config_prioridade_update', 'AdmPost::config_prioridade_update');
$routes->post('/adm/config_prioridade_modal', 'AdmPost::config_prioridade_modal');
$routes->post('/adm/lita_ordem', 'AdmPost::lita_ordem');
$routes->post('/adm/lita_filtro', 'AdmPost::lita_filtro');
$routes->post('/adm/config_finalidade_lista', 'AdmPost::config_finalidade_lista');
$routes->post('/adm/config_empresa_lista', 'AdmPost::config_empresa_lista');
$routes->post('/adm/config_empreendimento_lista', 'AdmPost::config_empreendimento_lista');
$routes->post('/adm/desenhos_add', 'DesenhistaPost::desenhos_add');
$routes->post('/adm/desenho_tag_lista', 'DesenhistaPost::desenho_tag_lista');
$routes->post('/adm/desenho_tag', 'DesenhistaPost::desenho_tag');
$routes->post('/adm/desenho_tag_cadastro', 'DesenhistaPost::desenho_tag_cadastro');
$routes->post('/adm/config_tag_update', 'DesenhistaPost::config_tag_update');
$routes->post('/adm/config_tag_modal', 'DesenhistaPost::config_tag_modal');
$routes->post('/adm/desenho_adicionar_temp', 'DesenhistaPost::desenho_adicionar_temp');
$routes->post('/adm/desenho_adicionar_modal', 'DesenhistaPost::desenho_adicionar_modal');
$routes->post('/adm/criar_temp', 'DesenhistaPost::criar_temp');
$routes->post('/adm/troca_status/(:any)/(:any)', 'AdmPost::troca_status/$1/$2');
$routes->post('/adm/relatorio_analitico', 'AdmPost::relatorio_analitico');
$routes->post('/adm/lista_desenhistas', 'AdmPost::lista_desenhistas');
$routes->post('/adm/lista_cortadores', 'AdmPost::lista_cortadores');


// Rotas para ações do "cortador"
$routes->get('/cortador', 'Cortador::lista_corte');

// Definição de rotas POST para ações do "cortador"
$routes->post('/login','Login::login');
$routes->post('/corte/cancelar_corte','CortadorPost::cancelar_corte');
$routes->post('/corte/lista_corte', 'CortadorPost::lista_corte');
$routes->post('/corte/caminho_desenho', 'CortadorPost::caminho_desenho');
$routes->post('/corte/confirmar_desenho', 'CortadorPost::confirmar_desenho');
$routes->post('/corte/confirmar_corte', 'CortadorPost::confirmar_corte');






/*
 * --------------------------------------------------------------------
 * Definições de Rota
 * --------------------------------------------------------------------
 */

// Recebemos um aumento de desempenho especificando a rota padrão
// porque não precisamos examinar diretórios.

/*
 * --------------------------------------------------------------------
 * Roteamento Adicional
 * --------------------------------------------------------------------
 *
 * Muitas vezes, é necessário roteamento adicional que pode anular
 * qualquer padrão neste arquivo. Rotas baseadas em ambiente são um exemplo disso.
 * Requer() arquivos de rota adicionais aqui para fazer isso acontecer.
 *
 * Você terá acesso ao objeto $routes dentro desse arquivo sem
 * precisar recarregá-lo.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
