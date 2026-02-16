<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require "../config/conexao.php";

try{

    if(!isset($_SESSION['usuario_id'])){

        echo json_encode([
            "success"=>false,
            "erro"=>"Usuário não logado"
        ]);
        exit;

    }

    if(!isset($_POST['mesa_id']) || empty($_POST['mesa_id'])){

        echo json_encode([
            "success"=>false,
            "erro"=>"Mesa não informada"
        ]);
        exit;

    }

    $mesa_id = intval($_POST['mesa_id']);
    $usuario_id = $_SESSION['usuario_id'];

    // verificar se já existe pedido aberto

    $sql = "
    SELECT id
    FROM pedidos
    WHERE mesa_id=:mesa_id
    AND status='aberto'
    LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":mesa_id"=>$mesa_id
    ]);

    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if($pedido){

        echo json_encode([
            "success"=>true,
            "pedido_id"=>$pedido['id']
        ]);
        exit;

    }

    // criar pedido novo

    $sql = "
    INSERT INTO pedidos
    (mesa_id, usuario_id, status, data_abertura)
    VALUES
    (:mesa_id, :usuario_id, 'aberto', NOW())
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":mesa_id"=>$mesa_id,
        ":usuario_id"=>$usuario_id
    ]);

    // pegar id criado

    $sql = "
    SELECT id
    FROM pedidos
    WHERE mesa_id=:mesa_id
    ORDER BY id DESC
    LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":mesa_id"=>$mesa_id
    ]);

    $pedido_id = $stmt->fetchColumn();

    echo json_encode([
        "success"=>true,
        "pedido_id"=>$pedido_id
    ]);

}
catch(Exception $e){

    echo json_encode([
        "success"=>false,
        "erro"=>$e->getMessage()
    ]);

}
