<?php
require_once __DIR__ . '/auth_state.php';

if (!isset($tituloPagina)) {
    $tituloPagina = 'DoraNina';
}
$usuarioLogado = isset($_SESSION['usuario_id']);
$usuarioAdmin = $usuarioLogado && (($_SESSION['usuario_tipo'] ?? '') === 'admin');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina); ?></title>
    <meta name="description" content="DoraNina - bolos artesanais sob encomenda, com pedidos online e acompanhamento pelo site.">
    <link rel="icon" type="image/png" href="assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/../assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="apple-touch-icon" href="assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/../assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= substr(md5_file(__DIR__ . '/../assets/css/style.css'), 0, 12); ?>">
</head>
<body>
<div class="site-bg" aria-hidden="true"></div>

<header class="topbar">
    <a class="brand" href="index.php" aria-label="DoraNina">
        <img src="assets/img/logo-doranina.png" alt="Logo DoraNina">
        <span>DoraNina</span>
    </a>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu">
        <i class="bi bi-list"></i>
    </button>

    <nav class="nav" id="mainNav">
        <a href="index.php#cardapio">Cardápio</a>
        <a href="index.php#como-funciona">Como funciona</a>
        <a href="index.php#avaliacoes">Avaliações</a>
        <a href="index.php#contato">Contato</a>
        <a href="carrinho.php" class="nav-cart">
            <i class="bi bi-bag-heart"></i>
            Carrinho
            <span data-cart-count>0</span>
        </a>

        <?php if ($usuarioLogado && !$usuarioAdmin): ?>
            <a href="meus_pedidos.php"><i class="bi bi-receipt"></i> Meus pedidos</a>
        <?php endif; ?>

        <?php if ($usuarioAdmin): ?>
            <a href="admin/dashboard.php" class="nav-admin-link">
                <i class="bi bi-shield-lock"></i>
                Painel admin
            </a>
        <?php endif; ?>

        <?php if ($usuarioLogado): ?>
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
        <?php else: ?>
            <a href="login.php"><i class="bi bi-person"></i> Entrar</a>
        <?php endif; ?>
    </nav>
</header>
