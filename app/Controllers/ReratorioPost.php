<?php
namespace App\Controllers;


use App\Controllers\Ferramentas;

use Mpdf\Mpdf;

class ReratorioPost extends Ferramentas
{
    function timeToSeconds($time)
    {
      list($hours, $minutes, $seconds) = explode(':', $time);
      return $hours * 3600 + $minutes * 60 + $seconds;
    }
  
    function secondsToTime($seconds)
    {
      $hours = floor($seconds / 3600);
      $minutes = floor(($seconds % 3600) / 60);
      $seconds = $seconds % 60;
      return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
  
    function relatorio_analitico()
    {
      if ($this->request->isAJAX()) {
        session_start();
  
  
        $dataFinal_str = service('request')->getPost('dataFinal');
        $dataInicial_str = service('request')->getPost('dataInicial');
        $desenhista_permissao = service('request')->getPost('desenhistas');
        $cortador_permissao = service('request')->getPost('cortador');
        $relatorio = service('request')->getPost('relatorio');
        $msg = array();
  
        if ($dataFinal_str == "") {
          $msg["Data Final"] = "É precisso selecionar uma data final.";
        }
        if ($dataInicial_str == "") {
          $msg["Data Inicial"] = "É precisso selecionar uma data inicial.";
        }
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataFinal_str)) {
          $msg["Data Final"] = "É precisso selecionar uma data final valida.";
        }
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $dataInicial_str)) {
          $msg["Data Inicial"] = "É precisso selecionar uma data inicial valida.";
        }
  
  
        $dataFinal = strtotime($dataFinal_str);
        $dataInicial = strtotime($dataInicial_str);
  
        // Compare as datas
        if (!($dataFinal >= $dataInicial)) {
          $msg["Data Inicial"] = "A data final não pode ser anterior à data inicial.";
        }
  
  
        if ($msg != []) {
          $data = [
            'ok' => false,
            'msg' => $msg
          ];
          return $this->response->setJSON($data);
        }
  
  
  
  
  
  
  
  
  
  
        // Inicialização de objetos para acessar tabelas do banco de dados
        $desenhos = new \App\Models\Desenhos();
        $prioridade = new \App\Models\Prioridade();
        $finalidade = new \App\Models\Finalidade();
        $empresa = new \App\Models\Empresa();
        $empreendimento = new \App\Models\Empreendimentos();
        $usuario = new \App\Models\Usuarios();
        $cortado = new \App\Models\Corte();
        // Recupera dados das tabelas do banco de dados
        $prioridade_data = $prioridade->find();
        $finalidade_data = $finalidade->find();
        $empresa_data = $empresa->find();
        $empreendimento_data = $empreendimento->find();
        $desenhos_data = $desenhos->find();
        $usuario_data = $usuario->find();
        $cortado_data = $cortado->find();
  
        $corte = array();
        $desenhista = array();
        $teste = 0;
        // Itera sobre os dados de desenhos para criar a lista
        foreach ($desenhos_data as $key => $value) {
          $ok_desenhista = false;
          $ok_cortador = false;
          if ($desenhista_permissao)
            foreach ($desenhista_permissao as $value1) {
              if ($_SESSION["lista_desenhista"][$value1]['id'] == $value['desenhista']) {
                $ok_desenhista = true;
              }
            }
          if ($cortador_permissao)
            foreach ($cortador_permissao as $value1) {
              if ($_SESSION["lista_cortador"][$value1]['id'] == $value['cortador']) {
                $ok_cortador = true;
              }
            }
  
  
          if (!$ok_cortador and !$ok_desenhista)
            continue;
  
          // $teste++;
          // Converter a data específica para timestamp
          $dataEspecifica_desenho_add = strtotime(str_replace('/', '-', Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add']))));
          $dataEspecifica_corte = strtotime(str_replace('/', '-', Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']))));
  
  
  
          $tags = explode('/', Ferramentas::decodificador($value['caminho']));
          // Remover os índices de 0 a 5
          $tags = array_slice($tags, 6);
  
          // Remover o último elemento
          unset($tags[count($tags) - 1]);
          $tags = implode(" - ", $tags);
          $data_hora_corte_fim = strtotime(str_replace('/', '-', Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']))));
  
          if (
            (
              (Ferramentas::decodificador($value['status']) == "cortado" or Ferramentas::decodificador($value['status']) == "cortado_notfile") &&
              (($dataEspecifica_corte >= $dataInicial && $dataEspecifica_corte <= $dataFinal) or
                ($dataEspecifica_corte == null && ($dataEspecifica_desenho_add >= $dataInicial && $dataEspecifica_desenho_add <= $dataFinal)))
            )
          ) {
  
            $data_hora_corte_fim = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_fim']));
            $data_hora_corte_ini = Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($cortado_data, 'id_desenho', $value['id']), ['data_add']));
  
            // if (strlen($data_hora_corte_fim) < 5) {
            //   continue;
            // }
            // Convertendo as datas para timestamps Unix
  
  
            $timestamp1 = strtotime(str_replace('/', '-', $data_hora_corte_fim));
            $timestamp2 = strtotime(str_replace('/', '-', $data_hora_corte_ini));
  
            // Calculando a diferença em segundos
            $diferencaSegundos = abs($timestamp2 - $timestamp1);
  
            // Convertendo a diferença para horas, minutos e segundos
            $horas = floor($diferencaSegundos / 3600);
            $minutos = floor(($diferencaSegundos % 3600) / 60);
            $segundos = $diferencaSegundos % 60;
  
            // Formatando a diferença como "horas:minutos:segundos"
            $diferencaHoras = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
            if ($data_hora_corte_fim == null or $data_hora_corte_ini == null) {
              $diferencaHoras = "00:00:00";
            }
  
            $corte[Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['cortador']), ['nome']))][] =
              [
                "desenhista" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])),
                "nome_arquivo" => Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))),
                "empresa" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])),
                "empreendimento" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])),
                "finalidade" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])),
                "tags" => $tags,
                "data_hora_add" => Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add'])),
                "data_hora_corte" => $data_hora_corte_fim,
                "tempo_corte" => $diferencaHoras,
                "cortador" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['cortador']), ['nome'])),
                "status" => Ferramentas::decodificador(Ferramentas::decodificador($value['status'])),
                "ok" => $ok_cortador
  
              ];
          } else {
            if ($value['status'] != 'apagado') {
              $value['status'] = 'corte';
            }
          }
  
  
  
  
  
  
  
          if (($dataEspecifica_desenho_add >= $dataInicial && $dataEspecifica_desenho_add <= $dataFinal)) {
  
  
  
            $desenhista[Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome']))][] =
              [
                "desenhista" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($usuario_data, 'id', $value['desenhista']), ['nome'])),
                "nome_arquivo" => Ferramentas::decodificador(Ferramentas::remove_id_file(Ferramentas::decodificador($value['nome']))),
                "empresa" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empresa_data, 'id', $value['empresa']), ['nome'])),
                "empreendimento" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($empreendimento_data, 'id', $value['empreendimento']), ['nome'])),
                "finalidade" => Ferramentas::decodificador(Ferramentas::array_index(Ferramentas::array_pesquisa($finalidade_data, 'id', $value['finalidade']), ['nome'])),
                "tags" => $tags,
                "data_hora_add" => Ferramentas::decodificador(Ferramentas::decodificador($value['data_hora_add'])),
                "status" => Ferramentas::decodificador(Ferramentas::decodificador($value['status'])),
                "ok" => $ok_desenhista
  
              ];
          }
  
        }
  
        $total_desenhos_tr = 0;
        $total_desenhos_apagados_tr = 0;
        $desenhista_tr = "";
        $desenhistas_tr = "";
        $N = 0;
        $N1 = 0;
        $desenhistas = '';
        $totalN = array_reduce($desenhista, function ($carry, $item) {
          return $carry + count($item);
        }, 0);
  
  
        foreach ($desenhista as $key => $value1) {
  
          $apagados = 0;
          $N1++;
          $total_desenhos_tr += count($value1);
  
  
          $temp_desenhista_tr = '<h3>' . $key . '</h3>
                 <table class="table tabela">
                <tr style="background-color: white;">
                  <th> Nº</th>
                  <th> Nome do arquivo </th>
                  <th> Empresa/Cliente </th>
                  <th> Empreendimento </th>
                  <th> Finalidade </th>
                  <th> Data de Envio </th>
                  <th> Status </th>
                </tr>';
          $N = 0;
          foreach ($value1 as $value) {
            if (!$value["ok"])
              continue;
  
            if ($value["status"] == "apagado"){
              $total_desenhos_apagados_tr++;
              $apagados++;
            }
  
            $temp_nome_arquivo = $value["nome_arquivo"];
            if (strpos($temp_nome_arquivo, "cortado") === 0) {
              // Remove o prefixo e os 8 caracteres seguintes
              $temp_nome_arquivo = substr($temp_nome_arquivo, 15);
            }
            $N++;
            $temp_desenhista_tr .= '
            <tr style="background-color: white;">
              <th> ' . str_pad($N, strlen(strval($totalN)), '0', STR_PAD_LEFT) . '</th>
              <th> ' . $temp_nome_arquivo . ' </th>
              <th> ' . $value["empresa"] . ' </th>
              <th> ' . $value["empreendimento"] . ' </th>
              <th> ' . $value["finalidade"] . ' </th>
              <th> ' . $value["data_hora_add"] . ' </th>
              <th> ' . str_replace(["cortado_notfile", "corte"], ["cortado", "pendente"], $value["status"]) . ' </th>
            </tr>
            ';
  
          }
          $temp_desenhista_tr .= '</table>';
          if ($N != 0) {
            $desenhista_tr .= $temp_desenhista_tr;
            $desenhistas .= '<p>' . $key . '</p>';
          }
  
          $desenhistas_tr .= '<tr style="background-color: white;">
          <th> ' . str_pad($N1, strlen(strval(count($desenhista))), '0', STR_PAD_LEFT) . '</th>
          <th> ' . $key . ' </th>
          <th> ' . count($value1) . ' </th>
          <th> ' . $apagados . ' </th>
          <th> ' . abs(count($value1)-$apagados) . ' </th>
        </tr>';
  
        }
        $qtd_desenho = $N;
  
        $total_corte_tr = 0;
        $corte_tr = "";
        $cortes_tr = "";
        $N = 0;
        $N1 = 0;
        $cortadores = '';
  
  
  
  
  
  
        foreach ($corte as $key => $value1) {
  
          $N1++;
          $totalTempoCorte = "";
          foreach ($value1 as $item) {
            if ($totalTempoCorte == "")
              $totalTempoCorte = $item["tempo_corte"];
            else
              $totalTempoCorte = $this->secondsToTime($this->timeToSeconds($item["tempo_corte"]) + $this->timeToSeconds($totalTempoCorte));
          }
          $total_corte_tr += count($value1);
          $cortes_tr .= '<tr style="background-color: white;">
              <th> ' . str_pad($N1, strlen(strval(count($corte))), '0', STR_PAD_LEFT) . '</th>
              <th> ' . $key . ' </th>
              <th> ' . count($value1) . ' </th>
              <th> ' . $totalTempoCorte . ' </th>
              <th> ' . $this->secondsToTime($this->timeToSeconds($totalTempoCorte) / count($value1)) . ' </th>
            </tr>';
          $temp_corte_tr = '<h3>' . $key . '</h3>
                    <table class="table tabela">
                    <tr style="background-color: white;">
                    <th> Nº</th>
                    <th> Desenhista </th>
                    <th> Nome do arquivo </th>
                    <th> Empresa/Cliente </th>
                    <th> Empreendimento </th>
                    <th> Finalidade </th>
                    <th> Data de Envio </th>
                    <th> Data de corte </th>
                    <th> Tempo de corte </th>
                    <th> Status </th>
                    </tr> ';
          $N = 0;
          foreach ($value1 as $value) {
            if (!$value["ok"])
              continue;
            $temp_nome_arquivo = $value["nome_arquivo"];
            if (strpos($temp_nome_arquivo, "cortado") === 0) {
              // Remove o prefixo e os 8 caracteres seguintes
              $temp_nome_arquivo = substr($temp_nome_arquivo, 15);
            }
            $N++;
            $temp_corte_tr .= '
            <tr style="background-color: white;">
              <th> ' . str_pad($N, strlen(strval(count($value1))), '0', STR_PAD_LEFT) . '</th>
              <th> ' . $value["desenhista"] . ' </th>
              <th> ' . $temp_nome_arquivo . ' </th>
              <th> ' . $value["empresa"] . ' </th>
              <th> ' . $value["empreendimento"] . ' </th>
              <th> ' . $value["finalidade"] . ' </th>
              <th> ' . $value["data_hora_add"] . ' </th>
              <th> ' . $value["data_hora_corte"] . ' </th>
              <th> ' . $value["tempo_corte"] . ' </th>
              <th> ' . str_replace(["cortado_notfile", "corte"], ["cortado", "pendente"], $value["status"]) . ' </th>
            </tr>
            
            ';
          }
          $temp_corte_tr .= '</table>';
          if ($N != 0) {
            $corte_tr .= $temp_corte_tr;
            $cortadores .= '<p>' . $key . '</p>';
          }
  
  
  
        }
        $qtd_corte = $N;
  
  
  
        $pdf = ' 
        <h1>WL Maquetaria</h1><br/>
                  <table style="width: 100%;  border: 0px; vertical-align: top;">
                  <tr>
                      <td>Relatorio do sistema de corte</td>
                      <td style="text-align: right;">Período: ' . date("d/m/Y", strtotime($dataInicial_str)) . ' a ' . date("d/m/Y", strtotime($dataFinal_str)) . '</td>
                  </tr>
                  <tr>
                      <td></td>
                      <td style="text-align: right;">Emissão: ' . date('d/m/Y H:i') . '</td>
                  </tr>
                  </table>
                  <br/><br/>
                  <table style="width: 100%;  border: 0px; vertical-align: top;">
                  <tr>
                      <td><b>Desenhistas participando</b></td>
                      <td><b>Cortados participando</b></td>
                  </tr>
                  <tr>
                      <td>' . $desenhistas . '</td>
                      <td>' . $cortadores . '</td>
                  </tr>
                  </table>
  <br/><br/>
                  <h2 >Desenhista</h2></br>
                <table class="table tabela">
                  <tr style="background-color: white;">
                  <th style="width: 7px;"> Nº</th>
                  <th style="width: 150px;"> Desenhista </th>
                  <th> Quant. de desenhos </th>
                  <th> Quant. de desenhos apagados </th>
                  <th> Total de desenhos</th>
                  </tr>
                  ' . $desenhistas_tr . '
                                  <tr>
                  <th colspan="2">Total de desenhos : ' . $total_desenhos_tr . ' </th>
  
                  <th>Total de desenhos apagados: ' . $total_desenhos_apagados_tr . ' </th>
  
                  <th colspan="2"><b>Total de desenhos adicionados: ' . abs($total_desenhos_tr - $total_desenhos_apagados_tr) . ' </b></td>
                  </tr>
                  
                </table>
                
                <h2 style="padding-top: 20px;">Cortador</h2></br>
                <table class="table tabela">
                  <tr style="background-color: white;">
                  <th> Nº</th>
                  <th> Desenhista </th>
                  <th> Quantidade de desenhos cortados </th>
                  <th> Tempo total de corte (h:m:s) </th>
                  <th> Tempo medio por corte (h:m:s)</th>
                  </tr>
                  ' . $cortes_tr . '
                  <tr>
                  <th colspan="5">Total de desenhos cortados: ' . $total_corte_tr . ' 
                  </tr>
                </table>
                  ';
  
  
        if ($desenhista_tr != "" and $relatorio == "true")
          $pdf .= '<h2 class="page-break">Desenhos enviados</h2></br></br>' . $desenhista_tr;
        if ($corte_tr != "" and $relatorio == "true")
          $pdf .= ' <h2 class="page-break">Desenhos cortados</h2></br></br>' . $corte_tr;
  
  
  
       
  
  
  
        if (($qtd_corte + $qtd_desenho) != 0) {
          $corte_porcento = intval(((100 * $qtd_corte) / ($qtd_corte + $qtd_desenho)));
          $desenhos_porcento = intval(((100 * $qtd_desenho) / ($qtd_corte + $qtd_desenho)));
        } else {
          $corte_porcento = 0;
          $desenhos_porcento = 0;
        }
        //       $pdf .= '
  // <center>
  //       <div class="grafico-circular"></div>
  //       </center>
  //       <div class="legenda">
  //         <div class="variavel1"><span></span>Cortados ' . $corte_porcento . '%</div>
  //         <div class="variavel2"><span></span>Enviados ' . $desenhos_porcento . '%</div>
  //       </div>
  
        //       ';
  
        $style = '
          <style>
  html, body {
      height: 100%;
      width: 100%;
      margin: 0;
      padding: 0;
      display: table;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
  }
  
  div.content {
      display: table-row;
      height: 100%;
  }
  
  div.list {
      display: table-footer-group;
      width: 100%;
  }
  
  .espaco {
      height: 30px;
      background-color: rgba(0,0,0,0);
  }
  
  .tabela h3 {
      color: #666;
      margin-bottom: 30px;
  }
  
  .tabela dt {
      float: left;
      animation: none;
      padding: 10px;
  }
  
  .tabela dl {
      overflow: hidden;
  }
  
  .tabela th {
      font-weight: 100;
      text-align: left;
      border: 0px solid black;
      padding: 5px;
  }
  
  .tabela td {
      text-align: center;
      border: 0px solid black;
      border-collapse: collapse;
      padding: 5px;
  }
  
  .tabela table {
      border-collapse: collapse;
      width: 100%;
  }
  
  .tabela {
      font-size: 12px;
      width: 100%;
      border-collapse: collapse;
  }
  
  .linha {
      background-color: #dddddd;
  }
  
  .caixa {
      border: 1px solid black;
      border-collapse: collapse;
      height: 60px;
      width: 100%;
      background-color: #E6E7E8;
      display: flex;
      align-items: center;
  }
  
  .caixa1 {
      height: 100%;
      width: 30px;
      background-color: #0098DA;
  }
  
  .caixa2 {
      height: 100%;
      width: 30px;
      background-color: #5CA47A;
  }
  
  .caixa3 {
      height: 100%;
      width: 30px;
      background-color: #faa952;
  }
  
  .caixa div {
      display: inline-block;
  }
  
  .text-center {
      padding: 30px;
  }
  
  #nome {
      text-align: center;
  }
  
  #nome_principal {
      text-align: center;
  }
  
  .caixa-center {
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
  }
  
  .container {
      width: 100%;
      max-width: 500px;
      font-size: 16px;
  }
  
  .lista {
      font-size: 12px;
      width: 100%;
  }
  
  .lista li {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
      font-size: 0.95em;
  }
  
  .lista li div {
      text-align: left;
  }
  
  .num {
      padding: 0px;
      width: 30px;
      text-align: center;
  }
  
  .nome {
      padding: 0px;
      height: 20px;
      flex: 1;
      background-color: #e0e0e0;
      border: 1px solid black;
      border-collapse: collapse;
  }
  
  .progress-container {
      height: 12px;
      width: 100%;
      background-color: #e0e0e0;
      display: flex;
  }
  
  .skill {
      background-color: #FAA954;
  }
  
  .tabela table {
      width: 100%;
      max-width: 100%;
  }
  
  .tabela tr, .tabela th, .tabela td {
      page-break-inside: avoid;
      word-wrap: break-word;
  }
  
  h2 {
      font-weight: bold;
      font-size: 1.5em;
      margin: 0;
      padding: 0;
      line-height: normal;
      text-transform: none;
      letter-spacing: normal;
      word-spacing: normal;
  }
  
  h1 {
      font-weight: bold;
      font-size: 2em;
      margin: 0;
      padding: 0;
      line-height: normal;
      letter-spacing: normal;
      word-spacing: normal;
  }
  
  .pontuacao {
      padding: 5px;
      width: 40px;
      text-align: right;
  }
  
  .escala {
      text-align: center;
      margin-top: 20px;
  }
  
  .caixa-lista {
      height: 90px;
      width: 100%;
      max-width: 364px;
  }
  
  .tabela table {
      border-collapse: collapse;
      width: 100%;
  }
  
  .tabela td, .tabela th {
      border-top: 1px solid black;
      border-bottom: 1px solid black;
      border-left: none;
      border-right: none;
      padding: 5px;
  }
  
  .tabela tr {
      min-height: 50px;
     
  }
  
  .page-break {
      page-break-before: always;
  }
  
  
              </style>';
  
        // Inicializa o mPDF
        $mpdf = new Mpdf();
  
        // Adiciona conteúdo ao PDF
        $mpdf->WriteHTML($style . $pdf);
  
        // Saída do PDF
        // Saída do PDF como binário
        $pdfContent = $mpdf->Output('', 'S');
  
        // Retorna a resposta JSON com o conteúdo PDF e o nome do arquivo
        $data = [
          'oi' => $relatorio,
          'ok' => true,
          'pdf' => base64_encode($pdfContent),
          'nome_pdf' => 'Relatorio Wl maquetaria ' . date("d_m_Y", strtotime($dataInicial_str)) . ' a ' . date("d_m_Y", strtotime($dataFinal_str)) . '.pdf'
        ];
  
  
  
  
        return $this->response->setJSON($data);
  
      }
    }


    function lista_desenhistas()
    {// 3
      if ($this->request->isAJAX()) {
        // Inicializa a sessão para acessar os dados da lista armazenados nela
        session_start();
  
        $usuarios = new \App\Models\Usuarios(); // Obtém a tabela de usuários do banco
        $usuarios_data = $usuarios->find();
  
        $lista = array();
        $id_temp = 0;
  
        foreach ($usuarios_data as $key => $value) {
          // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
          if ((Ferramentas::decodificador($value['tipo']) == '3' or Ferramentas::decodificador($value['tipo']) == '1')) { //verifica se é para mostrar os com estus ativo//verifica se é para mostrar os com estus ativo
            $lista['nome'][$value['status']][$id_temp] = Ferramentas::decodificador($value['nome']);
            $lista[strval($id_temp)] = $value;
  
          }
          $id_temp++;
  
        }
        $_SESSION["lista_desenhista"] = $lista;
  
        //retorna a lista para o ajax
        $data = [
          "lista" => $lista['nome']
        ];
  
        return $this->response->setJSON($data);
      }
    }

    function lista_cortadores()
    {// 3
      if ($this->request->isAJAX()) {
        // Inicializa a sessão para acessar os dados da lista armazenados nela
        session_start();
  
        $usuarios = new \App\Models\Usuarios(); // Obtém a tabela de usuários do banco
        $usuarios_data = $usuarios->find();
  
        $lista = array();
        $id_temp = 0;
  
        foreach ($usuarios_data as $key => $value) {
          // Cria a lista com base nos usuários ativos ou desativados, dependendo da solicitação
          if ((Ferramentas::decodificador($value['tipo']) == '2')
          ) { //verifica se é para mostrar os com estus ativo//verifica se é para mostrar os com estus ativo
            $lista['nome'][Ferramentas::decodificador($value['status'])][$id_temp] = Ferramentas::decodificador($value['nome']);
            $lista[strval($id_temp)] = $value;
          }
          $id_temp++;
  
        }
        $_SESSION["lista_cortador"] = $lista;
  
        //retorna a lista para o ajax
        $data = [
          "lista" => $lista['nome']
        ];
  
        return $this->response->setJSON($data);
      }
    }

}