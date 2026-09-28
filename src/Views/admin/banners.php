<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Banners - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 border rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fa-solid fa-images me-2"></i>Gerenciamento de Banners</h2>
        <a href="index.php?route=loja" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-store me-1"></i> Ver Loja
        </a>
    </div>

    <!-- Formulário de Envio de Imagem -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fa-solid fa-plus me-1"></i> Adicionar Novo Banner
        </div>
        <div class="card-body">
            <form action="index.php?route=admin-banners-salvar" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Posição</label>
                    <select name="posicao" class="form-select" required>
                        <option value="principal">Principal (Carrossel)</option>
                        <option value="lateral_1">Lateral 1 (Topo Direita)</option>
                        <option value="lateral_2">Lateral 2 (Baixo Direita)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Título</label>
                    <input type="text" name="titulo" class="form-control" placeholder="Ex: NOVO BANNER">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Subtítulo</label>
                    <input type="text" name="subtitulo" class="form-control" placeholder="Ex: Ofertas da semana">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Imagem do Banner</label>
                    <input type="file" name="imagem" class="form-control" accept="image/*" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fa-solid fa-upload me-1"></i> Salvar Banner
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Posição</th>
                    <th>Título</th>
                    <th>Subtítulo</th>
                    <th>Imagem</th>
                    <th style="width: 100px;" class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($banners)): ?>
                    <?php foreach ($banners as $b): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($b['posicao'] ?? 'principal') ?></span></td>
                            <td><strong><?= htmlspecialchars($b['titulo'] ?? '') ?></strong></td>
                            <td><small class="text-muted"><?= htmlspecialchars($b['subtitulo'] ?? '') ?></small></td>
                            <td>
                                <?php if (!empty($b['imagem'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($b['imagem']) ?>" style="height: 50px; width: 100px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span class="badge bg-danger">Sem imagem (NULL)</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?route=admin-banners-deletar&id=<?= $b['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este banner?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">Nenhum banner cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>