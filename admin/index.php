<?php

session_start();

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] != "admin"){
    header("Location: ../index.php");
    exit;
}

require "../config/conexao.php";

/* ESTATÍSTICAS */

$totalMesas = $pdo->query("SELECT COUNT(*) FROM mesas")->fetchColumn();

$mesasOcupadas = $pdo->query("
SELECT COUNT(*) FROM mesas WHERE status='ocupada'
")->fetchColumn();

$totalProdutos = $pdo->query("
SELECT COUNT(*) FROM produtos WHERE ativo=TRUE
")->fetchColumn();

$totalPedidos = $pdo->query("
SELECT COUNT(*) FROM pedidos WHERE status='aberto'
")->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Painel Admin</title>

<style>

body{
margin:0;
font-family:Arial;
background:#ecf0f1;
display:flex;
}

/* SIDEBAR */

.sidebar{

width:220px;
height:100vh;
background:#2c3e50;
color:white;
position:fixed;

}

.sidebar h2{

padding:20px;
margin:0;
background:#1a252f;

}

.sidebar a{

display:block;
padding:15px;
color:white;
text-decoration:none;
border-bottom:1px solid rgba(255,255,255,0.1);

}

.sidebar a:hover{

background:#34495e;

}

/* MAIN */

.main{

margin-left:220px;
flex:1;

}

/* TOPBAR */

.topbar{

background:white;
padding:15px;
font-size:20px;
font-weight:bold;
box-shadow:0 2px 5px rgba(0,0,0,0.1);

}

/* CARDS */

.cards{

display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:15px;
padding:15px;

}

.card{

background:white;
padding:20px;
border-radius:8px;
box-shadow:0 2px 5px rgba(0,0,0,0.1);

}

.card-title{

color:#7f8c8d;
font-size:14px;

}

.card-value{

font-size:28px;
font-weight:bold;
margin-top:5px;

}

/* CORES */

.blue{border-left:5px solid #3498db;}
.green{border-left:5px solid #27ae60;}
.red{border-left:5px solid #e74c3c;}
.orange{border-left:5px solid #e67e22;}

</style>

</head>

<body>

<div class="sidebar">

<h2>Admin</h2>

<a href="index.php">Dashboard</a>

<a href="mesas.php">Mesas</a>

<a href="produtos.php">Produtos</a>

<a href="grupos.php">Grupos</a>

<a href="usuarios.php">Usuários</a>

<a href="../dashboard.php">Voltar ao Sistema</a>

<a href="../logout.php">Sair</a>

</div>


<div class="main">

<div class="topbar">

Painel Administrativo

</div>


<div class="cards">

<div class="card blue">

<div class="card-title">Mesas Total</div>

<div class="card-value">
<?php echo $totalMesas; ?>
</div>

</div>


<div class="card green">

<div class="card-title">Mesas Ocupadas</div>

<div class="card-value">
<?php echo $mesasOcupadas; ?>
</div>

</div>


<div class="card orange">

<div class="card-title">Produtos</div>

<div class="card-value">
<?php echo $totalProdutos; ?>
</div>

</div>


<div class="card red">

<div class="card-title">Pedidos Abertos</div>

<div class="card-value">
<?php echo $totalPedidos; ?>
</div>

</div>


</div>

</div>

</body>
</html>
