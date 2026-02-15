<?php

require "../config/conexao.php";

$sql = "SELECT id, numero, status FROM mesas ORDER BY numero";

$stmt = $pdo->query($sql);

$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($mesas);
