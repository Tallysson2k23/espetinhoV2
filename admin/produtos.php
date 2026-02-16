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

/* BUSCAR PRODUTOS */

$sql = "SELECT produtos.*, grupos.nome as grupo_nome 
        FROM produtos
        LEFT JOIN grupos ON grupos.id = produtos.grupo_id
        ORDER BY produtos.id DESC";

$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Produtos</title>

<style>

body{
font-family:Arial;
background:#ecf0f1;
margin:0;
}

.topbar{
background:#2c3e50;
color:white;
padding:15px;
font-size:18px;
font-weight:bold;
}

.container{
padding:20px;
max-width:900px;
margin:auto;
}

.card{
background:white;
padding:20px;
border-radius:8px;
box-shadow:0 2px 5px rgba(0,0,0,0.1);
margin-bottom:20px;
}

.card-title{
font-size:18px;
margin-bottom:15px;
font-weight:bold;
}

input, select{
width:100%;
padding:12px;
margin-top:8px;
margin-bottom:15px;
border-radius:5px;
border:1px solid #ccc;
font-size:16px;
}

button{
background:#27ae60;
color:white;
border:none;
padding:12px;
width:100%;
border-radius:5px;
font-size:16px;
font-weight:bold;
cursor:pointer;
}

button:hover{
background:#219150;
}

.produto{
background:#f8f9fa;
padding:15px;
border-radius:6px;
margin-bottom:10px;
display:flex;
align-items:center;
gap:15px;
}

.produto img{
width:60px;
height:60px;
object-fit:cover;
border-radius:6px;
}

.produto-info{
flex:1;
}

.produto-nome{
font-weight:bold;
}

.produto-grupo{
font-size:13px;
color:#666;
}

.produto-preco{
font-weight:bold;
color:#27ae60;
}

</style>

</head>

<body>

<div class="topbar">
Cadastrar Produtos
</div>

<div class="container">

<div class="card">

<div class="card-title">
Novo Produto
</div>

<select id="grupo">
<option value="">Selecione grupo</option>

<?php foreach($grupos as $grupo): ?>
<option value="<?php echo $grupo['id']; ?>">
<?php echo $grupo['nome']; ?>
</option>
<?php endforeach; ?>

</select>

<input type="text" id="nome" placeholder="Nome do produto">

<input type="number" id="preco" placeholder="Preço" step="0.01">

<input type="file" id="imagem" accept="image/*">

<button onclick="salvar()">
Salvar Produto
</button>

</div>


<div class="card">

<div class="card-title">
Lista de Produtos
</div>

<?php foreach($produtos as $produto): ?>

<div class="produto">

<img src="../uploads/<?php echo $produto['imagem'] ?: 'sem-imagem.png'; ?>">

<div class="produto-info">

<div class="produto-nome">
<?php echo $produto['nome']; ?>
</div>

<div class="produto-grupo">
Grupo: <?php echo $produto['grupo_nome']; ?>
</div>

<div class="produto-preco">
R$ <?php echo number_format($produto['preco'],2,',','.'); ?>
</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>


<script>

function salvar(){

let grupo = document.getElementById("grupo").value;
let nome = document.getElementById("nome").value;
let preco = document.getElementById("preco").value;
let imagem = document.getElementById("imagem").files[0];

if(!grupo || !nome || !preco){

alert("Preencha tudo");
return;

}

let form = new FormData();

form.append("grupo_id", grupo);
form.append("nome", nome);
form.append("preco", preco);

if(imagem){
form.append("imagem", imagem);
}

fetch("../api/salvar_produto.php",{
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
