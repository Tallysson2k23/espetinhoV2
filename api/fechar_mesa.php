<?php

require "../config/conexao.php";

$pedido_id = $_POST['pedido_id'];

// fechar pedido

$sql = "

UPDATE pedidos
SET status='fechado',
fechado_em=NOW()
WHERE id=:pedido_id

";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":pedido_id",$pedido_id);

$stmt->execute();


// liberar mesa

$sql = "

UPDATE mesas
SET status='livre'
WHERE id = (
    SELECT mesa_id FROM pedidos WHERE id=:pedido_id
)

";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":pedido_id",$pedido_id);

$stmt->execute();

echo json_encode([
    "success"=>true
]);
