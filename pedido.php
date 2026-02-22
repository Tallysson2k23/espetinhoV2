<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit;
}

require "config/conexao.php";

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$pedido_id = intval($_GET['id']);
$mesa_id   = intval($_GET['mesa_id'] ?? 0);

if($mesa_id <= 0){

$sql = "SELECT mesa_id FROM pedidos WHERE id=:pedido_id LIMIT 1";

$stmt = $pdo->prepare($sql);

$stmt->execute([
":pedido_id"=>$pedido_id
]);

$mesa_id = $stmt->fetchColumn();

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

/* GERAL */

body{

font-family:Arial;
background:#f4f6f8;
margin:0;
padding-bottom:140px;

}

/* TOPO */

.topo{

background:#2c3e50;
color:white;

padding:15px;

display:flex;
align-items:center;

font-size:18px;
font-weight:bold;

box-shadow:0 2px 5px rgba(0,0,0,0.2);

}

.btn-voltar{

background:#34495e;
color:white;

border:none;

padding:8px 12px;

border-radius:6px;

cursor:pointer;

margin-right:10px;

}

.titulo{

flex:1;
text-align:center;
margin-right:40px;

}

/* CATEGORIAS BONITAS */

.categorias{

display:grid;
grid-template-columns:repeat(auto-fill,minmax(125px,1fr));
gap:15px;
padding:15px;

}


.categoria{

background:white;
border-radius:16px;

cursor:pointer;

box-shadow:0 4px 12px rgba(0,0,0,0.15);

transition:0.2s;

overflow:hidden;

display:flex;
flex-direction:column;

}

.categoria-img{

width:100%;
height:100px;

}

.categoria-img img{

width:100%;
height:100%;

object-fit:cover;

display:block;

}

.categoria-nome{

padding:12px 8px;

font-weight:bold;

background:white;

text-align:center;     /* ⭐ CENTRALIZA */

font-size:15px;

display:flex;          /* ⭐ CENTRALIZA VERTICAL */
align-items:center;
justify-content:center;

}



.categoria:hover{

transform:translateY(-5px) scale(1.03);

box-shadow:0 8px 20px rgba(0,0,0,0.25);

}


.categoria-icon{

font-size:28px;
margin-bottom:5px;

}

.categoria-nome{

font-weight:bold;

}

/* ITENS */

.secao{

padding:15px;

}

.secao h3{

margin:0 0 10px 0;

}

/* ITEM CARD */

.item{

background:white;

border-radius:12px;

padding:12px;

margin-bottom:10px;

display:flex;

align-items:center;

box-shadow:0 2px 6px rgba(0,0,0,0.1);

}

.item img{

width:70px;
height:70px;

border-radius:10px;

object-fit:cover;

margin-right:12px;

border:1px solid #ddd;

}

.item-info{

flex:1;

}

.item-nome{

font-weight:bold;

font-size:16px;

}

.item-qtd{

font-size:13px;

color:#777;

margin-top:3px;

}

.item-preco{

font-weight:bold;

color:#27ae60;

font-size:16px;

}

/* TOTAL */

.total-box{

position:fixed;

bottom:55px;

left:0;

width:100%;

background:white;

padding:15px 20px;

font-size:18px;

font-weight:bold;

display:flex;

justify-content:space-between;

align-items:center;

box-shadow:0 -2px 10px rgba(0,0,0,0.15);

box-sizing:border-box; /* ⭐ evita corte */

z-index:999;

}


/* BOTÃO */

.btn-enviar{

position:fixed;

bottom:0;

left:0;

width:100%;

background:#27ae60;

color:white;

padding:18px 20px;

font-size:18px;

font-weight:bold;

border:none;

cursor:pointer;

box-sizing:border-box; /* ⭐ evita corte */

z-index:1000;

}


.btn-enviar:hover{

background:#219150;

}

/* VAZIO */

.vazio{

text-align:center;

color:#777;

margin-top:20px;

}

.btn-excluir{

background:#e74c3c;

color:white;

border:none;

padding:6px 10px;

border-radius:6px;

cursor:pointer;

margin-top:5px;

display:block;

}

.btn-excluir:hover{

background:#c0392b;

}


</style>

</head>

<body>

<div class="topo">

<button class="btn-voltar" onclick="voltarMesas()">←</button>

<div class="titulo">
Pedido #<?php echo $pedido_id; ?>
</div>

</div>


<!-- CATEGORIAS -->

<div class="categorias">

<?php foreach($grupos as $grupo): ?>

<div class="categoria"

onclick="abrirGrupo(<?php echo $grupo['id']; ?>)">

<?php

$nome = strtolower($grupo['nome']);

$imagem = "imagens/categorias/padrao.png";

if(str_contains($nome,"bebida")) $imagem="imagens/categorias/bebidas.png";
if(str_contains($nome,"espet")) $imagem="imagens/categorias/espetos.png";
if(str_contains($nome,"por")) $imagem="imagens/categorias/porcoes.png";
if(str_contains($nome,"suco")) $imagem="imagens/categorias/sucos.png";
if(str_contains($nome,"cerveja")) $imagem="imagens/categorias/cervejas.png";
if(str_contains($nome,"almo")) $imagem="imagens/categorias/almoco.png";

?>

<div class="categoria-img">

<img src="<?php echo $imagem; ?>">

</div>



<div class="categoria-nome">
<?php echo $grupo['nome']; ?>
</div>

</div>

<?php endforeach; ?>

</div>


<!-- ITENS -->

<div class="secao">

<h3>Itens do Pedido</h3>

<div id="itens"></div>

</div>


<!-- TOTAL -->

<div class="total-box">

<div>TOTAL</div>

<div>
R$ <span id="total">0,00</span>
</div>

</div>


<!-- BOTÃO -->

<button class="btn-enviar"

onclick="enviarPedido()">

ENVIAR PEDIDO

</button>

<div id="msgSucesso" style="
position:fixed;
top:20px;
left:50%;
transform:translateX(-50%);
background:#27ae60;
color:white;
padding:12px 20px;
border-radius:8px;
font-weight:bold;
box-shadow:0 4px 10px rgba(0,0,0,0.2);
display:none;
z-index:2000;
">
Pedido enviado com sucesso!
</div>

<script>

function abrirGrupo(grupo_id){

window.location =
"produtos.php?pedido_id=<?php echo $pedido_id; ?>&mesa_id=<?php echo $mesa_id; ?>&grupo_id=" + grupo_id;

}

function voltarMesas(){

window.location="dashboard.php";

}

/* CARREGAR ITENS COM FOTO */
function carregarItens(){

fetch("api/listar_itens_carrinho.php?pedido_id=<?php echo $pedido_id; ?>")

.then(res=>res.json())

.then(itens=>{

let html="";

if(!itens || itens.length===0){

html="<div class='vazio'>Nenhum item no pedido</div>";

}else{

itens.forEach(item=>{

let img = item.imagem ? item.imagem : "sem_imagem.png";

html+=`

<div class="item">

<img src="uploads/${img}" onerror="this.src='uploads/sem_imagem.png'">

<div class="item-info">

<div class="item-nome">
${item.nome}
</div>

<div class="item-qtd">
Qtd: ${item.quantidade}
</div>

</div>

<div class="item-preco">

R$ ${item.total}

<button class="btn-excluir"
onclick="excluirItem(${item.id})">
🗑
</button>

</div>

</div>

`;

});

}

document.getElementById("itens").innerHTML=html;

});

}



/* TOTAL */

function atualizarTotal(){

fetch("api/carrinho_status.php?pedido_id=<?php echo $pedido_id; ?>")

.then(res=>res.json())

.then(data=>{

if(data.success){

document.getElementById("total").innerText=data.total;

}

});

}

function getPrinterByGroup(grupo){

grupo = grupo.toUpperCase();

if(grupo === "ALMOÇO" || grupo === "ESPETOS"){
    return "COZINHA";
}

if(grupo === "PORÇÕES"){
    return "PORCOES";
}

if(grupo === "CERVEJAS" || grupo === "BEBIDAS"){
    return "BEBIDAS";
}

if(grupo === "SUCOS"){
    return "SUCOS";
}

return null;

}

function gerarCupom(nomeTopo, itensGrupo){

let mesa = itensGrupo[0].mesa_id;
let garcom = itensGrupo[0].garcom.toUpperCase();

let agora = new Date();

let dataHora =
agora.getDate().toString().padStart(2,'0') + "/" +
(agora.getMonth()+1).toString().padStart(2,'0') + "/" +
agora.getFullYear().toString().slice(-2) + " " +
agora.getHours().toString().padStart(2,'0') + ":" +
agora.getMinutes().toString().padStart(2,'0') + ":" +
agora.getSeconds().toString().padStart(2,'0');

const largura = 32; // padrão 80mm

function centralizar(texto){
let espacos = Math.floor((largura - texto.length) / 2);
return " ".repeat(Math.max(0, espacos)) + texto + "\n";
}

function linha(){
return "-".repeat(largura) + "\n";
}

let texto = "";

texto += centralizar(nomeTopo.toUpperCase());
texto += linha();
texto += "Mesa: " + mesa + "\n";
texto += "Atendimento: <?php echo $pedido_id; ?>\n";
texto += linha();
texto += "QTD  DESCRIÇÃO\n";
texto += linha();

itensGrupo.forEach(item => {

let qtd = item.quantidade.toString().padEnd(4, " ");
let nome = item.produto;

if(nome.length > 26){
nome = nome.substring(0, 26);
}

texto += qtd + nome + "\n";

// 🔥 Se tiver observação, imprimir abaixo
if(item.observacao && item.observacao.trim() !== ""){

let obs = item.observacao.trim();

// quebrar observação longa em linhas
while(obs.length > 26){
texto += "     * " + obs.substring(0, 23) + "\n";
obs = obs.substring(23);
}

texto += "     * " + obs + "\n";
}

});

texto += linha();
texto += "Atendente: " + garcom + "\n";
texto += dataHora + "\n\n\n";

return texto;
}


function enviarPedido(){

// 🔥 PARAR AUTO UPDATE
clearInterval(window.intervalItens);
clearInterval(window.intervalTotal);

fetch("api/enviar_pedido.php",{

method:"POST",

body:new URLSearchParams({
pedido_id:"<?php echo $pedido_id; ?>"
})

})
.then(res=>res.json())
.then(data=>{

if(data.success){

let itens = data.itens;

// Separar por grupo
let grupos = {};

itens.forEach(item => {
if(!grupos[item.grupo]){
grupos[item.grupo] = [];
}
grupos[item.grupo].push(item);
});

let promessas = [];

Object.keys(grupos).forEach(nomeGrupo => {

let impressora = getPrinterByGroup(nomeGrupo);

if(!impressora){
console.log("Grupo sem impressora:", nomeGrupo);
return;
}

let nomeTopo = "";

if(impressora === "COZINHA") nomeTopo = "COZINHA ESPETOS";
if(impressora === "PORCOES") nomeTopo = "COZINHA PORÇÕES";
if(impressora === "BEBIDAS") nomeTopo = "BEBIDAS";
if(impressora === "SUCOS") nomeTopo = "SUCOS";

let textoCupom = gerarCupom(nomeTopo, grupos[nomeGrupo]);


//para ver cupom no console do navegador 
// console.log("CUPOM GERADO:");
console.log(textoCupom); 


let promessa = qz.print(
    qz.configs.create(impressora),
    [{
        type: 'raw',
        data: textoCupom
    }]
);

promessas.push(promessa);

});

// 🔥 Espera TODAS as impressões terminarem
Promise.all(promessas)
.then(() => {
console.log("Todas impressões concluídas");
window.location.href = "dashboard.php?msg=enviado";
})
.catch(err => {
console.error("Erro na impressão:", err);
alert("Erro ao imprimir. Verifique o console.");
});

}else{
alert(data.erro);
}

});

}

/* AUTO UPDATE */

window.intervalItens = setInterval(carregarItens,1000);
window.intervalTotal = setInterval(atualizarTotal,1000);

carregarItens();

atualizarTotal();

function excluirItem(id){

if(!confirm("Remover este item?")) return;

let form=new FormData();

form.append("id",id);

fetch("api/excluir_item.php",{

method:"POST",
body:form

})
.then(res=>res.json())
.then(data=>{

carregarItens();
atualizarTotal();

});

}


</script>



<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.3/qz-tray.js"></script>

<script>
qz.websocket.connect()
.then(() => {
    console.log("Conectado ao QZ Tray");
})
.catch(err => {
    console.error("Erro ao conectar com QZ:", err);
});
</script>



</body>
</html>
