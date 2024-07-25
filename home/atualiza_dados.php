<?php
// Crie arrays com os dados do gráfico
include('../db/db_comandos.php');
$envi = select('mensages', 'date','id_user!=\'webhook\' and id_user!=\'menu\'');
$rece= select('mensages', 'date','id_user=\'webhook\' or id_user=\'menu\'');
$mr1 = 0;
$mr2 = 0;
$mr3 = 0;
$mr4 = 0;
$mr5 = 0;
$mr6 = 0;
$mr7 = 0;
$mr8 = 0;
$mr9 = 0;
$mr10 = 0;
$mr11 = 0;
$mr12 = 0;

$me1 = 0;
$me2 = 0;
$me3 = 0;
$me4 = 0;
$me5 = 0;
$me6 = 0;
$me7 = 0;
$me8 = 0;
$me9 = 0;
$me10 = 0;
$me11 = 0;
$me12 = 0;
foreach ($envi as $key => $value) {
  switch (substr($value['data'], 5, 2)) {

    case '01':
      $me1++;
      break;
    case '02':
      $me2++;
      break;
    case '03':
      $me3++;
      break;
    case '04':
      $me4++;
      break;
    case '05':
      $me5++;
      break;
    case '06':
      $me6++;
      break;
    case '07':
      $me7++;
      break;
    case '08':
      $me8++;
      break;
    case '10':
      $me10++;
      break;
    case '11':
      $me11++;
      break;
    case '12':
      $me12++;
      break;
  }

}
foreach ($rece as $key => $value) {
  switch (substr($value['data'], 5, 2)) {

    case '01':
      $mr1++;
      break;
    case '02':
      $mr2++;
      break;
    case '03':
      $mr3++;
      break;
    case '04':
      $mr4++;
      break;
    case '05':
      $mr5++;
      break;
    case '06':
      $mr6++;
      break;
    case '07':
      $mr7++;
      break;
    case '08':
      $mr8++;
      break;
    case '10':
      $mr10++;
      break;
    case '11':
      $mr11++;
      break;
    case '12':
      $mr12++;
      break;
  }

}
$recebidas = array($me1, $me2,$me3, $me4, $me5, $me6, $me7, $me8, $me9, $me10, $me11, $me12);

$enviadas = array($mr1, $mr2,$mr3, $mr4, $mr5, $mr6, $mr7, $mr8, $mr9, $mr10, $mr11, $mr12);

// Simula a busca de novos dados aleatórios
$newData = array(
  "data" => $enviadas,
  "data1" => $recebidas

);

// Converte o array para formato JSON
echo json_encode($newData);
?>