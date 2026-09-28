<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar ou Criar Conta - Loja</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .voltar { display: block; text-align: center; margin-bottom: 30px; color: #333; text-decoration: none; font-weight: bold; }
        .container { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; max-width: 900px; margin: 0 auto; }
        .box { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 350px; }
        .box h2 { margin-top: 0; color: #333; font-size: 22px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; color: #fff; font-weight: bold; }
        .btn-login { background-color: #007bff; }
        .btn-login:hover { background-color: #0056b3; }
        .btn-registo { background-color: #28a745; }
        .btn-registo:hover { background-color: #218838; }
        .alerta { padding: 10px; margin-bottom: 15px; border-radius: 4px; color: #fff; background-color: #dc3545; text-align: center; font-size: 14px; }
    </style>
</head>
<body>

    <a href="index.php?route=loja" class="voltar">&larr; Voltar para a Loja</a>

    <div class="container">
        <!-- CAIXA DE LOGIN -->
        <div class="box">
            <h2>Já sou cliente</h2>
            
            <?php if (isset($_SESSION['erro_login'])): ?>
                <div class="alerta"><?= $_SESSION['erro_login']; unset($_SESSION['erro_login']); ?></div>
            <?php endif; ?>

            <form action="index.php?route=cliente-fazer-login" method="POST">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required placeholder="seu@email.com">
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required placeholder="Sua senha">
                </div>
                <button type="submit" class="btn btn-login">Entrar e Continuar</button>
            </form>
        </div>

        <!-- CAIXA DE REGISTO -->
        <div class="box">
            <h2>Criar nova conta</h2>
            
            <?php if (isset($_SESSION['erro_registo'])): ?>
                <div class="alerta"><?= $_SESSION['erro_registo']; unset($_SESSION['erro_registo']); ?></div>
            <?php endif; ?>

            <form action="index.php?route=cliente-fazer-registo" method="POST">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" required placeholder="João Silva">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required placeholder="seu@email.com">
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required placeholder="Crie uma senha">
                </div>
                <button type="submit" class="btn btn-registo">Criar Conta</button>
            </form>
        </div>
    </div>

</body>
</html>