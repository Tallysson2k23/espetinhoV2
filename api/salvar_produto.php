<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

require "../config/conexao.php";

try{

    // validar dados
    if(
        !isset($_POST['grupo_id']) ||
        !isset($_POST['nome']) ||
        !isset($_POST['preco'])
    ){
        echo json_encode([
            "success"=>false,
            "erro"=>"Dados incompletos"
        ]);
        exit;
    }

    $grupo_id = intval($_POST['grupo_id']);
    $nome = trim($_POST['nome']);
    $preco = floatval($_POST['preco']);

    if($grupo_id <= 0 || empty($nome) || $preco <= 0){
        echo json_encode([
            "success"=>false,
            "erro"=>"Dados inválidos"
        ]);
        exit;
    }

    /* UPLOAD DA IMAGEM */

    $imagemNome = null;

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0){

        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));

        // extensões permitidas
        $permitidas = ['jpg','jpeg','png','webp'];

        if(!in_array($ext, $permitidas)){
            echo json_encode([
                "success"=>false,
                "erro"=>"Formato de imagem inválido"
            ]);
            exit;
        }

        // gerar nome único
        $imagemNome = "produto_" . time() . "_" . rand(1000,9999) . "." . $ext;

        $destino = "../uploads/" . $imagemNome;

        if(!move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)){
            echo json_encode([
                "success"=>false,
                "erro"=>"Erro ao salvar imagem"
            ]);
            exit;
        }

    }

    /* SALVAR PRODUTO */

    $sql = "
    INSERT INTO produtos
    (grupo_id, nome, preco, imagem)
    VALUES
    (:grupo_id, :nome, :preco, :imagem)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ":grupo_id"=>$grupo_id,
        ":nome"=>$nome,
        ":preco"=>$preco,
        ":imagem"=>$imagemNome

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
