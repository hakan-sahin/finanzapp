<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=finanzen;charset=utf8mb4",
    "finanzenadmin",
    "AQ+.!0bbH658+j!2HWn#qdJ58kgB7h#K#",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);

define('APP_NAME', 'Finanzübersicht Sahin');
define('APP_VERSION', '2.0');
define('APP_AUTHOR', 'Hakan Sahin');
define('APP_LAST_UPDATE', '13-01-2026');
