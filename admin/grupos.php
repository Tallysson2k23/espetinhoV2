<?php

session_start();

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Acesso negado";
    exit;
}

require "../config/conexao.php";

/* BUSCAR GRUPOS */

$sql = "SELECT * FROM grupos ORDER BY nome";

$stmt = $pdo->query($sql);

$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Grupos</title>

<style>

body{
    font-family:Arial;
    background:#ecf0f1;
    margin:0;
}

/* TOPBAR */

.topbar{

    background:#2c3e50;
    color:white;
    padding:15px;
    font-size:18px;
    font-weight:bold;

}

/* CONTAINER */

.container{

    max-width:800px;
    margin:auto;
    padding:20px;

}

/* CARD */

.card{

    background:white;
    padding:20px;
    border-radius:8px;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
    margin-bottom:20px;

}

/* TITULO */

.card-title{

    font-size:18px;
    font-weight:bold;
    margin-bottom:15px;

}

/* INPUT */

input{

    width:100%;
    padding:12px;
    border-radius:5px;
    border:1px solid #ccc;
    margin-bottom:15px;
    font-size:16px;

}

/* BOTÃO */

button{

    width:100%;
    padding:12px;
    border:none;
    border-radius:5px;

    background:#27ae60;
    color:white;

    font-size:16px;
    font-weight:bold;

    cursor:pointer;

}

button:hover{

    background:#219150;

}

/* LISTA */

.grupo{

    background:#f8f9fa;
    padding:15px;
    border-radius:6px;
    margin-bottom:10px;

    display:flex;
    justify-content:space-between;
    align-items:center;

}

/* NOME */

.grupo-nome{

    font-weight:bold;
    font-size:16px;

}

/* STATUS BADGE */

.badge{

    padding:4px 8px;
    border-radius:4px;
    font-size:12px;
    background:#27ae60;
    color:white;

}

/* RESPONSIVO */

@media(max-width:600px){

.container{
padding:10px;
}

}

</style>

</head>

<body>

<div class="topbar">
Gerenciar Grupos
</div>


<div class="container">

<!-- CARD CADASTRO -->

<div class="card">

<div class="card-title">
Novo Grupo
</div>

<input type="text" id="nome" placeholder="Nome do grupo">

<button onclick="salvar()">
Salvar Grupo
</button>

</div>


<!-- CARD LISTA -->

<div class="card">

<div class="card-title">
Lista de Grupos
</div>

<div id="lista">

<?php foreach($grupos as $grupo): ?>

<div class="grupo">

<div class="grupo-nome">
<?php echo $grupo['nome']; ?>
</div>

<div class="badge">
Ativo
</div>

</div>

<?php endforeach; ?>

</div>

</div>

</div>


<script>

function salvar(){

let nome = document.getElementById("nome").value;

if(nome.trim() == ""){

alert("Digite o nome do grupo");
return;

}

let form = new FormData();

form.append("nome", nome);

fetch("../api/salvar_grupo.php",{

method:"POST",
body:form

})
.then(res=>res.json())
.then(data=>{

if(data.success){

location.reload();

}else{

alert(data.erro);

}

});

}

</script>

</body>
</html>
