<?php
session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location: ../index.php");
    exit;
}

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Acesso negado";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin</title>

<style>

body{
    font-family:Arial;
    background:#ecf0f1;
}

.menu{
    padding:20px;
}

a{
    display:block;
    padding:15px;
    background:white;
    margin-bottom:10px;
    text-decoration:none;
    color:black;
    border-radius:8px;
}

</style>

</head>

<body>

<div class="menu">

<h2>Painel Admin</h2>

<a href="grupos.php">Cadastrar Grupos</a>

<a href="produtos.php">Cadastrar Produtos</a>

<a href="../dashboard.php">Voltar</a>

</div>

</body>
</html>
