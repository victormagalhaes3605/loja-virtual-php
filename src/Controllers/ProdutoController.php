<?php

namespace Controllers;

use Models\Produto;

class ProdutoController {

    /**
     * Lista todos os produtos no painel administrativo
     */
    public function index() {
        $produtoModel = new \Models\Produto();
        
        // Agora temos a certeza que o método getAll() existe!
        $produtos = $produtoModel->getAll();

        // Carrega a tela
        require __DIR__ . '/../Views/produtos/index.php';
    }

    /**
     * Exibe o formulário de criação de produto
     */
    public function create() {
        require __DIR__ . '/../Views/admin/produtos/criar.php';
    }

    /**
     * Salva um novo produto no banco de dados
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $categoria = trim($_POST['categoria'] ?? ''); // NOVO CAMPO: Captura a categoria
            $precoInput = $_POST['preco'] ?? '0';
            $estoque = (int)($_POST['estoque'] ?? 0);
            $ativo = isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1;

            // -------------------------------------------------------------
            // TRATAMENTO DO PREÇO (Evita transformar 10 em 1000)
            // -------------------------------------------------------------
            $precoFinal = $this->formatarPrecoParaBanco($precoInput);

            // -------------------------------------------------------------
            // UPLOAD DE IMAGEM
            // -------------------------------------------------------------
            $nomeImagem = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $nomeImagem = 'prod_' . uniqid() . '.' . strtolower($extensao);
                $destino = __DIR__ . '/../../public/uploads/' . $nomeImagem;

                if (!is_dir(__DIR__ . '/../../public/uploads')) {
                    mkdir(__DIR__ . '/../../public/uploads', 0777, true);
                }

                move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
            }

            $produtoModel = new Produto();
            
            $dados = [
                'nome'      => $nome,
                'categoria' => $categoria, // NOVO CAMPO: Envia para o Model
                'preco'     => $precoFinal,
                'estoque'   => $estoque,
                'imagem'    => $nomeImagem,
                'ativo'     => $ativo
            ];

            if (method_exists($produtoModel, 'criar')) {
                $produtoModel->criar($dados);
            } elseif (method_exists($produtoModel, 'save')) {
                $produtoModel->save($dados);
            } elseif (method_exists($produtoModel, 'insert')) {
                $produtoModel->insert($dados);
            }

            header('Location: index.php?route=produtos');
            exit;
        }
    }

    /**
     * Exibe o formulário de edição do produto
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $produtoModel = new Produto();
        $produto = null;

        if (method_exists($produtoModel, 'getById')) {
            $produto = $produtoModel->getById($id);
        } elseif (method_exists($produtoModel, 'find')) {
            $produto = $produtoModel->find($id);
        }

        if (!$produto) {
            header('Location: index.php?route=produtos');         
           exit;
        }

        // CAMINHO CORRIGIDO DE ACORDO COM A SUA ESTRUTURA
        require __DIR__ . '/../Views/produtos/edit.php';
    }

    /**
     * Atualiza os dados do produto no banco de dados
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $categoria = trim($_POST['categoria'] ?? ''); // NOVO CAMPO: Captura a categoria
            $precoInput = $_POST['preco'] ?? '0';
            $estoque = (int)($_POST['estoque'] ?? 0);
            $ativo = isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1;

            // -------------------------------------------------------------
            // TRATAMENTO DO PREÇO (Evita transformar 10 em 1000)
            // -------------------------------------------------------------
            $precoFinal = $this->formatarPrecoParaBanco($precoInput);

            $produtoModel = new Produto();
            $produtoAtual = null;
            if (method_exists($produtoModel, 'getById')) {
                $produtoAtual = $produtoModel->getById($id);
            } elseif (method_exists($produtoModel, 'find')) {
                $produtoAtual = $produtoModel->find($id);
            }

            $nomeImagem = $produtoAtual['imagem'] ?? null;

            // Se enviou uma imagem nova
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $nomeImagem = 'prod_' . uniqid() . '.' . strtolower($extensao);
                $destino = __DIR__ . '/../../public/uploads/' . $nomeImagem;

                if (!is_dir(__DIR__ . '/../../public/uploads')) {
                    mkdir(__DIR__ . '/../../public/uploads', 0777, true);
                }

                move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
            }

            $dados = [
                'id'        => $_POST['id'] ?? 0,
                'nome'      => trim($_POST['nome'] ?? ''),
                'descricao' => trim($_POST['descricao'] ?? ''), // <-- Esta linha é obrigatória!
                'preco'     => $_POST['preco'] ?? 0,
                'categoria' => $_POST['categoria'] ?? '',
                'ativo'     => $_POST['ativo'] ?? 1,
                'imagem'    => $nomeImagem // (ou como tiver a sua lógica de imagem)
            ];

           if (method_exists($produtoModel, 'atualizar')) {
                $produtoModel->atualizar($id, $dados);
            } elseif (method_exists($produtoModel, 'update')) {
                // Correção: enviamos apenas a variável $dados, que já contém o ID lá dentro
                $produtoModel->update($dados);
            }

            header('Location: index.php?route=produtos');
            exit;
        }
    }

    /**
     * Remove um produto do banco
     */
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $produtoModel = new Produto();
            if (method_exists($produtoModel, 'deletar')) {
                $produtoModel->deletar($id);
            } elseif (method_exists($produtoModel, 'delete')) {
                $produtoModel->delete($id);
            }
        }

        // Certifique-se de que aqui diz apenas 'produtos'
        header('Location: index.php?route=produtos');
        exit;
    }

    /**
     * Função auxiliar para tratar preços vindos do formulário
     */
    private function formatarPrecoParaBanco($precoInput): float {
        if (is_numeric($precoInput)) {
            return (float)$precoInput;
        }

        $preco = str_replace('R$', '', $precoInput);
        $preco = trim($preco);

        // Caso venha formatado estilo PT/BR ex: "1.250,50" -> vira "1250.50"
        if (strpos($preco, ',') !== false) {
            $preco = str_replace('.', '', $preco);
            $preco = str_replace(',', '.', $preco);
        }

        return (float)$preco;
    }
}