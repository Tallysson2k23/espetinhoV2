<?php

require "../config/conexao.php";

$nome = $_POST['nome'];

$sql = "INSERT INTO grupos (nome)
        VALUES (:nome)";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":nome",$nome);

$stmt->execute();

echo json_encode([
    "success"=>true
]);
