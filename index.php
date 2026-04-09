<?php
require_once 'config/conexao.php';

$tituloPagina = 'DoraNina | Bolos artesanais sob encomenda';

$categorias = [];
$produtos = [];
$destaques = [];
$dbDisponivel = app_db_ready();

if ($dbDisponivel) {
    $categorias = array_map(
        static fn (array $row): string => (string)$row['categoria'],
        app_db_select_all("SELECT DISTINCT categoria FROM produtos WHERE ativo = 1 AND categoria IS NOT NULL AND categoria <> '' ORDER BY categoria ASC")
    );
    $produtos = app_db_select_all('SELECT * FROM produtos WHERE ativo = 1 ORDER BY destaque DESC, id DESC');
    $destaques = app_db_select_all('SELECT * FROM produtos WHERE ativo = 1 AND destaque = 1 ORDER BY id DESC LIMIT 3');
}

include 'includes/header.php';
?>
<main>
    <section class="hero section" id="inicio">
        <div class="hero-copy">
            <span class="eyebrow">100% handmade • sob encomenda</span>
            <h1>Bolos artesanais com estética delicada e sabor de lembrança boa.</h1>
            <p>
                Escolha os sabores, monte o carrinho no site e finalize tudo online com sua conta, sem precisar mandar mensagem para a loja.
            </p>

            <div class="hero-actions">
                <a class="btn btn-primary" href="#cardapio">
                    <i class="bi bi-cake2"></i>
                    Ver cardápio
                </a>
                <a class="btn btn-secondary" href="carrinho.php">
                    <i class="bi bi-bag-heart"></i>
                    Ver carrinho
                </a>
            </div>

            <div class="hero-badges">
                <div class="mini-card">
                    <strong>Entrega</strong>
                    <span>Canela, Gramado, Novo Hamburgo e região</span>
                </div>
                <div class="mini-card">
                    <strong>Antecedência</strong>
                    <span>Pedidos com pelo menos 2 horas</span>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="stamp">made with love</div>
            <div class="poster-card poster-main">
                <div class="poster-title">DoraNina</div>
                <div class="poster-photo"></div>
                <div class="poster-note">essa semana tem entrega!</div>
            </div>
            <div class="poster-card poster-floating">
                <img src="assets/img/logo-doranina.png" alt="Identidade visual DoraNina">
            </div>
        </div>
    </section>

    <section class="section highlights">
        <div class="highlight-pill"><i class="bi bi-heart-fill"></i> receitas afetivas</div>
        <div class="highlight-pill"><i class="bi bi-clock-history"></i> produção fresca</div>
        <div class="highlight-pill"><i class="bi bi-geo-alt-fill"></i> sob encomenda</div>
        <div class="highlight-pill"><i class="bi bi-bag-check-fill"></i> carrinho + pedidos pelo site</div>
    </section>

    <?php if (!empty($destaques)): ?>
    <section class="section featured-section">
        <div class="section-heading">
            <span class="eyebrow">Destaques</span>
            <h2>Favoritos da semana</h2>
            <p>Produtos em evidência no painel da confeitaria.</p>
        </div>

        <div class="featured-grid">
            <?php foreach ($destaques as $item): ?>
                <article class="featured-card">
                    <div class="featured-image">
                        <?php if (!empty($item['imagem'])): ?>
                            <img src="<?= htmlspecialchars($item['imagem']); ?>" alt="<?= htmlspecialchars($item['nome']); ?>">
                        <?php else: ?>
                            <div class="poster-photo"></div>
                        <?php endif; ?>
                    </div>
                    <div class="featured-body">
                        <span class="tag">destaque</span>
                        <h3><?= htmlspecialchars($item['nome']); ?></h3>
                        <p><?= htmlspecialchars($item['descricao']); ?></p>
                        <strong>A partir de R$ <?= number_format((float)$item['preco'], 2, ',', '.'); ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="section menu-section" id="cardapio">
        <div class="section-heading">
            <span class="eyebrow">Cardápio principal</span>
            <h2>Sabores com a cara da DoraNina</h2>
            <p>Use os filtros, adicione os bolos ao carrinho e finalize o pedido pelo próprio site.</p>
        </div>

        <div class="catalog-toolbar">
            <div class="filters" id="categoryFilters">
                <button class="filter-btn active" data-category="todos">Todos</button>
                <?php foreach ($categorias as $categoria): ?>
                    <button class="filter-btn" data-category="<?= htmlspecialchars($categoria); ?>"><?= htmlspecialchars($categoria); ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="menu-grid" id="productGrid">
            <?php if (!$dbDisponivel): ?>
                <div class="empty-block">
                    <i class="bi bi-tools"></i>
                    <h3>Catalogo em configuracao</h3>
                    <p>O site ja esta online, mas o banco de dados de producao ainda nao foi conectado.</p>
                </div>
            <?php elseif (!empty($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                    <article class="menu-card product-card" data-category="<?= htmlspecialchars($produto['categoria'] ?: 'Sem categoria'); ?>">
                        <div class="menu-card-top">
                            <span class="tag"><?= htmlspecialchars($produto['categoria'] ?: 'artesanal'); ?></span>
                            <?php if ((int)$produto['destaque'] === 1): ?>
                                <button class="icon-btn" aria-label="Destaque"><i class="bi bi-star-fill"></i></button>
                            <?php else: ?>
                                <button class="icon-btn" aria-label="Produto"><i class="bi bi-heart"></i></button>
                            <?php endif; ?>
                        </div>

                        <div class="product-thumb">
                            <?php if (!empty($produto['imagem'])): ?>
                                <img src="<?= htmlspecialchars($produto['imagem']); ?>" alt="<?= htmlspecialchars($produto['nome']); ?>">
                            <?php else: ?>
                                <div class="product-thumb-placeholder"><i class="bi bi-cake2-fill"></i></div>
                            <?php endif; ?>
                        </div>

                        <h3><?= htmlspecialchars($produto['nome']); ?></h3>
                        <p><?= htmlspecialchars($produto['descricao']); ?></p>

                        <div class="price-list">
                            <div>
                                <span>Preço base</span>
                                <strong>R$ <?= number_format((float)$produto['preco'], 2, ',', '.'); ?></strong>
                            </div>
                        </div>

                        <button
                            class="btn btn-soft"
                            onclick="addToCart(
                                <?= (int)$produto['id']; ?>,
                                '<?= htmlspecialchars(addslashes($produto['nome'])); ?>',
                                <?= number_format((float)$produto['preco'], 2, '.', ''); ?>,
                                '<?= htmlspecialchars(addslashes($produto['imagem'])); ?>'
                            )"
                        >
                            <i class="bi bi-bag-plus"></i>
                            Adicionar ao carrinho
                        </button>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-block">
                    <i class="bi bi-cake2"></i>
                    <h3>Nenhum produto cadastrado ainda</h3>
                    <p>Acesse o painel admin para cadastrar os primeiros bolos.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section info-section" id="como-funciona">
        <div class="section-heading align-left">
            <span class="eyebrow">Como funciona</span>
            <h2>Uma navegação mais clara para transformar visita em pedido</h2>
        </div>

        <div class="info-grid">
            <article class="info-card">
                <i class="bi bi-basket2-fill"></i>
                <h3>Produtos frescos</h3>
                <p>Nada de pronta entrega. A proposta da marca é artesanal, com produção sob encomenda.</p>
            </article>
            <article class="info-card">
                <i class="bi bi-alarm-fill"></i>
                <h3>Antecedência mínima</h3>
                <p>Pedidos devem ser feitos com pelo menos 2 horas de antecedência.</p>
            </article>
            <article class="info-card">
                <i class="bi bi-clock-fill"></i>
                <h3>Horário de atendimento</h3>
                <p>Segunda a sexta das 9h às 17h. Sábados e feriados das 9h às 11h.</p>
            </article>
            <article class="info-card">
                <i class="bi bi-truck"></i>
                <h3>Taxa de entrega</h3>
                <p>Canela R$ 10, Gramado R$ 20, Novo Hamburgo e região a combinar.</p>
            </article>
        </div>
    </section>

    <section class="section sizes-section">
        <div class="sizes-card sizes-card-large">
            <span class="eyebrow">Tamanhos disponíveis</span>
            <h2>Formatos pensados para momentos diferentes</h2>
            <div class="size-items">
                <div>
                    <strong>Retangular • 18,5 x 6,5 cm</strong>
                    <span>Serve 2–3 pessoas</span>
                </div>
                <div>
                    <strong>Redondo • 18 cm</strong>
                    <span>Serve 5–6 pessoas</span>
                </div>
                <div>
                    <strong>Piscininha</strong>
                    <span>Disponível nos sabores especiais</span>
                </div>
            </div>
        </div>

        <div class="sizes-card sizes-card-note">
            <div class="stamp stamp-soft">feito com carinho</div>
            <p>
                O site agora usa a mesma linguagem delicada da marca, mas com toda a parte funcional:
                catálogo dinâmico, carrinho, pedidos salvos e acompanhamento pela conta do cliente.
            </p>
        </div>
    </section>

    <section class="section reviews-section" id="avaliacoes">
        <div class="section-heading">
            <span class="eyebrow">Clients reviews</span>
            <h2>Avaliações em destaque</h2>
        </div>

        <div class="review-stack">
            <article class="review-card review-card-a">
                <div class="review-head">
                    <div class="avatar"><i class="bi bi-emoji-smile"></i></div>
                    <div>
                        <h3>Elisângela</h3>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
                <h4>Pronta para os próximos!</h4>
                <p>Amei muito! Sempre te admirei na cozinha, mas esses bolos estão um arraso!!!</p>
            </article>

            <article class="review-card review-card-b">
                <div class="review-head">
                    <div class="avatar"><i class="bi bi-emoji-smile"></i></div>
                    <div>
                        <h3>Rafa</h3>
                        <div class="stars">★★★★★</div>
                    </div>
                </div>
                <h4>Gostoso demais!!</h4>
                <p>
                    A cobertura é uma piada, boa demais. O bolo macio e molhadinho na medida.
                    Visualmente marcante e com cara de presente.
                </p>
            </article>
        </div>
    </section>

    <section class="section cta-section" id="contato">
        <div class="cta-card">
            <div>
                <span class="eyebrow">Contato</span>
                <h2>Quer transformar isso em pedido agora?</h2>
                <p>Use o botão abaixo para abrir o carrinho e concluir o pedido pela sua conta.</p>
            </div>
            <div class="cta-actions">
                <a class="btn btn-primary" href="carrinho.php">
                    <i class="bi bi-bag-heart"></i>
                    Abrir carrinho
                </a>
                <a class="btn btn-secondary" href="https://www.instagram.com/bolosdoranina/" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-instagram"></i>
                    Ver Instagram
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
