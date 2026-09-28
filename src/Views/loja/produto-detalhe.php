<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tratamento seguro dos dados do produto
$pId = $produto['id'] ?? 0;
$pNome = $produto['nome'] ?? $produto['titulo'] ?? 'Produto';
$pDescricao = $produto['descricao'] ?? $produto['detalhes'] ?? 'Nenhuma descrição detalhada informada para este produto.';
$pImagem = $produto['imagem'] ?? $produto['foto'] ?? '';
$pEstoque = $produto['estoque'] ?? 10;

$pPrecoBruto = $produto['preco'] ?? $produto['valor'] ?? 0;
if (is_string($pPrecoBruto)) {
    $pPrecoBruto = str_replace(',', '.', $pPrecoBruto);
}
$pPrecoFloat = (float)$pPrecoBruto;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pNome) ?> - Loja Virtual</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
/* Força a remoção de qualquer fundo branco em caixas principais no modo escuro */
body.dark-manager .product-detail-card, /* (Caso mantenha classes antigas) */
body.dark-mode .product-detail-card {
    background-color: #18191a !important;
    border-color: #2d2d2d !important;
    color: #f8f9fa !important;
}

/* Garante que o container principal de detalhes do produto fique escuro */
body.dark-mode .container {
    color: #f8f9fa !important;
}

/* Corrige o texto de detalhes, descrições e títulos para ficarem claros */
body.dark-mode p, 
body.dark-mode span, 
body.dark-mode strong, 
body.dark-mode li, 
body.dark-mode small {
    color: #d1d5db !important;
}

body.dark-mode h1, 
body.dark-mode h2, 
body.dark-mode h3, 
body.dark-mode h4 {
    color: #ffffff !important;
}

        body { background-color: #f4f5f7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; color: #444; }
        .top-notice { background-color: #f15a5a; color: #fff; padding: 6px; font-size: 11px; text-align: center; }
        .top-header { background: #fff; border-bottom: 1px solid #eee; padding: 8px 0; font-size: 11px; color: #777; }
        .top-header a { color: #666; text-decoration: none; margin-left: 12px; }
        .main-header { background: #fff; padding: 20px 0; border-bottom: 1px solid #e5e5e5; }
        .journal-logo { font-size: 28px; font-weight: 900; letter-spacing: -1px; color: #222; text-decoration: none; line-height: 1; }
        .journal-logo span { font-size: 10px; display: block; letter-spacing: 2px; color: #888; font-weight: 400; margin-top: 2px; }
        .product-detail-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 4px; padding: 30px; }
        .product-main-img { width: 100%; height: 380px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .product-price-lg { font-size: 28px; font-weight: 800; color: #f15a5a; margin-bottom: 20px; }
        .btn-add-cart { background-color: #f15a5a; color: #fff; font-weight: bold; text-transform: uppercase; padding: 12px 25px; border: none; border-radius: 2px; }
        .btn-add-cart:hover { background-color: #d94343; color: #fff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <!-- Marca / Título -->
    <a class="navbar-brand fw-bold" href="index.php?route=loja">
      <i class="fa-solid fa-store me-1"></i> Loja Virtual
    </a>
    
    <!-- Botão de Hambúrguer (Mobile) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Lista de Links Colapsável -->
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

      <!-- Links do lado direito (Painel, Carrinho e Tema) -->
      <ul class="navbar-nav align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link" href="index.php?route=admin"><i class="fa-solid fa-user me-1"></i> Painel Admin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?route=carrinho-ver"><i class="fa-solid fa-cart-shopping me-1"></i> Carrinho</a>
        </li>
        <li class="nav-item ms-lg-2">
          <!-- BOTÃO MODO ESCURO -->
          <button id="theme-toggle" class="btn btn-sm text-secondary" style="border: none; background: transparent;" title="Alternar tema">
            <i id="theme-icon" class="fa-solid fa-moon fs-5"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="main-header mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php?route=loja" class="journal-logo">
            LOJA VIRTUAL
            <span>DETALHES DO PRODUTO</span>
        </a>
        <a href="index.php?route=carrinho-ver" class="btn btn-outline-danger btn-sm fw-bold">
            <i class="fa-solid fa-cart-shopping me-1"></i> Ver Carrinho
        </a>
    </div>
</div>

<div class="container mb-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?route=loja" class="text-decoration-none text-secondary">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($pNome) ?></li>
        </ol>
    </nav>

    <div class="product-detail-card">
        <div class="row">
            <!-- Imagem do Produto -->
            <div class="col-md-5 mb-4 mb-md-0">
                <?php if (!empty($pImagem)): ?>
                    <img src="uploads/<?= htmlspecialchars($pImagem) ?>" class="product-main-img" alt="<?= htmlspecialchars($pNome) ?>">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center product-main-img text-muted">Sem Imagem</div>
                <?php endif; ?>
            </div>

            <!-- Informações e Ações -->
            <div class="col-md-7">
                <h2 class="fw-bold mb-3"><?= htmlspecialchars($pNome) ?></h2>
                <div class="product-price-lg">R$ <?= number_format($pPrecoFloat, 2, ',', '.') ?></div>
                
                <p class="text-muted mb-2"><i class="fa-solid fa-boxes-stacked me-1"></i> Em stock: <strong><?= $pEstoque ?> unidades</strong></p>
                
                <hr class="my-4">

                <div class="mt-4">
                    <h5 class="fw-bold">Descrição do Produto</h5>
                    <!-- A função nl2br garante que as quebras de linha (Enters) que você deu no painel apareçam corretamente na tela -->
                    <p class="text-muted text-break">
                        <?= nl2br(htmlspecialchars($produto['descricao'] ?? 'Nenhuma descrição disponível para este produto.')) ?>
                    </p>
                </div>
                <!-- Formulário para Adicionar ao Carrinho com Quantidade -->
                <form action="index.php?route=carrinho-add" method="POST" class="d-flex align-items-center gap-3">
                    <input type="hidden" name="produto_id" value="<?= $pId ?>">
                    
                    <div style="width: 100px;">
                        <label class="form-label small fw-bold">Quantidade</label>
                        <input type="number" name="quantidade" value="1" min="1" max="<?= $pEstoque ?>" class="form-control">
                    </div>

                    <div class="align-self-end">
                        <button type="submit" class="btn btn-add-cart">
                            <i class="fa-solid fa-cart-plus me-2"></i> Adicionar ao Carrinho
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto" style="font-size: 11px;">
    &copy; <?= date('Y') ?> - Loja Virtual. Todos os direitos reservados.
</footer>
<script src="js/tema.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>