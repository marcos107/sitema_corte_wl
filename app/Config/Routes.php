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




//temp
$routes->post('/troca_status/(:any)/(:any)', 'AdmPost::troca_status/$1/$2');

//nivel
$routes->get('/nivel', 'Pagina::nivel');   
$routes->post('/nivel_cadastrar_modal', 'NivelPost::nivel_cadastrar_modal');   
$routes->post('/nivel_lista_desativar', 'NivelPost::nivel_lista_desativar');   
$routes->post('/nivel_lista_ativar', 'NivelPost::nivel_lista_ativar');   
$routes->post('/nivel_lista', 'NivelPost::nivel_lista');   
$routes->post('/nivel_modifica_modal', 'NivelPost::nivel_modifica_modal');   
$routes->post('/nivel_modificar', 'NivelPost::nivel_modificar');   
$routes->post('/nivel_cadastrar', 'NivelPost::nivel_cadastrar');
$routes->post('/nivel_option', 'NivelPost::nivel_option');
$routes->post('/lista_nivel', 'NivelPost::lista_nivel');



//AddDesenhoPost
$routes->get('/desenho_adicionar', 'Pagina::desenho_adicionar');
$routes->post('/criar_pasta_temp', 'AddDesenhoPost::criar_pasta_temp');
$routes->post('/desenhos_add', 'AddDesenhoPost::desenhos_add');
$routes->post('/desenho_adicionar_temp', 'AddDesenhoPost::desenho_adicionar_temp');
$routes->post('/desenho_adicionar_modal', 'AddDesenhoPost::desenho_adicionar_modal');

//DesenhosPost
$routes->post('/desenho_update', 'DesenhoPost::desenho_update');
$routes->post('/desenho_modal', 'DesenhoPost::desenho_modal');
$routes->post('/nome_desenho', 'DesenhoPost::nome_desenho');
$routes->post('/apagar_desenho', 'DesenhoPost::apagar_desenho');
$routes->post('/subistituir_desenho', 'DesenhoPost::subistituir_desenho');
$routes->post('/subistituir_desenho_modal', 'DesenhoPost::subistituir_desenho_modal');
$routes->post('/desenho_novo_nome', 'DesenhoPost::desenho_novo_nome');
$routes->post('/recolocar_desenho', 'DesenhoPost::recolocar_desenho');


//MeusDesenhosPost
$routes->get('/desenho_meus', 'Pagina::desenho_meus');
$routes->post('/desenho_meus', 'MeusDesenhosPost::desenho_meus');
$routes->post('/desenho_meus_modal', 'MeusDesenhosPost::desenho_meus_modal');

//ListaCortePost
$routes->get('/lista_corte_adm', 'Pagina::lista_corte_adm');
$routes->get('/lista_corte', 'Pagina::lista_corte');
$routes->get('/lista_corte_cortador', 'Pagina::lista_corte_cortador');
$routes->post('/lista_corte', 'ListaCortePost::lista_corte_desenhista');
$routes->post('/lista_corte_cortador', 'ListaCortePost::lista_corte_cortador');
$routes->post('/lista_corte_adm', 'ListaCortePost::lista_corte_adm');
$routes->post('/cancelar_corte', 'ListaCortePost::cancelar_corte');
$routes->post('/confirmar_corte', 'ListaCortePost::confirmar_corte');
$routes->post('/caminho_desenho', 'ListaCortePost::caminho_desenho');
$routes->post('/atualiza_prio', 'ListaCortePost::atualiza_prio');


//SubpastaPost
$routes->get('/subpasta', 'Pagina::subpasta');
$routes->post('/desenho_tag', 'SubpastaPost::desenho_tag');
$routes->post('/desenho_tag_cadastro', 'SubpastaPost::desenho_tag_cadastro');
$routes->post('/tag_modal', 'SubpastaPost::tag_modal');
$routes->post('/tag_update', 'SubpastaPost::tag_update');
$routes->post('/desenho_tag_lista', 'SubpastaPost::desenho_tag_lista');

//DesenhosCortadosPost
$routes->get('/desenhos_cortados', 'Pagina::desenhos_cortados');
$routes->post('/desenhos_cortados', 'DesenhosCortadosPost::desenhos_cortados');

