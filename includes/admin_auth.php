<?php
require_once __DIR__ . '/auth_state.php';

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
