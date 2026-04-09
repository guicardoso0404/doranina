<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '' || $confirmarSenha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail valido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas nao coincidem.';
    } else {
        $existe = app_db_select_one('SELECT id FROM usuarios WHERE email = ? LIMIT 1', [$email]);

        if ($existe) {
            $erro = 'Esse e-mail ja esta cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $tipo = 'admin';

            try {
                app_db_execute(
                    'INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)',
                    [$nome, $email, $senhaHash, $tipo]
                );
                $sucesso = 'Administrador cadastrado com sucesso.';
            } catch (Throwable $e) {
                $erro = 'Nao foi possivel salvar o administrador.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar admin | DoraNina</title>
    <link rel="icon" type="image/png" href="../assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/../assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="apple-touch-icon" href="../assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/../assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?= substr(md5_file(__DIR__ . '/../assets/css/admin.css'), 0, 12); ?>">
</head>
<body class="admin-login-page">
    <div class="admin-login-card">
        <div class="login-brand">
            <img src="../assets/img/logo-doranina.png" alt="Logo DoraNina">
            <div>
                <strong>DoraNina</strong>
                <small>Novo administrador</small>
            </div>
        </div>

        <h1>Cadastrar admin</h1>
        <p>Crie um novo acesso administrativo usando o mesmo sistema de login da loja.</p>

        <?php if ($erro): ?>
            <div class="alert-error"><?= htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="alert-success"><?= htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <label>Nome</label>
            <input type="text" name="nome" placeholder="Nome do administrador" required>

            <label>E-mail</label>
            <input type="email" name="email" placeholder="admin@doranina.com" required>

            <label>Senha</label>
            <input type="password" name="senha" placeholder="Crie uma senha" required>

            <label>Confirmar senha</label>
            <input type="password" name="confirmar_senha" placeholder="Repita a senha" required>

            <button type="submit"><i class="bi bi-person-plus"></i> Cadastrar administrador</button>
        </form>

        <div class="login-actions">
            <a class="secondary-btn full-width-btn" href="dashboard.php"><i class="bi bi-arrow-left"></i> Voltar ao painel</a>
        </div>
    </div>
</body>
</html>
