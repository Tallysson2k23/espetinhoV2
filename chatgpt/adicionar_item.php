<?php

session_start();
header('Content-Type: application/json; charset=utf-8');
require "../config/conexao.php";

try{

    if(!isset($_SESSION['usuario_id'])){
        echo json_encode(["success"=>false,"erro"=>"Usuário não logado"]);
        exit;
    }

    if(!isset($_POST['mesa_id']) || 
       !isset($_POST['produto_id']) || 
       !isset($_POST['quantidade'])){
        echo json_encode(["success"=>false,"erro"=>"Dados incompletos"]);
        exit;
    }

    $mesa_id    = intval($_POST['mesa_id']);
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);
    $observacao = isset($_POST['observacao']) ? trim($_POST['observacao']) : '';

    if($mesa_id <= 0 || $produto_id <= 0 || $quantidade <= 0){
        echo json_encode(["success"=>false,"erro"=>"Dados inválidos"]);
        exit;
    }

    /* =======================================
       VERIFICA SE EXISTE PEDIDO ABERTO
    ======================================= */

    $sql = "SELECT id FROM pedidos 
            WHERE mesa_id=:mesa_id 
            AND status='aberto' 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":mesa_id"=>$mesa_id]);
    $pedido_id = $stmt->fetchColumn();

    $pedido_criado_agora = false;

    /* =======================================
       SE NÃO EXISTE, CRIA AGORA
    ======================================= */

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
        $pedido_criado_agora = true;
    }

    /* =======================================
       BUSCAR PREÇO DO PRODUTO
    ======================================= */

    $sql = "SELECT preco FROM produtos 
            WHERE id=:produto_id 
            AND ativo=TRUE 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":produto_id"=>$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$produto){
        echo json_encode(["success"=>false,"erro"=>"Produto não encontrado"]);
        exit;
    }

    $preco = $produto['preco'];

    /* =======================================
       INSERE ITEM
    ======================================= */

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

    /* =======================================
       SE FOI O PRIMEIRO ITEM → MARCA MESA
    ======================================= */

$pdo->prepare("
    UPDATE mesas 
    SET status='ocupada'
    WHERE id=:mesa_id
")->execute([
    ":mesa_id"=>$mesa_id
]);

    echo json_encode(["success"=>true]);

}catch(Exception $e){
    echo json_encode(["success"=>false,"erro"=>$e->getMessage()]);
}