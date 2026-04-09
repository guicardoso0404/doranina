<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$id = (int)($_GET['id'] ?? 0);
app_db_execute('DELETE FROM produtos WHERE id = ?', [$id]);

header('Location: produtos.php?msg=Produto excluido com sucesso');
exit;
?>
