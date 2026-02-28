<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require "../config/conexao.php";

$mesa_id = intval($_GET['mesa_id'] ?? 0);

if($mesa_id <= 0){
    echo json_encode(["success"=>false]);
    exit;
}

$sql = "
SELECT id FROM pedidos
WHERE mesa_id = :mesa_id
AND status = 'aberto'
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":mesa_id"=>$mesa_id]);
$pedido_id = $stmt->fetchColumn();

if(!$pedido_id){
    echo json_encode([
        "success"=>true,
        "quantidade"=>0,
        "total"=>"0,00"
    ]);
    exit;
}

$sql = "
SELECT 
    COALESCE(SUM(quantidade),0) as quantidade,
    COALESCE(SUM(quantidade * preco),0) as total
FROM pedido_itens
WHERE pedido_id = :pedido_id
AND status = 'carrinho'
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":pedido_id"=>$pedido_id]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "success"=>true,
    "quantidade"=>$dados['quantidade'],
    "total"=>number_format($dados['total'],2,',','.')
]);