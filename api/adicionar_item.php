<?php

session_start();
header('Content-Type: application/json; charset=utf-8');
require "../config/conexao.php";

try{

    if(!isset($_SESSION['usuario_id'])){
        echo json_encode(["success"=>false,"erro"=>"Usuário não logado"]);
        exit;
    }

    if(!isset($_POST['pedido_id']) || 
       !isset($_POST['produto_id']) || 
       !isset($_POST['quantidade'])){
        echo json_encode(["success"=>false,"erro"=>"Dados incompletos"]);
        exit;
    }

    $pedido_id  = intval($_POST['pedido_id']);
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);
  $observacao = isset($_POST['observacao']) 
    ? trim($_POST['observacao']) 
    : '';

if($observacao === ''){
    $observacao = '';
}

    if($pedido_id <= 0 || $produto_id <= 0 || $quantidade <= 0){
        echo json_encode(["success"=>false,"erro"=>"Dados inválidos"]);
        exit;
    }

    // Verifica pedido aberto
    $sql = "SELECT id, mesa_id 
            FROM pedidos 
            WHERE id=:pedido_id AND status='aberto' 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":pedido_id"=>$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$pedido){
        echo json_encode(["success"=>false,"erro"=>"Pedido fechado ou inexistente"]);
        exit;
    }

    $mesa_id = $pedido['mesa_id'];

    // Buscar preço
    $sql = "SELECT preco 
            FROM produtos 
            WHERE id=:produto_id AND ativo=TRUE 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":produto_id"=>$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$produto){
        echo json_encode(["success"=>false,"erro"=>"Produto não encontrado"]);
        exit;
    }

    $preco = $produto['preco'];

    // 🔍 Verificar se já existe item igual no carrinho
    $sql = "SELECT id 
            FROM pedido_itens
            WHERE pedido_id = :pedido_id
            AND produto_id = :produto_id
            AND status = 'carrinho'
            AND COALESCE(observacao,'') = COALESCE(:observacao,'')
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":pedido_id"=>$pedido_id,
        ":produto_id"=>$produto_id,
        ":observacao"=>$observacao
    ]);

    $itemExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if($itemExistente){

        // 🔥 SOMA quantidade
        $sql = "UPDATE pedido_itens
                SET quantidade = quantidade + :quantidade
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":quantidade"=>$quantidade,
            ":id"=>$itemExistente['id']
        ]);

    }else{

        // 🆕 INSERE novo item
        $sql = "INSERT INTO pedido_itens
                (pedido_id, produto_id, quantidade, preco, status, observacao)
                VALUES
                (:pedido_id, :produto_id, :quantidade, :preco, 'carrinho', :observacao)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":pedido_id"=>$pedido_id,
            ":produto_id"=>$produto_id,
            ":quantidade"=>$quantidade,
            ":preco"=>$preco,
            ":observacao"=>$observacao
        ]);
    }

    // Marca mesa ocupada
    $sql = "UPDATE mesas SET status='ocupada' WHERE id=:mesa_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":mesa_id"=>$mesa_id]);

    echo json_encode(["success"=>true]);

}catch(Exception $e){
    echo json_encode(["success"=>false,"erro"=>$e->getMessage()]);
}