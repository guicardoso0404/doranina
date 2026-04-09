<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$pedidos = app_db_select_all('SELECT * FROM pedidos ORDER BY id DESC');
$itensPorPedido = [];
$mensagem = $_GET['msg'] ?? '';

if (!empty($pedidos)) {
    $pedidoIds = array_map(static fn (array $pedido): int => (int)$pedido['id'], $pedidos);
    $placeholders = implode(', ', array_fill(0, count($pedidoIds), '?'));
    $itens = app_db_select_all(
        "SELECT * FROM pedido_itens WHERE pedido_id IN ($placeholders) ORDER BY pedido_id DESC, id ASC",
        $pedidoIds
    );

    foreach ($itens as $item) {
        $itensPorPedido[(int)$item['pedido_id']][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos | DoraNina Admin</title>
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
                <span class="eyebrow">Operacao</span>
                <h1>Pedidos</h1>
            </div>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert-success"><?= htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <section class="panel-card">
            <div class="accordion-list">
                <?php if (!empty($pedidos)): ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php $pedidoId = (int)$pedido['id']; ?>
                        <details class="order-card">
                            <summary>
                                <div>
                                    <strong>#<?= $pedidoId; ?> · <?= htmlspecialchars($pedido['cliente_nome']); ?></strong>
                                    <small><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])); ?></small>
                                </div>
                                <div class="order-summary-right">
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $pedido['status'])); ?>"><?= htmlspecialchars($pedido['status']); ?></span>
                                    <strong>R$ <?= number_format((float)$pedido['total'], 2, ',', '.'); ?></strong>
                                </div>
                            </summary>

                            <div class="order-body">
                                <div class="order-grid">
                                    <div>
                                        <h3>Cliente</h3>
                                        <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['cliente_nome']); ?></p>
                                        <p><strong>Telefone:</strong> <?= htmlspecialchars($pedido['cliente_telefone']); ?></p>
                                        <p><strong>Tipo de entrega:</strong> <?= $pedido['tipo_entrega'] === 'motoboy' ? 'Entrega por motoboy' : 'Retirada no local'; ?></p>
                                        <p><strong>Endereco:</strong> <?= htmlspecialchars($pedido['cliente_endereco']); ?></p>
                                    </div>
                                    <div>
                                        <h3>Itens</h3>
                                        <ul class="order-items-list">
                                            <?php foreach (($itensPorPedido[$pedidoId] ?? []) as $item): ?>
                                                <li><?= (int)$item['quantidade']; ?>x <?= htmlspecialchars($item['nome_produto']); ?> - R$ <?= number_format((float)$item['subtotal'], 2, ',', '.'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <p><strong>Observacoes:</strong> <?= htmlspecialchars($pedido['observacoes'] ?: 'Sem observacoes.'); ?></p>
                                    </div>
                                </div>

                                <form action="atualizar_status.php" method="POST" class="status-form">
                                    <input type="hidden" name="id" value="<?= $pedidoId; ?>">
                                    <select name="status">
                                        <?php foreach (['Novo', 'Em andamento', 'Finalizado', 'Cancelado'] as $status): ?>
                                            <option value="<?= $status; ?>" <?= $pedido['status'] === $status ? 'selected' : ''; ?>><?= $status; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="primary-btn">Atualizar status</button>
                                </form>
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-inline">Nenhum pedido foi registrado ainda.</div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
