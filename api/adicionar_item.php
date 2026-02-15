<?php

require "../config/conexao.php";

$pedido_id = $_POST['pedido_id'];
$produto_id = $_POST['produto_id'];
$quantidade = $_POST['quantidade'];

// buscar preço

$sql = "SELECT preco FROM produtos WHERE id=:produto_id";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":produto_id",$produto_id);

$stmt->execute();

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

$preco = $produto['preco'];

$sql = "INSERT INTO pedido_itens
        (pedido_id, produto_id, quantidade, preco)
        VALUES
        (:pedido_id, :produto_id, :quantidade, :preco)";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":pedido_id",$pedido_id);
$stmt->bindParam(":produto_id",$produto_id);
$stmt->bindParam(":quantidade",$quantidade);
$stmt->bindParam(":preco",$preco);

$stmt->execute();

echo json_encode([
    "success"=>true
]);
