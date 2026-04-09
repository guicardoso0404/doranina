<?php
$tituloPagina = 'Carrinho | DoraNina';
include 'includes/header.php';
?>
<main>
    <section class="page-hero section page-section">
        <span class="eyebrow">Seu pedido</span>
        <h1>Carrinho DoraNina</h1>
        <p>Confira os bolos selecionados, ajuste quantidades e siga para a finalização.</p>
    </section>

    <section class="section cart-section">
        <div class="cart-layout">
            <div class="cart-panel">
                <div class="panel-heading">
                    <h2>Itens selecionados</h2>
                    <a href="index.php#cardapio" class="text-link">Continuar comprando</a>
                </div>

                <div id="cart-items" class="cart-items"></div>

                <div id="empty-cart" class="empty-state hidden">
                    <i class="bi bi-bag-x"></i>
                    <h3>Seu carrinho está vazio</h3>
                    <p>Adicione produtos no catálogo para começar o pedido.</p>
                    <a href="index.php#cardapio" class="btn btn-primary">Ir para o cardápio</a>
                </div>
            </div>

            <aside class="summary-card sticky-card">
                <span class="eyebrow">Resumo</span>
                <h3>Pronto para finalizar?</h3>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong id="summary-subtotal">R$ 0,00</strong>
                </div>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <strong id="summary-total">R$ 0,00</strong>
                </div>

                <div class="summary-actions">
                    <a href="finalizar.php" class="btn btn-primary" id="go-checkout">
                        <i class="bi bi-bag-check"></i>
                        Continuar
                    </a>
                    <button type="button" class="btn btn-secondary" onclick="clearCart()">Limpar pedido</button>
                </div>
            </aside>
        </div>
    </section>
</main>

<script src="assets/js/carrinho.js?v=<?= substr(md5_file(__DIR__ . '/assets/js/carrinho.js'), 0, 12); ?>"></script>
<?php include 'includes/footer.php'; ?>
