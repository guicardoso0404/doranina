<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/conexao.php';
app_require_database();

if (($_SESSION['usuario_tipo'] ?? '') === 'admin') {
    header('Location: admin/pedidos.php');
    exit;
}

$tituloPagina = 'Meus pedidos | DoraNina';
$usuarioId = (int)$_SESSION['usuario_id'];
$pedidos = app_db_select_all('SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY id DESC', [$usuarioId]);
$itensPorPedido = [];
$sucesso = isset($_GET['sucesso']);

if (!empty($pedidos)) {
    $pedidoIds = array_map(static fn (array $pedido): int => (int)$pedido['id'], $pedidos);
    $placeholders = implode(', ', array_fill(0, count($pedidoIds), '?'));
    $itens = app_db_select_all(
        "SELECT pedido_id, nome_produto, quantidade, subtotal FROM pedido_itens WHERE pedido_id IN ($placeholders) ORDER BY pedido_id DESC, id ASC",
        $pedidoIds
    );

    foreach ($itens as $item) {
        $itensPorPedido[(int)$item['pedido_id']][] = $item;
    }
}

include 'includes/header.php';
?>
<main>
    <section class="page-hero section page-section">
        <span class="eyebrow">Minha conta</span>
        <h1>Meus pedidos</h1>
        <p>Acompanhe o historico e o status das encomendas feitas pela sua conta.</p>
    </section>

    <section class="section checkout-section">
        <div class="checkout-card">
            <div class="panel-heading">
                <h2>Pedidos de <?= htmlspecialchars($_SESSION['usuario_nome']); ?></h2>
            </div>

            <?php if ($sucesso): ?>
                <div class="alert-success" style="margin-bottom:20px;">Pedido realizado com sucesso! Seu carrinho foi limpo e o pedido ja aparece no historico.</div>
            <?php endif; ?>

            <div class="accordion-list customer-orders-list">
                <?php if (!empty($pedidos)): ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php $pedidoId = (int)$pedido['id']; ?>
                        <details class="order-card customer-order-card">
                            <summary>
                                <div>
                                    <strong>#<?= $pedidoId; ?> · <?= $pedido['tipo_entrega'] === 'motoboy' ? 'Entrega por motoboy' : 'Retirada no local'; ?></strong>
                                    <small>Feito em <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])); ?></small>
                                </div>
                                <div class="order-summary-right">
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $pedido['status'])); ?>"><?= htmlspecialchars($pedido['status']); ?></span>
                                    <strong>R$ <?= number_format((float)$pedido['total'], 2, ',', '.'); ?></strong>
                                </div>
                            </summary>
                            <div class="order-body">
                                <div class="order-grid">
                                    <div>
                                        <h3>Entrega</h3>
                                        <p><strong>Tipo:</strong> <?= $pedido['tipo_entrega'] === 'motoboy' ? 'Entrega por motoboy' : 'Retirada no local'; ?></p>
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
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-inline">Voce ainda nao fez nenhum pedido com esta conta.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
