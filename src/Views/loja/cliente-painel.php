<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente - Loja Virtual</title>
    <!-- Adicionamos o Bootstrap e o FontAwesome para garantir que o visual fica igual à foto -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; margin: 0; padding: 0; overflow-x: hidden; }
        
        /* Estrutura principal dividida em duas partes */
        .painel-wrapper { display: flex; min-height: 100vh; width: 100vw; }
        
        /* O Sidebar Exato da sua imagem */
        .sidebar { 
            width: 280px; 
            background-color: #f8f9fa; 
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            padding: 1rem;
        }
        
        .sidebar-brand { display: flex; align-items: center; color: #212529; text-decoration: none; margin-bottom: 1rem; }
        .sidebar-brand .logo-icon { background: #212529; color: white; border-radius: 6px; padding: 5px 10px; font-weight: bold; margin-right: 10px; font-size: 18px; }
        .sidebar-brand span { font-size: 20px; font-weight: 500; }
        
        .nav-pills .nav-link { color: #212529; margin-bottom: 5px; border-radius: 6px; transition: 0.2s; display: flex; align-items: center; }
        .nav-pills .nav-link i { width: 24px; text-align: center; margin-right: 10px; color: #6c757d; }
        
        .nav-pills .nav-link:hover { background-color: #e9ecef; }
        
        /* Botão Ativo Azul idêntico à imagem */
        .nav-pills .nav-link.active { background-color: #0d6efd; color: white; font-weight: 500; }
        .nav-pills .nav-link.active i { color: white; }
        
        /* Área Principal à Direita */
        .content-area { flex-grow: 1; background-color: #ffffff; padding: 40px; }
        
        /* Remove o sublinhado do dropdown do utilizador */
        .dropdown-toggle { text-decoration: none; }
    </style>
</head>
<body>

<div class="painel-wrapper">
    
    <!-- SIDEBAR (MENU LATERAL) -->
    <div class="sidebar">
        <!-- Logo e Título -->
        <a href="index.php?route=loja" class="sidebar-brand">
            <div class="logo-icon"><i class="fa-solid fa-store"></i></div>
            <span>Minha Loja</span>
        </a>
        
        <hr class="text-secondary">
        
        <!-- Lista de Links -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="index.php?route=cliente-painel" class="nav-link active" aria-current="page">
                    <i class="fa-solid fa-house"></i>
                    Início
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-box-open"></i>
                    Meus Pedidos
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-address-card"></i>
                    Meus Dados
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-headset"></i>
                    Suporte
                </a>
            </li>
        </ul>
        
        <hr class="text-secondary">
        
        <!-- Menu do Utilizador (Rodapé do Sidebar) -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center link-dark dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <!-- Bolinha com a Inicial do Nome do Cliente usando uma API gratuita de avatares -->
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['cliente_nome']); ?>&background=212529&color=fff&rounded=true" alt="User" width="32" height="32" class="me-2">
                <strong><?= htmlspecialchars(explode(' ', trim($_SESSION['cliente_nome']))[0]); ?></strong>
            </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
                <li><a class="dropdown-item" href="index.php?route=loja">Voltar à Loja</a></li>
                <li><a class="dropdown-item" href="#">Configurações</a></li>
                <li><a class="dropdown-item" href="#">Meu Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger fw-bold" href="index.php?route=cliente-logout">Sair</a></li>
            </ul>
        </div>
    </div>
    <!-- FIM DO SIDEBAR -->


    <!-- ÁREA DE CONTEÚDO PRINCIPAL -->
    <div class="content-area">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h2 class="fw-bold m-0">Visão Geral</h2>
            <a href="index.php?route=loja" class="btn btn-outline-secondary btn-sm">&larr; Voltar para a Loja</a>
        </div>
        
        <div class="alert alert-primary d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-info fs-4 me-3"></i>
            <div>
                <strong>Bem-vindo(a), <?= htmlspecialchars($_SESSION['cliente_nome']); ?>!</strong><br>
                Este é o seu painel de cliente. Aqui você poderá acompanhar os seus pedidos e atualizar as suas informações em breve.
            </div>
        </div>
        
        <!-- Cards de Resumo -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-light border-0 shadow-sm">
                    <div class="card-body text-center py-4">
                        <i class="fa-solid fa-box-open fs-1 text-muted mb-3"></i>
                        <h5 class="card-title">0</h5>
                        <p class="card-text text-muted">Pedidos Realizados</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <!-- FIM DA ÁREA DE CONTEÚDO -->

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>