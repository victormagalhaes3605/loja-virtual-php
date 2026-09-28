<?php
namespace Models;

use Config\Database;
use PDO;

class Usuario {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function buscarPorEmail($email) {
        $sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $sql->execute([$email]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
}