<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?= $venda['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 border rounded shadow-sm">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <h3 class="fw-bold m-0"><i class="fa-solid fa-box-open me-2 text-primary"></i>Pedido #<?= $venda['id'] ?></h3>
        <a href="index.php?route=admin-vendas" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar para Vendas
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fa-solid fa-user me-1"></i> Dados do Cliente
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nome:</strong> <?= htmlspecialchars($venda['cliente_nome']) ?></p>
                    <p class="mb-1"><strong>E-mail:</strong> <?= htmlspecialchars($venda['cliente_email']) ?></p>
                    <p class="mb-1"><strong>Telefone:</strong> <?= htmlspecialchars($venda['cliente_telefone']) ?></p>
                    <p class="mb-1 mt-3"><strong>Endereço de Entrega:</strong><br> <?= nl2br(htmlspecialchars($venda['endereco'])) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fa-solid fa-credit-card me-1"></i> Informações do Pagamento
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Data da Compra:</strong> <?= date('d/m/Y às H:i', strtotime($venda['created_at'] ?? 'now')) ?></p>
                    <p class="mb-1"><strong>Forma de Pagamento:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($venda['forma_pagamento']) ?></span></p>
                    <hr>
                    <h4 class="fw-bold text-success m-0">Total: R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3"><i class="fa-solid fa-list-check me-2"></i>Itens do Pedido</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">ID Prod</th>
                    <th>Produto</th>
                    <th class="text-center" style="width: 100px;">Qtd</th>
                    <th class="text-end" style="width: 150px;">Preço Unit.</th>
                    <th class="text-end" style="width: 150px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <?php 
                        $nomeProd = $item['nome'] ?? $item['titulo'] ?? 'Produto Desconhecido';
                        $subtotal = $item['quantidade'] * $item['preco'];
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?= $item['produto_id'] ?></td>
                        <td><?= htmlspecialchars($nomeProd) ?></td>
                        <td class="text-center"><?= $item['quantidade'] ?>x</td>
                        <td class="text-end">R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td class="text-end fw-bold">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end fw-bold">Valor Total do Pedido:</td>
                    <td class="text-end fw-bold text-danger fs-5">R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</body>
</html>