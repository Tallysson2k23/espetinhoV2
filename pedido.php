<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit;
}

require "config/conexao.php";

/* VALIDAR PEDIDO */

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$pedido_id = intval($_GET['id']);
$mesa_id   = intval($_GET['mesa_id'] ?? 0);

/* BUSCAR mesa_id SE NÃO VEIO */

if($mesa_id <= 0){

    $sql = "SELECT mesa_id FROM pedidos WHERE id=:pedido_id LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":pedido_id"=>$pedido_id
    ]);

    $mesa_id = $stmt->fetchColumn();

    if(!$mesa_id){
        echo "Pedido inválido.";
        exit;
    }

}

/* BUSCAR GRUPOS */

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

    /* IMPORTANTE PARA NÃO CORTAR */
    padding-bottom:140px;
}

/* TOPBAR */

.topo{
    background:#2c3e50;
    color:white;
    padding:15px;
    display:flex;
    align-items:center;
    font-size:18px;
    font-weight:bold;
}

.btn-voltar{
    background:#34495e;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:5px;
    cursor:pointer;
    margin-right:10px;
}

.btn-voltar:hover{
    background:#3d566e;
}

.titulo{
    flex:1;
    text-align:center;
    margin-right:40px;
}

/* GRUPOS */

.grupo{
    background:white;
    padding:18px;
    border-bottom:1px solid #ddd;
    cursor:pointer;
    font-size:18px;
}

.grupo:hover{
    background:#f8f8f8;
}

/* ITENS */

#itensCarrinho{
    margin-bottom:10px;
}

.item-carrinho{
    background:white;
    padding:12px;
    border-bottom:1px solid #ddd;
}

.item-nome{
    font-weight:bold;
}

.item-info{
    font-size:14px;
    color:#666;
}

/* BOTÃO ENVIAR */

.btn-enviar{

    position:fixed;
    bottom:60px;
    left:0;
    width:100%;

    background:#e67e22;
    color:white;

    padding:15px;

    border:none;

    font-size:18px;
    font-weight:bold;

    cursor:pointer;

    box-sizing:border-box;

    z-index:999;
}

/* CARRINHO */

.carrinho-bar{

    position:fixed;
    bottom:0;
    left:0;
    width:100%;

    background:#27ae60;
    color:white;

    padding:15px 20px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    font-size:16px;
    cursor:pointer;

    box-sizing:border-box;

    z-index:1000;
}

.carrinho-bar:hover{
    background:#219150;
}


.secao-titulo{

padding:10px;
background:#ddd;
font-weight:bold;
margin-top:10px;

}

.item-carrinho{

background:white;
margin:10px;
padding:12px;
border-radius:8px;
box-shadow:0 2px 5px rgba(0,0,0,0.1);

}

.total-box{

position:fixed;
bottom:110px;
left:0;
width:100%;

background:#2c3e50;
color:white;

padding:15px;

font-size:18px;
font-weight:bold;

text-align:center;

}



</style>

</head>

<body>

<div class="topo">

<button class="btn-voltar" onclick="voltarMesas()">←</button>

<span class="titulo">
Pedido #<?php echo $pedido_id; ?>
</span>

</div>


<!-- GRUPOS -->

<div>

<?php foreach($grupos as $grupo): ?>

<div class="grupo"
onclick="abrirGrupo(<?php echo $grupo['id']; ?>)">

<?php echo $grupo['nome']; ?>

</div>

<?php endforeach; ?>

</div>


<!-- ITENS DO CARRINHO -->
<div class="secao-titulo">
Itens do Pedido
</div>

<div id="itensCarrinho"></div>

<div class="total-box">
TOTAL: R$ <span id="totalPedido">0,00</span>
</div>

<button class="btn-enviar" onclick="enviarPedido()">
ENVIAR PEDIDO
</button>



<!-- BARRA CARRINHO -->




<script>

/* ABRIR GRUPO */

function abrirGrupo(grupo_id){

window.location =
"produtos.php?pedido_id=<?php echo $pedido_id; ?>&mesa_id=<?php echo $mesa_id; ?>&grupo_id=" + grupo_id;

}

/* ABRIR CARRINHO */

function abrirCarrinho(){

window.location =
"carrinho.php?pedido_id=<?php echo $pedido_id; ?>&mesa_id=<?php echo $mesa_id; ?>";

}

/* VOLTAR */

function voltarMesas(){

window.location="dashboard.php";

}

/* ATUALIZAR CARRINHO */

function atualizarCarrinho(){

fetch("api/carrinho_status.php?pedido_id=<?php echo $pedido_id; ?>")

.then(res=>res.json())

.then(data=>{

if(data.success){

document.getElementById("qtd").innerText = data.quantidade;
document.getElementById("total").innerText = data.total;
document.getElementById("totalPedido").innerText = data.total;

}

})

.catch(()=>{});

}

/* CARREGAR ITENS */

function carregarItens(){

fetch("api/listar_itens_carrinho.php?pedido_id=<?php echo $pedido_id; ?>")

.then(res=>res.json())

.then(itens=>{

let html="";

if(!itens || itens.length===0){

html="<div style='padding:15px;color:#777'>Nenhum item no carrinho</div>";

}else{

itens.forEach(item=>{

html+=`
<div class="item-carrinho">

<div class="item-nome">
${item.nome}
</div>

<div class="item-info">
Qtd: ${item.quantidade}  
R$ ${item.total}
</div>

</div>
`;

});

}

document.getElementById("itensCarrinho").innerHTML=html;

})

.catch(()=>{

document.getElementById("itensCarrinho").innerHTML="";

});

}

/* ENVIAR PEDIDO */

function enviarPedido(){

fetch("api/enviar_pedido.php",{

method:"POST",

body:new URLSearchParams({

pedido_id:"<?php echo $pedido_id; ?>"

})

})
.then(res=>res.json())

.then(data=>{

if(data.success){

alert("Pedido enviado com sucesso");

}else{

alert(data.erro || "Erro ao enviar");

}

carregarItens();
atualizarCarrinho();

})
.catch(()=>{

alert("Erro de conexão");

});

}


/* AUTO UPDATE */

setInterval(atualizarCarrinho,1000);
setInterval(carregarItens,1000);

atualizarCarrinho();
carregarItens();

</script>


</body>
</html>
