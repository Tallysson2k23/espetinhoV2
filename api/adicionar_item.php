<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require "../config/conexao.php";

try{

    // validar sessão
    if(!isset($_SESSION['usuario_id'])){

        echo json_encode([
            "success"=>false,
            "erro"=>"Usuário não logado"
        ]);
        exit;

    }

    // validar dados
    if(
        !isset($_POST['pedido_id']) ||
        !isset($_POST['produto_id']) ||
        !isset($_POST['quantidade'])
    ){

        echo json_encode([
            "success"=>false,
            "erro"=>"Dados incompletos"
        ]);
        exit;

    }

    $pedido_id = intval($_POST['pedido_id']);
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);

    if($pedido_id <= 0){

        echo json_encode([
            "success"=>false,
            "erro"=>"Pedido inválido"
        ]);
        exit;

    }

    if($produto_id <= 0){

        echo json_encode([
            "success"=>false,
            "erro"=>"Produto inválido"
        ]);
        exit;

    }

    if($quantidade <= 0){

        echo json_encode([
            "success"=>false,
            "erro"=>"Quantidade inválida"
        ]);
        exit;

    }

    // ✅ verificar se pedido existe e está aberto

    $sql = "
    SELECT id, mesa_id
    FROM pedidos
    WHERE id=:pedido_id
    AND status='aberto'
    LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":pedido_id"=>$pedido_id
    ]);

    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$pedido){

        echo json_encode([
            "success"=>false,
            "erro"=>"Pedido não existe ou já foi fechado"
        ]);
        exit;

    }

    $mesa_id = $pedido['mesa_id'];

    // buscar preço do produto

    $sql = "
    SELECT preco
    FROM produtos
    WHERE id=:produto_id
    AND ativo=TRUE
    LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":produto_id"=>$produto_id
    ]);

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$produto){

        echo json_encode([
            "success"=>false,
            "erro"=>"Produto não encontrado"
        ]);
        exit;

    }

    $preco = $produto['preco'];

    // inserir item no carrinho

    $sql = "
    INSERT INTO pedido_itens
    (pedido_id, produto_id, quantidade, preco, status)
    VALUES
    (:pedido_id, :produto_id, :quantidade, :preco, 'carrinho')
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":pedido_id"=>$pedido_id,
        ":produto_id"=>$produto_id,
        ":quantidade"=>$quantidade,
        ":preco"=>$preco
    ]);

    // marcar mesa como ocupada

    $sql = "
    UPDATE mesas
    SET status='ocupada'
    WHERE id=:mesa_id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":mesa_id"=>$mesa_id
    ]);

    echo json_encode([
        "success"=>true
    ]);

}
catch(Exception $e){

    echo json_encode([
        "success"=>false,
        "erro"=>$e->getMessage()
    ]);

}
