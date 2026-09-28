<?php
namespace Controllers;

use Models\Produto;
use Models\Venda;

class HomeController {
    private Produto $produtoModel;
    private Venda $vendaModel;

    public function __construct() {
        $this->produtoModel = new Produto();
        $this->vendaModel = new Venda();
    }

    public function index(): void {
        $produtos = $this->produtoModel->getAll();
        $resumoVendas = $this->vendaModel->getResumo();

        $totalProdutos = count($produtos);
        $totalEstoque = array_sum(array_column($produtos, 'estoque'));
        $totalVendas = $resumoVendas['total_vendas'] ?? 0;
        $faturamento = $resumoVendas['faturamento'] ?? 0;

        require_once __DIR__ . '/../Views/home/index.php';
    }
}