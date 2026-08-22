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
$routes->get('/login', 'Login::index'); // Alias para acessar a página de login diretamente
$routes->get('/logout', 'Login::logout'); // Rota para fazer logout
$routes->post('/login','Login::login');
$routes->post('/login_verificar_permissao','Login::login_verificar_permissao');
$routes->post('/recuperar_senha','Login::recuperar_senha');
$routes->post('/recuperar_senha_confirmar','Login::recuperar_senha_confirmar');

//api
$routes->post('/empreendimento_api', 'Api::empreendimento_api');   
$routes->post('/projetistas_api', 'Api::projetistas_api');   
$routes->post('/cortador_api', 'Api::cortador_api');   
$routes->post('/empreendimento_arquivos_api', 'Api::empreendimento_arquivos_api');   
$routes->post('/finalizar_empreendimento_api', 'Api::finalizar_empreendimento_api');   






//Ferramentas
$routes->post('/troca_status/(:any)/(:any)', 'Ferramentas::troca_status/$1/$2');

$routes->get('/verificarArquivos', 'Teste::verificarArquivos');   
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
$routes->post('/desenhos_add_uni', 'AddDesenhoPost::desenhos_add_uni');
$routes->post('/desenho_adicionar_temp', 'AddDesenhoPost::desenho_adicionar_temp');
$routes->post('/desenho_adicionar_modal', 'AddDesenhoPost::desenho_adicionar_modal');

//DesenhosPost
$routes->post('/desenho_update', 'DesenhoPost::desenho_update');
$routes->post('/desenho_modal', 'DesenhoPost::desenho_modal');
$routes->post('/nome_desenho', 'DesenhoPost::nome_desenho');
$routes->post('/apagar_desenho', 'DesenhoPost::apagar_desenho');
$routes->post('/desenho_novo_nome', 'DesenhoPost::desenho_novo_nome');
//$routes->post('/recolocar_desenho', 'DesenhoPost::recolocar_desenho');


//DesenhosMeusPost
$routes->get('/desenho_meus', 'Pagina::desenho_meus');
$routes->post('/desenho_meus', 'DesenhosMeusPost::desenho_meus');
$routes->post('/desenho_meus_modal', 'DesenhosMeusPost::desenho_meus_modal');
$routes->post('/subistituir_desenho', 'DesenhosMeusPost::subistituir_desenho');
$routes->post('/subistituir_desenho_modal', 'DesenhosMeusPost::subistituir_desenho_modal');

//ListaCortePost
$routes->get('/painel_tarefas', 'PainelTarefas::index');
$routes->post('/painel_tarefas_lista', 'PainelTarefas::lista');
$routes->post('/painel_tarefas_acao', 'PainelTarefas::acao');
$routes->post('/painel_tarefas_visualizar', 'PainelTarefas::visualizar');
$routes->get('/lista_Tarefas_adm', 'Pagina::lista_corte_adm');
$routes->get('/lista_Tarefas', 'Pagina::lista_corte');
$routes->get('/lista_tarefas', 'Pagina::lista_tarefas');
$routes->post('/lista_corte', 'ListaCortePost::lista_corte_desenhista');
$routes->post('/lista_tarefas', 'ListaCortePost::lista_tarefas');
$routes->post('/lista_corte_adm', 'ListaCortePost::lista_corte_adm');
$routes->post('/cancelar_corte', 'ListaCortePost::cancelar_corte');
$routes->post('/confirmar_corte', 'ListaCortePost::confirmar_corte');
$routes->post('/caminho_desenho', 'ListaCortePost::caminho_desenho');
$routes->post('/caminho_desenho_atual', 'ListaCortePost::caminho_desenho_atual');
$routes->post('/atualiza_prio', 'ListaCortePost::atualiza_prio');
$routes->post('/baixar_projeto', 'ListaCortePost::baixar_projeto');
$routes->post('/confirmar_projeto', 'ListaCortePost::confirmar_projeto');
$routes->post('/baixar_arquivo', 'ListaCortePost::baixar_arquivo');
$routes->post('/desenho_adicionar_projeto', 'ListaCortePost::desenho_adicionar_projeto');
$routes->post('/preparar_adicionar_projeto', 'ListaCortePost::preparar_adicionar_projeto');
$routes->post('/marcar_arquivo', 'ListaCortePost::marcar_arquivo');
$routes->post('/remover_arquivo_projeto', 'ListaCortePost::remover_arquivo_projeto');

