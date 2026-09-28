<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-12 text-center text-md-start">
        <h2 class="fw-bold">📊 Painel de Controle</h2>
        <p class="text-muted">Bem-vindo ao Sistema de Gestão de Vendas e Estoque.</p>
    </div>
</div>

<!-- Cards Informativos (KPIs) -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Faturamento Total</h6>
                    <h3 class="mb-0 fw-bold">R$ <?= number_format($faturamento, 2, ',', '.') ?></h3>
                </div>
                <div class="fs-1">💰</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Vendas Realizadas</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalVendas ?></h3>
                </div>
                <div class="fs-1">🛒</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Produtos Cadastrados</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalProdutos ?></h3>
                </div>
                <div class="fs-1">📦</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Itens em Estoque</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalEstoque ?> un</h3>
                </div>
                <div class="fs-1">📊</div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="fs-1 mb-2">🛍️</div>
                <h4>Frente de Caixa (PDV)</h4>
                <p class="text-muted">Inicie uma nova venda rápida, adicione produtos ao carrinho e dê baixa no estoque em tempo real.</p>
                <a href="index.php?route=vendas" class="btn btn-primary btn-lg mt-2">Ir para o PDV</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="fs-1 mb-2">📦</div>
                <h4>Gerenciar Produtos</h4>
                <p class="text-muted">Cadastre novos produtos, envie fotos de apresentação, consulte preços e ajuste o estoque.</p>
                <a href="index.php?route=produtos" class="btn btn-dark btn-lg mt-2">Gerenciar Produtos</a>
            </div>
        </div>
    </div>

    <a href="index.php?route=admin-banners" class="btn btn-primary fw-bold">
    <i class="fa-solid fa-images me-1"></i> Gerenciar Banners
    </a>

    <a href="index.php?route=admin-vendas" class="btn btn-success fw-bold m-1">
        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Gestão de Vendas
    </a>

    <a href="index.php?route=categorias" class="btn btn-warning fw-bold m-1">
        <i class="fa-solid fa-tags me-2"></i> Categorias
    </a>
</div>

<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layout.php';