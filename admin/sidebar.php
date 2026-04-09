<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <img src="../assets/img/logo-doranina.png" alt="Logo DoraNina">
        <div>
            <strong>DoraNina</strong>
            <small>Painel administrativo</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : ''; ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
        <a href="produtos.php" class="<?= in_array($current, ['produtos.php', 'editar_produto.php']) ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Produtos</a>
        <a href="pedidos.php" class="<?= $current === 'pedidos.php' ? 'active' : ''; ?>"><i class="bi bi-receipt-cutoff"></i> Pedidos</a>
        <a href="cadastro.php" class="<?= $current === 'cadastro.php' ? 'active' : ''; ?>"><i class="bi bi-person-plus"></i> Cadastrar admin</a>
    </nav>

    <div class="sidebar-secondary">
        <a href="../index.php" target="_blank"><i class="bi bi-arrow-up-right-square"></i> Ver loja</a>
        <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
</aside>
