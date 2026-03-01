<?php
require 'config.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM base_entries WHERE id=?");
    $stmt->execute([$id]);
}

/* 🔁 Zurück zur aufrufenden Seite */
$redirect = $_SERVER['HTTP_REFERER'] ?? 'base.php';
header("Location: $redirect");
exit;

