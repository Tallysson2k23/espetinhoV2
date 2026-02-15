<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require "../config/conexao.php";

if(!isset($_SESSION['usuario_id'])){
    echo json_encode(["success"=>false]);
    exit;
}

$mesa_id = $_POST['mesa_id'];

// verificar se já existe pedido aberto
$sql = "SELECT * FROM pedidos 
        WHERE mesa_id = :mesa_id 
        AND status = 'aberto'
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":mesa_id",$mesa_id);
$stmt->execute();

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if($pedido){

    echo json_encode([
        "success"=>true,
        "pedido_id"=>$pedido['id']
    ]);

    exit;
}

// criar novo pedido

$sql = "INSERT INTO pedidos (mesa_id, usuario_id, status)
        VALUES (:mesa_id, :usuario_id, 'aberto')
        RETURNING id";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":mesa_id",$mesa_id);
$stmt->bindParam(":usuario_id",$_SESSION['usuario_id']);

$stmt->execute();

$pedido_id = $stmt->fetchColumn();

// atualizar mesa para ocupada

$sql = "UPDATE mesas SET status='ocupada' WHERE id=:mesa_id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":mesa_id",$mesa_id);
$stmt->execute();

echo json_encode([
    "success"=>true,
    "pedido_id"=>$pedido_id
]);
