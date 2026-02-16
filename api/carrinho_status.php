<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require "../config/conexao.php";

try{

    if(!isset($_GET['pedido_id'])){

        echo json_encode([
            "success"=>false
        ]);
        exit;

    }

    $pedido_id = intval($_GET['pedido_id']);

    /* SOMAR ITENS DO CARRINHO */

    $sql = "

    SELECT 
        COALESCE(SUM(quantidade),0) as quantidade,
        COALESCE(SUM(quantidade * preco),0) as total

    FROM pedido_itens

    WHERE pedido_id = :pedido_id

    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":pedido_id"=>$pedido_id
    ]);

    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([

        "success"=>true,

        "quantidade"=>$dados['quantidade'],

        "total"=>number_format(
            $dados['total'],
            2,
            ',',
            '.'
        )

    ]);

}
catch(Exception $e){

    echo json_encode([
        "success"=>false,
        "erro"=>$e->getMessage()
    ]);

}
