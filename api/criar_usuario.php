<?php

require "../config/conexao.php";

$nome = $_POST['nome'];
$usuario = $_POST['usuario'];
$senha = md5($_POST['senha']);
$nivel = $_POST['nivel'];

$sql = "

INSERT INTO usuarios
(nome, usuario, senha, nivel)
VALUES
(:nome, :usuario, :senha, :nivel)

";

$stmt = $pdo->prepare($sql);

$stmt->execute([
":nome"=>$nome,
":usuario"=>$usuario,
":senha"=>$senha,
":nivel"=>$nivel
]);

header("Location: ../admin/usuarios.php");
