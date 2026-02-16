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

$sql = "
SELECT produtos.*, grupos.nome as grupo_nome
FROM produtos
LEFT JOIN grupos ON grupos.id = produtos.grupo_id
ORDER BY produtos.id DESC
";

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
    margin-bottom:20px;
}

.card-title{
    font-size:18px;
    margin-bottom:15px;
    font-weight:bold;
}

.produto{
    display:flex;
    align-items:center;
    gap:15px;
    padding:10px;
    border-bottom:1px solid #ddd;
    cursor:pointer;
}

.produto:hover{
    background:#f8f8f8;
}

.produto img{
    width:60px;
    height:60px;
    border-radius:6px;
    object-fit:cover;
}

.produto-info{
    flex:1;
}

.produto-nome{
    font-weight:bold;
}

.produto-preco{
    color:#27ae60;
}

.produto-grupo{
    font-size:12px;
    color:#666;
}

.produto-status{
    font-size:12px;
}

.ativo{
    color:#27ae60;
}

.inativo{
    color:#e74c3c;
}

.modal-bg{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    display:none;
    justify-content:center;
    align-items:center;
}

.modal{
    background:white;
    padding:20px;
    border-radius:8px;
    width:300px;
}

input, select{
    width:100%;
    padding:10px;
    margin-top:10px;
}

button{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.salvar{
    background:#27ae60;
    color:white;
}

.toggle{
    background:#e67e22;
    color:white;
}

.cancelar{
    background:#7f8c8d;
    color:white;
}

</style>

</head>

<body>

<div class="topbar">
Gerenciar Produtos
</div>

<div class="container">


<!-- NOVO PRODUTO -->

<div class="card">

<div class="card-title">
Novo Produto
</div>

<select id="novo_grupo">
<option value="">Selecione grupo</option>

<?php foreach($grupos as $g): ?>

<option value="<?php echo $g['id']; ?>">
<?php echo $g['nome']; ?>
</option>

<?php endforeach; ?>

</select>

<input id="novo_nome" placeholder="Nome">

<input id="novo_preco" type="number" step="0.01" placeholder="Preço">

<input type="file" id="novo_imagem">

<button class="salvar" onclick="salvarNovo()">
Cadastrar Produto
</button>

</div>


<!-- LISTA PRODUTOS -->

<div class="card">

<div class="card-title">
Lista de Produtos
</div>

<?php foreach($produtos as $p): ?>

<div class="produto" onclick="editarProduto(
<?php echo $p['id']; ?>,
'<?php echo addslashes($p['nome']); ?>',
<?php echo $p['preco']; ?>,
<?php echo $p['grupo_id']; ?>,
'<?php echo $p['imagem']; ?>',
<?php echo $p['ativo'] ? 'true' : 'false'; ?>
)">

<img src="../uploads/<?php echo $p['imagem'] ?: 'sem_imagem.png'; ?>">

<div class="produto-info">

<div class="produto-nome">
<?php echo $p['nome']; ?>
</div>

<div class="produto-grupo">
Grupo: <?php echo $p['grupo_nome']; ?>
</div>

<div class="produto-preco">
R$ <?php echo number_format($p['preco'],2,',','.'); ?>
</div>

<div class="produto-status <?php echo $p['ativo']?'ativo':'inativo'; ?>">
<?php echo $p['ativo']?'Ativo':'Inativo'; ?>
</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>


<!-- MODAL EDITAR -->

<div class="modal-bg" id="modal">

<div class="modal">

<input type="hidden" id="id">

<select id="grupo">

<?php foreach($grupos as $g): ?>

<option value="<?php echo $g['id']; ?>">
<?php echo $g['nome']; ?>
</option>

<?php endforeach; ?>

</select>

<input id="nome">

<input id="preco" type="number" step="0.01">

<input type="file" id="imagem">

<button class="salvar" onclick="salvar()">
Salvar Alterações
</button>

<button class="toggle" onclick="toggle()">
Ativar / Inativar
</button>

<button class="cancelar" onclick="fecharModal()">
Cancelar
</button>

</div>

</div>


<script>

let ativoAtual = true;


/* NOVO PRODUTO */

function salvarNovo(){

let grupo=document.getElementById("novo_grupo").value;
let nome=document.getElementById("novo_nome").value;
let preco=document.getElementById("novo_preco").value;
let imagem=document.getElementById("novo_imagem").files[0];

if(!grupo || !nome || !preco){

alert("Preencha tudo");
return;

}

let form=new FormData();

form.append("grupo_id",grupo);
form.append("nome",nome);
form.append("preco",preco);

if(imagem) form.append("imagem",imagem);

fetch("../api/salvar_produto.php",{

method:"POST",
body:form

})
.then(r=>r.json())
.then(d=>{

if(d.success){

location.reload();

}else{

alert(d.erro || "Erro");

}

});

}


/* EDITAR */

function editarProduto(id,nome,preco,grupo,imagem,ativo){

document.getElementById("id").value=id;
document.getElementById("nome").value=nome;
document.getElementById("preco").value=preco;
document.getElementById("grupo").value=grupo;

ativoAtual=ativo;

document.getElementById("modal").style.display="flex";

}

function fecharModal(){

document.getElementById("modal").style.display="none";

}


/* SALVAR EDIÇÃO */

function salvar(){

let form=new FormData();

form.append("id",document.getElementById("id").value);
form.append("nome",document.getElementById("nome").value);
form.append("preco",document.getElementById("preco").value);
form.append("grupo_id",document.getElementById("grupo").value);

let img=document.getElementById("imagem").files[0];

if(img) form.append("imagem",img);

fetch("../api/editar_produto.php",{

method:"POST",
body:form

})
.then(r=>r.json())
.then(d=>{

if(d.success){

location.reload();

}else{

alert(d.erro || "Erro");

}

});

}



/* TOGGLE */

function toggle(){

let form=new FormData();

form.append("id",document.getElementById("id").value);

fetch("../api/toggle_produto.php",{

method:"POST",
body:form

})
.then(r=>r.json())
.then(d=>{

if(d.success){

location.reload();

}else{

alert("Erro");

}

});

}


</script>

</body>
</html>
