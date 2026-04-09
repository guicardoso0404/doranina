<?php
require_once __DIR__ . '/includes/auth_state.php';

app_logout_user();

header('Location: index.php');
exit;
?>
