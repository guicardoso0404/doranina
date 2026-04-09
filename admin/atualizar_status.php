<?php
require_once '../includes/admin_auth.php';
require_once '../config/conexao.php';
app_require_database();

$id = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? 'Novo');

app_db_execute('UPDATE pedidos SET status = ? WHERE id = ?', [$status, $id]);

header('Location: pedidos.php?msg=Status atualizado com sucesso');
exit;
?>
