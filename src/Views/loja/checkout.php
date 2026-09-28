<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma de Pagamento - Loja Virtual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .payment-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .payment-option { border: 2px solid #e5e5e5; border-radius: 8px; padding: 15px; cursor: pointer; transition: 0.3s; margin-bottom: 15px; display: flex; align-items: center; }
        .payment-option:hover { border-color: #0d6efd; background-color: #f8fbff; }
        .payment-option.active { border-color: #0d6efd; background-color: #eff5ff; }
        .payment-icon { font-size: 24px; margin-right: 15px; width: 40px; text-align: center; }
        .btn-pagar { background-color: #28a745; color: white; font-weight: bold; font-size: 18px; padding: 12px; border: none; border-radius: 6px; width: 100%; transition: 0.3s; }
        .btn-pagar:hover { background-color: #218838; }
    </style>
</head>
<body>

<!-- Header Simples para Checkout -->
<div class="bg-white border-bottom py-3 mb-5">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="m-0 fw-bold"><i class="fa-solid fa-lock text-success me-2"></i> Pagamento Seguro</h4>
        <a href="index.php?route=carrinho-ver" class="text-decoration-none text-secondary">Voltar ao Carrinho</a>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="payment-card">
                
                <h3 class="mb-2">Olá, <?= htmlspecialchars(explode(' ', trim($_SESSION['cliente_nome']))[0]); ?>!</h3>
                <?php
// Calcula o valor total do carrinho diretamente para garantir que nunca vem zerado
$totalCarrinho = 0.0;
if (!empty($_SESSION['carrinho'])) {
    $prodModelTemp = new \Models\Produto();
    foreach ($_SESSION['carrinho'] as $pId => $qtd) {
        $q = is_array($qtd) ? ($qtd['quantidade'] ?? $qtd['qtd'] ?? 1) : (int)$qtd;
        $prod = method_exists($prodModelTemp, 'getById') ? $prodModelTemp->getById($pId) : null;
        if ($prod) {
            $preco = (float)str_replace(',', '.', $prod['preco'] ?? $prod['valor'] ?? 0);
            $totalCarrinho += ($preco * $q);
        }
    }
}
?>

<p class="text-muted mb-4">Escolha como deseja pagar o seu pedido de <strong>R$ <?= number_format($totalCarrinho, 2, ',', '.') ?></strong>.</p>

                <!-- Formulário que envia os dados para o Asaas -->
                <form action="index.php?route=processar-pagamento" method="POST">
                    
                    <!-- O Asaas exige um CPF válido para gerar PIX e Boleto -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label class="form-label fw-bold mb-1">Confirme o seu CPF/CNPJ (Obrigatório)</label>
                        <input type="text" name="cpf" class="form-control form-control-lg" placeholder="000.000.000-00" required>
                        <small class="text-muted">Necessário para a emissão da nota e do PIX.</small>
                    </div>

                    <h5 class="fw-bold mb-3">Selecione a forma de pagamento:</h5>
                    
                    <!-- Opção PIX -->
                    <label class="payment-option active" onclick="selecionarPagamento(this)">
                        <input type="radio" name="forma_pagamento" value="PIX" class="d-none" checked>
                        <div class="payment-icon text-success"><i class="fa-brands fa-pix"></i></div>
                        <div>
                            <strong class="d-block fs-5">PIX</strong>
                            <span class="text-muted small">Aprovação imediata. Escaneie o QR Code.</span>
                        </div>
                    </label>

                    <!-- Opção Boleto -->
                    <label class="payment-option" onclick="selecionarPagamento(this)">
                        <input type="radio" name="forma_pagamento" value="BOLETO" class="d-none">
                        <div class="payment-icon text-secondary"><i class="fa-solid fa-barcode"></i></div>
                        <div>
                            <strong class="d-block fs-5">Boleto Bancário</strong>
                            <span class="text-muted small">Aprovação em até 2 dias úteis.</span>
                        </div>
                    </label>

                    <button type="submit" class="btn-pagar mt-4 mt-2">
                        <i class="fa-solid fa-check me-2"></i> Finalizar Pagamento
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>


<script>
    // Apenas marca visualmente a opção de pagamento escolhida (PIX ou Boleto)
    function selecionarPagamento(elemento) {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('active'));
        elemento.classList.add('active');
    }
</script>
</body>
</html>