<?php
namespace Controllers;

use Models\Usuario;

class LoginController {

    // Mostra a tela de login
    public function index() {
        // Se já estiver logado, manda direto para o painel
        if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
            header('Location: index.php?route=produtos');
            exit;
        }
        
        require __DIR__ . '/../Views/admin/login.php';
    }

    // Processa os dados do formulário
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            $usuarioModel = new Usuario();
            $user = $usuarioModel->buscarPorEmail($email);

            // Verifica se o usuário existe e se a senha está correta
            if ($user && $user['senha'] === $senha) {
                // Guarda na sessão que o admin entrou!
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_nome'] = $user['nome'];
                
                header('Location: index.php?route=produtos');
                exit;
            } else {
                $_SESSION['erro_login'] = "E-mail ou senha incorretos!";
                header('Location: index.php?route=login');
                exit;
            }
        }
    }

    // Faz logout (sair)
    public function sair() {
        unset($_SESSION['admin_logado']);
        unset($_SESSION['admin_nome']);
        session_destroy();
        
        header('Location: index.php?route=login');
        exit;
    }
}