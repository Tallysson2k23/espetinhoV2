<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit;
}

require "config/conexao.php";

$pedido_id = $_GET['id'];

// buscar grupos

$sql = "SELECT * FROM grupos WHERE ativo=TRUE ORDER BY nome";

$stmt = $pdo->query($sql);

$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pedido</title>

<style>

body{

    font-family:Arial;
    background:#ecf0f1;
    margin:0;

}

.topo{

    background:#e74c3c;
    color:white;
    padding:15px;
    text-align:center;

}

.grupo{

    background:white;
    padding:20px;
    border-bottom:1px solid #ddd;
    cursor:pointer;
    font-size:18px;

}

.grupo:hover{

    background:#f1f1f1;

}

</style>

</head>

<body>

<div class="topo">

Pedido #<?php echo $pedido_id; ?>

<br>

<button onclick="abrirCarrinho()">Ver Carrinho</button>

</div>


<div>

<?php foreach($grupos as $grupo): ?>

<div class="grupo" onclick="abrirGrupo(<?php echo $grupo['id']; ?>)">

<?php echo $grupo['nome']; ?>

</div>

<?php endforeach; ?>

</div>

<script>

function abrirGrupo(grupo_id){

    window.location = "produtos.php?pedido_id=<?php echo $pedido_id; ?>&grupo_id=" + grupo_id;

}

</script>

<script>

function abrirGrupo(grupo_id){

    window.location = "produtos.php?pedido_id=<?php echo $pedido_id; ?>&grupo_id=" + grupo_id;

}

function abrirCarrinho(){

    window.location = "carrinho.php?pedido_id=<?php echo $pedido_id; ?>";

}

</script>


</body>
</html>
