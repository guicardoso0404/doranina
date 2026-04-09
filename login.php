<?php
require_once __DIR__ . '/includes/auth_state.php';
require_once __DIR__ . '/config/conexao.php';

if (isset($_SESSION['usuario_id'])) {
    if (($_SESSION['usuario_tipo'] ?? '') === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$erro = '';
$redirect = trim($_GET['redirect'] ?? ($_POST['redirect'] ?? ''));
$dbDisponivel = app_db_ready();

if (!$dbDisponivel) {
    $erro = 'O login ficara disponivel assim que o banco de producao for configurado.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $usuario = app_db_select_one(
            'SELECT id, nome, email, senha, tipo, ativo FROM usuarios WHERE email = ? LIMIT 1',
            [$email]
        );

        if ($usuario && (int)$usuario['ativo'] === 1 && password_verify($senha, $usuario['senha'])) {
            app_login_user($usuario);

            if ($usuario['tipo'] === 'admin') {
                header('Location: admin/dashboard.php');
                exit;
            }

            header('Location: ' . app_safe_redirect_path($redirect, 'index.php'));
            exit;
        }

        $erro = 'E-mail ou senha inválidos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | DoraNina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= substr(md5_file(__DIR__ . '/assets/css/admin.css'), 0, 12); ?>">
</head>
<body class="admin-login-page">
    <div class="admin-login-card">
        <div class="login-brand">
            <img src="assets/img/logo-doranina.png" alt="Logo DoraNina">
            <div>
                <strong>DoraNina</strong>
                <small>Conta da loja</small>
            </div>
        </div>

        <h1>Entrar</h1>
        <p>Acompanhe seus pedidos ou, se sua conta for administrativa, acesse o painel.</p>

        <?php if ($erro): ?>
            <div class="alert-error"><?= htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect); ?>">
            <label>E-mail</label>
            <input type="email" name="email" placeholder="seuemail@exemplo.com" required>

            <label>Senha</label>
            <input type="password" name="senha" placeholder="Digite sua senha" required>

            <button type="submit"><i class="bi bi-box-arrow-in-right"></i> Entrar</button>
        </form>

        <div class="login-actions">
            <a class="secondary-btn full-width-btn" href="cadastro.php"><i class="bi bi-person-plus"></i> Criar conta</a>
        </div>
    </div>
</body>
</html>
