<?php
require 'config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$sheetId = (int)($_POST['sheet_id'] ?? 0);
$type    = $_POST['type'] ?? '';
$title   = trim($_POST['title'] ?? '');
$amount  = (double)($_POST['amount'] ?? 0);

/* Typ normalisieren */
if ($type === 'oneTime') {
    $type = 'onetime';
}

/* Validierung */
if ($sheetId <= 0 || $title === '' || $amount <= 0) {
    die('Ungültige Eingabe');
}

/* Existiert die Übersicht wirklich? */
$stmt = $pdo->prepare("SELECT id FROM sheets WHERE id=?");
$stmt->execute([$sheetId]);
if (!$stmt->fetchColumn()) {
    die('Übersicht existiert nicht');
}

/* Insert */
$stmt = $pdo->prepare(
    "INSERT INTO entries (sheet_id, type, title, amount)
     VALUES (:sheet_id, :type, :title, :amount)"
);

$stmt->execute([
    ':sheet_id' => $sheetId,
    ':type'     => $type,
    ':title'    => $title,
    ':amount'   => $amount
]);

header("Location: index.php?sheet_id=".$sheetId);
exit;

