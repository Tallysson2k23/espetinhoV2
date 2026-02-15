<?php

require "../config/conexao.php";

$mesa_id = $_GET['mesa_id'];

$sql = "

SELECT id
FROM pedidos
WHERE mesa_id=:mesa_id
AND status='aberto'
LIMIT 1

";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":mesa_id",$mesa_id);

$stmt->execute();

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "success"=>true,
    "pedido_id"=>$pedido['id']
]);
