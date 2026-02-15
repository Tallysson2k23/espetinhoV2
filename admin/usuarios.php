<?php
session_start();

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Acesso negado";
    exit;
}

require "../config/conexao.php";

$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nivel = $_SESSION['usuario_nivel'];
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Usuários</title>

<style>

body{
    margin:0;
    font-family:Arial;
    background:#ecf0f1;
}

/* TOPBAR */

.topbar{

    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:60px;
    background:#2c3e50;
    color:white;
    display:flex;
    align-items:center;
    padding-left:60px;
    font-size:18px;
    font-weight:bold;
    z-index:1000;

}

/* BOTÃO MENU */

.menuBtn{

    position:fixed;
    top:10px;
    left:10px;
    font-size:22px;
    background:#2c3e50;
    color:white;
    border:none;
    padding:8px 12px;
    cursor:pointer;
    border-radius:5px;
    z-index:1200;

}

/* SIDEBAR */

.sidebar{

    width:220px;
    height:100vh;
    background:#2c3e50;
    color:white;
    position:fixed;
    left:-220px;
    top:0;
    transition:0.3s;
    z-index:1300;

}

.sidebar.active{
    left:0;
}

.sidebar h2{
    padding:15px;
    margin:0;
    background:#1a252f;
}

.usuarioBox{
    padding:15px;
    background:#22313f;
}

.sidebar a{

    display:block;
    padding:15px;
    color:white;
    text-decoration:none;

}

.sidebar a:hover{
    background:#34495e;
}

/* MAIN */

.main{

    padding:80px 20px;

}

/* HEADER */

.header{

    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;

}

/* BOTÃO */

.btn{

    background:#27ae60;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:5px;
    cursor:pointer;

}

.btn:hover{
    background:#219150;
}

.btn-danger{

    background:#e74c3c;

}

.btn-danger:hover{

    background:#c0392b;

}

/* CARD */

.card{

    background:white;
    padding:15px;
    margin-bottom:10px;
    border-radius:8px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);

}

/* STATUS */

.status-ativo{

    color:#27ae60;
    font-weight:bold;

}

.status-inativo{

    color:#e74c3c;
    font-weight:bold;

}

/* OVERLAY */

.overlay{

    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.3);
    display:none;
    z-index:1250;

}

.overlay.active{

    display:block;

}

</style>

</head>
<body>

<button class="menuBtn" onclick="toggleMenu()">☰</button>

<div class="topbar">
Gerenciar Usuários
</div>

<div class="overlay" id="overlay" onclick="fecharMenu()"></div>

<div class="sidebar" id="sidebar">

<h2>Espetinho</h2>

<div class="usuarioBox">

<?php echo $_SESSION['usuario_nome']; ?><br>
<small><?php echo ucfirst($_SESSION['usuario_nivel']); ?></small>

</div>

<a href="../dashboard.php">Mesas</a>
<a href="usuarios.php">Usuários</a>
<a href="../logout.php">Sair</a>

</div>

<div class="main">

<div class="header">

<h2>Usuários</h2>

<a href="criar_usuario.php">
<button class="btn">Novo Usuário</button>
</a>

</div>

<?php foreach($usuarios as $user): ?>

<div class="card">

<b><?php echo $user['nome']; ?></b><br>

Usuário: <?php echo $user['usuario']; ?><br>

Nível: <?php echo ucfirst($user['nivel']); ?><br>

Status:

<span class="<?php echo $user['ativo'] ? 'status-ativo' : 'status-inativo'; ?>">

<?php echo $user['ativo'] ? "Ativo" : "Inativo"; ?>

</span>

<br><br>

<a href="editar_usuario.php?id=<?php echo $user['id']; ?>">
<button class="btn">Editar</button>
</a>

<button class="btn-danger" onclick="toggleUsuario(<?php echo $user['id']; ?>)">

<?php echo $user['ativo'] ? "Inativar" : "Ativar"; ?>

</button>

</div>

<?php endforeach; ?>

</div>

<script>

function toggleMenu(){

document.getElementById("sidebar").classList.add("active");
document.getElementById("overlay").classList.add("active");

}

function fecharMenu(){

document.getElementById("sidebar").classList.remove("active");
document.getElementById("overlay").classList.remove("active");

}

function toggleUsuario(id){

fetch("../api/toggle_usuario.php?id="+id)
.then(()=>location.reload())

}

</script>

</body>
</html>
