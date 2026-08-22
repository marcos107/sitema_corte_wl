<?php

namespace App\Controllers;



use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class Ferramentas extends BaseController
{
    /**
     * Normaliza um caminho de diretório/arquivo:
     * - Substitui / e \ pelo DIRECTORY_SEPARATOR
     * - Remove duplicações de separadores
     * - Remove espaços em branco no início/fim
     * - Remove barra final (a menos que seja a raiz da unidade)
     *
     * @param string $rawPath  Caminho de entrada (podendo estar “quebrado”)
     * @return string          Caminho normalizado
     */
    public static function normalizePath(string $rawPath): string
    {
        // 1) trim
        $path = trim($rawPath);

        // 2) substitui / e \ por DIRECTORY_SEPARATOR
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // 3) colapsa repetição de separadores
        $sep = preg_quote(DIRECTORY_SEPARATOR, '#');
        $path = preg_replace("#{$sep}+#", DIRECTORY_SEPARATOR, $path);

        // 4) uppercase na unidade Windows (ex: c: -> C:)
        $path = preg_replace_callback(
            '/^([a-z]):/i',
            function ($m) {
                return strtoupper($m[1]) . ':';
            },
            $path
        );

        // 5) remove barra final, exceto em "C:\" (raiz)
        if (!preg_match('/^[A-Z]:' . preg_quote(DIRECTORY_SEPARATOR, '/') . '$/i', $path)) {
            $path = rtrim($path, DIRECTORY_SEPARATOR);
        }
        $path = str_replace(['\\'], ['/'], $path);

        return $path;
    }

    public static function wlStorageRoot(): string
    {
        $configurado = trim((string) (getenv('WL_STORAGE_ROOT') ?: ''));
        if ($configurado === '') {
            $configurado = PHP_OS_FAMILY === 'Windows' ? 'C:/wl' : '/srv/wl';
        }

        return rtrim(self::normalizePath($configurado), '/');
    }

    public static function wlStorageRelativePath(string $path): string
    {
        $normalizado = str_replace('\\', '/', trim($path));
        if ($normalizado === '') {
            return '';
        }

        $prefixos = [
            rtrim(str_replace('\\', '/', self::wlStorageRoot()), '/') . '/',
            'C:/wl/',
            'c:/wl/',
            'Z:/wl/',
            'z:/wl/',
            '/srv/wl/',
        ];

        $nasConfigurado = trim((string) (getenv('WL_NAS_SHARE') ?: ''));
        if ($nasConfigurado !== '') {
            array_unshift($prefixos, rtrim(str_replace('\\', '/', $nasConfigurado), '/') . '/');
        }

        foreach ($prefixos as $prefixo) {
            if (stripos($normalizado, $prefixo) === 0) {
                return ltrim(substr($normalizado, strlen($prefixo)), '/');
            }
        }

        return ltrim($normalizado, '/');
    }

    public static function wlStoragePath(string $path = ''): string
    {
        $root = self::wlStorageRoot();
        $relativo = self::wlStorageRelativePath($path);

        return $relativo === ''
            ? $root
            : $root . '/' . $relativo;
    }

    public static function wlNasPath(string $path): string
    {
        $share = trim((string) (getenv('WL_NAS_SHARE') ?: ''));
        if ($share === '') {
            $share = 'Z:/wl';
        }

        $relativo = self::wlStorageRelativePath($path);
        $usaBarrasInvertidas = str_contains($share, '\\');
        $separador = $usaBarrasInvertidas ? '\\' : '/';
        $share = rtrim(str_replace(['/', '\\'], $separador, $share), $separador);
        $relativo = str_replace(['/', '\\'], $separador, $relativo);

        return $relativo === '' ? $share : $share . $separador . $relativo;
    }


    /**
     * Normaliza uma string substituindo caracteres especiais, removendo símbolos não alfanuméricos,
     * convertendo para maiúsculas e substituindo espaços por underscores.
     *
     * @param string $str A string a ser normalizada.
     * @param array $trocar Um array de caracteres a serem substituídos na string original.
     * @param array $por Um array de caracteres pelos quais os valores de $trocar serão substituídos.
     * @return string A string normalizada com caracteres especiais removidos e espaços substituídos por underscores.
     */
    public static function norma_lizar_str($str, $trocar = [":", "_", ",", "."], $por = [" ", " ", " ", " "])
    {
        $str = str_replace($trocar, $por, $str);
        // Converte caracteres especiais para suas versões simples usando iconv
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);

        // Remove tudo que não seja letra, número ou espaço
        $str = preg_replace('/[^A-Za-z0-9 ]/', '', $str);
        $str = str_replace(' ', '_', $str);
        $str = preg_replace('/_+/', '_', $str);
        // Converte a string para maiúsculas
        $str = strtoupper($str);
        return $str;
    }


    /**
     * Gera um modal HTML dinâmico com base nos parâmetros fornecidos.
     * 
     * @param string $titulo O título a ser exibido no cabeçalho do modal.
     * @param string $conteudo O conteúdo HTML a ser exibido no corpo do modal.
     * @param string $modal_tamanho (Opcional) O tamanho da janela modal, baseado em classes CSS (ex: 'modal-lg' para um modal grande).
     * @param string $funcao_acionar (Opcional) A função JavaScript a ser chamada quando o botão de confirmação for clicado. O padrão é "confirmarModal()".
     * @param string $botao_confirmar (Opcional) O texto a ser exibido no botão de confirmação. O padrão é "Confirmar".
     * @param string $botao_cancelar (Opcional) O texto a ser exibido no botão de cancelamento. O padrão é "Cancelar".
     * 
     * @return string Retorna o HTML do modal pronto para ser renderizado.
     */
    function modal($titulo, $contetudo, $modal_tamanho = "", $funcao_acionar = "confirmarModal()", $botao_confirmar = "Confirmar", $botao_cancelar = "Cancelar")
    {
        $modal = '<div id="modal" class="modal-1" style="display: block;">
         <div class="modal-dialog ' . $modal_tamanho . '" role="document">
           <div class="modal-content">
             <div class="modal-header">
               <h5 class="modal-title" id="modal_titulo">' . $titulo . '</h5>
               <button type="button" class="close" onclick="fecharModal()">
                 <span aria-hidden="true">×</span>
               </button>
             </div>
             <div class="modal-body" id="modal_bory"><div class="form-group">
         ' . $contetudo . '
     
     
             <div class="modal-footer">
               <button type="button" class="btn btn-secondary" id="botao_fechar_modal" onclick="fecharModal()">' . $botao_cancelar . '</button>
               <button type="button" class="btn btn-primary" id="botao_confirmar_modal" onclick="' . $funcao_acionar . '">' . $botao_confirmar . '</button>
             </div></div></div>
           </div>
         </div>
       </div>';
        return $modal;
    }


    /**
     * Seleciona registros de uma tabela do banco de dados com base em critérios de pesquisa.
     *
     * @param array $array Um array contendo os registros da tabela.
     * @param string $pesquisa O campo usado como critério de pesquisa.
     * @param string $valor O valor a ser buscado no campo de pesquisa.
     * @return array Um array com os registros encontrados que correspondem aos critérios de pesquisa.
     */

    public static function array_pesquisa($array = '', $pesquisa = '', $valor = '')
    {
        if (!($array == '' || $pesquisa == '' || $valor == '')) {
            if (gettype($array) == "array") {
                foreach ($array as $key => $value) {
                    if (array_key_exists($pesquisa, $value)) {
                        $resposta = $value[$pesquisa];
                        if ($valor == $resposta) {
                            return $value;
                        }
                    }
                }
            }
        }
        return [];
    }
    /**
     * Remove o ID do nome do arquivo, se presente.
     *
     * Exemplo: Para a entrada da string 'mesa_6121_.dxf', o retorno padrão é 'mesa.dxf'.
     *
     * @param string $str A string com o nome do arquivo que pode conter um ID.
     * @return string A string com o nome do arquivo sem o ID. Se nenhum ID for encontrado, a string original é retornada.
     */
    public static function remove_id_file($str)
    {
        // Pega os 10 últimos caracteres da string.
        $lastTenChars = substr($str, -10);

        // Usa uma expressão regular para procurar um ID no formato '_<números>_' nos 10 últimos caracteres.
        preg_match('/_([0-9]+)_/', $lastTenChars, $matches);

        if (isset($matches[0])) {
            $id = $matches[0];
            // Remove o ID encontrado da string original (não só dos últimos 10 caracteres) e retorna o resultado.
            return str_replace($id, '', $str);
        } else {
            // Se nenhum ID for encontrado nos 10 últimos caracteres, retorna a string original.
            return $str;
        }
    }
    /**
     * Pesquisa em um array multidimensional e retorna todos os itens que correspondem
     * aos critérios especificados.
     *
     * @param array $array Array multidimensional onde a pesquisa será realizada.
     * @param array $pesquisa Array de chaves para pesquisar no array principal.
     * @param array $valor Array de valores correspondentes às chaves em $pesquisa.
     *
     * @return array Retorna um array de itens que correspondem aos critérios de pesquisa.
     *               Retorna um array vazio se não encontrar correspondências.
     */
    public static function array_pesquisa_mult_all($array = '', $pesquisa = '', $valor = '')
    {
        // Inicializa um array para armazenar os resultados encontrados
        $resultados = [];

        // Verifica se os parâmetros fornecidos são válidos e não vazios
        if (!($array == '' || $pesquisa == '' || $valor == '')) {
            // Confirma se os parâmetros são do tipo array
            if (gettype($array) == "array" && gettype($pesquisa) == "array" && gettype($valor) == "array") {

                // Percorre cada elemento do array fornecido
                foreach ($array as $key => $value) {
                    // Flag para verificar se o item atual corresponde à pesquisa
                    $match = true;

                    // Percorre cada chave e valor nos arrays de pesquisa e valor
                    foreach ($pesquisa as $key1 => $value1) {
                        // Verifica se a chave existe no item atual e se o valor corresponde
                        if (!array_key_exists($value1, $value) || $valor[$key1] != $value[$value1]) {
                            // Se não corresponder, define a flag como false e interrompe o loop interno
                            $match = false;
                            break;
                        }
                    }

                    // Se o item atual corresponde a todos os critérios de pesquisa
                    if ($match) {
                        // Adiciona o item atual ao array de resultados
                        $resultados[] = $value;
                    }
                }
            }
        }

        // Retorna o array de resultados (pode estar vazio se não houver correspondências)
        return $resultados;
    }

    /**
     * Busca múltiplos registros de um banco de dados com base em critérios de pesquisa.
     *
     * @param object $model Instância do modelo do banco de dados (exemplo: \App\Models\Tag).
     * @param array $campos Um array com os nomes dos campos a serem pesquisados.
     * @param array $valores Um array com os valores correspondentes a serem buscados nos campos.
     * @return array Um array com os registros encontrados que atendem aos critérios de pesquisa ou um array vazio se nenhum registro for encontrado.
     */
    public static function buscarRegistrosPorCriterios($model, array $campos, array $valores): array
    {
        // Verifica se o modelo, os campos e os valores foram fornecidos corretamente
        if ($model && !empty($campos) && !empty($valores) && count($campos) === count($valores)) {
            // Inicia o construtor de consultas
            $builder = $model->builder();

            // Adiciona as condições de pesquisa
            foreach ($campos as $index => $campo) {
                if (isset($valores[$index])) {
                    $builder->where($campo, $valores[$index]);
                }
            }

            // Executa a consulta e retorna os resultados como array
            $query = $builder->get();
            return $query->getResultArray();
        }

        // Retorna um array vazio caso os parâmetros sejam inválidos
        return [];
    }



    /**
     * Seleciona registros de uma tabela do banco de dados com base em critérios de pesquisa.
     *
     * @param array $array Um array contendo os registros da tabela.
     * @param array $pesquisa O campo usado como critério de pesquisa.
     * @param array $valor O valor a ser buscado no campo de pesquisa.
     * @return array Um array com os registros encontrados que correspondem aos critérios de pesquisa.
     */

    public static function array_pesquisa_mult($array = '', $pesquisa = '', $valor = '')
    {

        if (!($array == '' || $pesquisa == '' || $valor == '')) {
            if (gettype($array) == "array" && gettype($pesquisa) == "array" && gettype($valor) == "array") {
                $i = 0;

                foreach ($array as $key => $value) {

                    foreach ($pesquisa as $key1 => $value1) {
                        if (array_key_exists($value1, $value)) {
                            $resposta = $value[$value1];
                            if ($valor[$key1] == $resposta) {
                                $i++;
                            } else {
                                $i = 0;

                                break;
                            }
                        } else {
                            $i = 0;

                            break;
                        }
                    }
                    if ($i == count($pesquisa)) {

                        return $value;
                    }
                }
            }
        }
        return [];
    }
    /**
     * Obtém um valor de um array multidimensional com base em um índice fornecido.
     *
     * @param array $array O array multidimensional de onde obter o valor.
     * @param array $index O índice que representa o caminho para o valor desejado.
     * @return mixed O valor encontrado no caminho do índice fornecido. Se não for encontrado, retorna uma string vazia.
     */

    public static function array_index($array, array $index)
    {
        if (gettype($array) == "array") {
            for ($i = 0; $i < count($index); $i++) {

                if (array_key_exists($index[$i], $array)) {
                    $array = $array[$index[$i]];
                } else {
                    return "";
                }
            }
        } else {
            return "";
        }


        return $array;
    }

    /**
     * Codifica uma string substituindo caracteres especiais por códigos.
     *  Á , á , É , é , Í , í , Ó , ó , Ú , ú , À , à , È , è , Ì , ì , Ò , ò , Ù , ù ,   , â , Ê , ê ,
     *  Î , î , Ô , ô , Û , û , Ã , ã , Ñ , ñ , Õ , õ , Ç , ç , Ä , ä , Ë , ë , Ï , ï , Ö , ö , Ü , ü ,
     *  Ÿ , ÿ , À , à , È , è , Ì , ì , Ò , ò , Ù , ù , / , . , , , % , $ , # , ! , @ , & , * , ( , ) ,
     *  - , _ , + , = , { , } , [ , ] , | , \ , : , ; , " ," ", < , > , ? , ~ , ^ , ` , ´
     *
     * A função substitui caracteres especiais, como acentos, símbolos e caracteres especiais,
     * por códigos no formato "i001n", "i002n", "i003n" e assim por diante.
     *
     * @param string $str A string a ser codificada.
     * @return string A string codificada, com caracteres especiais substituídos pelos códigos correspondentes.
     *               Se a string de entrada contiver caracteres que não estão no alfabeto de números e espaços,
     *               a função retorna uma string vazia.
     */
    public static function codificador($str)
    {
        // Define um array com os caracteres especiais correspondentes aos códigos.
        $caracteresEspeciais = [
            'Á',
            'á',
            'É',
            'é',
            'Í',
            'í',
            'Ó',
            'ó',
            'Ú',
            'ú',
            'À',
            'à',
            'È',
            'è',
            'Ì',
            'ì',
            'Ò',
            'ò',
            'Ù',
            'ù',
            'Â',
            'â',
            'Ê',
            'ê',
            'Î',
            'î',
            'Ô',
            'ô',
            'Û',
            'û',
            'Ã',
            'ã',
            'Ñ',
            'ñ',
            'Õ',
            'õ',
            'Ç',
            'ç',
            'Ä',
            'ä',
            'Ë',
            'ë',
            'Ï',
            'ï',
            'Ö',
            'ö',
            'Ü',
            'ü',
            'Ÿ',
            'ÿ',
            'À',
            'à',
            'È',
            'è',
            'Ì',
            'ì',
            'Ò',
            'ò',
            'Ù',
            'ù',
            '/',
            '.',
            ',',
            '%',
            '$',
            '#',
            '!',
            '@',
            '&',
            '*',
            '(',
            ')',
            '-',
            '_',
            '+',
            '=',
            '{',
            '}',
            '[',
            ']',
            '|',
            '\\',
            ':',
            ';',
            '"',
            "'",
            '<',
            '>',
            '?',
            '~',
            '^',
            '`',
            '´'
        ];

        // Define um alfabeto de números e espaços.
        $alfabetoNumeros = [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            ' '
        ];

        // Gera um array com os códigos a serem usados.
        $codigos = array();
        for ($i = 1; $i <= count($caracteresEspeciais); $i++) {
            $numero = str_pad($i, 3, '0', STR_PAD_LEFT);
            $codigos[] = "i{$numero}n";
        }

        // Substitui os caracteres especiais pelos códigos na string.
        $str = str_replace($caracteresEspeciais, $codigos, $str);

        // Verifica se a string contém caracteres que não estão no alfabeto de números e espaços.
        foreach (str_split($str) as $key => $value) {
            if (!in_array($value, $alfabetoNumeros)) {
                return '';
            }
        }

        return $str;
    }


    /**
     * Decodifica uma string substituindo códigos por caracteres especiais.
     *
     * A função substitui códigos no formato "i001n", "i002n", "i003n" e assim por diante
     * por caracteres especiais correspondentes, como acentos, símbolos e caracteres especiais.
     *
     * @param string $str A string codificada que contém os códigos a serem decodificados.
     * @return string A string decodificada, com os códigos substituídos pelos caracteres especiais correspondentes.
     */
    public static function decodificador($str, $ignora = [])
    {
        // Define um array com os caracteres especiais correspondentes aos códigos.
        $caracteresEspeciais = [
            'Á',
            'á',
            'É',
            'é',
            'Í',
            'í',
            'Ó',
            'ó',
            'Ú',
            'ú',
            'À',
            'à',
            'È',
            'è',
            'Ì',
            'ì',
            'Ò',
            'ò',
            'Ù',
            'ù',
            'Â',
            'â',
            'Ê',
            'ê',
            'Î',
            'î',
            'Ô',
            'ô',
            'Û',
            'û',
            'Ã',
            'ã',
            'Ñ',
            'ñ',
            'Õ',
            'õ',
            'Ç',
            'ç',
            'Ä',
            'ä',
            'Ë',
            'ë',
            'Ï',
            'ï',
            'Ö',
            'ö',
            'Ü',
            'ü',
            'Ÿ',
            'ÿ',
            'À',
            'à',
            'È',
            'è',
            'Ì',
            'ì',
            'Ò',
            'ò',
            'Ù',
            'ù',
            '/',
            '.',
            ',',
            '%',
            '$',
            '#',
            '!',
            '@',
            '&',
            '*',
            '(',
            ')',
            '-',
            '_',
            '+',
            '=',
            '{',
            '}',
            '[',
            ']',
            '|',
            '\\',
            ':',
            ';',
            '"',
            "'",
            '<',
            '>',
            '?',
            '~',
            '^',
            '`',
            '´'
        ];

        // Gera um array com os códigos a serem substituídos.
        $codigos = array();
        for ($i = 1; $i <= count($caracteresEspeciais); $i++) {
            $numero = str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!in_array("i{$numero}n", self::array_codificar($ignora)))
                $codigos[] = "i{$numero}n";
            else
                $codigos[] = "i999n";
        }
        // Substitui os códigos pelos caracteres especiais na string.
        $str = str_replace($codigos, $caracteresEspeciais, $str);
        // Gera um array com os códigos a serem substituídos.
        $codigos = array();
        for ($i = 1; $i <= count($caracteresEspeciais); $i++) {
            $numero = str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!in_array("i{$numero}n", self::array_codificar($ignora)))
                $codigos[] = "I{$numero}N";
            else
                $codigos[] = "i999n";
        }
        // Substitui os códigos pelos caracteres especiais na string.
        $str = str_replace($codigos, $caracteresEspeciais, $str);


        return $str;
    }




    /**
     * Mapeia todas as subpastas e arquivos existentes em uma pasta dada.
     *
     * @param string $pasta A pasta de partida a ser mapeada.
     * @param array $p (opcional) Um array que armazena os caminhos dos subdiretórios e arquivos encontrados.
     * @return array Um array contendo todos os caminhos para as subpastas e arquivos encontrados.
     */
    public static function map_pasta($pasta, $p = array())
    {
        if (file_exists($pasta)) {
            $diretorio = dir($pasta);

            while ($arquivo = $diretorio->read()) {
                if ($arquivo != '.' && $arquivo != '..') {
                    if (is_dir($pasta . $arquivo)) {
                        // Se o item é uma subpasta, chama a função recursivamente para mapear os itens dentro dela.
                        $p = array_merge($p, self::map_pasta($pasta . $arquivo . '/', $p));
                    } else {
                        // Se o item é um arquivo, adiciona o caminho completo ao array.
                        array_push($p, ($pasta . $arquivo));
                    }
                }
            }

            $diretorio->close();
        }

        // Remove duplicatas no array de caminhos e retorna o resultado.
        return array_unique($p);
    }


    /**
     * Pega o tipo de arquivo a partir de uma string que contém o nome do arquivo.
     *
     * Exemplo: Para a entrada da string 'mesa_6121_.dxf', o retorno padrão é 'dxf'.
     *
     * @param string $inputString Uma string contendo o nome do arquivo.
     * @return string O tipo do arquivo (extensão) contido na string, ou uma string vazia se nenhuma extensão for encontrada.
     */
    public static function get_type_file(string $inputString = '')
    {
        // Encontra a posição do último ponto na string.
        $lastDotPosition = strrpos($inputString, '.');

        if ($lastDotPosition !== false) {
            // Obtém o texto após o último ponto na string, que representa a extensão do arquivo.
            $reductionText = substr($inputString, $lastDotPosition + 1);
            return $reductionText;
        } else {
            // Caso não haja ponto na string, retorna uma string vazia, indicando que nenhuma extensão foi encontrada.
            return "";
        }
    }

    /**
     * Pega o ID do arquivo a partir de uma string que contém o nome do arquivo.
     *
     * Exemplo: Para a entrada da string 'mesa_6121_.dxf', o retorno padrão é '6121'.
     *
     * @param string $inputString Uma string contendo o nome do arquivo.
     * @return string O ID do arquivo contido na string, ou uma string vazia se nenhum ID for encontrado.
     */
    public static function get_id_file(string $inputString = '')
    {
        // Usa expressão regular para encontrar um ID no formato '_<números>_' na string.
        preg_match('/_([0-9]+)_/', $inputString, $matches);
        if (isset($matches[0])) {
            $id = $matches[0];
            // Remove os caracteres '_' do ID e retorna apenas os números.
            return str_replace('_', '', $id);
        } else {
            // Caso nenhum ID seja encontrado na string, retorna uma string vazia.
            return "";
        }
    }

    /**
     * Pega o tipo do arquivo a partir de uma string que contém o nome do arquivo.
     * Exemplo: Para a entrada da string 'mesa_6121_.dxf', o retorno padrão é 'dxf'.
     * 
     * @param string $str string com o nome do arquivo.
     * @param bool $type = true retorna o tipo do arquivo junto com o nome, = false não retorna o tipo do arquivo junto com o nome.
     * @param string $barra = '/' caso utilize outro metodo para separação do diretorio.
     * @return string string com o tipo do arquivo.
     */
    public static function get_name_file(string $str = '', bool $type = true, string $barra = '/')
    {
        $lastDotPosition = strrpos($str, $barra);

        if ($lastDotPosition !== false) {
            $reductionText = substr($str, $lastDotPosition + 1);
            if ($type) {
                return $reductionText;
            }
        } else {
            // Caso não haja ponto na string, pode retornar uma mensagem de erro ou o próprio str.
            if ($type) {
                return $str;
            }
            return str_replace('.' . self::get_type_file($str), '', $str);
        }
        return $str;
    }

    /**
     * Cria um diretório.
     *
     * @param $caminho string com o caminho a ser criado.
     * @return array retorna um array com erros, caso haja algum.
     */
    public static function criet_diretorio($caminho)
    {
        try {
            $caminho = str_replace(' ', '', (string) $caminho);
            $caminho = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $caminho);

            if ($caminho === '') {
                throw new \RuntimeException('O caminho do diretório está vazio.');
            }

            if (is_dir($caminho)) {
                return [];
            }

            // O mkdir recursivo preserva caminhos absolutos do Linux e unidades do Windows.
            if (!@mkdir($caminho, 0777, true) && !is_dir($caminho)) {
                throw new \RuntimeException("Falha ao criar o diretório: {$caminho}");
            }

            return [];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }





    /**
     * Codifica todas as strings de um array substituindo os caracteres abaixo:
     *  Á , á , É , é , Í , í , Ó , ó , Ú , ú , À , à , È , è , Ì , ì , Ò , ò , Ù , ù ,   , â , Ê , ê ,
     *  Î , î , Ô , ô , Û , û , Ã , ã , Ñ , ñ , Õ , õ , Ç , ç , Ä , ä , Ë , ë , Ï , ï , Ö , ö , Ü , ü ,
     *  Ÿ , ÿ , À , à , È , è , Ì , ì , Ò , ò , Ù , ù , / , . , , , % , $ , # , ! , @ , & , * , ( , ) ,
     *  - , _ , + , = , { , } , [ , ] , | , \ , : , ; , " ," ", < , > , ? , ~ , ^ , ` , ´
     * por sequências como "i001n", "i002n", "i003n" e assim por diante.
     *
     * $array array para ser codificada as strings.
     *  string array codificada.
     */
    public static function array_codificar($array)
    {
        foreach ($array as $key => $value) {
            // Verifica se o valor é um array
            if (gettype($value) == "array") {
                // Se for um array, chama a função array_codificar recursivamente
                $array[$key] = Ferramentas::array_codificar($value);
            } else {
                // Se for uma string, chama a função codificador para realizar a codificação
                $array[$key] = Ferramentas::codificador($value);
            }
        }
        return $array;
    }

    /**
     * Decodifica as strings de um array substituindo i001n, i002n, i003n... respectivamente pelos caracteres abaixo:
     *  Á , á , É , é , Í , í , Ó , ó , Ú , ú , À , à , È , è , Ì , ì , Ò , ò , Ù , ù ,   , â , Ê , ê ,
     *  Î , î , Ô , ô , Û , û , Ã , ã , Ñ , ñ , Õ , õ , Ç , ç , Ä , ä , Ë , ë , Ï , ï , Ö , ö , Ü , ü ,
     *  Ÿ , ÿ , À , à , È , è , Ì , ì , Ò , ò , Ù , ù , / , . , , , % , $ , # , ! , @ , & , * , ( , ) ,
     *  - , _ , + , = , { , } , [ , ] , | , \ , : , ; , " ," ", < , > , ? , ~ , ^ , ` , ´
     *
     * @param $array array codificada.
     * @return string array Decodificada.
     */
    public static function array_decodificador($array)
    {
        foreach ($array as $key => $value) {
            // Verifica se o valor é um array
            if (gettype($value) == "array") {
                // Se for um array, chama a função array_decodificador recursivamente
                $array[$key] = Ferramentas::array_decodificador($value);
            } else {
                // Se for uma string, chama a função decodificador para realizar a decodificação
                $array[$key] = Ferramentas::decodificador($value);
            }
        }
        return $array;
    }

    /**
     * Função troca_status()
     *
     * Esta função é responsável por alterar o status de um objeto no banco de dados para "ativo" ou "desativado".
     *
     * @param string $table O nome da tabela do banco de dados onde a alteração deve ser realizada.
     * @param string $status O novo status a ser definido ("ativo" ou "desativado").
     *
     * Retorna um JSON indicando se a operação foi bem-sucedida ou não.
     */
    function troca_status($table = null, $status = NULL)
    {
        if ($status == "desativado" || $status == "ativo") { // Verifica se a variável status está correta
            if ($this->request->isAJAX()) {
                session_start();
                $id = service('request')->getPost('id'); // Obtém o ID falso fornecido via AJAX
                $lista = $_SESSION["lista"]; // Obtém a lista de IDs

                if (Ferramentas::array_index($lista, [$id]) != "") { // Verifica se o ID existe na lista
                    $item = '';
                    switch ($table) { // Determina qual tabela do banco de dados deve ser atualizada
                        case 'user':
                            $db = new \App\Models\Usuarios();
                            $item = "user";
                            break;
                        case 'empreendimentos':
                            $db = new \App\Models\Empreendimentos();
                            $item = "empreendimentos";
                            break;
                        case 'empresa':
                            $db = new \App\Models\Empresa();
                            $item = "empresa";
                            break;
                        case 'finalidade':
                            $db = new \App\Models\Finalidade();
                            $item = "finalidade";
                            break;
                        case 'prioridade':
                            $db = new \App\Models\Prioridade();
                            $item = "prioridade";
                            break;
                        case 'filtros':
                            $db = new \App\Models\Filtros();
                            $item = "filtros";
                            break;
                        case 'tag':
                            $db = new \App\Models\Subpasta();
                            $item = "subpasta";
                            break;
                        default:
                            $data = [
                                //caso não exista retorna que deu errado
                                "ok" => false,
                            ];
                            return $this->response->setJSON($data);
                            break;
                    }
                    $idItem = Ferramentas::array_index($lista, [$id]);
                    $registroAtual = Ferramentas::array_pesquisa($db->find(), 'id', $idItem);
                    $alteracao = new \App\Models\Alteracoes();

                    // Registra a alteração no histórico de alterações
                    $data = [
                        "individuo" => $_SESSION["usuario"],
                        "id_item" => Ferramentas::array_index($lista, [$id]),
                        "antes" => Ferramentas::array_index(Ferramentas::array_pesquisa($db->find(), 'id', Ferramentas::array_index($lista, [$id])), ['status']),
                        "depois" => $status,
                        "item" => $item,
                        "info_mais" => "status",
                        "data_add" => Ferramentas::codificador(date('d/m/Y H:i'))

                    ];
                    $alteracao->insertWithDetails(
                        [
                            "usuario_id" => $_SESSION["usuario"],
                            "individuo" => $_SESSION["usuario"],
                            "id_item" => $idItem,
                            "item" => $item,
                            "info_mais" => "status",
                            "_meta" => [
                                "acao" => "ferramentas.status",
                                "origem" => "ferramentas",
                                "tabela" => $table,
                            ],
                        ],
                        [
                            [
                                "campo" => "status",
                                "valor_antes" => Ferramentas::array_index($registroAtual, ['status']),
                                "valor_depois" => $status,
                            ],
                        ]
                    );

                    // Atualiza o status no banco de dados
                    $db->update($idItem, ['status' => $status]); //faz o update no banco e troca o id falso pelo verdadeiro
                    $data = [
                        //retorna que deu certo para o ajax
                        "ok" => true,
                    ];
                } else {
                    $data = [
                        //se o não ouver nada na lista retorna que deu errado
                        "ok" => false,
                    ];
                }
                return $this->response->setJSON($data);
            }
        }
    }

    function re_colcoar_desenho($id)
    {
        // Inicia a sessão, se ainda não estiver ativa.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }


        $caminho = '';
        $desenhos = new \App\Models\Desenhos();
        $desenhos_renovar = $desenhos->where("id", $id)->find();

        $id_lista = $id; // Obtém o ID da linha original.

        // if ($desenhos_renovar) 
        {
            $novaEntrada = $desenhos_renovar[0];

            // Remove o ID e o cortador da nova entrada, para evitar conflitos.
            unset($novaEntrada['id'], $novaEntrada['cortador'], $novaEntrada['ordem'], $novaEntrada['data_add'], $novaEntrada['corte_id']);


            $novaEntrada['status'] = 'pendente';


            $nome = Ferramentas::decodificador($novaEntrada['nome']);
            $extencao = '.' . Ferramentas::get_type_file($nome);


            $caminho = dirname($novaEntrada['diretorio']) . DIRECTORY_SEPARATOR;
            $nomeArquivo = basename($novaEntrada['diretorio']);
            $nome = "";
            $nome2 = "";
            // Verifica se o arquivo existe
            if (!file_exists($caminho . $novaEntrada['nome'])) {
                if (!file_exists($caminho . $nomeArquivo)) {

                    // Verifica se o nome já tem ponto
                    if (strpos($nomeArquivo, '.') === false) {
                        // Pega a posição do último "_"
                        $pos = strrpos($nomeArquivo, '_');
                        if ($pos !== false) {
                            // Insere um ponto logo depois do último "_"
                            $nome = substr_replace($nomeArquivo, '.', $pos + 1, 0);
                        }
                    }
                    if (!file_exists($caminho . $nome)) {
                        // Verifica se o nome já tem ponto
                        if (strpos($novaEntrada['nome'], '.') === false) {
                            // Pega a posição do último "_"
                            $pos = strrpos($novaEntrada['nome'], '_');
                            if ($pos !== false) {
                                // Insere um ponto logo depois do último "_"
                                $nome2 = substr_replace($novaEntrada['nome'], '.', $pos + 1, 0);
                            }
                        }
                        if (!file_exists($caminho . $nome2)) {

                            return (["ok" => false, 1 => $caminho . $novaEntrada['nome'], 2 => $caminho . $nomeArquivo, 3 => $caminho . $nome, 4 => $caminho . $nome2, "original" => $novaEntrada['diretorio'], "id" => $novaEntrada]);
                        } else {
                            $nomeArquivo = $nome2;
                        }
                    } else {
                        $nomeArquivo = $nome;
                    }
                }
            } else {
                $nomeArquivo = $novaEntrada['nome'];
            }
            // return ( ["ok" => true , 1=>$caminho . $novaEntrada['nome'],2=>$caminho . $nomeArquivo , 3=>$caminho . $nome , 4 =>$caminho . $nome2,"original"=>$novaEntrada['diretorio'],"id"=>$novaEntrada]);

            // Remove o prefixo padrão: cortado_DD_MM_AAAA__HH_MM_
            $s = preg_replace('/^cortado_\d{2}_\d{2}_\d{4}__\d{2}_\d{2}_/', '', $nomeArquivo);

            // Extrai nome e extensão achatada (ex: "_468_3550_dxf" -> ".dxf")
            if (preg_match('/^(.*?)(?:_[0-9_]+_([a-z0-9]+))$/i', $s, $m)) {
                $nome = trim($m[1]);
                $extensao = '.' . strtolower($m[2]);
            } else {
                // fallback: tenta detectar extensão normal
                $nome = trim($s);
                $ext = pathinfo($s, PATHINFO_EXTENSION);
                $extensao = $ext ? '.' . strtolower($ext) : '';
            }

            $nomeExtraido = [
                'nome' => $nome,
                'extensao' => $extensao
            ];



            do {
                $radom = rand(1000, 9999);
                $novo_nome = $nomeExtraido['nome'] . '_' . $radom . "_" . $nomeExtraido['extensao'];
            } while (file_exists($caminho . $novo_nome));

            // Faz uma cópia do arquivo original com o novo nome.
            copy($caminho . $nomeArquivo, $caminho . $novo_nome);

            $novaEntrada['caminho'] = $caminho . $novo_nome;
            $novaEntrada['nome'] =  $novo_nome;
            //return $this->response->setJSON(['1' => $caminho. $novo_nome, '2'=>$nome]);

            // Insere a nova entrada no banco de dados.
            $desenhos->insert($novaEntrada);

            // Atualiza o status da entrada original para 'duplicado' com o novo ID.
            $desenhos->update($id_lista, ['status' => 'duplicado_' . $desenhos->insertID()]);
            return $caminho;
        }
    }








    /**
     * Reordena de forma global (única sequência 1..N) todos os desenhos ativos,
     * movendo um ID (ou um array de IDs) para a posição desejada e renumerando tudo.
     *
     * @param int|int[]|null $id            ID único ou array de IDs a reposicionar
     * @param int|null       $prioridade_id (opcional) filtra só esse grupo de prioridade
     * @param int|null       $ordem         posição 1-based onde inserir o ID (ou o bloco de IDs).
     *                                       Se omitido (ou $id=null), apenas renumera de 1..N.
     * @throws \Exception
     */
    /**
     * Reordena “in‐place” todos os desenhos de um mesmo processo,
     * movendo um desenho (ou um bloco de desenhos) para a posição desejada
     * e renumerando de 1..N sem jamais gerar duplicatas nem buracos.
     *
     * @param int|int[]|null $id            ID único ou array de IDs a reposicionar
     * @param int|null       $prioridade_id (opcional) filtra também por prioridade
     * @param int|null       $ordem         posição 1-based onde inserir o(s) ID(s).
     *                                       Se omitido, só renumera de 1..N.
     * @throws \Exception
     */
    /**
     * Reordena “in‐place” todos os desenhos do mesmo processo (e opcionalmente prioridade),
     * movendo um único ID ou um array de IDs para a posição desejada e renumerando
     * corretamente sem jamais duplicar nem pular números.
     *
     * @param int|int[]|null $id            ID único ou array de IDs a reposicionar
     * @param int|null       $prioridade_id Se informado, filtra também por prioridade
     * @param int|null       $ordem         Posição 1‐based onde inserir o(s) ID(s).
     *                                       Se omitido (ou $id=null), só renumera tudo.
     * @throws \Exception
     */
    function re_ordenar_ordem_desenho(int|array $id = null, int $prioridade_id = null, int $ordem = null)
    {
        $model = new \App\Models\Desenhos();

        // ──────────────────────────────────────────────────────────────────────────
        // Se vier $id + $ordem, trata como “mover dentro de um processo”
        if ($id !== null && is_int($ordem)) {
            // normaliza lista de IDs a mover
            $toMove = is_array($id) ? $id : [$id];

            // 1) Busca processos_id de cada ID
            $rows = $model
                ->select('id, processos_id')
                ->whereIn('id', $toMove)
                ->findAll();

            if (empty($rows)) {
                throw new \Exception("Nenhum ID informado foi encontrado.");
            }

            // 2) Garante que todos pertencem ao mesmo processo
            $processosIds = array_unique(array_column($rows, 'processos_id'));
            if (count($processosIds) > 1) {
                throw new \Exception("IDs pertencem a processos diferentes.");
            }
            $processos_id = (int) $processosIds[0];

            // 3) Busca todos os desenhos desse processo (e opcional prioridade)
            $qb = $model->builder()
                ->whereIn('status', ['pendente', 'processando'])
                ->where('processos_id', $processos_id);
            if ($prioridade_id !== null) {
                $qb->where('prioridade_id', $prioridade_id);
            }
            $todos = $qb
                ->orderBy('ordem IS NULL', 'ASC', false)
                ->orderBy('ordem', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            // 4) Extrai só os IDs nessa ordem
            $allIds = array_column($todos, 'id');

            // 5) Filtra só os IDs válidos dentro do processo
            $toMove = array_values(array_intersect($toMove, $allIds));
            if (empty($toMove)) {
                throw new \Exception("IDs não pertencem a este processo ou não estão ativos.");
            }

            // 6) Calcula posição de inserção dentro de [1..(total-bloco+1)]
            $maxInsert = count($allIds) - count($toMove) + 1;
            $pos1 = max(1, min($ordem, $maxInsert));

            // 7) Remove esses IDs da lista original
            $remaining = [];
            foreach ($allIds as $x) {
                if (!in_array($x, $toMove, true)) {
                    $remaining[] = $x;
                }
            }

            // 8) Insere o bloco em zero‐based ($pos1-1)
            array_splice($remaining, $pos1 - 1, 0, $toMove);

            // 9) Monta batch renumerando de 1..N
            $batch = [];
            foreach ($remaining as $i => $itemId) {
                $batch[] = [
                    'id' => $itemId,
                    'ordem' => $i + 1,
                ];
            }

            if (!empty($batch)) {
                $model->updateBatch($batch, 'id');
            }
            return;
        }

        // ──────────────────────────────────────────────────────────────────────────
        // Caso contrário: renumeração completa de todos os processos
        // (filtrados por prioridade se informado), cada processo de 1..N
        $qb = $model->builder()
            ->whereIn('status', ['pendente', 'processando']);
        if ($prioridade_id !== null) {
            $qb->where('prioridade_id', $prioridade_id);
        }

        // traz todos já ordenados por processo, ordem e id
        $todos = $qb
            ->orderBy('processos_id', 'ASC')
            ->orderBy('ordem IS NULL', 'ASC', false)
            ->orderBy('ordem', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        // agrupa por processos_id
        $groups = [];
        foreach ($todos as $d) {
            $groups[$d['processos_id']][] = $d;
        }

        // monta batch: cada grupo renumerado de 1..N
        $batch = [];
        foreach ($groups as $lista) {
            foreach ($lista as $i => $d) {
                $batch[] = [
                    'id' => $d['id'],
                    'ordem' => $i + 1,
                ];
            }
        }

        if (!empty($batch)) {
            $model->updateBatch($batch, 'id');
        }
    }




    /**
     * Re‐numera de 1..N todos os desenhos com status “pendente” ou “processando”,
     * separadamente para cada par (processos_id + prioridade_id), sem buracos
     * nem duplicações — garantindo que, mesmo que dois desenhos tenham a mesma
     * ordem antiga, eles recebam posições 1,2,3… distintas.
     *
     * Para cada desenho cuja posição mudar:
     *   • desativa o registro ATIVO antigo em `ordem`
     *   • insere um novo registro com a nova ordem e status = 'ativo'
     *
     * @throws \RuntimeException Em caso de erro no banco.
     */
    public function sincronizarNovasOrdens()
    {
return;
        $db = \Config\Database::connect();
        $ordemModel = new \App\Models\Ordem();
        $desenhosModel = new \App\Models\Desenhos();
        $projetoModel = new \App\Models\Projeto();

        // 1. Buscar todos os DESENHOS pendentes/processando 
        $desenhos = $desenhosModel
            ->select([
                'desenhos.id AS desenho_id',
                'NULL AS projeto_id',
                'desenhos.processos_id',
                'desenhos.prioridade_id',
                'o.id AS ordem_id',
                'o.ordem AS ordem',
                'o.status AS status_ordem',
                'o.data_add AS ordem_data_add'
            ])
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->whereIn('desenhos.status', ['pendente', 'processando'])
            ->findAll();

        // 2. Buscar todos os PROJETOS ativos 
        $projetos = $projetoModel
            ->select([
                'NULL AS desenho_id',
                'projeto.id AS projeto_id',
                'NULL AS processos_id', // Se tiver, traga o correto!
                'NULL AS prioridade_id', // Se tiver, traga o correto!
                'o.id AS ordem_id',
                'o.ordem AS ordem',
                'o.status AS status_ordem',
                'o.data_add AS ordem_data_add'
            ])
            ->join('ordem o', "o.projeto_id = projeto.id AND o.status = 'ativo'", 'left')
            ->where('projeto.status', 'ativo')
            ->findAll();

        // 3. Junta tudo
        $itens = array_merge($desenhos, $projetos);

        // 4. Agrupa — Cada grupo é: tipo + processos_id + prioridade_id (ou só projeto se não tem processo/prioridade)
        $grupos = [];
        foreach ($itens as $o) {
            if (!empty($o['projeto_id']) && empty($o['desenho_id'])) {
                // Se projeto tem processo/prioridade, use aqui!
                $key = 'P_' . ($o['processos_id'] ?? 0) . '_' . ($o['prioridade_id'] ?? 0);
            } elseif (!empty($o['desenho_id'])) {
                $key = 'D_' . $o['processos_id'] . '_' . $o['prioridade_id'];
            } else {
                continue;
            }
            $grupos[$key][] = $o;
        }

        $batchDesativa = [];
        $batchInsere = [];
        $batchUpdDesenhos = [];

        foreach ($grupos as $lista) {
            // Agrupa por ordem, os sem ordem vão para o fim
            $byOrdem = [];
            $semOrdemNoGrupo = [];
            foreach ($lista as $item) {
                $ordem = !empty($item['ordem']) ? (int) $item['ordem'] : 0;
                if ($ordem > 0) {
                    $byOrdem[$ordem][] = $item;
                } else {
                    $semOrdemNoGrupo[] = $item;
                }
            }
            ksort($byOrdem);

            $ordemSequencial = 1;
            // Normaliza os com ordem
            foreach ($byOrdem as $mesmaOrdem) {
                usort($mesmaOrdem, function ($a, $b) {
                    $da = strtotime($a['ordem_data_add'] ?? '1970-01-01 00:00:00');
                    $db = strtotime($b['ordem_data_add'] ?? '1970-01-01 00:00:00');
                    if ($da != $db)
                        return $db <=> $da;
                    return ($b['ordem_id'] ?? 0) <=> ($a['ordem_id'] ?? 0);
                });
                foreach ($mesmaOrdem as $i => $item) {
                    $novaOrdem = $ordemSequencial++;
                    // Só mexe se precisar
                    if ((int) $item['ordem'] !== $novaOrdem) {
                        if (!empty($item['ordem_id'])) {
                            $batchDesativa[] = $item['ordem_id'];
                        }
                        $novo = [
                            'desenho_id' => $item['desenho_id'],
                            'projeto_id' => $item['projeto_id'],
                            'processos_id' => $item['processos_id'],
                            'prioridade_id' => $item['prioridade_id'],
                            'ordem' => $novaOrdem,
                            'status' => 'ativo'
                        ];
                        $batchInsere[] = $novo;
                        if (!empty($item['desenho_id'])) {
                            $batchUpdDesenhos[] = [
                                'id' => $item['desenho_id'],
                                'ordem' => $novaOrdem
                            ];
                        }
                    }
                }
            }
            // Cataloga os sem ordem no fim!
            foreach ($semOrdemNoGrupo as $item) {
                $novaOrdem = $ordemSequencial++;
                $novo = [
                    'desenho_id' => $item['desenho_id'],
                    'projeto_id' => $item['projeto_id'],
                    'processos_id' => $item['processos_id'],
                    'prioridade_id' => $item['prioridade_id'],
                    'ordem' => $novaOrdem,
                    'status' => 'ativo'
                ];
                $batchInsere[] = $novo;
                if (!empty($item['desenho_id'])) {
                    $batchUpdDesenhos[] = [
                        'id' => $item['desenho_id'],
                        'ordem' => $novaOrdem
                    ];
                }
            }
        }

        // Executa em transação
        $db->transBegin();
        try {
            if ($batchDesativa) {
                $ordemModel->whereIn('id', $batchDesativa)
                    ->set('status', 'desativado')
                    ->update();
            }
            if ($batchInsere) {
                $ordemModel->insertBatch($batchInsere);
            }
            if ($batchUpdDesenhos) {
                $desenhosModel->updateBatch($batchUpdDesenhos, 'id');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Falha ao sincronizar ordens: ' . $e->getMessage());
        }
        return $grupos;
    }

    public static function statusAtivosOrdem(): array
    {
        return ['pendente', 'cortando', 'processando'];
    }

    private static function statusPermiteOrdem(?string $status): bool
    {
        $status = strtolower(trim((string) $status));
        return $status !== '' && in_array($status, self::statusAtivosOrdem(), true);
    }

    public static function garantirOrdemAtivaDesenho(
        int $desenhoId,
        ?int $processosId = null,
        ?int $prioridadeId = null
    ): ?array {
        if ($desenhoId <= 0) {
            return null;
        }

        $ordemModel = new \App\Models\Ordem();
        $ordemAtiva = $ordemModel
            ->where('desenho_id', $desenhoId)
            ->where('projeto_id IS NULL', null, false)
            ->where('status', 'ativo')
            ->first();

        $grupoInformado = $processosId !== null && $processosId > 0 && $prioridadeId !== null && $prioridadeId > 0;
        $grupoAtivoCompativel = is_array($ordemAtiva)
            && (
                !$grupoInformado
                || (
                    (int) ($ordemAtiva['processos_id'] ?? 0) === (int) $processosId
                    && (int) ($ordemAtiva['prioridade_id'] ?? 0) === (int) $prioridadeId
                )
            );

        if (is_array($ordemAtiva) && (int) ($ordemAtiva['ordem'] ?? 0) > 0 && $grupoAtivoCompativel) {
            return $ordemAtiva;
        }

        if ($processosId === null || $processosId <= 0 || $prioridadeId === null || $prioridadeId <= 0) {
            $desenho = (new \App\Models\Desenhos())
                ->select('id, status, processos_id, prioridade_id')
                ->where('id', $desenhoId)
                ->first();

            if (
                !is_array($desenho)
                || empty($desenho['id'])
                || !self::statusPermiteOrdem((string) ($desenho['status'] ?? ''))
            ) {
                return is_array($ordemAtiva) ? $ordemAtiva : null;
            }

            $processosId = (int) ($desenho['processos_id'] ?? 0);
            $prioridadeId = (int) ($desenho['prioridade_id'] ?? 0);
        }

        if ($processosId <= 0 || $prioridadeId <= 0) {
            return is_array($ordemAtiva) ? $ordemAtiva : null;
        }

        $maxBuilder = $ordemModel
            ->selectMax('ordem', 'max_ordem')
            ->where('status', 'ativo')
            ->where('processos_id', $processosId)
            ->where('prioridade_id', $prioridadeId)
            ->where('desenho_id IS NOT NULL', null, false);

        if (is_array($ordemAtiva) && !empty($ordemAtiva['id'])) {
            $maxBuilder->where('id !=', (int) $ordemAtiva['id']);
        }

        $maxLinha = $maxBuilder->first();
        $novaOrdem = (int) ($maxLinha['max_ordem'] ?? 0) + 1;
        if ($novaOrdem <= 0) {
            $novaOrdem = 1;
        }

        $payload = [
            'desenho_id' => $desenhoId,
            'projeto_id' => null,
            'processos_id' => $processosId,
            'prioridade_id' => $prioridadeId,
            'ordem' => $novaOrdem,
            'status' => 'ativo',
        ];

        if (is_array($ordemAtiva) && !empty($ordemAtiva['id'])) {
            $ordemModel->update((int) $ordemAtiva['id'], $payload);
            $payload['id'] = (int) $ordemAtiva['id'];
            $payload['data_add'] = (string) ($ordemAtiva['data_add'] ?? date('Y-m-d H:i:s'));
            return $payload;
        }

        $payload['data_add'] = date('Y-m-d H:i:s');
        $ordemModel->insert($payload);
        $payload['id'] = (int) $ordemModel->getInsertID();

        return $payload;
    }

    public static function sincronizarOrdensDesenhosAtivos(?int $processoId = null): int
    {
        $desenhosModel = new \App\Models\Desenhos();

        $builder = $desenhosModel
            ->select([
                'desenhos.id',
                'desenhos.processos_id',
                'desenhos.prioridade_id',
                'o.id AS ordem_id',
                'o.ordem AS ordem_atual',
            ])
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->whereIn('desenhos.status', self::statusAtivosOrdem());

        if ($processoId !== null && $processoId > 0) {
            $builder->where('desenhos.processos_id', $processoId);
        }

        $desenhos = $builder
            ->groupStart()
                ->where('o.id', null)
                ->orWhere('o.ordem IS NULL', null, false)
                ->orWhere('o.ordem <=', 0)
            ->groupEnd()
            ->orderBy('desenhos.data_add', 'ASC')
            ->orderBy('desenhos.id', 'ASC')
            ->findAll();

        $sincronizados = 0;
        foreach ($desenhos as $desenho) {
            $ordem = self::garantirOrdemAtivaDesenho(
                (int) ($desenho['id'] ?? 0),
                (int) ($desenho['processos_id'] ?? 0),
                (int) ($desenho['prioridade_id'] ?? 0)
            );

            if (is_array($ordem) && !empty($ordem['id'])) {
                $sincronizados++;
            }
        }

        return $sincronizados;
    }

    public static function sincronizarOrdensFaltantes(): void
    {
        $db = \Config\Database::connect();
        $ordemModel = new \App\Models\Ordem();
        $desenhosModel = new \App\Models\Desenhos();
        $projetoModel = new \App\Models\Projeto();

        // 1. Desenhos ativos sem ordem ativa
        $desenhos = $desenhosModel
            ->select(['id', 'processos_id', 'prioridade_id'])
            ->whereIn('status', self::statusAtivosOrdem())
            ->findAll();

        // 2. Projetos ativos sem ordem ativa
        $projetos = $projetoModel
            ->select(['id'])
            ->where('status', 'ativo')
            ->findAll();

        $db->transBegin();
        try {
            // ---- Para DESENHOS, grupo por processos_id+prioridade_id ----
            $desenhosPorGrupo = [];
            foreach ($desenhos as $d) {
                $existe = $ordemModel
                    ->where('desenho_id', $d['id'])
                    ->where('status', 'ativo')
                    ->first();
                if (!$existe) {
                    $key = $d['processos_id'] . '_' . $d['prioridade_id'];
                    $desenhosPorGrupo[$key][] = $d;
                }
            }

            foreach ($desenhosPorGrupo as $key => $lista) {
                [$proc_id, $prio_id] = explode('_', $key);
                $max = $ordemModel
                    ->selectMax('ordem')
                    ->where('processos_id', $proc_id)
                    ->where('prioridade_id', $prio_id)
                    ->where('status', 'ativo')
                    ->where('desenho_id IS NOT NULL', null, false)
                    ->first();
                $proxOrdem = isset($max['ordem']) && $max['ordem'] > 0 ? $max['ordem'] + 1 : 1;
                foreach ($lista as $d) {
                    $ordemModel->insert([
                        'desenho_id' => $d['id'],
                        'projeto_id' => null,
                        'processos_id' => $proc_id,
                        'prioridade_id' => $prio_id,
                        'ordem' => $proxOrdem++,
                        'status' => 'ativo',
                        'data_add' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // ---- Para PROJETOS, ordem global sequencial ----
            $projetosComOrdem = $ordemModel
                ->select('projeto_id')
                ->where('projeto_id IS NOT NULL', null, false)
                ->where('status', 'ativo')
                ->groupBy('projeto_id')
                ->findAll();
            $idsProjetosComOrdem = array_column($projetosComOrdem, 'projeto_id');

            $maxProj = $ordemModel
                ->selectMax('ordem')
                ->where('projeto_id IS NOT NULL', null, false)
                ->where('status', 'ativo')
                ->first();
            $proxOrdemProj = isset($maxProj['ordem']) && $maxProj['ordem'] > 0 ? $maxProj['ordem'] + 1 : 1;

            foreach ($projetos as $p) {
                if (!in_array($p['id'], $idsProjetosComOrdem)) {
                    $ordemModel->insert([
                        'desenho_id' => null,
                        'projeto_id' => $p['id'],
                        'processos_id' => null,
                        'prioridade_id' => null,
                        'ordem' => $proxOrdemProj++,
                        'status' => 'ativo',
                        'data_add' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // --- TRATAMENTO DE ORDENS DUPLICADAS (incrementa o mais velho até não haver duplicados) ---

            // 1. Para DESENHOS (por processos_id + prioridade_id)
            $ordensDesenhos = $ordemModel
                ->where('desenho_id IS NOT NULL', null, false)
                ->where('status', 'ativo')
                ->orderBy('processos_id')
                ->orderBy('prioridade_id')
                ->orderBy('ordem')
                ->orderBy('data_add')
                ->findAll();

            $grupos = [];
            foreach ($ordensDesenhos as $o) {
                $key = $o['processos_id'] . '_' . $o['prioridade_id'];
                $grupos[$key][] = $o;
            }

            foreach ($grupos as $grupo) {
                $usadas = [];
                foreach ($grupo as $o) {
                    $ordem = (int) $o['ordem'];
                    if (!isset($usadas[$ordem])) {
                        $usadas[$ordem] = $o['id'];
                        continue;
                    }
                    // Duplicado: encontra a próxima ordem livre
                    $novaOrdem = $ordem + 1;
                    while (isset($usadas[$novaOrdem])) {
                        $novaOrdem++;
                    }
                    $ordemModel->update($o['id'], ['ordem' => $novaOrdem]);
                    $usadas[$novaOrdem] = $o['id'];
                }
            }

            // 2. Para PROJETOS (global)
            $ordensProjetos = $ordemModel
                ->where('projeto_id IS NOT NULL', null, false)
                ->where('status', 'ativo')
                ->orderBy('projeto_id')
                ->orderBy('ordem')
                ->orderBy('data_add')
                ->findAll();

            $usadas = [];
            foreach ($ordensProjetos as $o) {
                $ordem = (int) $o['ordem'];
                if (!isset($usadas[$ordem])) {
                    $usadas[$ordem] = $o['id'];
                    continue;
                }
                $novaOrdem = $ordem + 1;
                while (isset($usadas[$novaOrdem])) {
                    $novaOrdem++;
                }
                $ordemModel->update($o['id'], ['ordem' => $novaOrdem]);
                $usadas[$novaOrdem] = $o['id'];
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Erro ao sincronizar ordens faltantes: ' . $e->getMessage());
        }
    }


    /**
     * Insere ordens faltantes para **todos** os projetos ativos.
     * Para cada projeto, triangula seus desenhos via projeto_desenho ⇒ desenhos
     * e garante uma ordem ativa (maior +1 ou 1) para cada combinação
     * (processos_id, prioridade_id) encontrada.
     */
    public static function sincronizarOrdensProjetosAtivos(): void
    {
        $db = \Config\Database::connect();
        $ordemModel = new \App\Models\Ordem();
        $projetoModel = new \App\Models\Projeto();

        // 1) Carrega todos os projetos ativos
        $projetos = $projetoModel
            ->select('id')
            ->where('status', 'ativo')
            ->findAll();

        $db->transBegin();
        try {
            foreach ($projetos as $p) {
                $projId = (int) $p['id'];

                // 2) Para este projeto, pega todos os grupos (processos, prioridade)
                $grupos = $db->table('projeto_desenho pd')
                    ->select('d.processos_id, d.prioridade_id')
                    ->join('desenhos d', 'd.id = pd.desenho_id', 'inner')
                    ->where('pd.projeto_id', $projId)
                    ->groupBy(['d.processos_id', 'd.prioridade_id'])
                    ->get()
                    ->getResultArray();

                // Se não houver desenhos vinculados, pula para o próximo projeto
                if (empty($grupos)) {
                    continue;
                }

                foreach ($grupos as $g) {
                    $proc = (int) $g['processos_id'];
                    $prio = (int) $g['prioridade_id'];
                    if ($proc === 0 || $prio === 0) {
                        // ignora valores inválidos
                        continue;
                    }

                    // 3) Só insere se ainda não existe ordem ativa para este tripé
                    $existe = $ordemModel
                        ->where('projeto_id', $projId)
                        ->where('processos_id', $proc)
                        ->where('prioridade_id', $prio)
                        ->where('status', 'ativo')
                        ->first();

                    if ($existe) {
                        continue;
                    }

                    // 4) Calcula próxima ordem (max + 1) ou 1
                    $max = $ordemModel
                        ->selectMax('ordem', 'max_ordem')
                        ->where('projeto_id', $projId)
                        ->where('processos_id', $proc)
                        ->where('prioridade_id', $prio)
                        ->where('status', 'ativo')
                        ->first();

                    $proxOrdem = !empty($max['max_ordem']) ? $max['max_ordem'] + 1 : 1;

                    // 5) Insere novo registro
                    $ordemModel->insert([
                        'desenho_id' => null,
                        'projeto_id' => $projId,
                        'processos_id' => $proc,
                        'prioridade_id' => $prio,
                        'ordem' => $proxOrdem,
                        'status' => 'ativo'
                    ]);
                }
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException('Erro ao sincronizar ordens de projetos ativos: ' . $e->getMessage());
        }
    }










    /**
     * Reposiciona um ou mais desenhos dentro da sequência de ordens
     * de um dado (processos_id, prioridade_id), fazendo:
     * 1) Shift +N em todas as ordens ≥ $targetOrder
     * 2) Atribui aos $ids as ordens começando em $targetOrder, em sequência
     *
     * @param int|int[] $ids            ID único ou array de IDs de desenhos
     * @param int       $targetOrder    Posição 1‐based onde o primeiro ID deve ficar
     * @param int       $prioridade_id  Apenas desenhos desta prioridade
     * @param int       $processos_id   Apenas desenhos deste processo
     * @throws \RuntimeException       Em caso de erro de banco
     */
    public static function reordenarPorPrioridade(
        int|array $ids,
        int $targetOrder,
        int $prioridade_id,
        int $processos_id,
        bool $projeto = false
    ): void {
        $toMove = is_array($ids) ? array_values($ids) : [(int) $ids];
        $ordemModel = new \App\Models\Ordem();
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($toMove as $itemId) {
            if (!$projeto) {
                self::garantirOrdemAtivaDesenho((int) $itemId, $processos_id, $prioridade_id);
            }

            // 1. Busca o registro ATIVO
            $builderRec = $ordemModel->builder()
                ->where('processos_id', $processos_id)
                //->where('prioridade_id', $prioridade_id)
                ->where('status', 'ativo');

            if ($projeto) {
                $builderRec->where('projeto_id', $itemId)
                    ->where('desenho_id IS NULL', null, false);
            } else {
                $builderRec->where('desenho_id', $itemId)
                    ->where('projeto_id IS NULL', null, false);
            }

            $rec = $builderRec->get()->getRowArray();
            if (!$rec) {
                $db->transRollback();
                throw new \RuntimeException(
                    "Ordem ativa não encontrada para " .
                        ($projeto ? "projeto_id={$itemId}" : "desenho_id={$itemId}") .
                        ", prioridade={$prioridade_id}, processo={$processos_id}"
                );
            }

            $oldOrd = (int) $rec['ordem'];

            if ($oldOrd === $targetOrder && $prioridade_id == $rec['prioridade_id']) {
                //continue;
            }

            // 2. Ajusta as ordens dos OUTROS itens do grupo, não só do próprio
            $builderShift = $ordemModel->builder()
                ->where('processos_id', $processos_id)
                ->where('prioridade_id', $prioridade_id)
                ->where('status', 'ativo');

            if ($projeto) {
                $builderShift->where('desenho_id IS NULL', null, false);
                // Pega todos projetos do grupo MENOS o atual
                $builderShift->where('projeto_id !=', $itemId);
            } else {
                $builderShift->where('projeto_id IS NULL', null, false);
                $builderShift->where('desenho_id !=', $itemId);
            }

            // Agora sim: shift dos demais, não só do próprio
            if ($targetOrder > $oldOrd) {
                // Movendo para frente: os que estavam entre old+1 e targetOrder perdem 1
                $builderShift
                    ->set('ordem', 'ordem - 1', false)
                    ->where('ordem >', $oldOrd)
                    ->where('ordem <=', $targetOrder);
            } else {
                // Movendo para trás: os que estavam entre targetOrder e old-1 ganham 1
                $builderShift
                    ->set('ordem', 'ordem + 1', false)
                    ->where('ordem >=', $targetOrder)
                    ->where('ordem <', $oldOrd);
            }
            $builderShift->update();

            // 3. Desativa antiga
            $ordemModel
                ->where('id', $rec['id'])
                ->set('status', 'desativado')
                ->update();

            // 4. Cria novo registro como histórico de ordem ativa
            $newRow = [
                'desenho_id' => $projeto ? null : $itemId,
                'projeto_id' => $projeto ? $itemId : null,
                'prioridade_id' => $prioridade_id,
                'processos_id' => $processos_id,
                'ordem' => $targetOrder,
                'status' => 'ativo',
                'data_add' => date('Y-m-d H:i:s'),
            ];
            $ordemModel->insert($newRow);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            $err = $db->error();
            throw new \RuntimeException(
                "Falha ao reordenar (proc={$processos_id}, prio={$prioridade_id}): " .
                    "[{$err['code']}] {$err['message']}"
            );
        }
    }















    function enviar_desenho($ip, $dir)
    {
        $url = "http://{$ip}:5000/lista_corte";
        $dir = basename($dir);
        if (file_get_contents($dir) === false) {
            die("Erro ao ler o arquivo.");
        }

        $Data = [
            'file' => new \CURLFile($dir, mime_content_type($dir), basename($dir)),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Erro: ' . curl_error($ch);
        } else {
            echo $response;
        }

        curl_close($ch);
    }


    /**
     * Converte uma string “YYYY-MM-DD HH:MM:SS” para “DD/MM/YYYY HH:MM:SS”
     *
     * @param string $datetime Ex.: "2025-04-16 15:41:00"
     * @return string|false    Ex.: "16/04/2025 15:41:00" ou false em caso de falha
     */
    function formatarDataHora(?string $datetime): string
    {
        // Se for null, vazio ou placeholder MySQL
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
            return '';
        }

        // Tenta criar DateTime
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        if (!$dt) {
            return '';
        }

        return $dt->format('d/m/Y H:i:s');
    }




    public static function desativarOrdems()
    {

        $ordemModel = new \App\Models\Ordem();

        $d = $ordemModel->select(['ordem.id', 'd.status'])
            ->join('desenhos d', "ordem.desenho_id = d.id AND ordem.status = 'ativo'", 'left')
            ->whereNotIn('d.status', self::statusAtivosOrdem())
            ->findAll();

        foreach ($d as $item) {
            $ordemModel->update($item['id'], ['status' => 'desativado']);
        }
    }

    public static function sicroniaOrdems()
    {

        $ordemModel = new \App\Models\Ordem();
        $desenhosModel = new \App\Models\Desenhos();


        $desenhos = $desenhosModel
            ->select([
                'desenhos.id            AS desenho_id',
                'NULL                   AS projeto_id',
                'desenhos.processos_id',
                'desenhos.prioridade_id',
                'o.id                   AS ordem_id',
                'o.ordem                AS ordem',
                'o.status               AS status_ordem',
                'o.data_add             AS ordem_data_add',
                'desenhos.data_add      AS desenho_data_add'
            ])
            ->join('ordem o', "o.desenho_id = desenhos.id AND o.status = 'ativo'", 'left')
            ->join('projeto_desenho pd', 'pd.desenho_id = desenhos.id', 'left') // <-- Verificação
            ->whereIn('desenhos.status', self::statusAtivosOrdem())
            ->where('o.id', null) // Desenhos sem ordem
            ->where('pd.projeto_id', null) // <-- Desenhos que NÃO estão vinculados a um projeto
            ->orderBy('desenhos.data_add', 'ASC')
            ->findAll();


        $ordens = $ordemModel
            ->select('projeto_id, prioridade_id, ordem, processos_id')
            ->selectMax('ordem', 'ordem_max')
            ->where('status', 'ativo')
            ->groupBy(['projeto_id', 'prioridade_id'])
            ->findAll();

        $ordensMAX = array();
        foreach ($ordens as $key => $value) {
            $ordensMAX[$value['prioridade_id']][$value['processos_id']] = intval($value['ordem']);
        }

        foreach ($desenhos as $key => $value) {
            if (isset($ordensMAX[$value['prioridade_id']][$value['processos_id']])) {
                $ordem = $ordensMAX[$value['prioridade_id']][$value['processos_id']];
                $ordem++;

                $novo = [
                    'desenho_id' => $value['desenho_id'],
                    'processos_id' => $value['processos_id'],
                    'prioridade_id' => $value['prioridade_id'],
                    'ordem' => $ordem,
                    'status' => 'ativo'
                ];
                $ordemModel->insert($novo);
                $ordensMAX[$value['prioridade_id']][$value['processos_id']] = $ordem;
            }
        }
    }

    public static function ordenarOrdems(?int $processoId = null)
    {
        $ordemModel = new \App\Models\Ordem();
        self::sincronizarOrdensDesenhosAtivos($processoId);

        $builder = $ordemModel->db->table('desenhos d')
            ->select('
            d.id AS desenho_id, 
            d.prioridade_id, 
            d.processos_id, 
            o.id AS ordem_id, 
            o.ordem AS ordem_atual
        ')
            ->join('ordem o', 'o.desenho_id = d.id AND o.status = "ativo"', 'left')
            ->whereIn('d.status', self::statusAtivosOrdem());

        if ($processoId !== null && $processoId > 0) {
            $builder->where('d.processos_id', $processoId);
        }

        $dados = $builder
            ->orderBy('o.ordem IS NULL', '', false) // Desenhos sem ordem no final
            ->orderBy('d.prioridade_id', 'ASC')
            ->orderBy('d.processos_id', 'ASC')
            ->orderBy('o.ordem', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($dados)) {
            return ['status' => 'ok', 'message' => 'Nenhum ajuste necessário'];
        }

        $updates = [];
        $inserts = [];

        $maxBuilder = $ordemModel->db->table('ordem')
            ->select('prioridade_id, processos_id, MAX(ordem) as max_ordem')
            ->where('status', 'ativo')
            ->groupBy('prioridade_id, processos_id');

        if ($processoId !== null && $processoId > 0) {
            $maxBuilder->where('processos_id', $processoId);
        }

        $maxOrdens = $maxBuilder
            ->get()
            ->getResultArray();

        // Converte para um mapa para acesso rápido
        $ordemMaxMap = [];
        foreach ($maxOrdens as $m) {
            $ordemMaxMap[$m['prioridade_id']][$m['processos_id']] = (int) $m['max_ordem'];
        }

        $lastPrioridade = null;
        $lastProcesso = null;
        $ordem = 0;

        foreach ($dados as $item) {
            if ($item['prioridade_id'] !== $lastPrioridade || $item['processos_id'] !== $lastProcesso) {
                $ordem = 0;
                $lastPrioridade = $item['prioridade_id'];
                $lastProcesso = $item['processos_id'];
            }

            if ($item['ordem_id']) {
                // Tem ordem → corrigir se necessário
                $ordem++;
                if ((int) $item['ordem_atual'] !== $ordem) {
                    $updates[] = [
                        'id' => $item['ordem_id'],
                        'ordem' => $ordem
                    ];
                }
            } else {
                $maxAtual = $ordemMaxMap[$item['prioridade_id']][$item['processos_id']] ?? 0;
                $novaOrdem = $maxAtual + 1;

                $ordemMaxMap[$item['prioridade_id']][$item['processos_id']] = $novaOrdem;

                $inserts[] = [
                    'desenho_id' => $item['desenho_id'],
                    'ordem' => $novaOrdem,
                    'status' => 'ativo',
                    'prioridade_id' => $item['prioridade_id'],
                    'processos_id' => $item['processos_id']
                ];
            }
        }

        // Executa transação
        $ordemModel->db->transStart();
        if (!empty($updates)) {
            $ordemModel->updateBatch($updates, 'id');
        }
        if (!empty($inserts)) {
            $ordemModel->insertBatch($inserts);
        }
        $ordemModel->db->transComplete();

        return [
            'status' => 'ok',
            '1' => $maxOrdens
        ];
    }

    // public function ordenarOrdems2()
    // {
    //     //return;
    //     $ordemModel = new \App\Models\Ordem();

    //     $dados = $ordemModel->db->table('projeto p')
    //         ->select('
    //     p.id AS projeto_id,
    //     o.ordem AS ordem, 
    //     d.prioridade_id,
    //     d.processos_id,
    //     o.id AS ordem_id,
    //     o.ordem AS ordem_atual
    // ')
    //         ->join('projeto_desenho pd', 'pd.projeto_id = p.id', 'left')
    //         ->join('desenhos d', 'pd.desenho_id = d.id', 'left')
    //         ->join('ordem o', 'o.projeto_id = p.id AND o.status = "ativo"', 'left')
    //         ->whereIn('d.status', ['pendente', 'processando'])
    //         ->groupBy('p.id')
    //         ->orderBy('ordem', 'ASC') // Primeiro os que têm menor ordem
    //         ->get()
    //         ->getResultArray();



    //     if (empty($dados)) {
    //         return ['status' => 'ok', 'message' => 'Nenhum ajuste necessário'];
    //     }

    //     $updates = [];
    //     $inserts = [];

    //     $maxOrdens = $ordemModel->db->table('ordem')
    //         ->select('prioridade_id, processos_id, MAX(ordem) as max_ordem')
    //         ->where('status', 'ativo')
    //         ->groupBy('prioridade_id, processos_id')
    //         ->get()
    //         ->getResultArray();

    //     // Converte para um mapa para acesso rápido
    //     $ordemMaxMap = [];
    //     foreach ($maxOrdens as $m) {
    //         $ordemMaxMap[$m['prioridade_id']][$m['processos_id']] = (int) $m['max_ordem'];
    //     }

    //     $lastPrioridade = null;
    //     $lastProcesso = null;
    //     $ordem = 0;

    //     foreach ($dados as $item) {
    //         if ($item['prioridade_id'] !== $lastPrioridade || $item['processos_id'] !== $lastProcesso) {
    //             $ordem = 0;
    //             $lastPrioridade = $item['prioridade_id'];
    //             $lastProcesso = $item['processos_id'];
    //         }

    //         if ($item['ordem_id']) {
    //             // Tem ordem → corrigir se necessário
    //             $ordem++;
    //             if ((int) $item['ordem_atual'] !== $ordem) {
    //                 $updates[] = [
    //                     'id' => $item['ordem_id'],
    //                     'ordem' => $ordem
    //                 ];
    //             }
    //         } else {
    //             $maxAtual = $ordemMaxMap[$item['prioridade_id']][$item['processos_id']] ?? 0;
    //             $novaOrdem = $maxAtual + 1;

    //             $ordemMaxMap[$item['prioridade_id']][$item['processos_id']] = $novaOrdem;

    //             $inserts[] = [
    //                 'projeto_id' => $item['projeto_id'],
    //                 'ordem' => $novaOrdem,
    //                 'status' => 'ativo',
    //                 'prioridade_id' => $item['prioridade_id'],
    //                 'processos_id' => $item['processos_id']
    //             ];
    //         }
    //     }

    //     // Executa transação
    //     $ordemModel->db->transStart();
    //     if (!empty($updates)) {
    //         $ordemModel->updateBatch($updates, 'id');
    //     }
    //     if (!empty($inserts)) {
    //         $ordemModel->insertBatch($inserts);
    //     }
    //     $ordemModel->db->transComplete();

    //     return [
    //         'status' => 'ok',
    //         '1' => $dados,
    //         '2' =>$maxOrdens
    //     ];
    // }
    public static function ordenarOrdems2()
    {
        $db = \Config\Database::connect();
        $ordemModel = new \App\Models\Ordem();
        $projetoModel = new \App\Models\Projeto();

        $out = [
            'status' => 'ok',
            'ids_desativadas' => [],
            'ids_inseridas' => [],
            'grupos_processados' => 0,
            'grupos_reordenados' => 0,
            'detalhes_reordenacao' => [],
        ];

        // Evita que warnings derrubem a transação inteira
        $db->transStrict(false);
        $db->transBegin();

        try {
            /* ============================================================
             * A) DESATIVAR ordens ATIVAS de projetos FINALIZADOS
             *    ordem.projeto_id -> projeto.id (status='finalizado')
             * ============================================================ */
            $q1 = $db->table('ordem')
                ->select('id')
                ->where('status', 'ativo')
                ->whereIn('projeto_id', function ($q) {
                    $q->from('projeto')
                        ->select('id')
                        ->where('status', 'finalizado');
                })
                ->get();

            if ($q1 === false) {
                $err = $db->error();
                throw new \RuntimeException('Erro A1 (SELECT idsDesativar): ' . $err['message']);
            }

            $idsDesativar = $q1->getResultArray();
            if (!empty($idsDesativar)) {
                $ids = array_column($idsDesativar, 'id');
                $ok = $db->table('ordem')
                    ->whereIn('id', $ids)
                    ->set('status', 'desativado')
                    ->update();
                if ($ok === false) {
                    $err = $db->error();
                    throw new \RuntimeException('Erro A2 (UPDATE desativar): ' . $err['message']);
                }
                $out['ids_desativadas'] = $ids;
            }

            /* ============================================================
             * B) INSERIR 1 ordem padrão para cada projeto NÃO FINALIZADO
             *    que NÃO possui nenhuma ordem ATIVA
             *    (preenche data_add para evitar NOT NULL)
             * ============================================================ */
            $processoPadrao = 1;
            $prioridadePadrao = 1;
            $ordemInicial = 1;
            $agora = date('Y-m-d H:i:s');

            $q2 = $db->table('projeto')
                ->select('id AS projeto_id')
                ->where('status !=', 'finalizado')
                ->whereNotIn('id', function ($q) {
                    $q->from('ordem')
                        ->distinct()
                        ->select('projeto_id')
                        ->where('status', 'ativo')
                        ->where('projeto_id IS NOT NULL', null, false);
                })
                ->get();

            if ($q2 === false) {
                $err = $db->error();
                throw new \RuntimeException('Erro B1 (SELECT projetos sem ordem ativa): ' . $err['message']);
            }

            $projetosSemOrdem = $q2->getResultArray();
            if (!empty($projetosSemOrdem)) {
                $rows = [];
                foreach ($projetosSemOrdem as $p) {
                    $rows[] = [
                        'desenho_id' => null,                 // ajuste se necessário
                        'projeto_id' => (int) $p['projeto_id'],
                        'prioridade_id' => $prioridadePadrao,
                        'ordem' => $ordemInicial,        // posição inicial
                        'processos_id' => $processoPadrao,
                        'status' => 'ativo',
                        'data_add' => $agora,               // IMPORTANTE p/ NOT NULL
                    ];
                }
                if (!empty($rows)) {
                    $ok = $db->table('ordem')->insertBatch($rows);
                    if ($ok === false) {
                        $err = $db->error();
                        throw new \RuntimeException('Erro B2 (INSERT ordens padrão): ' . $err['message']);
                    }
                    $out['ids_inseridas'] = array_column($projetosSemOrdem, 'projeto_id');
                }
            }

            /* ============================================================
             * C) REORDENAR 1..N por (processos_id, prioridade_id) no campo "ordem"
             *    — apenas quem precisar
             * ============================================================ */
            $q3 = $db->table('ordem')
                ->select('processos_id, prioridade_id')
                ->where('status', 'ativo')
                ->where('projeto_id IS NOT NULL', null, false)
                ->groupBy(['processos_id', 'prioridade_id'])
                ->get();

            if ($q3 === false) {
                $err = $db->error();
                throw new \RuntimeException('Erro C1 (SELECT grupos): ' . $err['message']);
            }

            $grupos = $q3->getResultArray();

            foreach ($grupos as $g) {
                $out['grupos_processados']++;

                $procId = (int) $g['processos_id'];
                $prioId = (int) $g['prioridade_id'];

                $q4 = $db->table('ordem')
                    ->select('id, ordem')
                    ->where('status', 'ativo')
                    ->where('processos_id', $procId)
                    ->where('prioridade_id', $prioId)
                    ->where('projeto_id IS NOT NULL', null, false)
                    ->orderBy('CASE WHEN ordem IS NULL OR ordem <= 0 THEN 999999999 ELSE ordem END', 'ASC', false)
                    ->orderBy('id', 'ASC')
                    ->get();

                if ($q4 === false) {
                    $err = $db->error();
                    throw new \RuntimeException("Erro C2 (SELECT ordens grupo {$procId}-{$prioId}): " . $err['message']);
                }

                $ordensGrupo = $q4->getResultArray();

                $batch = [];
                $novaPos = 1;
                foreach ($ordensGrupo as $r) {
                    $old = (int) ($r['ordem'] ?? 0);
                    if ($old !== $novaPos) {
                        $batch[] = ['id' => (int) $r['id'], 'ordem' => $novaPos];
                    }
                    $novaPos++;
                }

                if (!empty($batch)) {
                    foreach (array_chunk($batch, 1000) as $chunk) {
                        $ok = $db->table('ordem')->updateBatch($chunk, 'id');
                        if ($ok === false) {
                            $err = $db->error();
                            throw new \RuntimeException("Erro C3 (UPDATE batch grupo {$procId}-{$prioId}): " . $err['message']);
                        }
                    }
                    $out['grupos_reordenados']++;
                    $out['detalhes_reordenacao'][] = [
                        'grupo' => "{$procId}-{$prioId}",
                        'count' => count($batch),
                    ];
                }
            }

            // ===== Commit com diagnóstico detalhado se der ruim =====
            if (!$db->transStatus()) {
                $err = $db->error();
                $db->transRollback();
                $out['status'] = 'erro';
                $out['erro_msg'] = 'Falha na transação: ' . ($err['message'] ?? 'sem detalhe');
                $out['last_query'] = method_exists($db, 'getLastQuery') && $db->getLastQuery()
                    ? (string) $db->getLastQuery()
                    : null;
                return $out;
            }

            $db->transCommit();
            return $out;
        } catch (\Throwable $e) {
            $db->transRollback();
            return [
                'status' => 'erro',
                'erro_msg' => $e->getMessage(),
                'ids_desativadas' => $out['ids_desativadas'] ?? [],
                'ids_inseridas' => $out['ids_inseridas'] ?? [],
                'grupos_processados' => $out['grupos_processados'] ?? 0,
                'grupos_reordenados' => $out['grupos_reordenados'] ?? 0,
                'detalhes_reordenacao' => $out['detalhes_reordenacao'] ?? [],
            ];
        }
    }




    public static function envia_email($attributes = [])
    {
        require_once APPPATH . 'Libraries/phpmailer/src/PHPMailer.php';
        require_once APPPATH . 'Libraries/phpmailer/src/SMTP.php';
        require_once APPPATH . 'Libraries/phpmailer/src/Exception.php';

        $email = new PHPMailer(true);

        $host = trim((string) env('mail.smtp.host', ''));
        $port = (int) env('mail.smtp.port', 587);
        $secure = strtolower(trim((string) env('mail.smtp.secure', 'tls')));
        $username = trim((string) env('mail.smtp.user', ''));
        $password = (string) env('mail.smtp.pass', '');
        $authType = trim((string) env('mail.smtp.auth_type', ''));
        $timeout = (int) env('mail.smtp.timeout', 15);

        $fromAddress = trim((string) env('mail.from.address', $username));
        $fromName = trim((string) env('mail.from.name', 'Sitema de Corte'));

        $allowSelfSignedRaw = strtolower(trim((string) env('mail.smtp.allow_self_signed', 'false')));
        $allowSelfSigned = in_array($allowSelfSignedRaw, ['1', 'true', 'yes', 'on'], true);

        if ($host === '' || $fromAddress === '') {
            throw new \RuntimeException('Configuracao de e-mail ausente: defina mail.smtp.host e mail.from.address no .env');
        }

        if ($username === '' || $password === '') {
            throw new \RuntimeException('Configuracao SMTP incompleta: defina mail.smtp.user e mail.smtp.pass no .env');
        }

        $email->isSMTP();
        $email->Host = $host;
        $email->Port = $port > 0 ? $port : 587;
        $email->SMTPAuth = true;
        $email->Username = $username;
        $email->Password = $password;
        $email->Timeout = $timeout > 0 ? $timeout : 15;
        $email->CharSet = 'UTF-8';
        $email->isHTML(true);
        $email->SMTPAutoTLS = true;

        if ($authType !== '') {
            $email->AuthType = $authType;
        }

        if ($secure === 'ssl' || $secure === 'smtps') {
            $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($secure === 'none' || $secure === 'off') {
            $email->SMTPSecure = false;
            $email->SMTPAutoTLS = false;
        } else {
            $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        if ($allowSelfSigned) {
            $email->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $email->setFrom($fromAddress, $fromName);
        $email->addAddress((string) ($attributes['to'] ?? ''));
        $email->Subject = (string) ($attributes['subject'] ?? '');
        $email->Body = (string) ($attributes['message'] ?? '');
        $email->AltBody = strip_tags((string) ($attributes['message'] ?? ''));

        return (bool) $email->send();
    }

    function send_email_phpmail($to_email = NULL, $to_name = NULL, $title = NULL, $content = NULL)
    {

        require_once APPPATH . 'Libraries/phpmailer/src/PHPMailer.php';
        require_once APPPATH . 'Libraries/phpmailer/src/SMTP.php';
        require_once APPPATH . 'Libraries/phpmailer/src/Exception.php';

        $mail = new PHPMailer(true);
        // SMTP configuration
        $mail->IsSMTP();
        $mail->Host = 'smtp.rpps.com.br';
        $mail->Username = 'sistema@rpps.com.br';
        $mail->Password = '@W3bpr3vMails@';
        $mail->Port = 587;
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('sistema@rpps.com.br', 'Sitema de Corte');

        // Add a recipient
        $mail->addAddress($to_email, $to_name);

        // Email subject
        $mail->Subject = $title;

        // Set email format to HTML
        $mail->isHTML(true);

        // Email body content
        $mailContent = $content;
        $mail->Body = $mailContent;

        // Send email
        return $mail->send();

        /*if($mail->send()){
            return;
        }else{
            return;
        }*/
    }














public function ordenar()
{
    $processosId = 2;

    $db = \Config\Database::connect();
    $ordemModel = new \App\Models\Ordem();

    // ========= CONFIG DEBUG =========
    $DEBUG = true; // mude para false quando estiver ok
    $log = function ($titulo, $data = null) use ($DEBUG) {
        if (!$DEBUG) return;
        echo "\n\n==================== {$titulo} ====================\n";
        if ($data !== null) {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }
    };

    // ========= 1) BUSCA PROJETOS VÁLIDOS + PRIORIDADE (vem dos desenhos) =========
    $Projetos = $db->table('projeto')
        ->select('projeto.id, projeto.data_add, MIN(desenhos.prioridade_id) AS prioridade_id')
        ->join('projeto_desenho', 'projeto_desenho.projeto_id = projeto.id', 'inner')
        ->join('desenhos', 'desenhos.id = projeto_desenho.desenho_id', 'inner')
        ->where('projeto.status', 'ativo')
        ->where('desenhos.processos_id', $processosId)
        ->groupBy('projeto.id, projeto.data_add')
        ->orderBy('projeto.data_add', 'ASC')
        ->get()
        ->getResultArray();

    $log('DEBUG 1 - Projetos válidos (com prioridade MIN)', $Projetos);

    if (!$Projetos) {
        $log('DEBUG - Nenhum projeto válido encontrado. Saindo.');
        echo "<pre>"; print_r([]); echo "</pre>";
        return;
    }

    $idsProjetos = array_values(array_unique(array_column($Projetos, 'id')));

    $log('DEBUG 2 - IDs dos projetos válidos', $idsProjetos);

    // ========= 2) CONTAGEM ATUAL DE ORDENS ATIVAS =========
    $ativoProjeto = $db->table('ordem')
        ->where('status', 'ativo')
        ->where('processos_id', $processosId)
        ->where('desenho_id', null)
        ->countAllResults();

    $ativoAvulso = $db->table('ordem')
        ->where('status', 'ativo')
        ->where('processos_id', $processosId)
        ->where('desenho_id IS NOT NULL', null, false)
        ->countAllResults();

    $log('DEBUG 3 - Contagem ATIVO', [
        'ATIVO (PROJETO - desenho_id NULL)' => $ativoProjeto,
        'ATIVO (AVULSO  - desenho_id NOT NULL)' => $ativoAvulso
    ]);

    // ========= 3) GARANTE 1 ORDEM ATIVA POR PROJETO (sem criar duplicado infinito) =========
    foreach ($Projetos as &$p) {
        $projetoId = (int)$p['id'];
        $prioridadeId = (int)$p['prioridade_id'];

        // A) existe ordem ativa?
        $ativa = $ordemModel
            ->select('id, ordem, status')
            ->where('processos_id', $processosId)
            ->where('prioridade_id', $prioridadeId)
            ->where('projeto_id', $projetoId)
            ->where('desenho_id', null)
            ->where('status', 'ativo')
            ->first();

        if ($ativa) {
            $p['acao'] = 'existia';
            $p['tem_ordem'] = 1;
            $p['ordem_valor'] = (int)$ativa['ordem'];
            continue;
        }

        // B) não existe ativa -> tenta reativar uma desativada (a mais recente)
        $desativada = $ordemModel
            ->select('id, ordem, status')
            ->where('processos_id', $processosId)
            ->where('prioridade_id', $prioridadeId)
            ->where('projeto_id', $projetoId)
            ->where('desenho_id', null)
            ->where('status', 'desativado') // seu banco usa "desativado"
            ->orderBy('id', 'DESC')
            ->first();

        if ($desativada) {
            $ordemModel->update($desativada['id'], ['status' => 'ativo']);

            $p['acao'] = 'reativado';
            $p['tem_ordem'] = 0;
            $p['ordem_valor'] = (int)$desativada['ordem'];
            continue;
        }

        // C) não existe nenhuma linha -> cria no final da fila daquela prioridade
        $rowMax = $ordemModel
            ->select('MAX(ordem) AS max_ordem')
            ->where('status', 'ativo')
            ->where('processos_id', $processosId)
            ->where('prioridade_id', $prioridadeId)
            ->where('desenho_id', null)
            ->get()
            ->getRowArray();

        $proxima = ((int)($rowMax['max_ordem'] ?? 0)) + 1;

        $ok = $ordemModel->insert([
            'projeto_id'    => $projetoId,
            'processos_id'  => $processosId,
            'prioridade_id' => $prioridadeId,
            'ordem'         => $proxima,
            'status'        => 'ativo',
            'desenho_id'    => null,
        ]);

        $p['acao'] = $ok ? 'criado' : 'erro_insert';
        $p['tem_ordem'] = 0;
        $p['ordem_valor'] = $proxima;

        if ($ok === false) {
            $log("ERRO INSERT - projeto {$projetoId}", $ordemModel->errors());
        }
    }

    $log('DEBUG 4 - Projetos após garantir ordem ativa (antes de normalizar)', $Projetos);

    // ========= 4) NORMALIZA (fecha buracos) POR PRIORIDADE - FILA PROJETO =========
    $rows = $ordemModel
        ->select('id, projeto_id, prioridade_id, ordem, status')
        ->where('status', 'ativo')
        ->where('processos_id', $processosId)
        ->where('desenho_id', null)
        ->orderBy('prioridade_id', 'ASC')
        ->orderBy('ordem', 'ASC')
        ->orderBy('id', 'ASC')
        ->findAll();

    $log('DEBUG 5 - Ordens ATIVAS (PROJETO) antes da normalização', $rows);

    $porPrioridade = [];
    foreach ($rows as $r) {
        $pid = (int)($r['prioridade_id'] ?? 0);
        $porPrioridade[$pid][] = $r;
    }

    $updates = [];
    foreach ($porPrioridade as $prioridadeId => $lista) {
        $esperado = 1;
        foreach ($lista as $item) {
            $atual = (int)$item['ordem'];
            if ($atual !== $esperado) {
                $ordemModel->update($item['id'], ['ordem' => $esperado]);
                $updates[] = [
                    'id' => $item['id'],
                    'prioridade_id' => $prioridadeId,
                    'projeto_id' => $item['projeto_id'],
                    'de' => $atual,
                    'para' => $esperado
                ];
            }
            $esperado++;
        }
    }

    $log('DEBUG 6 - Updates feitos na normalização', $updates);

    // ========= 5) LÊ DE NOVO e injeta ordem_valor real para retornar certinho =========
    $ordensDepois = $ordemModel
        ->select('projeto_id, prioridade_id, ordem')
        ->where('status', 'ativo')
        ->where('processos_id', $processosId)
        ->where('desenho_id', null)
        ->findAll();

    $mapa = [];
    foreach ($ordensDepois as $o) {
        $mapa[(int)$o['projeto_id'] . '|' . (int)$o['prioridade_id']] = (int)$o['ordem'];
    }

    foreach ($Projetos as &$p) {
        $chave = (int)$p['id'] . '|' . (int)$p['prioridade_id'];
        if (isset($mapa[$chave])) {
            $p['ordem_valor'] = $mapa[$chave];
        }
    }

    $log('DEBUG 7 - Ordens ATIVAS (PROJETO) depois da normalização', $ordensDepois);
    $log('RESULT FINAL - Projetos', $Projetos);

    echo "<pre>";
    print_r($Projetos);
    echo "</pre>";
}






}
