<?php
require_once __DIR__ . '/includes/auth_state.php';
require_once __DIR__ . '/config/conexao.php';

$erro = '';
$sucesso = '';
$dbDisponivel = app_db_ready();

if (!$dbDisponivel) {
    $erro = 'O cadastro ficara disponivel assim que o banco de producao for configurado.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $erro = 'Ja existe uma conta com esse e-mail.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $tipo = 'cliente';

            try {
                app_db_execute(
                    'INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)',
                    [$nome, $email, $senhaHash, $tipo]
                );
                $sucesso = 'Conta criada com sucesso. Agora voce ja pode entrar.';
            } catch (Throwable $e) {
                $erro = 'Nao foi possivel criar a conta.';
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
    <title>Criar conta | DoraNina</title>
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
                <small>Novo cadastro</small>
            </div>
        </div>

        <h1>Criar conta</h1>
        <p>Use sua conta para acompanhar pedidos feitos pelo site.</p>

        <?php if ($erro): ?>
            <div class="alert-error"><?= htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="alert-success"><?= htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <label>Nome</label>
            <input type="text" name="nome" placeholder="Seu nome completo" required>

            <label>E-mail</label>
            <input type="email" name="email" placeholder="seuemail@exemplo.com" required>

            <label>Senha</label>
            <input type="password" name="senha" placeholder="Crie uma senha" required>

            <label>Confirmar senha</label>
            <input type="password" name="confirmar_senha" placeholder="Repita a senha" required>

            <button type="submit"><i class="bi bi-person-plus"></i> Criar conta</button>
        </form>

        <div class="login-actions">
            <a class="secondary-btn full-width-btn" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Ja tenho conta</a>
        </div>
    </div>
</body>
</html>
