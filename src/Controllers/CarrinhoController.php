<?php
namespace Controllers;

use Models\Produto;

class CarrinhoController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $carrinhoSessao = $_SESSION['carrinho'] ?? [];
        $itens = [];
        $total = 0.0;

        $produtoModel = new Produto();

        foreach ($carrinhoSessao as $produtoId => $qtd) {
            $quantidade = is_array($qtd) ? ($qtd['quantidade'] ?? $qtd['qtd'] ?? 1) : (int)$qtd;

            if ($quantidade <= 0) continue;

            $produto = null;
            if (method_exists($produtoModel, 'getById')) {
                $produto = $produtoModel->getById($produtoId);
            } elseif (method_exists($produtoModel, 'find')) {
                $produto = $produtoModel->find($produtoId);
            } elseif (method_exists($produtoModel, 'getAll')) {
                $todos = $produtoModel->getAll();
                foreach ($todos as $p) {
                    if (($p['id'] ?? null) == $produtoId) {
                        $produto = $p;
                        break;
                    }
                }
            }

            if ($produto) {
                $precoBruto = $produto['preco'] ?? $produto['valor'] ?? 0;
                $precoFloat = (float)$precoBruto;

                $subtotal = $precoFloat * $quantidade;
                $total += $subtotal;

                $itens[] = [
                    'id' => $produto['id'] ?? $produtoId,
                    'nome' => $produto['nome'] ?? 'Produto',
                    'preco' => $precoFloat,
                    'imagem' => $produto['imagem'] ?? '',
                    'quantidade' => $quantidade,
                    'subtotal' => $subtotal
                ];
            }
        }

        require __DIR__ . '/../Views/loja/carrinho.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoId = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT) ?? 1;

            if ($produtoId) {
                if (!isset($_SESSION['carrinho'])) {
                    $_SESSION['carrinho'] = [];
                }

                if (isset($_SESSION['carrinho'][$produtoId])) {
                    if (is_array($_SESSION['carrinho'][$produtoId])) {
                        $_SESSION['carrinho'][$produtoId]['quantidade'] += $quantidade;
                    } else {
                        $_SESSION['carrinho'][$produtoId] += $quantidade;
                    }
                } else {
                    $_SESSION['carrinho'][$produtoId] = $quantidade;
                }
            }
        }

        header('Location: index.php?route=carrinho-ver');
        exit;
    }

    public function remove() {
        $produtoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($produtoId && isset($_SESSION['carrinho'][$produtoId])) {
            unset($_SESSION['carrinho'][$produtoId]);
        }

        header('Location: index.php?route=carrinho-ver');
        exit;
    }

    public function clear() {
        $_SESSION['carrinho'] = [];
        header('Location: index.php?route=carrinho-ver');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quantidades = $_POST['quantidades'] ?? $_POST['quantidade'] ?? [];

            foreach ($quantidades as $produtoId => $qtd) {
                $qtd = (int)$qtd;
                if ($qtd <= 0) {
                    unset($_SESSION['carrinho'][$produtoId]);
                } else {
                    $_SESSION['carrinho'][$produtoId] = $qtd;
                }
            }
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit;
        }

        header('Location: index.php?route=carrinho-ver');
        exit;
    }

    public function checkout() {
    if (empty($_SESSION['carrinho'])) {
        header('Location: index.php?route=loja');
        exit;
    }
    require __DIR__ . '/../Views/loja/checkout.php';
}

public function finalizarPedido() {

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o cliente NÃO estiver logado
if (!isset($_SESSION['cliente_id'])) {
    // Guarda a página atual para onde ele queria ir para redirecionar depois do login
    $_SESSION['redirect_after_login'] = 'index.php?route=checkout'; // ou o nome da sua rota de pagamento
    
    header("Location: index.php?route=login");
    exit;
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $endereco = trim($_POST['endereco'] ?? '');
        $pagamento = trim($_POST['pagamento'] ?? 'dinheiro');
        
        $carrinho = $_SESSION['carrinho'] ?? [];
        if (empty($carrinho)) {
            header('Location: index.php?route=loja');
            exit;
        }

        $produtoModel = new \Models\Produto();
        $vendaModel = new \Models\Venda();

        $valorTotal = 0;
        $itensPedido = [];

        foreach ($carrinho as $pId => $qtd) {
            $quantidade = is_array($qtd) ? ($qtd['quantidade'] ?? $qtd['qtd'] ?? 1) : (int)$qtd;
            
            // Busca o produto
            $produto = null;
            if (method_exists($produtoModel, 'getById')) {
                $produto = $produtoModel->getById($pId);
            } elseif (method_exists($produtoModel, 'find')) {
                $produto = $produtoModel->find($pId);
            }

            if ($produto) {
                $precoBruto = $produto['preco'] ?? $produto['valor'] ?? 0;
                $precoFloat = (float)str_replace(',', '.', $precoBruto);
                
                $subtotal = $precoFloat * $quantidade;
                $valorTotal += $subtotal;

                $itensPedido[] = [
                    'produto_id' => $pId,
                    'quantidade' => $quantidade,
                    'preco' => $precoFloat
                ];
            }
        }

        // Salva a venda na base de dados
        if (method_exists($vendaModel, 'criar')) {
            $vendaModel->criar([
                'cliente_nome' => $nome,
                'cliente_email' => $email,
                'cliente_telefone' => $telefone,
                'endereco' => $endereco,
                'forma_pagamento' => $pagamento,
                'valor_total' => $valorTotal,
                'itens' => $itensPedido
            ]);
        }

        // Limpa o carrinho após finalizar
        unset($_SESSION['carrinho']);

        echo "<script>alert('Pedido realizado com sucesso! Obrigado pela compra.'); window.location.href='index.php?route=loja';</script>";
        exit;
    }
}
}