<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$id = (int)($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$preco = (float)($_POST['preco'] ?? 0);
$imagem = trim($_POST['imagem'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$destaque = isset($_POST['destaque']) ? 1 : 0;
$ativo = isset($_POST['ativo']) ? 1 : 0;

app_db_execute(
    'UPDATE produtos SET nome = ?, descricao = ?, preco = ?, imagem = ?, categoria = ?, destaque = ?, ativo = ? WHERE id = ?',
    [$nome, $descricao, $preco, $imagem, $categoria, $destaque, $ativo, $id]
);

header('Location: produtos.php?msg=Produto atualizado com sucesso');
exit;
?>