$routes->post('/ver_desenho', 'ListaCortePost::ver_desenho');   


//SubpastaPost
$routes->get('/subpasta', 'Pagina::subpasta');
$routes->post('/desenho_tag', 'SubpastaPost::desenho_tag');
$routes->post('/desenho_tag_cadastro', 'SubpastaPost::desenho_tag_cadastro');
$routes->post('/tag_modal', 'SubpastaPost::tag_modal');
$routes->post('/tag_update', 'SubpastaPost::tag_update');
$routes->post('/desenho_tag_lista', 'SubpastaPost::desenho_tag_lista');

//DesenhosCortadosPost
$routes->get('/desenhos_cortados', 'PainelTarefas::index');
$routes->post('/desenhos_cortados', 'DesenhosCortadosPost::desenhos_cortados');
$routes->post('/lista_recolcoar', 'DesenhosCortadosPost::lista_recolcoar');
$routes->post('/solicitar_recolocar_desenho', 'DesenhosCortadosPost::solicitar_recolocar_desenho');
$routes->post('/recolocar_desenho', 'DesenhosCortadosPost::recolocar_desenho');


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
$routes->get('/empresas_lista', 'EmpresaPost::empresas_lista');



//EmpreendimentoPost
$routes->get('/empreendimento', 'Pagina::empreendimento');
$routes->post('/empreendimento', 'EmpreendimentoPost::empreendimento');
$routes->post('/empreendimento_cadastrar', 'EmpreendimentoPost::empreendimento_cadastrar');
$routes->post('/empreendimento_modal', 'EmpreendimentoPost::empreendimento_modal');
$routes->post('/empreendimento_update', 'EmpreendimentoPost::empreendimento_update');
$routes->post('/empreendimento_lista', 'EmpreendimentoPost::empreendimento_lista');
$routes->get('/empreendimentos_lista', 'EmpreendimentoPost::empreendimentos_lista');


//UsuarioPost
$routes->get('/usuario', 'Pagina::usuario');
$routes->post('/user_cadastrar', 'UsuarioPost::user_cadastrar');
$routes->post('/user_modificar_update', 'UsuarioPost::user_modificar_update');
$routes->post('/user_modificar_modal', 'UsuarioPost::user_modificar_modal');
$routes->post('/user_modificar', 'UsuarioPost::user_modificar');


//RelatorioPost
$routes->get('/relatorios', 'Pagina::relatorios');
$routes->post('/relatorio', 'RelatorioPost::relatorio');
$routes->post('/lista_usuarios_niveis', 'RelatorioPost::lista_usuarios_niveis');

//LogsPost
$routes->get('/logs_alteracoes', 'Pagina::logs_alteracoes');
$routes->post('/logs_alteracoes_lista', 'LogsPost::logs_alteracoes_lista');

//processos
$routes->get('/processos', 'Pagina::processos');
$routes->post('/processos_cadastrar', 'ProcessosPost::processos_cadastrar');
$routes->post('/processos', 'ProcessosPost::processos');
$routes->post('/processos_lista', 'ProcessosPost::processos_lista');
$routes->post('/processos_modifica_modal', 'ProcessosPost::processos_modifica_modal');
$routes->post('/processos_modificar', 'ProcessosPost::processos_modificar');



//api
$routes->post('/comecar_conversa', 'Api::comecar_conversa');
$routes->post('/login_api', 'Api::login_api');
$routes->post('/lista_tarefas_api', 'Api::lista_tarefas_api');
$routes->post('/download_lista_tarefas_api', 'Api::download_lista_tarefas_api');
$routes->post('/confirmar_tarefa_api', 'Api::confirmar_tarefa_api');
$routes->post('/iniciar_tarefa_api', 'Api::iniciar_tarefa_api');
$routes->post('/login_cancelar_api', 'Api::login_cancelar_api');
$routes->post('/login_config_api', 'Api::login_config_api');

$routes->get('/login_api', 'Api::login_api');
$routes->get('/download_lista_tarefas_api', 'Api::download_lista_tarefas_api');


$routes->post('/enviar_para_lista_corte', 'ListaCortePost::enviar_para_lista_corte');


$routes->get('/ordenar', 'Ferramentas::ordenar');
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
