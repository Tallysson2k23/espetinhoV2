<?php
session_start();

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Acesso negado";
    exit;
}

require "../config/conexao.php";
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Criar Usuário</title>

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

    z-index:1100;

}

.menuBtn{

    width:auto !important;
    display:inline-block;

    font-size:22px;
    background:#34495e;
    color:white;
    border:none;
    padding:8px 12px;
    cursor:pointer;
    border-radius:5px;
    margin-left:10px;

}


.topbar-title{

    margin-left:15px;
    font-size:18px;
    font-weight:bold;

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
    display:flex;
    justify-content:center;

}

/* FORM CARD */

.card{

    background:white;
    padding:20px;
    border-radius:8px;
    width:100%;
    max-width:400px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);

}

.card h2{
    margin-top:0;
}

/* INPUTS */

input, select{

    width:100%;
    padding:10px;
    margin-bottom:10px;
    border-radius:5px;
    border:1px solid #ccc;

}

/* BUTTON */

button{

    background:#27ae60;
    color:white;
    border:none;
    padding:12px;
    width:100%;
    border-radius:5px;
    cursor:pointer;
    font-size:16px;

}

button:hover{

    background:#219150;

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

<div class="topbar">

    <button class="menuBtn" onclick="toggleMenu()">☰</button>

    <div class="topbar-title">
        Criar Usuário
    </div>

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

<div class="card">

<h2>Novo Usuário</h2>

<form method="POST" action="../api/criar_usuario.php">

Nome:
<input name="nome" required>

Usuário:
<input name="usuario" required>

Senha:
<input name="senha" type="password" required>

Nível:
<select name="nivel">
<option value="garcom">Garçom</option>
<option value="admin">Admin</option>
</select>

<button type="submit">Criar Usuário</button>

</form>

</div>

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

</script>

</body>
</html>
