<?php 
    // Proteções para não dar erro caso algum campo esteja vazio na base de dados
    $pId = $produto['id'] ?? 0;
    $pNome = $produto['nome'] ?? '';
    $pPreco = $produto['preco'] ?? '';
    $pEstoque = $produto['estoque'] ?? 0;
    $pCategoria = $produto['categoria'] ?? '';
    $pImagem = $produto['imagem'] ?? '';
    $pAtivo = isset($produto['ativo']) ? $produto['ativo'] : 1;
?>
<?php ob_start(); ?>
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">✏️ Editar Produto</h5>
                    <a href="index.php?route=produtos" class="btn btn-sm btn-dark"><i class="fa-solid fa-arrow-left me-1"></i> Voltar</a>
                </div>
                <div class="card-body p-4">
                    
                    <form action="index.php?route=produtos-update" method="POST" enctype="multipart/form-data">
                        <!-- Campo Oculto com o ID do Produto -->
                        <input type="hidden" name="id" value="<?= $pId ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome *</label>
                            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($pNome) ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Preço *</label>
                                <input type="text" name="preco" class="form-control" required value="<?= htmlspecialchars((string)$pPreco) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição do Produto</label>
                                <textarea name="descricao" class="form-control" rows="5" placeholder="Escreva os detalhes, especificações e vantagens do produto..."><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Estoque *</label>
                                <input type="number" name="estoque" class="form-control" required value="<?= $pEstoque ?>">
                            </div>
                            
                            <!-- NOVO CAMPO: Categoria (com seleção automática) -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-primary">Categoria</label>
                                <select name="categoria" class="form-select border-primary">
                                    <option value="" <?= $pCategoria === '' ? 'selected' : '' ?>>Sem Categoria</option>
                                    <option value="Eletronicos" <?= $pCategoria === 'Eletronicos' ? 'selected' : '' ?>>Eletrônicos</option>
                                    <option value="Roupas" <?= $pCategoria === 'Roupas' ? 'selected' : '' ?>>Roupas</option>
                                    <option value="Acessorios" <?= $pCategoria === 'Acessorios' ? 'selected' : '' ?>>Acessórios</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Imagem (Deixe em branco para manter a atual)</label>
                            <input type="file" name="imagem" class="form-control" accept="image/*">
                            
                            <!-- Mostra a imagem atual caso ela exista -->
                            <?php if (!empty($pImagem) && file_exists(__DIR__ . '/../../../public/uploads/' . $pImagem)): ?>
                                <div class="mt-3 p-2 border rounded d-inline-block bg-light">
                                    <img src="uploads/<?= htmlspecialchars($pImagem) ?>" style="height: 60px; object-fit: cover;" class="rounded">
                                    <span class="ms-2 text-muted small">Imagem atual</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Produto Ativo/Inativo -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" <?= $pAtivo == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="ativo">Produto Ativo (Visível na Loja)</label>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success fw-bold px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$content = ob_get_clean(); 
require_once __DIR__ . '/../layout.php';
?>