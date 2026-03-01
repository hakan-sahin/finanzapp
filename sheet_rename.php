<?php
require 'config.php';
$pdo->prepare("UPDATE sheets SET name=? WHERE id=?")
    ->execute([$_POST['name'],$_POST['sheet_id']]);
header("Location: index.php?sheet_id=".$_POST['sheet_id']);

