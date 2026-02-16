<?php

require "../config/conexao.php";

$pedido_id = $_GET['pedido_id'];

// verificar se tem itens

$sql = "

SELECT COUNT(*) FROM pedido_itens
WHERE pedido_id=:pedido_id

";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":pedido_id",$pedido_id);
$stmt->execute();

$total = $stmt->fetchColumn();

if($total == 0){

    // pegar mesa_id

    $sql = "SELECT mesa_id FROM pedidos WHERE id=:pedido_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":pedido_id",$pedido_id);
    $stmt->execute();

    $mesa_id = $stmt->fetchColumn();

    // deletar pedido vazio

    $sql = "DELETE FROM pedidos WHERE id=:pedido_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":pedido_id",$pedido_id);
    $stmt->execute();

    // garantir mesa livre

    $sql = "UPDATE mesas SET status='livre' WHERE id=:mesa_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":mesa_id",$mesa_id);
    $stmt->execute();

}
