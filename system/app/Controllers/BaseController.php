<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Classe BaseController
 *
 * BaseController fornece um local conveniente para carregar componentes
 * e executando funções necessárias a todos os seus controladores.
 * Estenda esta classe em quaisquer novos controladores:
 * classe Home estende BaseController
 *
 * Por segurança, certifique-se de declarar quaisquer novos métodos como protegidos ou privados.
 */
abstract class BaseController extends Controller
{
    /**
     * Instância do objeto Request principal.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Uma matriz de ajudantes a serem carregados automaticamente
     * instanciação de classe. Esses ajudantes estarão disponíveis
     * para todos os outros controladores que estendem BaseController.
     *
     * @var matriz
     */
    protected $helpers = [];

    /**
     * Certifique-se de declarar propriedades para qualquer busca de propriedade que você inicializou.
     * A criação de propriedades dinâmicas está obsoleta no PHP 8.2.
     */
    //protected $session;

    /**
     * Construtor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        date_default_timezone_set('America/Sao_Paulo');
        //Não edite esta linha
        parent::initController($request, $response, $logger);

        // Pré-carregue quaisquer modelos, bibliotecas, etc., aqui.

        // E.g.: $this->session = \Config\Services::session();
    }
}