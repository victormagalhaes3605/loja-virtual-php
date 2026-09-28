<?php
namespace Models;

class Categoria {
    private $pdo;

    public function __construct() {
        // Nome da base de dados corrigido para 'gestao_vendas'
        $this->pdo = new \PDO("mysql:host=localhost;dbname=gestao_vendas;charset=utf8", "root", "");
    }

    // Puxa todas as categorias
    public function getAll() {
        $sql = $this->pdo->prepare("SELECT * FROM categorias ORDER BY nome ASC");
        $sql->execute();
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Adiciona uma nova categoria
    public function adicionar($nome) {
        $sql = $this->pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
        return $sql->execute([$nome]);
    }

    // Remove uma categoria
    public function remover($id) {
        $sql = $this->pdo->prepare("DELETE FROM categorias WHERE id = ?");
        return $sql->execute([$id]);
    }
}