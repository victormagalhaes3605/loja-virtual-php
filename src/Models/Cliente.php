<?php
namespace Models;

use PDO;

class Cliente {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Registar novo cliente
    public function criar($nome, $email, $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)");
        return $stmt->execute([$nome, $email, $hash]);
    }

    // Procurar cliente por email para o login
    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}