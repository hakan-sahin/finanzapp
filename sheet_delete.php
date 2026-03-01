<?php
require 'config.php';

$sheetId = (int)($_POST['sheet_id'] ?? 0);

if ($sheetId > 0) {
    $stmt = $pdo->prepare("DELETE FROM sheets WHERE id=?");
    $stmt->execute([$sheetId]);
}

/* Egal was passiert → zurück zur App */
header("Location: index.php");
exit;

