<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/conexao.php';
require_once __DIR__ . '/includes/email_helper.php';
app_require_database();

if (($_SESSION['usuario_tipo'] ?? '') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$clienteNome = trim($_POST['cliente_nome'] ?? '');
$clienteTelefone = trim($_POST['cliente_telefone'] ?? '');
$clienteEndereco = trim($_POST['cliente_endereco'] ?? '');
$tipoEntrega = trim($_POST['tipo_entrega'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');
$itensJson = $_POST['itens_json'] ?? '[]';
$totalRecebido = (float)($_POST['total'] ?? 0);
$usuarioId = (int)($_SESSION['usuario_id'] ?? 0);

$itens = json_decode($itensJson, true);
$tiposEntregaValidos = ['retirada', 'motoboy'];

if (
    $usuarioId <= 0 ||
    empty($clienteNome) ||
    empty($clienteTelefone) ||
    !in_array($tipoEntrega, $tiposEntregaValidos, true) ||
    !is_array($itens) ||
    count($itens) === 0
) {
    die('Dados invalidos do pedido.');
}

if ($tipoEntrega === 'motoboy' && $clienteEndereco === '') {
    die('Informe o endereco para a entrega por motoboy.');
}

if ($tipoEntrega === 'retirada') {
    $clienteEndereco = 'Retirada no local';
}

$totalCalculado = 0;
$itensNormalizados = [];

foreach ($itens as $item) {
    $nome = trim((string)($item['nome'] ?? 'Produto'));
    $quantidade = max(1, (int)($item['quantidade'] ?? 0));
    $preco = (float)($item['preco'] ?? 0);
    $subtotal = $quantidade * $preco;

    $itensNormalizados[] = [
        'id' => (int)($item['id'] ?? 0),
        'nome' => $nome,
        'quantidade' => $quantidade,
        'preco' => $preco,
        'subtotal' => $subtotal,
    ];

    $totalCalculado += $subtotal;
}

$total = $totalCalculado > 0 ? $totalCalculado : $totalRecebido;
$conn = app_db();
$conn->beginTransaction();

try {
    if (app_db_driver() === 'pgsql') {
        $stmtPedido = $conn->prepare("INSERT INTO pedidos (
            usuario_id,
            cliente_nome,
            cliente_telefone,
            cliente_endereco,
            tipo_entrega,
            observacoes,
            total,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Novo') RETURNING id");
        $stmtPedido->execute([
            $usuarioId,
            $clienteNome,
            $clienteTelefone,
            $clienteEndereco,
            $tipoEntrega,
            $observacoes,
            $total,
        ]);
        $pedidoId = (int)$stmtPedido->fetchColumn();
    } else {
        $stmtPedido = $conn->prepare("INSERT INTO pedidos (
            usuario_id,
            cliente_nome,
            cliente_telefone,
            cliente_endereco,
            tipo_entrega,
            observacoes,
            total,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Novo')");
        $stmtPedido->execute([
            $usuarioId,
            $clienteNome,
            $clienteTelefone,
            $clienteEndereco,
            $tipoEntrega,
            $observacoes,
            $total,
        ]);
        $pedidoId = (int)$conn->lastInsertId();
    }

    $stmtItem = $conn->prepare("INSERT INTO pedido_itens (
        pedido_id,
        produto_id,
        nome_produto,
        quantidade,
        preco_unitario,
        subtotal
    ) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($itensNormalizados as $item) {
        $stmtItem->execute([
            $pedidoId,
            $item['id'],
            $item['nome'],
            $item['quantidade'],
            $item['preco'],
            $item['subtotal'],
        ]);
    }

    $conn->commit();
} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    throw $e;
}

$pedidoEmail = [
    'id' => $pedidoId,
    'cliente_nome' => $clienteNome,
    'cliente_telefone' => $clienteTelefone,
    'cliente_endereco' => $clienteEndereco,
    'tipo_entrega' => $tipoEntrega,
    'observacoes' => $observacoes,
    'status' => 'Novo',
    'total' => $total,
];

enviarEmailPedido($pedidoEmail, $itensNormalizados);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido realizado</title>
    <link rel="icon" type="image/png" href="assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/assets/img/logo-doranina.png'), 0, 12); ?>">
    <link rel="apple-touch-icon" href="assets/img/logo-doranina.png?v=<?= substr(md5_file(__DIR__ . '/assets/img/logo-doranina.png'), 0, 12); ?>">
    <style>
        :root {
            --bg: #f5f1e8;
            --paper: #fdfaf4;
            --ink: #2b2d2f;
            --muted: #5f6670;
            --blue-dark: #4f7595;
            --line: rgba(79, 117, 149, 0.18);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: var(--bg);
            font-family: Arial, sans-serif;
            color: var(--ink);
        }
        .success-card {
            width: min(100%, 520px);
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 32px;
            text-align: center;
            box-shadow: 0 18px 50px rgba(58, 87, 111, 0.12);
        }
        .success-icon {
            width: 74px;
            height: 74px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #eef4fb;
            color: var(--blue-dark);
            font-size: 34px;
            font-weight: 700;
        }
        h1 { margin: 0 0 10px; font-size: 30px; }
        p { margin: 0 0 10px; color: var(--muted); line-height: 1.7; }
        a {
            display: inline-block;
            margin-top: 18px;
            padding: 14px 20px;
            border-radius: 999px;
            background: var(--blue-dark);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
    <script>
        localStorage.removeItem('doranina_cart');
        localStorage.removeItem('carrinho');
        setTimeout(function () {
            window.location.href = 'meus_pedidos.php?sucesso=1';
        }, 1500);
    </script>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">&#10003;</div>
        <h1>Pedido realizado com sucesso!</h1>
        <p>Seu pedido foi enviado e ja esta disponivel na sua conta.</p>
        <p>Voce sera redirecionado para acompanhar o status.</p>
        <noscript><a href="meus_pedidos.php?sucesso=1">Ver meus pedidos</a></noscript>
    </div>
</body>
</html>
