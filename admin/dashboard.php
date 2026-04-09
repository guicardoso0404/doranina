<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$totalProdutos = (int)app_db_select_value('SELECT COUNT(*) FROM produtos');
$totalAtivos = (int)app_db_select_value('SELECT COUNT(*) FROM produtos WHERE ativo = 1');
$totalPedidos = (int)app_db_select_value('SELECT COUNT(*) FROM pedidos');
$totalNovos = (int)app_db_select_value("SELECT COUNT(*) FROM pedidos WHERE status = 'Novo'");
$ultimosPedidos = app_db_select_all('SELECT id, cliente_nome, total, status, criado_em FROM pedidos ORDER BY id DESC LIMIT 5');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | DoraNina Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/../assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="apple-touch-icon" href="../assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/../assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= substr(md5_file(__DIR__ . '/../assets/css/admin.css'), 0, 12); ?>">
</head>
<body class="admin-body">
    <?php include 'sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <span class="eyebrow">Visao geral</span>
                <h1>Dashboard</h1>
            </div>
            <div class="admin-user">Ola, <?= htmlspecialchars($_SESSION['usuario_nome']); ?></div>
        </div>

        <section class="metric-grid">
            <article class="metric-card"><i class="bi bi-box-seam"></i><div><strong><?= $totalProdutos; ?></strong><span>Produtos cadastrados</span></div></article>
            <article class="metric-card"><i class="bi bi-check2-circle"></i><div><strong><?= $totalAtivos; ?></strong><span>Produtos ativos</span></div></article>
            <article class="metric-card"><i class="bi bi-receipt"></i><div><strong><?= $totalPedidos; ?></strong><span>Pedidos registrados</span></div></article>
            <article class="metric-card"><i class="bi bi-clock-history"></i><div><strong><?= $totalNovos; ?></strong><span>Pedidos novos</span></div></article>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <h2>Ultimos pedidos</h2>
                <a href="pedidos.php">Ver todos</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimosPedidos as $pedido): ?>
                            <tr>
                                <td>#<?= (int)$pedido['id']; ?></td>
                                <td><?= htmlspecialchars($pedido['cliente_nome']); ?></td>
                                <td>R$ <?= number_format((float)$pedido['total'], 2, ',', '.'); ?></td>
                                <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $pedido['status'])); ?>"><?= htmlspecialchars($pedido['status']); ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
