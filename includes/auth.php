<?php
require_once __DIR__ . '/auth_state.php';

if (!isset($_SESSION['usuario_id'])) {
    $redirect = $_SERVER['REQUEST_URI'] ?? 'index.php';
    header('Location: login.php?redirect=' . urlencode($redirect));
    exit;
}
?>
