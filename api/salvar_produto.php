<?php

require "../config/conexao.php";

$grupo_id = $_POST['grupo_id'];
$nome = $_POST['nome'];
$preco = $_POST['preco'];

$sql = "INSERT INTO produtos (grupo_id, nome, preco)
        VALUES (:grupo_id, :nome, :preco)";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":grupo_id",$grupo_id);
$stmt->bindParam(":nome",$nome);
$stmt->bindParam(":preco",$preco);

$stmt->execute();

echo json_encode([
    "success"=>true
]);
