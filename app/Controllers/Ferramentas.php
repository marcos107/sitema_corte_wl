<?php

namespace App\Controllers;

class Ferramentas extends BaseController
{

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
                        $p = array_merge($p, map_pasta($pasta . $arquivo . '/', $p));
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
        // Divide o caminho em diretórios individuais
        $diretorios = explode('/', $caminho);
        $path = '';
        $erro = array();

        foreach ($diretorios as $diretorio) {
            $path .= $diretorio . '/';

            // Verifica se o diretório não existe
            if (!is_dir($path)) {
                // Tenta criar o diretório com permissões 0777
                if (!mkdir($path, 0777)) {
                    // Se a criação falhar, adiciona uma mensagem de erro ao array de erros
                    $erro[] = ('Falha ao criar o diretório: ' . $path);
                }
            }
        }
        return $erro;
    }




    /**
     * Codifica todas as strings de um array substituindo os caracteres abaixo:
     *  Á , á , É , é , Í , í , Ó , ó , Ú , ú , À , à , È , è , Ì , ì , Ò , ò , Ù , ù ,   , â , Ê , ê ,
     *  Î , î , Ô , ô , Û , û , Ã , ã , Ñ , ñ , Õ , õ , Ç , ç , Ä , ä , Ë , ë , Ï , ï , Ö , ö , Ü , ü ,
     *  Ÿ , ÿ , À , à , È , è , Ì , ì , Ò , ò , Ù , ù , / , . , , , % , $ , # , ! , @ , & , * , ( , ) ,
     *  - , _ , + , = , { , } , [ , ] , | , \ , : , ; , " ," ", < , > , ? , ~ , ^ , ` , ´
     * por sequências como "i001n", "i002n", "i003n" e assim por diante.
     *
     * @param $array array para ser codificada as strings.
     * @return string array codificada.
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
                            $db = new \App\Models\Tag();
                            $item = "tag";
                            break;
                        default:
                            $data = [
                                //caso não exista retorna que deu errado
                                "ok" => false,
                            ];
                            return $this->response->setJSON($data);
                            break;
                    }
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
                    $alteracao->insert($data);

                    // Atualiza o status no banco de dados
                    $db->update(Ferramentas::array_index($lista, [$id]), ['status' => $status]); //faz o update no banco e troca o id falso pelo verdadeiro
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
}
