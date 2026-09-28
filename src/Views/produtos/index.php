<?php ob_start(); ?>
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white"><h5 class="mb-0">📦 Novo Produto</h5></div>
            <div class="card-body">
                <form action="index.php?route=produtos-store" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Preço *</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Estoque *</label>
                            <input type="number" name="estoque" class="form-control" required>
                        </div>
                    </div>

                    <!-- NOVO CAMPO: Categoria -->
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-select">
                            <option value="">Sem Categoria</option>
                            <option value="Eletronicos">Eletrônicos</option>
                            <option value="Roupas">Roupas</option>
                            <option value="Acessorios">Acessórios</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <input type="file" name="imagem" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar Produto</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white"><h5 class="mb-0">Lista de Produtos</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Categoria</th> <!-- Nova coluna na tabela -->
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th class="text-center" style="width: 140px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($produtos) && is_array($produtos)): ?>
                            <?php foreach ($produtos as $p): ?>
                            <?php 
                                // Proteções contra colunas vazias ou inexistentes no banco
                                $img = $p['imagem'] ?? $p['foto'] ?? '';
                                $nome = $p['nome'] ?? $p['titulo'] ?? 'Produto Sem Nome';
                                $categoria = $p['categoria'] ?? '';
                                $preco = (float)($p['preco'] ?? $p['valor'] ?? 0);
                                $estoque = (int)($p['estoque'] ?? $p['quantidade'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($img) && file_exists(__DIR__ . '/../../../public/uploads/' . $img)): ?>
                                        <img src="uploads/<?= htmlspecialchars($img) ?>" style="width:40px; height:40px; object-fit:cover;" class="rounded">
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Sem foto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($nome) ?></td>
                                
                                <!-- Exibição da Categoria -->
                                <td>
                                    <?php if (!empty($categoria)): ?>
                                        <span class="badge bg-info text-dark"><?= htmlspecialchars($categoria) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>R$ <?= number_format($preco, 2, ',', '.') ?></td>
                                <td><span class="badge bg-<?= $estoque > 5 ? 'success' : 'danger' ?>"><?= $estoque ?> un</span></td>
                                <td class="text-center">
                                    <!-- Botão Editar -->
                                    <a href="index.php?route=produtos-edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning" title="Editar">✏️</a>
                                    
                                    <!-- Botão Excluir com confirmação -->
                                    <a href="index.php?route=produtos-delete&id=<?= $p['id'] ?>" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Tem certeza que deseja excluir o produto <?= htmlspecialchars($nome) ?>?');" 
                                    title="Excluir">🗑️</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Nenhum produto encontrado na base de dados.</td>
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