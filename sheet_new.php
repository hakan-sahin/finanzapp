<?php
require 'config.php';

$name = trim($_GET['name'] ?? '');

if ($name === '') {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO sheets (name) VALUES (?)");
$stmt->execute([$name]);

$id = $pdo->lastInsertId();

header("Location: index.php?sheet_id=".$id);
exit;

