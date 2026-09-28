<?php ob_start(); ?>
<div class="container mt-4">
    <div class="row">
        
        <!-- Formulário para Adicionar -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fa-solid fa-plus me-1"></i> Nova Categoria
                </div>
                <div class="card-body">
                    <form action="index.php?route=categorias-store" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nome da Categoria</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Informática" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Categorias -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fa-solid fa-tags me-1"></i> Categorias Cadastradas
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome da Categoria</th>
                                <th class="text-center" style="width: 100px;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categorias)): ?>
                                <?php foreach ($categorias as $cat): ?>
                                    <tr>
                                        <td class="text-muted">#<?= $cat['id'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($cat['nome']) ?></td>
                                        <td class="text-center">
                                            <a href="index.php?route=categorias-delete&id=<?= $cat['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Tem certeza que deseja apagar a categoria <?= htmlspecialchars($cat['nome']) ?>?');">
                                               <i class="fa-solid fa-trash">x</i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layout.php';
?>