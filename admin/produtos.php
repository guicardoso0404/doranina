<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$produtos = app_db_select_all('SELECT * FROM produtos ORDER BY id DESC');
$mensagem = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | DoraNina Admin</title>
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
                <span class="eyebrow">Catalogo</span>
                <h1>Produtos</h1>
            </div>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert-success"><?= htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <section class="panel-card">
            <div class="panel-head">
                <h2>Novo produto</h2>
            </div>
            <form action="salvar_produto.php" method="POST" class="admin-grid-form">
                <div class="input-group">
                    <label>Nome</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="input-group">
                    <label>Categoria</label>
                    <input type="text" name="categoria" placeholder="Ex.: Especiais">
                </div>
                <div class="input-group full">
                    <label>Descricao</label>
                    <textarea name="descricao" rows="4" required></textarea>
                </div>
                <div class="input-group">
                    <label>Preco base</label>
                    <input type="number" step="0.01" min="0" name="preco" required>
                </div>
                <div class="input-group">
                    <label>URL da imagem</label>
                    <input type="text" name="imagem" placeholder="https://...">
                </div>
                <div class="input-checks full">
                    <label><input type="checkbox" name="destaque" value="1"> Produto em destaque</label>
                    <label><input type="checkbox" name="ativo" value="1" checked> Produto ativo</label>
                </div>
                <button type="submit" class="primary-btn"><i class="bi bi-plus-circle"></i> Cadastrar produto</button>
            </form>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <h2>Produtos cadastrados</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preco base</th>
                            <th>Status</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <?php
                            $imagemProduto = trim((string)($produto['imagem'] ?? ''));
                            $usaLogoPadrao = $imagemProduto === '';
                            $srcImagemProduto = $usaLogoPadrao ? '../assets/img/logo-doranina.png' : $imagemProduto;
                            ?>
                            <tr>
                                <td>#<?= (int)$produto['id']; ?></td>
                                <td>
                                    <div class="product-mini">
                                        <img
                                            class="<?= $usaLogoPadrao ? 'product-mini-logo' : ''; ?>"
                                            src="<?= htmlspecialchars($srcImagemProduto); ?>"
                                            alt="<?= htmlspecialchars($usaLogoPadrao ? 'Logo DoraNina' : $produto['nome']); ?>"
                                        >
                                        <div>
                                            <strong><?= htmlspecialchars($produto['nome']); ?></strong>
                                            <small><?= htmlspecialchars(mb_strimwidth((string)$produto['descricao'], 0, 72, '...')); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($produto['categoria'] ?: '-'); ?></td>
                                <td>R$ <?= number_format((float)$produto['preco'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php if ((int)$produto['ativo'] === 1): ?>
                                        <span class="status-badge status-em-andamento">Ativo</span>
                                    <?php else: ?>
                                        <span class="status-badge status-cancelado">Inativo</span>
                                    <?php endif; ?>
                                    <?php if ((int)$produto['destaque'] === 1): ?>
                                        <span class="status-badge status-novo">Destaque</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-links">
                                        <a href="editar_produto.php?id=<?= (int)$produto['id']; ?>"><i class="bi bi-pencil-square"></i></a>
                                        <a href="excluir_produto.php?id=<?= (int)$produto['id']; ?>" onclick="return confirm('Deseja excluir este produto?');"><i class="bi bi-trash3"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
