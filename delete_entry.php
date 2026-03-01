<?php
require 'config.php';

$id    = (int)($_GET['id'] ?? 0);
$sheet = (int)($_GET['sheet'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM entries WHERE id=?");
    $stmt->execute([$id]);
}

header("Location: index.php?sheet_id=".$sheet);
exit;

