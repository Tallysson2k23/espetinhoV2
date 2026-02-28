<?php
session_start();
header('Content-Type: application/json');
require "../config/conexao.php";

$mesa_id = intval($_GET['mesa_id'] ?? 0);

if($mesa_id <= 0){
    echo json_encode(["success"=>false]);
    exit;
}

// Apenas verifica se existe pedido aberto
$sql = "
SELECT id 
FROM pedidos
WHERE mesa_id = :mesa_id
AND status = 'aberto'
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":mesa_id"=>$mesa_id]);

$pedido_id = $stmt->fetchColumn();

if($pedido_id){
    echo json_encode([
        "success"=>true,
        "pedido_id"=>$pedido_id
    ]);
}else{
    echo json_encode([
        "success"=>false
    ]);
}