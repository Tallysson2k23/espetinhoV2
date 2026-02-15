<?php

session_start();

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Acesso negado";
    exit;
}

require "../config/conexao.php";

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
    padding:20px;
}

input, button{
    padding:10px;
    margin-top:10px;
}

.grupo{
    background:#ecf0f1;
    padding:10px;
    margin-top:5px;
}

</style>

</head>

<body>

<h2>Grupos</h2>

<input type="text" id="nome" placeholder="Nome do grupo">

<button onclick="salvar()">Salvar</button>

<h3>Lista:</h3>

<div id="lista">

<?php foreach($grupos as $grupo): ?>

<div class="grupo">

<?php echo $grupo['nome']; ?>

</div>

<?php endforeach; ?>

</div>

<script>

function salvar(){

    let nome = document.getElementById("nome").value;

    let form = new FormData();
    form.append("nome", nome);

    fetch("../api/salvar_grupo.php",{
        method:"POST",
        body:form
    })
    .then(res=>res.json())
    .then(data=>{

        location.reload();

    });

}

</script>

</body>
</html>
