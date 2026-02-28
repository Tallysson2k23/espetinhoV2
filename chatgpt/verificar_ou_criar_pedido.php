<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require "../config/conexao.php";

if(!isset($_SESSION['usuario_id'])){
    echo json_encode(["success"=>false]);
    exit;
}

$mesa_id = intval($_GET['mesa_id'] ?? 0);

if($mesa_id <= 0){
    echo json_encode(["success"=>false]);
    exit;
}

/* VERIFICA SE EXISTE */

$sql = "
SELECT id FROM pedidos
WHERE mesa_id = :mesa_id
AND status = 'aberto'
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":mesa_id"=>$mesa_id]);

$pedido_id = $stmt->fetchColumn();

/* SE NÃO EXISTIR → CRIA */

if(!$pedido_id){

    $sql = "
    INSERT INTO pedidos (mesa_id, usuario_id, status, criado_em)
    VALUES (:mesa_id, :usuario_id, 'aberto', NOW())
    RETURNING id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":mesa_id"=>$mesa_id,
        ":usuario_id"=>$_SESSION['usuario_id']
    ]);

    $pedido_id = $stmt->fetchColumn();

    $pdo->prepare("
        UPDATE mesas 
        SET status='ocupada', data_abertura=NOW()
        WHERE id=:mesa_id
    ")->execute([":mesa_id"=>$mesa_id]);
}

echo json_encode([
    "success"=>true,
    "pedido_id"=>$pedido_id
]);