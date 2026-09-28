<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$carrinho = $_SESSION['carrinho'] ?? [];
$itensCarrinho = [];
$subtotalGeral = 0.0;
$totalItensQtd = 0;

if (!empty($carrinho)) {
    $produtoModel = new \Models\Produto();

    foreach ($carrinho as $pId => $qtd) {
        if (empty($pId) || $pId <= 0) {
            continue;
        }

        $quantidade = is_array($qtd) ? ($qtd['quantidade'] ?? $qtd['qtd'] ?? 1) : (int)$qtd;
        if ($quantidade <= 0) {
            continue;
        }

        // Busca o produto no banco
        $produto = null;
        if (method_exists($produtoModel, 'getById')) {
            $produto = $produtoModel->getById($pId);
        } elseif (method_exists($produtoModel, 'find')) {
            $produto = $produtoModel->find($pId);
        } elseif (method_exists($produtoModel, 'findById')) {
            $produto = $produtoModel->findById($pId);
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
            $precoBruto = $produto['preco'] ?? $produto['valor'] ?? $produto['preco_venda'] ?? 0;
            
            // Tratamento direto sem remover pontos de decimais do MySQL
            if (is_string($precoBruto)) {
                $precoBruto = str_replace(',', '.', $precoBruto);
            }
            $precoFloat = (float)$precoBruto;
            $subtotalItem = $precoFloat * $quantidade;

            $subtotalGeral += $subtotalItem;
            $totalItensQtd += $quantidade;

            $itensCarrinho[] = [
                'id'         => $pId,
                'nome'       => $produto['nome'] ?? $produto['titulo'] ?? 'Produto',
                'imagem'     => $produto['imagem'] ?? $produto['foto'] ?? '',
                'preco'      => $precoFloat,
                'quantidade' => $quantidade,
                'subtotal'   => $subtotalItem
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - Journal</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f5f7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; color: #444; }
        .top-notice { background-color: #2ecc71; color: #fff; padding: 6px; font-size: 11px; text-align: center; }
        .top-notice a { color: #fff; text-decoration: underline; }
        .top-header { background: #fff; border-bottom: 1px solid #eee; padding: 8px 0; font-size: 11px; color: #777; }
        .top-header a { color: #666; text-decoration: none; margin-left: 12px; }
        .main-header { background: #fff; padding: 25px 0; border-bottom: 1px solid #e5e5e5; }
        .journal-logo { font-size: 32px; font-weight: 900; letter-spacing: -1px; color: #222; text-decoration: none; line-height: 1; }
        .cart-btn { background-color: #f15a5a; color: #fff; font-weight: bold; border: none; padding: 9px 18px; border-radius: 2px; text-decoration: none; display: inline-block; font-size: 13px; }
        .nav-journal { background-color: #3b4751; }
        .nav-journal .nav-link { color: #fff; font-weight: 700; text-transform: uppercase; font-size: 12px; padding: 12px 20px !important; border-right: 1px solid #4a5763; }
        .nav-journal .nav-link.active { background-color: #f15a5a; }
        .cart-table th { background-color: #f8f9fa; font-size: 11px; text-transform: uppercase; color: #666; }
        .cart-table td { vertical-align: middle; }
        .product-img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .summary-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 2px; padding: 20px; }
        .btn-checkout { background-color: #f15a5a; color: #fff; font-weight: bold; width: 100%; border: none; padding: 12px; font-size: 14px; text-transform: uppercase; }
        .btn-checkout:hover { background-color: #d94343; color: #fff; }

        body.dark-mode .summary-card,
body.dark-mode .card,
body.dark-mode .table-responsive {
    background-color: #18191a !important;
    border-color: #2d2d2d !important;
    color: #f8f9fa !important;
}

/* Cabeçalho da tabela do carrinho */
body.dark-mode .cart-table th {
    background-color: #222324 !important;
    color: #adb5bd !important;
    border-color: #2d2d2d !important;
}

/* Células da tabela do carrinho */
body.dark-mode .cart-table td {
    color: #f8f9fa !important;
    border-color: #2d2d2d !important;
}

/* Textos e títulos dentro do resumo e carrinho no modo escuro */
body.dark-mode .summary-card h4,
body.dark-mode .summary-card p,
body.dark-mode .summary-card span,
body.dark-mode .summary-card div {
    color: #f8f9fa !important;
}

/* Mantém o destaque do preço total legível */
body.dark-mode .summary-card .text-danger,
body.dark-mode .product-price {
    color: #ff6b6b !important;
}
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

<!-- O seu Nav original -->
    <div class="nav-journal-wrapper w-100">
        <div class="container">        
            <nav class=" nav-journal d-flex align-items-center py-2" id="categoriesNav">
                <a href="index.php?route=loja" class="<?= empty($_GET['categoria']) ? 'text-white fw-bold' : 'text-white-50' ?> text-decoration-none me-4 text-uppercase flex-shrink-0" style="transition: 0.3s;">
                    TODOS OS PRODUTOS
                </a>

                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <?php 
                            $ativa = (isset($_GET['categoria']) && $_GET['categoria'] == $cat['nome']) ? 'text-white fw-bold' : 'text-white-50';
                        ?>
                        <a href="index.php?route=loja&categoria=<?= urlencode($cat['nome']) ?>" class="<?= $ativa ?> text-decoration-none me-4 text-uppercase flex-shrink-0" style="transition: 0.3s;">
                            <?= htmlspecialchars($cat['nome']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>

<div class="container mb-5">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-cart-shopping me-2"></i>Carrinho de Compras</h3>

    <div class="d-flex justify-content-between mb-3">
        <a href="index.php?route=loja" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Continuar Comprando
        </a>
        <?php if (!empty($itensCarrinho)): ?>
            <a href="index.php?route=carrinho-limpar" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-trash me-1"></i> Limpar Carrinho
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($itensCarrinho)): ?>
        <form action="index.php?route=carrinho-atualizar" method="POST">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="table-responsive bg-white border p-3">
                        <table class="table cart-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>FOTO</th>
                                    <th>PRODUTO</th>
                                    <th class="text-end">PREÇO</th>
                                    <th class="text-center" style="width: 100px;">QTD</th>
                                    <th class="text-end">SUBTOTAL</th>
                                    <th class="text-center">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itensCarrinho as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item['imagem'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($item['imagem']) ?>" class="product-img-thumb" alt="">
                                            <?php else: ?>
                                                <div class="product-img-thumb bg-light d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-image"></i></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($item['nome']) ?></div>
                                            <small class="text-muted">ID: #<?= $item['id'] ?></small>
                                        </td>
                                        <td class="text-end fw-bold">
                                            R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                        </td>
                                        <td>
                                            <input type="number" name="quantidade[<?= $item['id'] ?>]" value="<?= $item['quantidade'] ?>" min="1" class="form-control form-control-sm text-center">
                                        </td>
                                        <td class="text-end fw-bold">
                                            R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?route=carrinho-remover&id=<?= $item['id'] ?>" class="text-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card">
                        <h5 class="fw-bold mb-3">Resumo do Pedido</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Quantidade de Itens:</span>
                            <span class="fw-bold"><?= $totalItensQtd ?> un</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold">R$ <?= number_format($subtotalGeral, 2, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Total:</h5>
                            <h4 class="fw-extrabold text-danger mb-0">R$ <?= number_format($subtotalGeral, 2, ',', '.') ?></h4>
                        </div>
                        <a href="index.php?route=checkout" class="btn btn-danger w-100 fw-bold py-2 mt-3" style="background-color: #f15a5a; border: none;">
                             FORMA DE PAGAMENTO <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="bg-white border p-5 text-center my-4">
            <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
            <h5>O seu carrinho está vazio.</h5>
            <p class="text-muted mb-4">Adicione produtos para continuar a comprar.</p>
            <a href="index.php?route=loja" class="btn btn-danger fw-bold">Ir para a Loja</a>
        </div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto" style="font-size: 11px;">
    &copy; <?= date('Y') ?> - Sistema de Vendas MVC com Carrinho de Compras.
</footer>

</body>
<script src="js/tema.js"></script>
</html>