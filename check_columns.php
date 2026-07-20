<?php
require_once 'config/database.php';
$stmt = $pdo->query("DESCRIBE warga");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($columns as $c) echo $c['Field']."\n";
?>
