<?php

require "../config/conexao.php";

if(!isset($_POST['ids'])){
    echo json_encode(["success"=>false]);
    exit;
}

$ids = $_POST['ids']; // array de ids

$placeholders = implode(',', array_fill(0, count($ids), '?'));

$sql = "UPDATE pedido_itens
        SET impresso = TRUE
        WHERE id IN ($placeholders)";

$stmt = $pdo->prepare($sql);
$stmt->execute($ids);

echo json_encode(["success"=>true]);