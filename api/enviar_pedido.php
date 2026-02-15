<?php

require "../config/conexao.php";

$pedido_id = $_POST['pedido_id'];

$sql = "

UPDATE pedido_itens
SET status='enviado'
WHERE pedido_id=:pedido_id
AND status='carrinho'

";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":pedido_id",$pedido_id);

$stmt->execute();

echo json_encode([
    "success"=>true
]);
