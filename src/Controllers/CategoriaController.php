<?php
namespace Controllers;

use Models\Categoria;

class CategoriaController {

    // Mostra a lista de categorias
    public function index() {
        $catModel = new Categoria();
        $categorias = $catModel->getAll();
        
        require __DIR__ . '/../Views/categorias/index.php';
    }

    // Salva uma nova categoria
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            if (!empty($nome)) {
                $catModel = new Categoria();
                $catModel->adicionar($nome);
            }
            header('Location: index.php?route=categorias');
            exit;
        }
    }

    // Exclui uma categoria
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $catModel = new Categoria();
            $catModel->remover($id);
        }
        header('Location: index.php?route=categorias');
        exit;
    }
}