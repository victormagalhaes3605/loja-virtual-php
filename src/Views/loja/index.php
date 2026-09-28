<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------
// 1. Tratamento e Mapeamento dos Banners por Posição
// -------------------------------------------------------------
$bannerModelObj = new \Models\Banner();

if (isset($banners) && is_array($banners)) {
    $bannersMapeados = $banners;
} else {
    $bannersMapeados = $bannerModelObj->getAllMapped();
}

$bannersPrincipais = $bannersMapeados['principal'] ?? [];

if (!empty($bannersPrincipais) && !isset($bannersPrincipais[0])) {
    $bannersPrincipais = [$bannersPrincipais];
}

// -------------------------------------------------------------
// 2. Cálculo dinâmico do Carrinho no Cabeçalho
// -------------------------------------------------------------
$carrinhoTop = $_SESSION['carrinho'] ?? [];
$qtdTotalHeader = 0;
$valorTotalHeader = 0.0;

if (!empty($carrinhoTop)) {
    $produtoModel = new \Models\Produto();

    foreach ($carrinhoTop as $pId => $qtd) {
        if (empty($pId) || $pId <= 0) {
            unset($_SESSION['carrinho'][$pId]);
            continue;
        }

        $quantidade = is_array($qtd) ? ($qtd['quantidade'] ?? $qtd['qtd'] ?? 1) : (int)$qtd;
        if ($quantidade <= 0) {
            unset($_SESSION['carrinho'][$pId]);
            continue;
        }

        $produto = null;
        if (method_exists($produtoModel, 'getById')) {
            $produto = $produtoModel->getById($pId);
        } elseif (method_exists($produtoModel, 'find')) {
            $produto = $produtoModel->find($pId);
        } elseif (method_exists($produtoModel, 'getAll')) {
            $todos = $produtoModel->getAll();
            foreach ($todos as $p) {
                if (($p['id'] ?? null) == $pId) {
                    $produto = $p;
                    break;
                }
            }
        }

        if ($produto) {
            $qtdTotalHeader += $quantidade;
            
            $precoBruto = $produto['preco'] ?? $produto['valor'] ?? 0;
            if (is_string($precoBruto)) {
                $precoBruto = str_replace(',', '.', $precoBruto);
            }
            $precoFloat = (float)$precoBruto;

            $valorTotalHeader += ($precoFloat * $quantidade);
        } else {
            unset($_SESSION['carrinho'][$pId]);
        }
    }
}

// -------------------------------------------------------------
// 3. Recebe os produtos já filtrados pelo Controlador (Categoria ou Busca)
// -------------------------------------------------------------
$termoBusca = trim($_GET['busca'] ?? '');
$categoriaAtual = trim($_GET['categoria'] ?? '');

// Se a variável $produtos não vier definida do controlador por algum motivo, puxa todos por segurança
if (!isset($produtos)) {
    $prodObj = new \Models\Produto();
    $produtos = method_exists($prodObj, 'getAll') ? $prodObj->getAll() : [];
}

