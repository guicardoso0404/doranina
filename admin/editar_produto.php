<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$id = (int)($_GET['id'] ?? 0);
$produto = app_db_select_one('SELECT * FROM produtos WHERE id = ?', [$id]);

if (!$produto) {
    header('Location: produtos.php?msg=Produto nao encontrado');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar produto | DoraNina Admin</title>
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
                <h1>Editar produto</h1>
            </div>
        </div>

        <section class="panel-card">
            <form action="atualizar_produto.php" method="POST" class="admin-grid-form">
                <input type="hidden" name="id" value="<?= (int)$produto['id']; ?>">

                <div class="input-group">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']); ?>" required>
                </div>

                <div class="input-group">
                    <label>Categoria</label>
                    <input type="text" name="categoria" value="<?= htmlspecialchars((string)($produto['categoria'] ?? '')); ?>">
                </div>

                <div class="input-group full">
                    <label>Descricao</label>
                    <textarea name="descricao" rows="4" required><?= htmlspecialchars($produto['descricao']); ?></textarea>
                </div>

                <div class="input-group">
                    <label>Preco base</label>
                    <input type="number" step="0.01" min="0" name="preco" value="<?= htmlspecialchars((string)$produto['preco']); ?>" required>
                </div>

                <div class="input-group">
                    <label>URL da imagem</label>
                    <input type="text" name="imagem" value="<?= htmlspecialchars((string)($produto['imagem'] ?? '')); ?>">
                </div>

                <div class="input-checks full">
                    <label><input type="checkbox" name="destaque" value="1" <?= (int)$produto['destaque'] === 1 ? 'checked' : ''; ?>> Produto em destaque</label>
                    <label><input type="checkbox" name="ativo" value="1" <?= (int)$produto['ativo'] === 1 ? 'checked' : ''; ?>> Produto ativo</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="primary-btn"><i class="bi bi-check2-circle"></i> Salvar alteracoes</button>
                    <a href="produtos.php" class="secondary-btn">Voltar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
