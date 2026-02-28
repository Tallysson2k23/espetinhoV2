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

/* ================= DESKTOP BASE ================= */

.pdv-container{
    display:flex;
    height:100%;
}

.pdv-left{
    width:65%;
    padding:20px;
    overflow:auto;
}

.pdv-right{
    width:35%;
    background:#f4f6f8;
    padding:20px;
    border-left:2px solid #ddd;
    display:flex;
    flex-direction:column;
}

.carrinho{
    flex:1;
    overflow:auto;
    margin-bottom:20px;
}

.total-box{
    font-size:20px;
    font-weight:bold;
    margin-bottom:15px;
}

.btn-finalizar{
    background:#27ae60;
    color:white;
    border:none;
    padding:15px;
    font-size:16px;
    border-radius:8px;
    cursor:pointer;
}

/* ================= GRUPOS ================= */

.grupos-scroll{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

.grupo-btn{
    background:white;
    border:none;
    padding:10px 15px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
    box-shadow:0 3px 8px rgba(0,0,0,0.1);
}

.grupo-btn.active{
    background:#1f3a5c;
    color:white;
}

/* ================= PRODUTOS ================= */

.produtos-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
    gap:15px;
}

.produto-card{
    background:white;
    padding:10px;
    border-radius:10px;
    text-align:center;
    cursor:pointer;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.produto-img{
    width:100%;
    height:100px;
    object-fit:cover;
    border-radius:8px;
    margin-bottom:5px;
}

/* ================= MOBILE ================= */

@media(max-width:768px){

    .pdv-container{
        flex-direction:column;
    }

    .pdv-left{
        width:100%;
        padding:10px;
    }

    .pdv-right{
        display:none;
    }

    .grupos-scroll{
        display:grid;
        grid-template-columns:repeat(2,1fr);
        gap:8px;
    }

    .grupo-btn{
        font-size:13px;
        padding:8px;
    }

    .produtos-grid{
        grid-template-columns:repeat(2,1fr);
        gap:8px;
    }

    .produto-card{
        padding:6px;
    }

    .produto-img{
        height:75px;
    }

    .produto-card strong{
        font-size:12px;
    }

    #btnCarrinhoMobile{
        position:fixed;
        bottom:0;
        left:0;
        width:100%;
        background:#27ae60;
        color:white;
        text-align:center;
        padding:15px;
        font-weight:bold;
        z-index:6000;
    }

}

</style>

<div class="pdv-container">

    <div class="pdv-left">

        <div class="pdv-title">
            Mesa <?php echo $mesa_id; ?>
        </div>

        <div class="grupos-scroll">
            <?php foreach($grupos as $grupo): ?>
                <button class="grupo-btn"
                        onclick="window.abrirGrupoPDV(<?php echo $grupo['id']; ?>)">
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

        <button class="btn-finalizar"
                onclick="finalizarPedidoPDV()">
            Finalizar Pedido
        </button>

    </div>

</div>

<!-- BOTÃO MOBILE -->
<div id="btnCarrinhoMobile" onclick="abrirCarrinhoMobile()">
🛒 Ver Pedido (<span id="totalMobile">0,00</span>)
</div>

<script>

function abrirCarrinhoMobile(){
    document.querySelector(".pdv-right").style.display = "flex";
}

let pedidoAtual = null;

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

function carregarCarrinhoPDV(){

    fetch("api/listar_itens_carrinho.php?mesa_id=<?php echo $mesa_id; ?>")
    .then(res=>res.json())
    .then(itens=>{

        let html = "";

        if(!itens || itens.length === 0){
            html = "Nenhum item no pedido.";
        }else{
            itens.forEach(item=>{
                html += `
                    <div style="margin-bottom:10px;">
                        <strong>${item.nome}</strong><br>
                        Qtd: ${item.quantidade}<br>
                        R$ ${item.total}
                        <hr>
                    </div>
                `;
            });
        }

        document.getElementById("carrinhoPDV").innerHTML = html;

    });

    fetch("api/carrinho_status.php?mesa_id=<?php echo $mesa_id; ?>")
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            document.getElementById("totalPDV").innerText = data.total;
            document.getElementById("totalMobile").innerText = data.total;
        }
    });
}

function finalizarPedidoPDV(){
    if(!pedidoAtual){
        alert("Nenhum pedido aberto.");
        return;
    }
    window.location = "fechar_mesa.php?pedido_id=" + pedidoAtual;
}

setInterval(carregarCarrinhoPDV, 2000);
carregarCarrinhoPDV();

</script>