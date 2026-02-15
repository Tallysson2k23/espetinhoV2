<?php

require "../config/conexao.php";

$id = $_GET['id'];

// buscar status atual
$sql = "SELECT ativo FROM usuarios WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":id",$id);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$novoStatus = $user['ativo'] ? false : true;

// atualizar
$sql = "UPDATE usuarios SET ativo=:ativo WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":ativo",$novoStatus);
$stmt->bindParam(":id",$id);
$stmt->execute();

echo json_encode(["success"=>true]);
