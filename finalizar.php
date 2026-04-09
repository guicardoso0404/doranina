<?php
require_once __DIR__ . '/includes/auth.php';

if (($_SESSION['usuario_tipo'] ?? '') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$tituloPagina = 'Finalizar pedido | DoraNina';
include 'includes/header.php';
?>
<main>
    <section class="page-hero section page-section">
        <span class="eyebrow">Finalização</span>
        <h1>Confirme os dados do pedido</h1>
        <p>O pedido será salvo no sistema e você poderá acompanhar o status pela sua conta.</p>
    </section>

    <section class="section checkout-section">
        <div class="checkout-layout">
            <div class="checkout-card">
                <div class="panel-heading">
                    <h2>Dados do cliente</h2>
                </div>

                <form action="processar_pedido.php" method="POST" id="checkout-form" class="checkout-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="cliente_nome">Nome</label>
                            <input type="text" name="cliente_nome" id="cliente_nome" value="<?= htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="cliente_telefone">Telefone</label>
                            <input type="text" name="cliente_telefone" id="cliente_telefone" required>
                        </div>

                    <div class="form-group">
                        <label for="tipo_entrega">Tipo de entrega</label>
                        <div class="select-modern">
                            <select name="tipo_entrega" id="tipo_entrega" required>
                                <option value="">Selecione uma opção</option>
                                <option value="retirada">Vou retirar no local</option>
                                <option value="motoboy">Receber por motoboy</option>
                            </select>
                        </div>
                    </div>

                        <div class="form-group full" id="endereco-group">
                            <label for="cliente_endereco">Endereço</label>
                            <input type="text" name="cliente_endereco" id="cliente_endereco" placeholder="Rua, número, bairro e referência">
                            <small id="endereco-help" style="display:block;margin-top:8px;color:var(--muted);">Informe o endereço apenas se escolher entrega por motoboy.</small>
                        </div>

                        <div class="form-group full">
                            <label for="observacoes">Observações</label>
                            <textarea name="observacoes" id="observacoes" rows="4" placeholder="Ponto de referência, preferência de cobertura, retirada de algum ingrediente, nome na embalagem..."></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="itens_json" id="itens_json">
                    <input type="hidden" name="total" id="total_input">

                    <button type="submit" class="btn btn-primary submit-btn">
                        <i class="bi bi-bag-check"></i>
                        Finalizar pedido
                    </button>
                </form>
            </div>

            <aside class="summary-card sticky-card">
                <span class="eyebrow">Resumo</span>
                <h3>Itens do pedido</h3>
                <div id="checkout-items" class="mini-cart"></div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <strong id="checkout-total">R$ 0,00</strong>
                </div>
            </aside>
        </div>
    </section>
</main>

<script src="assets/js/carrinho.js?v=<?= substr(md5_file(__DIR__ . '/assets/js/carrinho.js'), 0, 12); ?>"></script>
<script>
    prepararCheckout();
    initCheckoutDelivery();
</script>
<?php include 'includes/footer.php'; ?>
