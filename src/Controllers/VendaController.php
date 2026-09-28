<?php
namespace Controllers;

use Models\Produto;
use Models\Venda;
use Exception;

class VendaController {
    private Produto $produtoModel;
    private Venda $vendaModel;

    public function __construct() {
        $this->produtoModel = new Produto();
        $this->vendaModel = new Venda();
    }

    public function index(): void {
        $produtos = $this->produtoModel->getDisponiveis();
        require_once __DIR__ . '/../Views/vendas/index.php';
    }

    public function checkout(): void {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['itens'])) {
                throw new Exception("Nenhum item selecionado.");
            }

            $this->vendaModel->processarVenda($data['itens']);
            echo json_encode(['success' => true, 'message' => 'Venda realizada com sucesso!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}