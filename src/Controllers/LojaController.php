<?php
namespace Controllers;

use Models\Produto;
use Models\Categoria;

class LojaController {
    
    public function index() {
        $produtoModel = new Produto();
        $categoriaModel = new Categoria();

        $categorias = $categoriaModel->getAll();
        
        $categoriaClicada = $_GET['categoria'] ?? '';

        if (!empty($categoriaClicada)) {
            $produtos = $produtoModel->buscarPorCategoria($categoriaClicada);
        } else {
            $produtos = $produtoModel->getAll(); 
        }

        require __DIR__ . '/../Views/loja/index.php';
    }

    public function detalhe() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $produtoModel = new Produto();
        $produto = $produtoModel->find($id);

        if (!$produto) {
            http_response_code(404);
            echo "Produto não encontrado.";
            return;
        }

        require __DIR__ . '/../Views/loja/produto-detalhe.php';
    }

    public function processarPagamento() {
    if (ob_get_length()) ob_clean();

    try {
        // Lê os dados enviados diretamente pelo formulário POST tradicional
        $body = $_POST;

        if (empty($body) || empty($body['cpf'])) {
            die("Erro: Nenhum dado de pagamento recebido.");
        }

        // Chave de API do Asaas (Sandbox)
        $config = include __DIR__ . '/../../config.php'; // Ajuste os '..' conforme a pasta onde estiver
        $apiKey = $config['asaas_api_key'];
        $userAgent = "MinhaLojaVirtualPHP";

        // 1. Criar o cliente no Asaas
        $cpfLimpo = preg_replace('/[^0-9]/', '', $body['cpf']);
        $urlCliente = "https://sandbox.asaas.com/api/v3/customers";
        
        $dadosCliente = [
            "name" => $_SESSION['cliente_nome'] ?? 'Cliente Teste',
            "email" => "cliente" . ($_SESSION['cliente_id'] ?? '1') . "@loja.com",
            "cpfCnpj" => $cpfLimpo
        ];

        $ch = curl_init($urlCliente);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dadosCliente));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "access_token: " . $apiKey,
            "User-Agent: " . $userAgent
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $responseCliente = curl_exec($ch);
        $resCliente = json_decode($responseCliente, true);
        $customerId = $resCliente['id'] ?? null;
        curl_close($ch);

        if (!$customerId) {
            die("Erro ao registrar cliente no Asaas: " . htmlspecialchars($responseCliente));
        }

        // 2. Criar a Cobrança no Asaas
        $urlPagamento = "https://sandbox.asaas.com/api/v3/payments";
        
        // Calcula o valor total do carrinho atual
        $totalCarrinho = 0.0;
        if (!empty($_SESSION['carrinho'])) {
            $prodModelTemp = new \Models\Produto();
            foreach ($_SESSION['carrinho'] as $pId => $qtd) {
                $q = is_array($qtd) ? ($qtd['quantidade'] ?? $qtd['qtd'] ?? 1) : (int)$qtd;
                $prod = method_exists($prodModelTemp, 'getById') ? $prodModelTemp->getById($pId) : null;
                if ($prod) {
                    $preco = (float)str_replace(',', '.', $prod['preco'] ?? $prod['valor'] ?? 0);
                    $totalCarrinho += ($preco * $q);
                }
            }
        }
        if ($totalCarrinho <= 0) $totalCarrinho = 399.90; // Fallback de segurança

        $dadosCobranca = [
            "customer" => $customerId,
            "billingType" => $body['forma_pagamento'] ?? "PIX",
            "value" => $totalCarrinho,
            "dueDate" => date('Y-m-d', strtotime('+1 day')),
            "description" => "Compra na Loja Virtual",
            "externalReference" => "pedido_" . time()
        ];

        $ch = curl_init($urlPagamento);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dadosCobranca));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "access_token: " . $apiKey,
            "User-Agent: " . $userAgent
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $responsePagamento = curl_exec($ch);
        $httpCodePagamento = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $resultadoPagamento = json_decode($responsePagamento, true);
        curl_close($ch);

        if ($httpCodePagamento >= 200 && $httpCodePagamento < 300 && isset($resultadoPagamento['id'])) {
            
            // Esvazia o carrinho imediatamente
            unset($_SESSION['carrinho']);

            // Pega o link do Asaas
            $urlAsaas = $resultadoPagamento['invoiceUrl'] ?? $resultadoPagamento['bankSlipUrl'] ?? null;

            if ($urlAsaas) {
                // Redireciona o navegador diretamente para o Asaas de forma nativa!
                header("Location: " . $urlAsaas);
                exit;
            }
        }

        // Se falhar
        echo "Erro ao gerar cobrança: " . htmlspecialchars($responsePagamento);
        exit;

    } catch (\Exception $e) {
        echo "Erro interno: " . $e->getMessage();
        exit;
    }
}
}