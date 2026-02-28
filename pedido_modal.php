<?php
session_start();

if(!isset($_SESSION['usuario_id'])){
    echo "Sessão expirada.";
    exit;
}

require "config/conexao.php";

$mesa_id = intval($_GET['mesa_id'] ?? 0);

if($mesa_id <= 0){
    echo "Mesa inválida";
    exit;
}

$sql = "SELECT * FROM grupos WHERE ativo=TRUE ORDER BY nome";
$stmt = $pdo->query($sql);
$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
body{margin:0;font-family:Arial;background:#f4f6f8;}

.pdv-container{display:flex;height:100vh;}
.pdv-left{width:65%;padding:20px;overflow:auto;}
.pdv-right{width:35%;background:#fff;padding:20px;border-left:2px solid #ddd;display:flex;flex-direction:column;}

.grupos-scroll{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:15px;}
.grupo-btn{
background:#1f3a5c;
color:white;
border:none;
padding:8px 12px;
border-radius:8px;
cursor:pointer;
}

.produtos-grid{
display:grid;
grid-template-columns:repeat(auto-fill,minmax(140px,1fr));
gap:10px;
}

.produto-card{
background:white;
padding:10px;
border-radius:8px;
box-shadow:0 3px 8px rgba(0,0,0,0.1);
text-align:center;
cursor:pointer;
}

.carrinho{flex:1;overflow:auto;margin-bottom:10px;}
.total-box{font-weight:bold;margin-bottom:10px;}

.btn-finalizar{
background:#27ae60;
color:white;
border:none;
padding:10px;
border-radius:8px;
cursor:pointer;
}

#modalProduto{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
justify-content:center;
align-items:center;
z-index:9999;
}

.modal-box{
background:white;
padding:20px;
border-radius:10px;
width:90%;
max-width:400px;
}
</style>

<div class="pdv-container">

<div class="pdv-left">

<h3>Mesa <?php echo $mesa_id; ?></h3>

<div class="grupos-scroll">
<?php foreach($grupos as $grupo): ?>
<button class="grupo-btn"
        onclick="carregarProdutos(<?php echo $grupo['id']; ?>)">
    <?php echo $grupo['nome']; ?>
</button>
<?php endforeach; ?>
</div>

<div id="areaProdutosPDV">
Selecione uma categoria
</div>

</div>

<div class="pdv-right">

<div class="carrinho" id="carrinhoPDV">
Nenhum item ainda.
</div>

<div class="total-box">
Total: R$ <span id="totalPDV">0,00</span>
</div>

<button class="btn-finalizar" onclick="finalizarPedido()">
Finalizar Pedido
</button>

</div>

</div>

<!-- MODAL -->
<div id="modalProduto">
<div class="modal-box">

<h3 id="nomeProdutoModal"></h3>

<label>Quantidade:</label>
<input type="number" id="quantidadeProduto" value="1" min="1"
style="width:100%;padding:8px;margin-bottom:10px;">

<label>Observação:</label>
<textarea id="obsProduto"
style="width:100%;padding:8px;margin-bottom:15px;"></textarea>

<button id="btnConfirmar"
style="background:#27ae60;color:white;border:none;padding:10px;width:100%;border-radius:6px;margin-bottom:8px;">
Adicionar
</button>

<button onclick="fecharModalProduto()"
style="background:#e74c3c;color:white;border:none;padding:10px;width:100%;border-radius:6px;">
Cancelar
</button>

</div>
</div>

<script>

let pedidoAtual = null;
let produtoSelecionado = null;

function obterPedido(){
return fetch("api/verificar_ou_criar_pedido.php?mesa_id=<?php echo $mesa_id; ?>")
.then(res => res.json())
.then(data => {
if(data.success){
pedidoAtual = data.pedido_id;
return pedidoAtual;
}
});
}

function carregarProdutos(grupo_id){
fetch("produtos_modal.php?grupo_id=" + grupo_id + "&mesa_id=<?php echo $mesa_id; ?>")
.then(res => res.text())
.then(html => {
document.getElementById("areaProdutosPDV").innerHTML = html;

ativarEventosProdutos(); // IMPORTANTE
});
}

function ativarEventosProdutos(){

document.querySelectorAll(".produto-card").forEach(card => {

card.addEventListener("click", function(){

produtoSelecionado = this.dataset.id;
document.getElementById("nomeProdutoModal").innerText = this.dataset.nome;
document.getElementById("quantidadeProduto").value = 1;
document.getElementById("obsProduto").value = "";
document.getElementById("modalProduto").style.display = "flex";

});

});

}

function fecharModalProduto(){
document.getElementById("modalProduto").style.display = "none";
}

document.getElementById("btnConfirmar").addEventListener("click", function(){

let qtd = document.getElementById("quantidadeProduto").value;
let obs = document.getElementById("obsProduto").value;

fetch("api/adicionar_item.php",{
method:"POST",
headers:{
"Content-Type":"application/x-www-form-urlencoded"
},
body:new URLSearchParams({
mesa_id: <?php echo $mesa_id; ?>,
produto_id: produtoSelecionado,
quantidade: qtd,
observacao: obs
})
})
.then(res=>res.json())
.then(data=>{
if(data.success){
fecharModalProduto();
carregarCarrinho();
}else{
alert("Erro ao adicionar produto");
}
});

});

function carregarCarrinho(){

obterPedido().then(function(pedido_id){

if(!pedido_id) return;

fetch("api/listar_itens_carrinho.php?pedido_id=" + pedido_id)
.then(res=>res.json())
.then(itens=>{

let html = "";

if(!itens || itens.length === 0){
html = "Nenhum item no pedido.";
}else{
itens.forEach(item=>{
html += `
<div style="margin-bottom:8px;">
<strong>${item.nome}</strong><br>
Qtd: ${item.quantidade}<br>
Obs: ${item.observacao || ""}<br>
R$ ${item.total}
<hr>
</div>
`;
});
}

document.getElementById("carrinhoPDV").innerHTML = html;

});

fetch("api/carrinho_status.php?pedido_id=" + pedido_id)
.then(res=>res.json())
.then(data=>{
if(data.success){
document.getElementById("totalPDV").innerText = data.total;
}
});

});
}

function finalizarPedido(){
if(!pedidoAtual){
alert("Nenhum pedido aberto.");
return;
}
window.location = "fechar_mesa.php?pedido_id=" + pedidoAtual;
}

setInterval(carregarCarrinho,2000);
carregarCarrinho();

</script>