<?php
ini_set('session.cookie_lifetime', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', '/', $class);

    $fileSrc = __DIR__ . '/../src/' . $classPath . '.php';
    if (file_exists($fileSrc)) {
        require_once $fileSrc;
        return;
    }

    $fileConfig = __DIR__ . '/../config/' . str_replace('Config/', '', $classPath) . '.php';
    if (file_exists($fileConfig)) {
        require_once $fileConfig;
        return;
    }
});

use Controllers\ClienteController;
use Controllers\AuthController;
use Controllers\HomeController;
use Controllers\ProdutoController;
use Controllers\VendaController;
use Controllers\LojaController;
use Controllers\CarrinhoController;
use Controllers\CategoriaController;

$route = $_GET['route'] ?? 'loja';

// -------------------------------------------------------------
// CADEADO DE SEGURANÇA MÁXIMA - Protege as rotas do painel admin
// -------------------------------------------------------------
$rotasProtegidas = [
    'admin', 'home', 'vendas', 'vendas-checkout', 'admin-vendas', 'admin-vendas-detalhe',
    'produtos', 'produtos-store', 'produtos-edit', 'produtos-update', 'produtos-delete',
    'categorias', 'categorias-store', 'categorias-delete',
    'admin-banners', 'admin-banners-salvar', 'admin-banners-deletar'
];

if (in_array($route, $rotasProtegidas)) {
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true || empty($_SESSION['admin_id'])) {
        session_unset();
        session_destroy();
        header('Location: index.php?route=login');
        exit;
    }

    $limiteInatividade = 900; // 15 minutos
    
    if (isset($_SESSION['admin_ultimo_acesso'])) {
        $tempoParado = time() - $_SESSION['admin_ultimo_acesso'];
        
        if ($tempoParado > $limiteInatividade) {
            session_unset();
            session_destroy();
            header('Location: index.php?route=login');
            exit;
        }
    }
    
    $_SESSION['admin_ultimo_acesso'] = time();
}
// -------------------------------------------------------------


switch ($route) {
    // Rotas de Login / Autenticação (Admin)
    case 'acesso-restrito':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['admin_logado'], $_SESSION['admin_id'], $_SESSION['admin_nome']);
        header('Location: index.php?route=login');
        exit;
    case 'login':
        (new AuthController())->exibirLogin();
        break;
    case 'login-auth':
        (new AuthController())->logarAdmin();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;

    // Rotas da Loja / Vitrine (Públicas)
    case 'loja':
        (new LojaController())->index();
        break;
    case 'produto':
        (new LojaController())->detalhe();
        break;
    case 'processar-pagamento':
        (new LojaController())->processarPagamento();
        exit;

    // Rotas do Carrinho
    case 'carrinho-add':
        (new CarrinhoController())->add();
        break;
    case 'carrinho-ver':
        (new CarrinhoController())->index();
        break;
    case 'carrinho-remover':
    case 'carrinho-remove':
        (new CarrinhoController())->remove();
        break;
    case 'carrinho-limpar':
        (new CarrinhoController())->clear();
        break;
    case 'carrinho-update':
        (new CarrinhoController())->update();
        break;
        
    // --- ROTA DE CHECKOUT PROTEGIDA PARA CLIENTES ---
    case 'checkout': 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se o cliente não estiver logado, manda para o login
        if (empty($_SESSION['cliente_id'])) {
            $_SESSION['redirecionar_pos_login'] = 'checkout';
            header('Location: index.php?route=cliente-login'); 
            exit;
        }

        // Carrega o visual do checkout
        require_once __DIR__ . '/../src/Views/loja/checkout.php'; 
        break;

    case 'checkout-finalizar':
        (new CarrinhoController())->finalizarPedido();
        break;

    // Rotas do Painel Administrativo
    case 'admin':
    case 'home':
        (new HomeController())->index();
        break;
    case 'vendas':
        (new VendaController())->index();
        break;
    case 'vendas-checkout':
        (new VendaController())->checkout();
        break;
    case 'produtos':
        (new ProdutoController())->index();
        break;
    case 'produtos-store':
        (new ProdutoController())->store();
        break;
    case 'produtos-edit':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        (new ProdutoController())->edit($id ?? 0);
        break;
    case 'produtos-update':
        (new ProdutoController())->update();
        break;
    case 'produtos-delete':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        (new ProdutoController())->delete($id ?? 0);
        break;
    case 'categorias':
        (new CategoriaController())->index();
        break;
    case 'categorias-store':
        (new CategoriaController())->store();
        break;
    case 'categorias-delete':
        (new CategoriaController())->delete();
        break;
    case 'admin-banners':
        (new Controllers\BannerController())->index();
        break;
    case 'admin-banners-salvar':
        (new Controllers\BannerController())->store();
        break;
    case 'admin-banners-deletar':
        (new Controllers\BannerController())->delete();
        break;
    case 'admin-vendas':
        (new Controllers\AdminVendaController())->index();
        break;
    case 'admin-vendas-detalhe':
        (new Controllers\AdminVendaController())->detalhe();
        break;

    // Rotas de Autenticação do Cliente
    case 'cliente-login':
        (new AuthController())->exibirLoginCliente(); // Correto: abre a área do cliente
        break;
    case 'cliente-fazer-login':
        (new AuthController())->logar();
        break;
    case 'cliente-fazer-registo':
        (new AuthController())->registar();
        break;
    case 'cliente-logout':
        (new AuthController())->logoutCliente(); 
        break;
    case 'cliente-painel':
        (new ClienteController())->painel();
        break;

    default:
        http_response_code(404);
        echo "Página não encontrada!";
        break;
}