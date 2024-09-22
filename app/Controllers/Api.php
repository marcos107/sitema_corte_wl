<?php

namespace App\Controllers;

use App\Controllers\Ferramentas;
use Config\App;

class Api extends Ferramentas
{
    function comecar_conversa()
    {
        // Verificar se a requisição é POST e o conteúdo é JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $key_validacao = random_bytes(32);
            $key_ping = random_bytes(12);  // 12 bytes = 96 bits (Nonce para a versão IETF)
            // 1ª rodada: Gerar chave e IV para AES-256-CBC
            $aes_secret_key = random_bytes(32); // 32 bytes = 256 bits
            $aes_iv = random_bytes(16);  // 16 bytes = 128 bits (IV)

            // Criptografia com AES-256-CBC
            $encrypted_with_aes = openssl_encrypt("confirmando linha", 'aes-256-cbc', $aes_secret_key, OPENSSL_RAW_DATA, $aes_iv);

            // Concatenar $str.$aes_secret_key . $aes_iv diretamente
            $combined_str = $encrypted_with_aes . $aes_secret_key . $aes_iv;

            // 2ª rodada: Criptografia com ChaCha20-Poly1305 (IETF) usando a string concatenada
            $chacha_encrypted = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
                $combined_str,          // Concatenar mensagem criptografada com AES, chave e IV
                '',                     // AAD (Additional Authenticated Data) - opcional
                $key_ping,              // Nonce (12 bytes)
                $key_validacao          // Chave secreta (32 bytes)
            );

            // 32 bits aleatórios (4 bytes)
            $random_bits_32 = random_bytes(4); // Adiciona 32 bits aleatórios para confundir

            // 64 bits aleatórios (8 bytes) para mais confusão
            $random_bits_64 = random_bytes(8);

            // Inserir os 64 bits aleatórios no início da string para segurança
            $combined_with_64_bits = $random_bits_64 . $chacha_encrypted;

            // Adicionar $key_validacao e $key_ping concatenados ao final da string
            $final_str = $combined_with_64_bits . $key_ping . $key_validacao;

            // Codificar tudo em base64
            

            // Retornar uma resposta JSON
            return $this->response->setJSON([base64_encode($final_str)]);
        }

        // Se a requisição não for POST ou os dados estiverem vazios
        return $this->response->setJSON(['status' => $_SERVER['REQUEST_METHOD'], 'mensagem' => 'Requisição inválida']);
    }
}
