<?php
namespace Controllers;

use Models\Venda;

class AdminVendaController {

    public function index() {
        $vendaModel = new Venda();
        $vendas = $vendaModel->getAll();
        
        // Aponta para a sua pasta Views/vendas/
        require __DIR__ . '/../Views/vendas/index.php';
    }

    public function detalhe() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: index.php?route=admin-vendas');
            exit;
        }

        $vendaModel = new Venda();
        $venda = $vendaModel->getById($id);
        $itens = $vendaModel->getItens($id);

        if (!$venda) {
            echo "<script>alert('Venda não encontrada!'); window.location.href='index.php?route=admin-vendas';</script>";
            exit;
        }

        // Aponta para o ficheiro de detalhes na sua pasta Views/vendas/
        require __DIR__ . '/../Views/vendas/detalhe.php';
    }
}