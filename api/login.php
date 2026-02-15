<?php
session_start();
require "../config/conexao.php";

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

$senha = md5($senha);

$sql = "SELECT * FROM usuarios 
        WHERE usuario = :usuario 
        AND senha = :senha
        AND ativo = TRUE";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":usuario",$usuario);
$stmt->bindParam(":senha",$senha);

$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user){

    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_nome'] = $user['nome'];
    $_SESSION['usuario_nivel'] = $user['nivel'];

    echo json_encode([
        "success"=>true
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Usuário ou senha inválidos"
    ]);

}
