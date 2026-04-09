<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$preco = (float)($_POST['preco'] ?? 0);
$imagem = trim($_POST['imagem'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$destaque = isset($_POST['destaque']) ? 1 : 0;
$ativo = isset($_POST['ativo']) ? 1 : 0;

app_db_execute(
    'INSERT INTO produtos (nome, descricao, preco, imagem, categoria, destaque, ativo) VALUES (?, ?, ?, ?, ?, ?, ?)',
    [$nome, $descricao, $preco, $imagem, $categoria, $destaque, $ativo]
);

header('Location: produtos.php?msg=Produto cadastrado com sucesso');
exit;
?>
