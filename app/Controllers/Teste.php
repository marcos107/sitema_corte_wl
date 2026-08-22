<?php

namespace App\Controllers;

use App\Controllers\EmpresaPost;
use App\Controllers\FinalidadePost;
use App\Controllers\Ferramentas;
use Config\App;

class Teste extends EmpresaPost
{
//    function baseSemId(string $nome): string
// {
//     // Remove barra inicial, se existir
//     $nome = ltrim($nome, "/\\");
//     // Corrige "__dxf" -> "__.dxf"
//     $nome = preg_replace('/__dxf$/i', '__.dxf', $nome);
//     // Normaliza extensão
//     $nome = preg_replace('/_\.?dxf$/i', '_.dxf', $nome);

//     // Remove hora do padrão "__HH_MM_"
//     // Fica só: cortado_dd_mm_aaaa_RESTO
//     $nome = preg_replace('/__\d{2}_\d{2}_/i', '_', $nome);

//     // Captura tudo antes do último _<ID>_.dxf
//     if (preg_match('/^(.+)_\d+_.dxf$/i', $nome, $m)) {
//         return $m[1];
//     }

//     // Fallback: remove extensão e último _<ID>, se houver
//     $semExt = preg_replace('/\.?dxf$/i', '', $nome);
//     $semId  = preg_replace('/_\d+$/', '', $semExt);
//     return $semId;
// }

//     function verificarArquivos()
//     {
//         $Desenhos = new \App\Models\Desenhos();

//         // Busca nome e diretorio
//         $resultados = $Desenhos
//             ->select('nome, diretorio, data_add, id')
//             ->like('status', 'not')
//             ->orderBy('data_add', 'DESC')
//             ->findAll();
//         $sim = 0;
//         $nao = 0;

//         $temp = 0;
//         $minDate = null;
// $maxDate = null;
// $oi = "";
//                 foreach ($resultados as $row) {

// // Atualiza menor e maior data_add
// $dataAtual = $row['data_add'];
//   $caminhoOriginal = explode("\\", $row['diretorio'])[0] . $row['nome'];


//             // Caminho original (do banco)
          

//             // Gera o nome modificado a partir do nome original
//             // Exemplo: adicionando prefixo "cortado_" + timestamp

//             // Junta com o diretório
//             $dir = $caminhoOriginal;


//             // Verifica se existe
//             if (file_exists($dir)) {
//                 // echo "✅ Arquivo encontrado: {$dir}<br>";
//                 $sim++;
//                 // ação X
//             } else {
//              //   echo '<br>--------------------------------------------------<br>';
//                 // ====== SEGUNDA ETAPA: procurar por MESMA BASE (ID diferente) ======

//                 // 1) Base do nome do banco (tira o último _<ID> e normaliza)
//                 $nome_base = $this->baseSemId($row['nome']);

//                 // 2) Pasta do arquivo (mantendo sua lógica; se preferir, use rtrim($row['diretorio'], "/\\"))
//                 $pasta = rtrim(explode("\\", $row['diretorio'])[0], "/\\");
//                 if (!is_dir($pasta)) {
//                     $nao++;
//                     // opcional: echo "⚠️ Pasta inválida: {$pasta}<br>";
//                     continue;
//                 }

//                 // 3) Lista todos os arquivos e compara as bases
//                 // echo $pasta.'<br><br>';
//                 $lista = scandir($pasta);
//                 $encontradosMesmaBase = [];
//                 $test = [];
//                 foreach ($lista as $arq) {
//                     if ($arq === '.' || $arq === '..')
//                         continue;

//                     $base_arq = $this->baseSemId($arq);
//                   //  echo $base_arq.'<br>';
//                     if (strcasecmp($base_arq, $nome_base) === 0) {
//                         // Encontrou pelo menos um arquivo com a mesma base (ID pode ser diferente)
//                         $encontradosMesmaBase[] = $pasta . DIRECTORY_SEPARATOR . $arq;
//                     }
//                 }

//                 if (!empty($encontradosMesmaBase)) {
//                     // Considera "achado" (coexistência: mesma base, possivelmente outro ID)
//                     // Liste se quiser ver quais são:
//                     // foreach ($encontradosMesmaBase as $p) { echo "⚠️ Mesma base: {$p}<br>"; }
//                     $sim++;
//                 } else {
//                     $nao++;
// if ($minDate === null || $dataAtual < $minDate) {
//     $minDate = $dataAtual;
// }
// if ($maxDate === null || $dataAtual > $maxDate) {
//     $oi =  $row['id'];
//     $maxDate = $dataAtual;
// }
//                  //    echo '<br>G--------<br>'.$nome_base;
//                  //     echo '<br>'.$row['nome'];

//                      // echo '<br>'.$row['diretorio'].'<br>';
//                       $temp++;
//   //                    if($temp == 3)
// //return;
//                 }
//                // echo '<br>-----------------------------------<br>';
//             }
            
//         }
//         echo "✅ {$sim}  ❌{$nao} --- {$oi}<br>";
//         echo "📅 Menor data: {$minDate} <br>";
// echo "📅 Maior data: {$maxDate} <br>";
//     }


/**
 * Normaliza e devolve a "base" do nome (sem hora e sem o último ID).
 * Remove "__HH_MM_" após a data e corrige "__dxf" -> "__.dxf".
 * Ex.: cortado_16_09_2025__09_09_ACABAMENTO_..._121_8727_dxf
 *   -> base: cortado_16_09_2025_ACABAMENTO_..._121
 */

/** Remove hora (__HH_MM_), corrige "__dxf" e remove último _ID antes da extensão. */
function baseSemId(string $nome): string
{
    $nome = ltrim($nome, "/\\");
    $nome = preg_replace('/__dxf$/i', '__.dxf', $nome);  // "__dxf" -> "__.dxf"
    $nome = preg_replace('/_\.?dxf$/i', '_.dxf', $nome); // força "_.dxf"
    $nome = preg_replace('/__\d{2}_\d{2}_/i', '_', $nome); // remove hora "__HH_MM_"

    if (preg_match('/^(.+)_\d+_.dxf$/i', $nome, $m)) {
        return $m[1];
    }
    $semExt = preg_replace('/\.?dxf$/i', '', $nome);
    $semId  = preg_replace('/_\d+$/', '', $semExt);
    return $semId;
}

/** Remove acentos. */
function stripAccents(string $s): string
{
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    return $t !== false ? $t : $s;
}

/** Normaliza para comparação: minúsculas, sem acento, só [a-z0-9] e espaço. */
function normalizeForCompare(string $s): string
{
    $s = mb_strtolower($this->stripAccents($s), 'UTF-8');
    $s = preg_replace('/[^a-z0-9]+/i', ' ', $s);
    $s = preg_replace('/\s+/', ' ', trim($s));
    return $s ?? '';
}

/** Extrai tokens (palavras) relevantes. */
function tokensFrom(string $s): array
{
    $s = $this->normalizeForCompare($s);
    if ($s === '') return [];
    $toks = preg_split('/\s+/', $s);
    // opcional: remover stopwords específicas do seu domínio
    // $stop = ['cortado', 'loja', 'mdf']; // exemplo, se quiser
    // $toks = array_values(array_diff($toks, $stop));
    return $toks;
}

/** Extrai apenas números “semânticos” (mantém 1mm como 1, 121, 869, etc.). */
function numericTokens(string $s): array
{
    $s = $this->normalizeForCompare($s);
    preg_match_all('/\d+/', $s, $m);
    return $m[0] ?? [];
}

/** Gera n-gramas de tamanho $n (default 3). */
function ngrams(string $s, int $n = 3): array
{
    $s = ' ' . $this->normalizeForCompare($s) . ' ';
    $L = strlen($s);
    if ($L < $n) return $L ? [$s] : [];
    $out = [];
    for ($i = 0; $i <= $L - $n; $i++) {
        $out[] = substr($s, $i, $n);
    }
    return $out;
}

/** Dice coefficient entre duas strings, via n-gramas (3). */
function diceSimilarity(string $a, string $b): float
{
    $A = $this->ngrams($a, 3);
    $B = $this->ngrams($b, 3);
    if (!$A || !$B) return 0.0;

    $cntA = array_count_values($A);
    $cntB = array_count_values($B);
    $inter = 0;
    foreach ($cntA as $k => $va) {
        if (isset($cntB[$k])) {
            $inter += min($va, $cntB[$k]);
        }
    }
    $tot = array_sum($cntA) + array_sum($cntB);
    return $tot > 0 ? (2.0 * $inter) / $tot : 0.0; // [0,1]
}

/** Jaccard de conjunto de tokens (ordem não importa). */
function jaccardTokens(array $t1, array $t2): float
{
    if (!$t1 || !$t2) return 0.0;
    $s1 = array_unique($t1);
    $s2 = array_unique($t2);
    $inter = array_intersect($s1, $s2);
    $union = array_unique(array_merge($s1, $s2));
    $a = count($union);
    return $a ? (count($inter) / $a) : 0.0; // [0,1]
}

/** Overlap de números (prioriza coincidência de números como 1, 121, 869…). */
function numericOverlap(string $a, string $b): float
{
    $na = array_unique($this->numericTokens($a));
    $nb = array_unique($this->numericTokens($b));
    if (!$na || !$nb) return 0.0;
    $inter = array_intersect($na, $nb);
    // normaliza pelo maior conjunto (mais conservador)
    $den = max(count($na), count($nb));
    return $den ? (count($inter) / $den) : 0.0; // [0,1]
}

/** Bônus de prefixo: quanto do início coincide (em tokens). */
function prefixBonus(string $a, string $b): float
{
    $t1 = $this->tokensFrom($a);
    $t2 = $this->tokensFrom($b);
    if (!$t1 || !$t2) return 0.0;
    $min = min(count($t1), count($t2));
    $eq = 0;
    for ($i = 0; $i < $min; $i++) {
        if ($t1[$i] === $t2[$i]) $eq++;
        else break;
    }
    // peso pequeno, retorna fração [0,0.2] no score final
    return $min ? ($eq / $min) : 0.0; // [0,1], aplicaremos peso pequeno
}

/**
 * Score híbrido 0–100 para comparar nomes:
 * - Dice (3-gram)         35%
 * - Jaccard (tokens)      25%
 * - Sobreposição numérica 25%
 * - Bônus prefixo         15% (peso aplicado sobre [0,1])
 */
function hybridSimilarityScore(string $a, string $b): float
{
    $dice = $this->diceSimilarity($a, $b);               // [0,1]
    $jac  = $this->jaccardTokens($this->tokensFrom($a), $this->tokensFrom($b)); // [0,1]
    $num  = $this->numericOverlap($a, $b);               // [0,1]
    $pre  = $this->prefixBonus($a, $b);                  // [0,1]

    $score01 = 0.35*$dice + 0.25*$jac + 0.25*$num + 0.15*$pre;
    return round($score01 * 100.0, 2);
}

/**
 * Para cada desenho (nome/diretorio):
 *   - calcula a melhor similaridade (score híbrido) com arquivos .dxf da mesma pasta;
 *   - agrega por quantidade usando um limiar (ex.: 80%).
 * Mostra resumo e gráfico de barras (quantidade).
 */
function verificarPorSimilaridadeQuantidadeMelhor(array $resultados, float $limiarOk = 80.0): void
{
    $total = count($resultados);
    $qOk = 0;
    $qNok = 0;
    $detalhes = [];

    foreach ($resultados as $row) {
        $nome = (string)($row['nome'] ?? '');
        $dirRaw = (string)($row['diretorio'] ?? '');
        $pasta = explode('\\',$dirRaw)[0];

        $alvoBase = $this->baseSemId($nome);
        $bestPct = 0.0;
        $bestFile = '';

        if (is_dir($pasta)) {
            $lista = scandir($pasta);
            foreach ($lista as $arq) {
                if ($arq === '.' || $arq === '..') continue;
                if (!preg_match('/\.?dxf$/i', $arq)) continue;

                $baseArq = $this->baseSemId($arq);
                $pct = $this->hybridSimilarityScore($alvoBase, $baseArq);

                if ($pct > $bestPct) {
                    $bestPct = $pct;
                    $bestFile = $arq;
                }
            }
        } else {
            $bestFile = '(pasta inválida)';
            $bestPct = 0.0;
        }

        if ($bestPct >= $limiarOk) $qOk++;
        else                       $qNok++;

        $detalhes[] = [
            'alvo' => $nome,
            'pasta'=> $pasta,
            'best' => $bestFile ? ($pasta . DIRECTORY_SEPARATOR . $bestFile) : '(nenhum .dxf)',
            'pct'  => $bestPct,
        ];
    }

    // ------- Saída visual (quantidade + amostras) -------
    $percOk  = $total ? round(($qOk/$total)*100, 2) : 0.0;
    $percNok = $total ? round(($qNok/$total)*100, 2) : 0.0;

    echo '<style>
    .qty-wrap{font-family:system-ui,Segoe UI,Arial,sans-serif;max-width:860px}
    .cards{display:flex;gap:12px;margin:12px 0}
    .card{border:1px solid #ddd;border-radius:8px;padding:10px 14px;background:#fff;flex:1}
    .card h4{margin:0 0 6px 0;font-size:15px;color:#333}
    .big{font-size:24px;font-weight:700}
    .muted{color:#666;font-size:12px}
    .bar{height:22px;background:#eee;border-radius:6px;overflow:hidden}
    .bar>span{display:block;height:100%}
    .ok>span{background:#4caf50}
    .nok>span{background:#e53935}
    table.sim{border-collapse:collapse;width:100%;margin-top:14px}
    table.sim th,table.sim td{border:1px solid #ddd;padding:6px 8px;font-size:13px}
    table.sim th{background:#f7f7f7;text-align:left}
    </style>';

    echo '<div class="qty-wrap">';
    echo '<h3>Similaridade por quantidade (limiar: '.$limiarOk.'%)</h3>';

    echo '<div class="cards">';
    echo   '<div class="card"><h4>Total</h4><div class="big">'.$total.'</div></div>';
    echo   '<div class="card"><h4>Encontrados</h4><div class="big">'.$qOk.'</div><div class="muted">'.$percOk.'%</div></div>';
    echo   '<div class="card"><h4>Não encontrados</h4><div class="big">'.$qNok.'</div><div class="muted">'.$percNok.'%</div></div>';
    echo '</div>';

    echo '<h4>Gráfico de quantidade</h4>';
    echo '<div class="card">';
    echo   '<div style="display:grid;grid-template-columns:140px 1fr;gap:10px;align-items:center">';
    echo     '<div class="muted">Encontrados</div>';
    echo     '<div class="bar ok"><span style="width:'.$percOk.'%"></span></div>';
    echo     '<div class="muted">Não encontrados</div>';
    echo     '<div class="bar nok"><span style="width:'.$percNok.'%"></span></div>';
    echo   '</div>';
    echo   '<div class="muted" style="margin-top:6px">* Percentual sobre '.$total.' desenhos</div>';
    echo '</div>';

    echo '<h4 style="margin-top:16px;">Amostras (melhor similaridade por desenho)</h4>';
    echo '<table class="sim"><tr><th>#</th><th>Desenho (banco)</th><th>Pasta</th><th>Melhor match</th><th>Score</th></tr>';
    $i=1;
    foreach ($detalhes as $d) {
        echo '<tr>';
        echo '<td>'.($i++).'</td>';
        echo '<td>'.htmlspecialchars($d['alvo']).'</td>';
        echo '<td>'.htmlspecialchars($d['pasta']).'</td>';
        echo '<td>'.htmlspecialchars($d['best']).'</td>';
        echo '<td>'.number_format($d['pct'], 2, ',', '.').'%</td>';
        echo '</tr>';
    }
    echo '</table>';

    echo '</div>';
}


 function verificarArquivos()
{

$Desenhos = new \App\Models\Desenhos();
$resultados = $Desenhos
    ->select('nome, diretorio')
    ->like('status','not')
    ->orderBy('data_add','DESC')
    ->findAll();

// Limiar mais “rigoroso” (ex.: 80%)
$this->verificarPorSimilaridadeQuantidadeMelhor($resultados, 10.0);



}

}