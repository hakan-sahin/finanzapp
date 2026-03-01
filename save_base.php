<?php
require 'config.php';

$type   = $_POST['type'] ?? null;
$title  = trim($_POST['title'] ?? '');
$amount = $_POST['amount'] ?? null;
$id     = $_POST['id'] ?? null;

if ($type && $title !== '' && $amount !== null) {

    if ($id) {
        // Update
        $stmt = $pdo->prepare(
            "UPDATE base_entries SET title=?, amount=? WHERE id=?"
        );
        $stmt->execute([$title, $amount, $id]);
    } else {
        // Insert
        $stmt = $pdo->prepare(
            "INSERT INTO base_entries (type, title, amount) VALUES (?, ?, ?)"
        );
        $stmt->execute([$type, $title, $amount]);
    }
}

/* 🔁 Zurück zur aufrufenden Seite */
$redirect = $_SERVER['HTTP_REFERER'] ?? 'base.php';
header("Location: $redirect");
exit;