//ExtencaoPost
$routes->get('/tipo_de_arquivo', 'Pagina::extencao');
$routes->post('/extencao', 'ExtencaoPost::extencao');
$routes->post('/extencao_cadastrar', 'ExtencaoPost::extencao_cadastrar');
$routes->post('/extencao_modal', 'ExtencaoPost::extencao_modal');
$routes->post('/extencao_update', 'ExtencaoPost::extencao_update');
$routes->post('/lista_filtro', 'ExtencaoPost::lista_filtro');

//ProridadePost
$routes->get('/prioridade', 'Pagina::prioridade');
$routes->post('/prioridade', 'PrioridadePost::prioridade');
$routes->post('/ordem_max', 'PrioridadePost::ordem_max');
$routes->post('/prioridade_cadastrar', 'PrioridadePost::prioridade_cadastrar');
$routes->post('/prioridade_modal', 'PrioridadePost::prioridade_modal');
$routes->post('/prioridade_update', 'PrioridadePost::prioridade_update');
$routes->post('/lista_ordem', 'PrioridadePost::lista_ordem');
$routes->post('/prioridade_lista', 'PrioridadePost::prioridade_lista');


//FinalidadePost
$routes->get('/finalidade', 'Pagina::finalidade');
$routes->post('/finalidade', 'FinalidadePost::finalidade');
$routes->post('/finalidade_cadastrar', 'FinalidadePost::finalidade_cadastrar');
$routes->post('/finalidade_modal', 'FinalidadePost::finalidade_modal');
$routes->post('/finalidade_update', 'FinalidadePost::finalidade_update');
$routes->post('/finalidade_lista', 'FinalidadePost::finalidade_lista');


//EmpresaPost
$routes->get('/empresa', 'Pagina::empresa');
$routes->post('/empresa_cliente', 'EmpresaPost::empresa_cliente');
$routes->post('/empresa_cadastrar', 'EmpresaPost::empresa_cadastrar');
$routes->post('/empresa_modal', 'EmpresaPost::empresa_modal');
$routes->post('/empresa_update', 'EmpresaPost::empresa_update');
$routes->post('/lista_empresa', 'EmpresaPost::lista_empresa');
$routes->post('/empresa_lista', 'EmpresaPost::empresa_lista');



//EmpreendimentoPost
$routes->get('/empreendimento', 'Pagina::empreendimento');
$routes->post('/empreendimento', 'EmpreendimentoPost::empreendimento');
$routes->post('/empreendimento_cadastrar', 'EmpreendimentoPost::empreendimento_cadastrar');
$routes->post('/empreendimento_modal', 'EmpreendimentoPost::empreendimento_modal');
$routes->post('/empreendimento_update', 'EmpreendimentoPost::empreendimento_update');
$routes->post('/empreendimento_lista', 'EmpreendimentoPost::empreendimento_lista');


//UsuarioPost
$routes->get('/user_cadastrar', 'Pagina::user_cadastrar');
$routes->get('/user_modificar', 'Pagina::user_modificar');
$routes->post('/user_cadastrar', 'UsuarioPost::user_cadastrar');
$routes->post('/user_modificar_update', 'UsuarioPost::user_modificar_update');
$routes->post('/user_modificar_modal', 'UsuarioPost::user_modificar_modal');
$routes->post('/user_modificar', 'UsuarioPost::user_modificar');


//ReratorioPost
$routes->get('/relatorios', 'Pagina::relatorios');
$routes->post('/relatorio_analitico', 'ReratorioPost::relatorio_analitico');
$routes->post('/lista_desenhistas', 'ReratorioPost::lista_desenhistas');
$routes->post('/lista_cortadores', 'ReratorioPost::lista_cortadores');




// // Rotas para ações do "desenhista"
// $routes->get('/desenhista', 'Desenhista::index');
// $routes->get('/desenhista/desenho_adicionar', 'Desenhista::desenho_adicionar');
// $routes->get('/desenhista/desenho_meus', 'Desenhista::desenho_meus');
// $routes->get('/desenhista/lista_corte', 'Desenhista::lista_corte');
// $routes->get('/desenhista/subpastas', 'Desenhista::tags');
// $routes->get('/desenhista/empreendimento', 'Desenhista::empreendimento');

