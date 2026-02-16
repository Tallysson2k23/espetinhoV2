<?php

session_start();
header('Content-Type: application/json');

require "../config/conexao.php";

$id=$_POST['id'];

$sql="UPDATE produtos
SET ativo = NOT ativo
WHERE id=:id";

$stmt=$pdo->prepare($sql);
$stmt->execute([":id"=>$id]);

echo json_encode(["success"=>true]);
