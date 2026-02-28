<?php

session_start();
header('Content-Type: application/json; charset=utf-8');
require "../config/conexao.php";

try{

    $pedido_id = 0;

    // Se vier pedido_id
    if(isset($_GET['pedido_id'])){
        $pedido_id = intval($_GET['pedido_id']);
    }

    // Se vier mesa_id, buscar pedido aberto
    if(!$pedido_id && isset($_GET['mesa_id'])){

        $mesa_id = intval($_GET['mesa_id']);

        $stmt = $pdo->prepare("
            SELECT id 
            FROM pedidos
            WHERE mesa_id = :mesa_id
            AND status = 'aberto'
            LIMIT 1
        ");

        $stmt->execute([
            ":mesa_id"=>$mesa_id
        ]);

        $pedido_id = $stmt->fetchColumn();
    }

    if(!$pedido_id){
        echo json_encode([
            "success"=>true,
            "quantidade"=>0,
            "total"=>"0,00"
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(quantidade),0) as quantidade,
            COALESCE(SUM(quantidade * preco),0) as total
        FROM pedido_itens
        WHERE pedido_id = :pedido_id
        AND status = 'carrinho'
    ");

    $stmt->execute([
        ":pedido_id"=>$pedido_id
    ]);

    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success"=>true,
        "quantidade"=>$dados['quantidade'],
        "total"=>number_format($dados['total'],2,',','.')
    ]);

}
catch(Exception $e){

    echo json_encode([
        "success"=>false,
        "erro"=>$e->getMessage()
    ]);

}