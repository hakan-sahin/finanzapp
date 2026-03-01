<?php
require 'config.php';

/* sheet_id bestimmen */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sheet_id'])) {
    $sheetId = (int)$_POST['sheet_id'];
} elseif (isset($_GET['sheet_id'])) {
    $sheetId = (int)$_GET['sheet_id'];
} else {
    $sheetId = 0;
}

/* Existieren Übersichten? */
$totalSheets = $pdo->query("SELECT COUNT(*) FROM sheets")->fetchColumn();
if ($totalSheets == 0) {
    $pdo->prepare("INSERT INTO sheets (name) VALUES ('Meine erste Übersicht')")->execute();
    $sheetId = $pdo->lastInsertId();
}

/* Fallback */
if ($sheetId <= 0) {
    $sheetId = $pdo->query("SELECT id FROM sheets ORDER BY id LIMIT 1")->fetchColumn();
}

/* Aktuelle Übersicht */
$stmt = $pdo->prepare("SELECT * FROM sheets WHERE id=?");
$stmt->execute([$sheetId]);
$currentSheet = $stmt->fetch();
if (!$currentSheet) {
    header("Location: index.php");
    exit;
}

/* Übersichten */
$sheets = $pdo->query("SELECT * FROM sheets ORDER BY id")->fetchAll();

/* Grunddaten */
$baseIncome  = $pdo->query("SELECT * FROM base_entries WHERE type='income'")->fetchAll();
$baseMonthly = $pdo->query("SELECT * FROM base_entries WHERE type='monthly' ORDER BY title")->fetchAll();

/* Übersichtseinträge */
$stmt = $pdo->prepare("SELECT * FROM entries WHERE sheet_id=?");
$stmt->execute([$sheetId]);
$entries = $stmt->fetchAll();

/* Helfer */
function sum($a){ return array_sum(array_column($a,'amount')); }

/* Zusammenführen */
$incomeList  = array_merge($baseIncome, array_filter($entries, fn($e)=>$e['type']==='income'));
$monthlyList = array_merge($baseMonthly, array_filter($entries, fn($e)=>$e['type']==='monthly'));
$oneTimeList = array_filter($entries, fn($e)=>$e['type']==='oneTime');

/* Summen */
$sumIncome  = sum($incomeList);
$sumMonthly = sum($monthlyList);
$sumOneTime = sum($oneTimeList);
$balance    = $sumIncome - $sumMonthly - $sumOneTime;
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Finanzen</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="style.css">
</head>
<body>

<footer class="app-footer">
  <?= APP_NAME ?> · Version <?= APP_VERSION ?> ·
  © <?= APP_AUTHOR ?> ·
  Letzte Änderung: <?= APP_LAST_UPDATE ?>
</footer>

<div style="position:absolute; top:20px; right:20px;">
  <a href="base.php" style="
    padding:8px 12px;
    background:#3a7afe;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
  ">
    Grunddaten verwalten
  </a>
</div>

<div class="container">
<h1>💰 Finanzübersicht</h1>

<!-- ÜBERSICHTEN -->
<div class="sheets">
<form method="get" style="display:inline">
<select name="sheet_id" onchange="this.form.submit()">
<?php foreach($sheets as $s): ?>
<option value="<?= $s['id'] ?>" <?= $s['id']==$sheetId?'selected':'' ?>>
<?= htmlspecialchars($s['name']) ?>
</option>
<?php endforeach; ?>
</select>
</form>

<button onclick="newSheet()">Neue Übersicht</button>

<form method="post" action="sheet_delete.php" style="display:inline"
onsubmit="return confirm('Übersicht wirklich löschen?')">
<input type="hidden" name="sheet_id" value="<?= $sheetId ?>">
<button>Löschen</button>
</form>
</div>

<!-- SUMMARY -->
<div class="summary">
<div class="box green">Einnahmen: <?= number_format($sumIncome,2) ?> €</div>
<div class="box red">Monatlich: <?= number_format($sumMonthly,2) ?> €</div>
<div class="box red">Einmalig: <?= number_format($sumOneTime,2) ?> €</div>
<div class="box <?= $balance>=0?'green':'red' ?>">
Kassenbestand: <?= number_format($balance,2) ?> €
</div>
</div>

<!-- TABELLEN -->
<div class="summary">

<!-- Einnahmen -->
<div class="box">
<h2>Einnahmen <button onclick="addEntry('income')">+</button></h2>
<table>
<tr><th>Bezeichnung</th><th>Betrag</th><th></th></tr>
<?php foreach($incomeList as $e): ?>
<tr>
<td><?= htmlspecialchars($e['title']) ?></td>
<td><?= number_format($e['amount'],2) ?></td>
<td>
<?php if(isset($e['sheet_id'])): ?>
<a href="delete_entry.php?id=<?= $e['id'] ?>&sheet=<?= $sheetId ?>">✖</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<!-- Monatlich -->
<div class="box">
<h2>Monatliche Ausgaben <button onclick="addEntry('monthly')">+</button></h2>
<table>
<tr><th>Bezeichnung</th><th>Betrag</th><th></th></tr>
<?php foreach($monthlyList as $e): ?>
<tr>
<td><?= htmlspecialchars($e['title']) ?></td>
<td><?= number_format($e['amount'],2) ?></td>
<td>
<?php if(isset($e['sheet_id'])): ?>
<a href="delete_entry.php?id=<?= $e['id'] ?>&sheet=<?= $sheetId ?>">✖</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

<!-- Einmalig -->
<div class="box">
<h2>Einmalige Zahlungen <button onclick="addEntry('oneTime')">+</button></h2>
<table>
<tr><th>Bezeichnung</th><th>Betrag</th><th></th></tr>
<?php foreach($oneTimeList as $e): ?>
<tr>
<td><?= htmlspecialchars($e['title']) ?></td>
<td><?= number_format($e['amount'],2) ?></td>
<td>
<a href="delete_entry.php?id=<?= $e['id'] ?>&sheet=<?= $sheetId ?>">✖</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

</div>
</div>

<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
}

function newSheet(){
  const name = prompt("Name der neuen Übersicht:");
  if(name && name.trim() !== ""){
    window.location = "sheet_new.php?name=" + encodeURIComponent(name.trim());
  }
}

function addEntry(type){
  const title = prompt("Bezeichnung:");
  if(!title) return;

  const amount = prompt("Betrag (€):");
  if(!amount || isNaN(amount)) return;

  const form = document.createElement("form");
  form.method = "post";
  form.action = "save_entry.php";

  form.innerHTML = `
    <input type="hidden" name="sheet_id" value="<?= $sheetId ?>">
    <input type="hidden" name="type" value="${type}">
    <input type="hidden" name="title" value="${title}">
    <input type="hidden" name="amount" value="${amount}">
  `;

  document.body.appendChild(form);
  form.submit();
}
</script>

<footer class="app-footer">
  <?= APP_NAME ?> · Version <?= APP_VERSION ?> ·
  © <?= APP_AUTHOR ?> ·
  Letzte Änderung: <?= APP_LAST_UPDATE ?>
</footer>

</body>
</html>