// // Definição de rotas POST para ações do "desenhista"
// $routes->post('/desenhista/lita_filtro', 'AdmPost::lita_filtro');
// $routes->post('/desenhista/desenho_adicionar_temp', 'DesenhistaPost::desenho_adicionar_temp');
// $routes->post('/desenhista/desenho_adicionar_modal', 'DesenhistaPost::desenho_adicionar_modal');
// $routes->post('/desenhista/prioridade_lista', 'AdmPost::prioridade_lista');
// $routes->post('/desenhista/desenho_tag_lista', 'DesenhistaPost::desenho_tag_lista');
// $routes->post('/desenhista/finalidade_lista', 'AdmPost::finalidade_lista');
// $routes->post('/desenhista/empresa_lista', 'AdmPost::empresa_lista');
// $routes->post('/desenhista/empreendimento_lista', 'AdmPost::empreendimento_lista');
// $routes->post('/desenhista/desenhos_add', 'DesenhistaPost::desenhos_add');
// $routes->post('/desenhista/criar_pasta_temp', 'DesenhistaPost::criar_pasta_temp');
// $routes->post('/desenhista/lista_corte', 'DesenhistaPost::lista_corte');
// $routes->post('/desenhista/desenho_tag_cadastro', 'DesenhistaPost::desenho_tag_cadastro');
// $routes->post('/desenhista/tag_modal', 'DesenhistaPost::tag_modal');
// $routes->post('/desenhista/tag_update', 'DesenhistaPost::tag_update');
// $routes->post('/desenhista/troca_status/(:any)/(:any)', 'AdmPost::troca_status/$1/$2');
// $routes->post('/desenhista/desenho_tag', 'DesenhistaPost::desenho_tag');


// // Rotas para ações do "adm" (administrador)
// $routes->get('/adm/desenhos_add', 'DesenhistaPost::desenhos_add');
// $routes->get('/adm/desenho_tag_lista', 'DesenhistaPost::desenho_tag_lista');
// $routes->get('/adm', 'Adm::index');
// $routes->get('/adm/desenho_adicionar', 'Adm::desenho_adicionar');
// $routes->get('/adm/desenho_meus', 'Adm::desenho_meus');
// $routes->get('/adm/lista_corte', 'Adm::lista_corte');
// $routes->get('/adm/tipo_de_arquivo', 'Adm::tipo_de_arquivo');
// $routes->get('/adm/prioridade', 'Adm::prioridade');
// $routes->get('/adm/finalidade', 'Adm::finalidade');
// $routes->get('/adm/empresa_cliente', 'Adm::empresa_cliente');
// $routes->get('/adm/empreendimento', 'Adm::empreendimento');
// $routes->get('/adm/user_cadastrar', 'Adm::user_cadastrar');
// $routes->get('/adm/user_modificar', 'Adm::user_modificar');
// $routes->get('/adm/subpastas', 'adm::tags');
// $routes->get('/adm/desenhos_cortados', 'adm::desenhos_cortados');
// $routes->get('/adm/relatorios_analitico', 'adm::relatorios_analitico');



