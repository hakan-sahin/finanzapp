<?php
require 'config.php';

$baseIncome  = $pdo->query("SELECT * FROM base_entries WHERE type='income' ORDER BY title")->fetchAll();
$baseMonthly = $pdo->query("SELECT * FROM base_entries WHERE type='monthly' ORDER BY title")->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Grunddaten verwalten</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div style="padding:20px; display:flex; justify-content:space-between; align-items:center;">
  <h1>Grunddaten</h1>
  <a href="index.php" style="
    padding:8px 12px;
    background:#3a7afe;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
  ">
    ← Zurück
  </a>
</div>

<div class="container">

<h2>Feste Einnahmen</h2>
<table>
<tr><th>Bezeichnung</th><th>Betrag (€)</th><th></th></tr>
<?php foreach($baseIncome as $b): ?>
<tr>
  <td><?= htmlspecialchars($b['title']) ?></td>
  <td><?= number_format($b['amount'],2) ?></td>
  <td><a href="delete_base.php?id=<?= $b['id'] ?>">✖</a></td>
</tr>
<?php endforeach; ?>
</table>

<form method="post" action="save_base.php" style="margin-bottom:30px;">
  <input type="hidden" name="type" value="income">
  <input name="title" placeholder="Bezeichnung" required>
  <input name="amount" type="number" step="0.01" placeholder="Betrag" required>
  <button>+</button>
</form>

<h2>Monatliche Ausgaben</h2>
<table>
<tr><th>Bezeichnung</th><th>Betrag (€)</th><th></th></tr>
<?php foreach($baseMonthly as $b): ?>
<tr>
  <td><?= htmlspecialchars($b['title']) ?></td>
  <td><?= number_format($b['amount'],2) ?></td>
  <td><a href="delete_base.php?id=<?= $b['id'] ?>">✖</a></td>
</tr>
<?php endforeach; ?>
</table>

<form method="post" action="save_base.php">
  <input type="hidden" name="type" value="monthly">
  <input name="title" placeholder="Bezeichnung" required>
  <input name="amount" type="number" step="0.01" placeholder="Betrag" required>
  <button>+</button>
</form>

</div>

<footer class="app-footer">
  <?= APP_NAME ?> · Version <?= APP_VERSION ?> ·
  © <?= APP_AUTHOR ?> ·
  Letzte Änderung: <?= APP_LAST_UPDATE ?>
</footer>

</body>
</html>

