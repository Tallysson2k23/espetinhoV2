<?php

require "../config/conexao.php";

$id = $_GET['id'];

$sql = "UPDATE usuarios SET ativo=false WHERE id=:id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":id",$id);
$stmt->execute();