// // Definição de rotas POST para ações do "adm"
// $routes->post('/adm/subistituir_desenho', 'DesenhistaPost::subistituir_desenho');
// $routes->post('/adm/subistituir_desenho_modal', 'DesenhistaPost::subistituir_desenho_modal');
// $routes->post('/adm/apagar_desenho', 'DesenhistaPost::apagar_desenho');
// $routes->post('/adm/desenho_novo_nome', 'DesenhistaPost::desenho_novo_nome');
// $routes->post('/adm/recolocar_desenho', 'DesenhistaPost::recolocar_desenho');
// $routes->post('/adm/nome_desenho', 'DesenhistaPost::nome_desenho');
// $routes->post('/adm/lista_corte', 'AdmPost::lista_corte');
// $routes->post('/adm/desenho_meus', 'AdmPost::desenho_meus');
// $routes->post('/adm/desenhos_cortados', 'AdmPost::desenhos_cortados');
// $routes->post('/adm/tipo_de_arquivo', 'AdmPost::tipo_de_arquivo');
// $routes->post('/adm/prioridade', 'AdmPost::prioridade');
// $routes->post('/adm/finalidade', 'AdmPost::finalidade');
// $routes->post('/adm/empresa_cliente', 'AdmPost::empresa_cliente');
// $routes->post('/adm/user_cadastrar', 'AdmPost::user_cadastrar');
// $routes->post('/adm/empreendimento', 'AdmPost::empreendimento');
// $routes->post('/adm/empreendimento_modal', 'AdmPost::empreendimento_modal');
// $routes->post('/adm/empreendimento_cadastrar', 'AdmPost::empreendimento_cadastrar');
// $routes->post('/adm/prioridade_lista', 'AdmPost::prioridade_lista');
// $routes->post('/adm/desenho_meus_modal', 'AdmPost::desenho_meus_modal');
// $routes->post('/adm/desenho_modal', 'AdmPost::desenho_modal');
// $routes->post('/adm/user_modificar', 'AdmPost::user_modificar');
// $routes->post('/adm/lita_funcao', 'AdmPost::lita_funcao');
// $routes->post('/adm/lista_empresa', 'AdmPost::lista_empresa');
// $routes->post('/adm/empresa_cadastrar', 'AdmPost::empresa_cadastrar');
// $routes->post('/adm/finalidade_cadastrar', 'AdmPost::finalidade_cadastrar');
// $routes->post('/adm/prioridade_cadastrar', 'AdmPost::prioridade_cadastrar');
// $routes->post('/adm/ordem_max', 'AdmPost::ordem_max');
// $routes->post('/adm/tipo_de_arquivo_cadastrar', 'AdmPost::tipo_de_arquivo_cadastrar');
// $routes->post('/adm/user_modificar_modal', 'AdmPost::user_modificar_modal');
// $routes->post('/adm/user_modificar_update', 'AdmPost::user_modificar_update');
// $routes->post('/adm/empreendimento_update', 'AdmPost::empreendimento_update');
// $routes->post('/adm/empresa_modal', 'AdmPost::empresa_modal');
// $routes->post('/adm/empresa_update', 'AdmPost::empresa_update');
// $routes->post('/adm/finalidade_modal', 'AdmPost::finalidade_modal');
// $routes->post('/adm/finalidade_update', 'AdmPost::finalidade_update');
// $routes->post('/adm/tipo_de_arquivo_update', 'AdmPost::tipo_de_arquivo_update');
// $routes->post('/adm/tipo_de_arquivo_modal', 'AdmPost::tipo_de_arquivo_modal');
// $routes->post('/adm/prioridade_update', 'AdmPost::prioridade_update');
// $routes->post('/adm/prioridade_modal', 'AdmPost::prioridade_modal');
// $routes->post('/adm/lita_ordem', 'AdmPost::lita_ordem');
// $routes->post('/adm/lita_filtro', 'AdmPost::lita_filtro');
// $routes->post('/adm/finalidade_lista', 'AdmPost::finalidade_lista');
// $routes->post('/adm/empresa_lista', 'AdmPost::empresa_lista');
// $routes->post('/adm/empreendimento_lista', 'AdmPost::empreendimento_lista');
// $routes->post('/adm/desenhos_add', 'DesenhistaPost::desenhos_add');
// $routes->post('/adm/desenho_tag_lista', 'DesenhistaPost::desenho_tag_lista');
// $routes->post('/adm/desenho_tag', 'DesenhistaPost::desenho_tag');
// $routes->post('/adm/desenho_tag_cadastro', 'DesenhistaPost::desenho_tag_cadastro');
// $routes->post('/adm/tag_update', 'DesenhistaPost::tag_update');
// $routes->post('/adm/tag_modal', 'DesenhistaPost::tag_modal');
// $routes->post('/adm/desenho_adicionar_temp', 'DesenhistaPost::desenho_adicionar_temp');
// $routes->post('/adm/desenho_adicionar_modal', 'DesenhistaPost::desenho_adicionar_modal');
// $routes->post('/adm/criar_pasta_temp', 'DesenhistaPost::criar_pasta_temp');
// $routes->post('/adm/troca_status/(:any)/(:any)', 'AdmPost::troca_status/$1/$2');
// $routes->post('/adm/relatorio_analitico', 'AdmPost::relatorio_analitico');
// $routes->post('/adm/lista_desenhistas', 'AdmPost::lista_desenhistas');
// $routes->post('/adm/lista_cortadores', 'AdmPost::lista_cortadores');


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
