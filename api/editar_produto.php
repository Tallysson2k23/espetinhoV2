<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require "../config/conexao.php";

try{

    /* VALIDAR LOGIN */

    if(!isset($_SESSION['usuario_id'])){

        echo json_encode([
            "success"=>false,
            "erro"=>"Não autorizado"
        ]);
        exit;

    }

    /* VALIDAR DADOS */

    if(
        !isset($_POST['id']) ||
        !isset($_POST['nome']) ||
        !isset($_POST['preco']) ||
        !isset($_POST['grupo_id'])
    ){

        echo json_encode([
            "success"=>false,
            "erro"=>"Dados incompletos"
        ]);
        exit;

    }

    $id     = intval($_POST['id']);
    $nome   = trim($_POST['nome']);
    $preco  = floatval($_POST['preco']);
    $grupo  = intval($_POST['grupo_id']);

    if($id <= 0){

        echo json_encode([
            "success"=>false,
            "erro"=>"ID inválido"
        ]);
        exit;

    }

    /* UPLOAD IMAGEM */

    $imagemNome = null;

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){

        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

        $extPermitidas = ["jpg","jpeg","png","webp"];

        if(!in_array($ext,$extPermitidas)){

            echo json_encode([
                "success"=>false,
                "erro"=>"Formato de imagem inválido"
            ]);
            exit;

        }

        $imagemNome = "produto_" . time() . "." . $ext;

        $caminho = "../uploads/" . $imagemNome;

        if(!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)){

            echo json_encode([
                "success"=>false,
                "erro"=>"Erro ao salvar imagem"
            ]);
            exit;

        }

    }

    /* UPDATE */

    if($imagemNome){

        $sql = "
        UPDATE produtos SET
        nome=:nome,
        preco=:preco,
        grupo_id=:grupo,
        imagem=:imagem
        WHERE id=:id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            ":nome"=>$nome,
            ":preco"=>$preco,
            ":grupo"=>$grupo,
            ":imagem"=>$imagemNome,
            ":id"=>$id

        ]);

    }else{

        $sql = "
        UPDATE produtos SET
        nome=:nome,
        preco=:preco,
        grupo_id=:grupo
        WHERE id=:id
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            ":nome"=>$nome,
            ":preco"=>$preco,
            ":grupo"=>$grupo,
            ":id"=>$id

        ]);

    }

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
