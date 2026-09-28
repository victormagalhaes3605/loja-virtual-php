<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Vendas - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 border rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Gestão de Vendas</h2>
        <a href="index.php?route=admin" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Painel
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 80px;">Pedido #</th>
                    <th>Cliente</th>
                    <th>Data da Compra</th>
                    <th>Pagamento</th>
                    <th>Total</th>
                    <th style="width: 120px;" class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($vendas)): ?>
                    <?php foreach ($vendas as $v): ?>
                        <tr>
                            <td class="fw-bold text-center"><?= $v['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($v['cliente_nome']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($v['cliente_email']) ?></small>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($v['created_at'] ?? 'now')) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($v['forma_pagamento']) ?></span></td>
                            <td class="fw-bold text-success">R$ <?= number_format($v['valor_total'], 2, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="index.php?route=admin-vendas-detalhe&id=<?= $v['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-eye me-1"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhuma venda registada até o momento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>