// Se houver termo de pesquisa digitado, aplica o filtro por nome em cima da lista atual
if (!empty($termoBusca)) {
    $produtos = array_filter($produtos, function($p) use ($termoBusca) {
        $nomeProduto = strtolower($p['nome'] ?? $p['titulo'] ?? '');
        $pesquisa = strtolower($termoBusca);
        return strpos($nomeProduto, $pesquisa) !== false;
    });
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja Virtual - Início</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS CORRIGIDO E INTEGRADO -->
    <style>
        body { background-color: #f4f5f7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; color: #444; overflow-x: hidden; }
        
        .top-notice { background-color: #f15a5a; color: #fff; padding: 6px; font-size: 11px; text-align: center; }
        .top-header { background: #fff; border-bottom: 1px solid #eee; padding: 8px 0; font-size: 11px; color: #777; }
        .top-header a { color: #666; text-decoration: none; margin-left: 12px; }
        .top-header a:hover { color: #f15a5a; }

        .main-header { background: #fff; padding: 20px 0; border-bottom: 1px solid #e5e5e5; }
        .journal-logo { font-size: 28px; font-weight: 900; letter-spacing: -1px; color: #222; text-decoration: none; line-height: 1; }
        .journal-logo span { font-size: 10px; display: block; letter-spacing: 2px; color: #888; font-weight: 400; margin-top: 2px; }

        .search-box { display: flex; align-items: center; width: 100%; max-width: 300px; }
        .search-input { border: 1px solid #e1e1e1; border-right: none; border-radius: 0; padding: 8px 12px; font-size: 12px; width: 100%; }
        .search-btn { background: #fff; border: 1px solid #e1e1e1; border-left: none; padding: 8px 12px; color: #666; }

        .cart-btn { background-color: #f15a5a; color: #fff; font-weight: bold; border: none; padding: 9px 18px; border-radius: 2px; text-decoration: none; display: inline-block; font-size: 13px; white-space: nowrap; }
        .cart-btn:hover { background-color: #d94343; color: #fff; }

        .categories-container {
            position: relative;
            width: 100%;
        } /* CHAVETA FECHADA AQUI (Estava a quebrar o seu código) */

        .product-detail-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 4px; padding: 30px; }
        .product-main-img { width: 100%; height: 380px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .product-price-lg { font-size: 28px; font-weight: 800; color: #f15a5a; margin-bottom: 20px; }
        .btn-add-cart { background-color: #f15a5a; color: #fff; font-weight: bold; text-transform: uppercase; padding: 12px 25px; border: none; border-radius: 2px; }
        .btn-add-cart:hover { background-color: #d94343; color: #fff; }      

        /* Estilo do menu com scroll horizontal escondido */
        .nav-journal-wrapper { width: 100% !important; background-color: #18191a; border-bottom: 1px solid #2d2d2d; margin-bottom: 20px; }   
        .nav-journal {
            justify-content: flex-start !important; overflow-x: auto !important; white-space: nowrap !important;
            padding-left: 15px !important; padding-right: 15px !important; scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch; scrollbar-width: none;
        }
        .nav-journal::-webkit-scrollbar { display: none; }
        .nav-journal a { flex-shrink: 0; }

        /* Setas de navegação lateral */
        .category-arrow {
            display: none; position: absolute; top: 50%; transform: translateY(-50%); background-color: rgba(0, 0, 0, 0.7);
            color: #fff; border: none; width: 32px; height: 32px; border-radius: 50%; z-index: 10;
            align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
        .category-prev { left: 5px; }
        .category-next { right: 5px; }

        /* Carrossel de Banners Limpo */
        .hero-slider-container { position: relative; width: 100%; margin-bottom: 30px; }
        .hero-banner-clean {
            width: 100%; height: 420px; background-size: contain; background-position: center;
            background-repeat: no-repeat; background-color: #f8f9fa;
        }
        .carousel-control-prev, .carousel-control-next {
            width: 5%; height: 45px; background: transparent !important; border: none !important;
            border-radius: 50%; top: 50%; transform: translateY(-50%); opacity: 0.9; z-index: 10;
        }
        .carousel-control-prev-icon, .carousel-control-next-icon { background-color: transparent !important; border-radius: 0 !important; background-image: none !important; }
        .carousel-control-prev span, .carousel-control-next span { background-color: transparent !important; box-shadow: none !important; }

        .product-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 2px; padding: 15px; margin-bottom: 20px; text-align: center; }
        .product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .product-img { width: 100%; height: 160px; object-fit: contain; background-color: #fff; margin-bottom: 15px; }
        .product-title { font-size: 13px; font-weight: 700; color: #333; text-decoration: none; display: block; margin-bottom: 8px; height: 36px; overflow: hidden; }
        .product-price { font-size: 16px; font-weight: 800; color: #f15a5a; margin-bottom: 12px; }

        /* Responsividade Limpa */
        @media (max-width: 768px) {
            .main-header .container { display: flex; flex-direction: column; align-items: stretch !important; text-align: center; gap: 15px; }
            .journal-logo { text-align: center; display: inline-block; }
            .search-box { max-width: 100% !important; width: 100% !important; }
            .cart-btn { width: 100%; text-align: center; }
            .hero-banner-clean { height: 280px; }
            .top-header .container { flex-direction: column; text-align: center; gap: 6px; padding-top: 5px; padding-bottom: 5px; }
            .top-header a { margin: 2px 6px !important; font-size: 10px; }
            .top-header span { display: none; }
        }

        /* --- MODO ESCURO (DARK MODE) --- */
        body.dark-mode .journal-logo { color: #ffffff !important; }
        body.dark-mode .journal-logo span { color: #cccccc !important; }
        body.dark-mode .main-header { color: #ffffff !important; }
        body.dark-mode .hero-banner-clean, body.dark-mode .hero-slider-container { background-color: #18191a !important; }
        
        [data-bs-theme="dark"] body { background-color: #121212 !important; color: #f8f9fa !important; }
        [data-bs-theme="dark"] .main-header, [data-bs-theme="dark"] .nav-journal { background-color: #18191a !important; border-color: #2d2d2d !important; color: #f8f9fa !important; }
        [data-bs-theme="dark"] .product-card { background-color: #1e1e1e !important; border: 1px solid #2d2d2d !important; color: #f8f9fa !important; }
        [data-bs-theme="dark"] .product-title { color: #ffffff !important; }
        [data-bs-theme="dark"] .product-price { color: #ff6b6b !important; }
        [data-bs-theme="dark"] .search-input { background-color: #2b2b2b !important; border-color: #404040 !important; color: #fff !important; }
        [data-bs-theme="dark"] .search-input::placeholder { color: #adb5bd !important; }
        [data-bs-theme="dark"] .search-btn { background-color: #333333 !important; border-color: #404040 !important; color: #fff !important; } 

        /* --- ESTILOS DO OFFCANVAS (MENU LATERAL) --- */
        .profile-card { background-color: #f8f9fa; border: 1px solid #dee2e6; color: #212529; }
        .offcanvas-link { color: #212529; transition: background-color 0.2s, color 0.2s; }
        .offcanvas-link:hover { background-color: #e9ecef; color: #212529; }
        .offcanvas-title-text { color: #212529; }

        /* Offcanvas no Modo Escuro */
        [data-bs-theme="dark"] .offcanvas, body.dark-mode .offcanvas { background-color: #18191a !important; }
        [data-bs-theme="dark"] .profile-card, body.dark-mode .profile-card { background-color: #242526 !important; border-color: #3a3b3c !important; color: #f8f9fa !important; }
        [data-bs-theme="dark"] .offcanvas-link, body.dark-mode .offcanvas-link { color: #e4e6eb !important; }
        [data-bs-theme="dark"] .offcanvas-link:hover, body.dark-mode .offcanvas-link:hover { background-color: #3a3b3c !important; color: #ffffff !important; }
        [data-bs-theme="dark"] .offcanvas-title-text, body.dark-mode .offcanvas-title-text { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .offcanvas .text-muted, body.dark-mode .offcanvas .text-muted { color: #b0b3b8 !important; }
        [data-bs-theme="dark"] .offcanvas .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php?route=loja">
      <i class="fa-solid fa-store me-1"></i> Loja Virtual
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php?route=loja">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?route=loja&categoria=Acessórios">Acessórios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?route=loja&categoria=Eletrônicos">Eletrônicos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?route=loja&categoria=Roupas">Roupas</a>
        </li>
      </ul>

      <!-- LIGAÇÃO COM A NOVA ÁREA DO CLIENTE AQUI -->
      <ul class="navbar-nav align-items-lg-center">
       <?php if (isset($_SESSION['cliente_id'])): ?>
            <!-- Cliente Logado: Abre o Menu Lateral (Offcanvas) -->
            <li class="nav-item">
                <a class="nav-link fw-bold text-success" href="#" data-bs-toggle="offcanvas" data-bs-target="#menuLateralCliente">
                    <i class="fa-solid fa-user-check me-1"></i> Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?>
                </a>
            </li>
        <?php else: ?>
            <!-- Cliente Deslogado: Vai para Entrar/Registar -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?route=cliente-login">
                    <i class="fa-solid fa-user me-1"></i> Entrar / Registar
                </a>
            </li>
        <?php endif; ?>

        <!-- Link Escondido do Admin (pode retirar se quiser que o cliente não o veja) -->
        <li class="nav-item">
          <a class="nav-link" href="index.php?route=admin"><i class="fa-solid fa-gear me-1"></i></a>
        </li>

        <li class="nav-item ms-lg-2">
          <button id="theme-toggle" class="btn btn-sm text-secondary" style="border: none; background: transparent;" title="Alternar tema">
            <i id="theme-icon" class="fa-solid fa-moon fs-5"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="main-header">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php?route=loja" class="journal-logo">
            LOJA VIRTUAL
            <span>SISTEMA DE VENDAS</span>
        </a>

        <form action="index.php" method="GET" class="search-box m-0">
            <input type="hidden" name="route" value="loja">
            <input type="text" name="busca" class="form-control search-input" placeholder="Pesquisar produtos..." value="<?= htmlspecialchars($termoBusca) ?>">
            <button type="submit" class="btn search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <a href="index.php?route=carrinho-ver" class="cart-btn">
            <i class="fa-solid fa-cart-shopping me-1"></i> 
            <?= $qtdTotalHeader ?> item(s) - R$ <?= number_format($valorTotalHeader, 2, ',', '.') ?>
        </a>
    </div>
</div>

<div class="categories-container position-relative mb-4">
    <button class="category-arrow category-prev" id="scrollPrev"><i class="fa-solid fa-chevron-left"></i></button>
    <div class="nav-journal-wrapper w-100">
        <div class="container">        
            <nav class="nav-journal d-flex align-items-center py-2" id="categoriesNav">
                <a href="index.php?route=loja" class="<?= empty($_GET['categoria']) ? 'text-white fw-bold' : 'text-white-50' ?> text-decoration-none me-4 text-uppercase flex-shrink-0" style="transition: 0.3s;">
                    TODOS OS PRODUTOS
                </a>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <?php $ativa = (isset($_GET['categoria']) && $_GET['categoria'] == $cat['nome']) ? 'text-white fw-bold' : 'text-white-50'; ?>
                        <a href="index.php?route=loja&categoria=<?= urlencode($cat['nome']) ?>" class="<?= $ativa ?> text-decoration-none me-4 text-uppercase flex-shrink-0" style="transition: 0.3s;">
                            <?= htmlspecialchars($cat['nome']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    <button class="category-arrow category-next" id="scrollNext"><i class="fa-solid fa-chevron-right"></i></button>
</div>

<div class="container mb-5">
    <!-- Carrossel de Banners Limpo -->
    <div class="row mb-4">
        <div class="col-12">
            <?php if (!empty($bannersPrincipais)): ?>
                <div id="carouselMainJournal" class="carousel slide hero-slider-container" data-bs-ride="carousel">
                    
                    <?php if (count($bannersPrincipais) > 1): ?>
                        <div class="carousel-indicators">
                            <?php foreach ($bannersPrincipais as $idx => $b): ?>
                                <button type="button" data-bs-target="#carouselMainJournal" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="carousel-inner" style="border-radius: 4px; overflow: hidden;">
                        <?php foreach ($bannersPrincipais as $idx => $b): ?>
                            <?php 
                                $img = $b['imagem'] ?? '';
                                $bgStyle = !empty($img) ? "background-image: url('uploads/".htmlspecialchars($img)."');" : "";
                                $linkBanner = (!empty($b['link']) && $b['link'] !== '#') ? htmlspecialchars($b['link']) : '';
                            ?>
                            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                <?php if (!empty($linkBanner)): ?>
                                    <a href="<?= $linkBanner ?>">
                                        <div class="hero-banner-clean" style="<?= $bgStyle ?>"></div>
                                    </a>
                                <?php else: ?>
                                    <div class="hero-banner-clean" style="<?= $bgStyle ?>"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($bannersPrincipais) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselMainJournal" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselMainJournal" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>

                </div>
            <?php else: ?>
                <div class="hero-banner-clean bg-light d-flex align-items-center justify-content-center text-muted">
                    Nenhum banner principal cadastrado
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <h4 class="fw-bold mb-3 mt-2">Nossos Produtos</h4>
            
    <div class="row">
        <?php if (!empty($produtos)): ?>
            <?php foreach ($produtos as $prod): ?>
                <?php
                    if (isset($prod['ativo']) && $prod['ativo'] == 0) { continue; }

                    $pId = $prod['id'] ?? 0;
                    $pNome = $prod['nome'] ?? $prod['titulo'] ?? 'Produto';
                    $pImagem = $prod['imagem'] ?? $prod['foto'] ?? '';
                    
                    $pPrecoBruto = $prod['preco'] ?? $prod['valor'] ?? 0;
                    if (is_string($pPrecoBruto)) { $pPrecoBruto = str_replace(',', '.', $pPrecoBruto); }
                    $pPrecoFloat = (float)$pPrecoBruto;
                ?>
                <div class="col-md-3 col-sm-6">
                    <div class="product-card">
                        <a href="index.php?route=produto&id=<?= $pId ?>" class="text-decoration-none">
                            <?php if (!empty($pImagem)): ?>
                                <img src="uploads/<?= htmlspecialchars($pImagem) ?>" class="product-img" alt="<?= htmlspecialchars($pNome) ?>">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center product-img text-muted">Sem Imagem</div>
                            <?php endif; ?>
                        </a>

                        <a href="index.php?route=produto&id=<?= $pId ?>" class="product-title"><?= htmlspecialchars($pNome) ?></a>
                        <div class="product-price">R$ <?= number_format($pPrecoFloat, 2, ',', '.') ?></div>

                        <form action="index.php?route=carrinho-add" method="POST" class="form-adicionar-carrinho">
                            <input type="hidden" name="produto_id" value="<?= $pId ?>">
                            <input type="hidden" name="quantidade" value="1">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                                <i class="fa-solid fa-cart-plus me-1"></i> Adicionar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4">
                <p class="text-muted fs-6">Nenhum produto cadastrado até o momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto" style="font-size: 11px;">
    &copy; <?= date('Y') ?> - Loja Virtual. Todos os direitos reservados.
</footer>

<<!-- ========================================== -->
<!-- MENU LATERAL DO CLIENTE (OFFCANVAS)        -->
<!-- ========================================== -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="menuLateralCliente" aria-labelledby="menuLateralLabel" style="width: 300px;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title d-flex align-items-center fw-bold offcanvas-title-text" id="menuLateralLabel">
        <div class="bg-primary text-white rounded px-2 py-1 me-2"><i class="fa-solid fa-store"></i></div>
        Minha Conta
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    
    <!-- Perfil do Cliente -->
    <div class="d-flex align-items-center mb-4 p-3 rounded shadow-sm profile-card">
         <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['cliente_nome'] ?? 'Cliente'); ?>&background=0d6efd&color=fff&rounded=true" alt="Avatar" width="48" height="48" class="me-3">
         <div>
             <strong class="d-block fs-5">
                 <?= htmlspecialchars(explode(' ', trim($_SESSION['cliente_nome'] ?? 'Cliente'))[0]); ?>
             </strong>
             <span class="text-muted small">Cliente da Loja</span>
         </div>
    </div>

    <!-- Lista de Links -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="#" class="nav-link active d-flex align-items-center" data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-house me-3 text-center" style="width: 20px;"></i> Início
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="#" class="nav-link offcanvas-link d-flex align-items-center">
                <i class="fa-solid fa-box-open text-secondary me-3 text-center" style="width: 20px;"></i> Meus Pedidos
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="#" class="nav-link offcanvas-link d-flex align-items-center">
                <i class="fa-solid fa-address-card text-secondary me-3 text-center" style="width: 20px;"></i> Meus Dados
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="#" class="nav-link offcanvas-link d-flex align-items-center">
                <i class="fa-solid fa-headset text-secondary me-3 text-center" style="width: 20px;"></i> Suporte
            </a>
        </li>
    </ul>
    
    <hr class="text-secondary">
    
    <!-- Botão Sair no Rodapé -->
    <a href="index.php?route=cliente-logout" class="btn btn-outline-danger w-100 fw-bold">
        <i class="fa-solid fa-right-from-bracket me-2"></i> Sair da Conta
    </a>
  </div>
</div>
<!-- ========================================== -->

<script src="js/tema.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. Lógica do Carrinho de Compras
    // ==========================================
    const forms = document.querySelectorAll('.form-adicionar-carrinho');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            const textoOriginal = btn.innerHTML;
            
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> A adicionar...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Adicionado!';
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    location.reload(); 
                }, 600);
            })
            .catch(error => {
                console.error('Erro ao adicionar:', error);
                btn.innerHTML = textoOriginal;
                btn.disabled = false;
            });
        });
    });

    // ==========================================
    // Lógica do Scroll Lateral de Categorias
    // ==========================================
    const nav = document.getElementById('categoriesNav');
    const btnPrev = document.getElementById('scrollPrev');
    const btnNext = document.getElementById('scrollNext');

    if (btnPrev && btnNext && nav) {
        btnNext.addEventListener('click', function() {
            nav.scrollBy({ left: 200, behavior: 'smooth' });
        });
        btnPrev.addEventListener('click', function() {
            nav.scrollBy({ left: -200, behavior: 'smooth' });
        });
    }
});
</script>
</body>
</html>