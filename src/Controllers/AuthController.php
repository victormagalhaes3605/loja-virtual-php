<?php
namespace Controllers;

use Config\Database;
use PDO;

class AuthController {
    private $clienteModel;
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../Models/Cliente.php';
        $this->db = Database::getConnection();
        $this->clienteModel = new \Models\Cliente($this->db);
    }

    public function exibirLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once __DIR__ . '/../Views/admin/login.php';
    }
    public function exibirLoginCliente() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Se o cliente já estiver logado, manda-o de volta para a loja ou checkout
        if (isset($_SESSION['cliente_id'])) {
            $destino = $_SESSION['redirecionar_pos_login'] ?? 'loja';
            unset($_SESSION['redirecionar_pos_login']);
            header("Location: index.php?route=" . $destino);
            exit;
        }

        // Chama a view exclusiva do cliente
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function logarAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = "Preencha todos os campos.";
            header("Location: index.php?route=login");
            exit;
        }

        // --- CÓDIGO DE SALVAÇÃO: Cria o admin se não existir nenhum utilizador ---
        try {
            $check = $this->db->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
            if ($check == 0 && $email === 'admin@loja.com') {
                // Insere o admin na tabela vazia para o Victor conseguir entrar!
                $stmt = $this->db->prepare("INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)");
                $stmt->execute(['Administrador', $email, password_hash($senha, PASSWORD_DEFAULT)]);
                echo "<div style='background: #d4edda; color: #155724; padding: 20px;'><b>Sucesso!</b> Administrador criado na base de dados vazia. A redirecionar...</div>";
                header("Refresh: 2; url=index.php?route=admin");
                
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id'] = $this->db->lastInsertId();
                $_SESSION['admin_nome'] = 'Administrador';
                exit;
            }
        } catch (\Exception $e) {
            // Ignora o erro e segue em frente
        }
        // ------------------------------------------------------------------------

        $admin = $this->clienteModel->buscarPorEmail($email);

        if ($admin) {
            $senhaDb = trim($admin['senha'] ?? '');

            if ($senha === $senhaDb || password_verify($senha, $senhaDb)) {
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'] ?? 'Administrador';

                header("Location: index.php?route=admin");
                exit;
            }
        }

        $_SESSION['erro_login'] = "E-mail ou palavra-passe incorretos.";
        header("Location: index.php?route=login");
        exit;
    }

    public function logar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = "Preencha todos os campos.";
            header("Location: index.php?route=cliente-login"); // Ajuste para a sua rota de login de clientes
            exit;
        }

        // Aproveitamos o mesmo model que já funciona para o admin!
        $cliente = $this->clienteModel->buscarPorEmail($email);

        if ($cliente) {
            $senhaDb = trim($cliente['senha'] ?? '');
            
            // Valida a senha (igual ao que fizemos no admin)
            if ($senha === $senhaDb || password_verify($senha, $senhaDb)) {
                
                // Grava a sessão exclusiva do cliente
                $_SESSION['cliente_id'] = $cliente['id'];
                $_SESSION['cliente_nome'] = $cliente['nome'];

                // MAGIA: Se ele foi barrado no checkout, devolve-o para lá!
                if (isset($_SESSION['redirecionar_pos_login'])) {
                    $rotaDestino = $_SESSION['redirecionar_pos_login'];
                    unset($_SESSION['redirecionar_pos_login']); // Limpa para não prender o utilizador
                    header("Location: index.php?route=" . $rotaDestino);
                    exit;
                }

                // Se for um login normal (menu), vai para a loja
                header("Location: index.php?route=loja");
                exit;
            }
        }

        $_SESSION['erro_login'] = "E-mail ou palavra-passe incorretos.";
        header("Location: index.php?route=cliente-login");
        exit;
    }

    public function registar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($nome) || empty($email) || empty($senha)) {
            $_SESSION['erro_registo'] = "Preencha todos os campos.";
            header("Location: index.php?route=cliente-login");
            exit;
        }

        // Verifica se já existe
        if ($this->clienteModel->buscarPorEmail($email)) {
            $_SESSION['erro_registo'] = "Este e-mail já está registado.";
            header("Location: index.php?route=cliente-login");
            exit;
        }

        // Insere o cliente na base de dados
        try {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $senhaHash]);
            
            // Auto-login após o registo!
            $_SESSION['cliente_id'] = $this->db->lastInsertId();
            $_SESSION['cliente_nome'] = $nome;

            // O mesmo redirecionamento inteligente para o checkout
            if (isset($_SESSION['redirecionar_pos_login'])) {
                $rotaDestino = $_SESSION['redirecionar_pos_login'];
                unset($_SESSION['redirecionar_pos_login']);
                header("Location: index.php?route=" . $rotaDestino);
                exit;
            }

            header("Location: index.php?route=loja");
            exit;

        } catch (\Exception $e) {
            $_SESSION['erro_registo'] = "Erro ao registar: " . $e->getMessage();
            header("Location: index.php?route=cliente-login");
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Destrói a sessão por completo, limpando tanto o cliente quanto o admin
        session_unset();
        session_destroy();
        
        // Redireciona de volta para a tela de login
        header("Location: index.php?route=login");
        exit;
    }

    public function logoutCliente() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Apaga apenas as informações do cliente (mantém o admin logado se for o caso)
        unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);

        // Opcional: Se quiser que ele vá para o login do cliente, mude 'loja' para 'cliente-login'
        header("Location: index.php?route=loja");
        exit;
    }
}