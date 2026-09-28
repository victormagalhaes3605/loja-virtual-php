<?php
namespace Controllers;

class ClienteController {
    public function painel() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se tentar entrar sem login, chuta para a tela de entrar
        if (empty($_SESSION['cliente_id'])) {
            $_SESSION['redirecionar_pos_login'] = 'cliente-painel';
            header("Location: index.php?route=cliente-login");
            exit;
        }

        // Se estiver tudo certo, mostra a tela do painel
        require_once __DIR__ . '/../Views/loja/cliente-painel.php';
    }
}