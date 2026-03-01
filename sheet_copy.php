<?php
require 'config.php';
$id = $_POST['sheet_id'];
$name = "Kopie ".date("Y-m-d H:i");
$pdo->prepare("INSERT INTO sheets (name) VALUES (?)")->execute([$name]);
$newId = $pdo->lastInsertId();
$pdo->prepare(
"INSERT INTO entries (sheet_id,type,title,amount,note)
 SELECT ?,type,title,amount,note FROM entries WHERE sheet_id=?"
)->execute([$newId,$id]);
header("Location: index.php?sheet_id=".$newId);

