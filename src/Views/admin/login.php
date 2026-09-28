<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .login-box { width: 100%; max-width: 400px; margin: 80px auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="login-box card shadow-lg border-0 rounded-3">
        <div class="card-header bg-dark text-white text-center py-4 rounded-top-3">
            <h4 class="mb-0"><i class="fa-solid fa-lock me-2"></i> Área Restrita</h4>
        </div>
        <div class="card-body p-4 p-md-5">
            
            <?php if (isset($_SESSION['erro_login'])): ?>
                <div class="alert alert-danger text-center fw-bold">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> <?= $_SESSION['erro_login']; ?>
                    <?php unset($_SESSION['erro_login']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?route=login-auth" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" required placeholder="E-mail">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-key"></i></span>
                        <input type="password" name="senha" class="form-control" required placeholder="Senha">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold fs-5">
                    Entrar no Painel <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                </button>
            </form>
            
        </div>
    </div>
</div>

</body>
</html>