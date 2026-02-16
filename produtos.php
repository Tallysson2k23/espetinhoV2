<?php

session_start();

require "config/conexao.php";


$pedido_id = intval($_GET['pedido_id']);
$grupo_id = intval($_GET['grupo_id']);
$mesa_id   = intval($_GET['mesa_id']);

// ✅ verificar se pedido ainda existe e está aberto

$sql = "
SELECT id
FROM pedidos
WHERE id=:pedido_id
AND status='aberto'
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":pedido_id"=>$pedido_id
]);

if(!$stmt->fetch()){

    // recriar pedido automaticamente

    $sql = "
    INSERT INTO pedidos
    (mesa_id, usuario_id, status, data_abertura)
    VALUES
    (:mesa_id, :usuario_id, 'aberto', NOW())
    RETURNING id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":mesa_id"=>$mesa_id,
        ":usuario_id"=>$_SESSION['usuario_id']
    ]);

    $pedido_id = $stmt->fetchColumn();

}


$sql = "SELECT * FROM produtos 
        WHERE grupo_id = :grupo_id
        AND ativo=TRUE
        ORDER BY nome";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":grupo_id",$grupo_id);

$stmt->execute();

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
    padding-bottom:80px;
}

/* TOPBAR */

.topo{
    background:#2c3e50;
    color:white;
    padding:15px;
    text-align:center;
    font-size:18px;
}

/* PRODUTO */

.produto{
    background:white;
    padding:18px;
    border-bottom:1px solid #ddd;
    cursor:pointer;
}

.produto:hover{
    background:#f8f8f8;
}

/* CARRINHO BAR */

.carrinho-bar{
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    background:#27ae60;
    color:white;
    padding:15px;
    display:flex;
    justify-content:space-between;
    cursor:pointer;
}

/* MODAL */

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
    width:250px;
    text-align:center;
}

.qtd-controls{
    display:flex;
    justify-content:center;
    align-items:center;
    margin:15px 0;
}

.qtd-btn{
    padding:10px;
    font-size:18px;
    margin:5px;
    cursor:pointer;
}

.add-btn{
    background:#27ae60;
    color:white;
    border:none;
    padding:12px;
    width:100%;
    border-radius:5px;
}

.cancel-btn{
    background:#7f8c8d;
    color:white;
    border:none;
    padding:12px;
    width:100%;
    border-radius:5px;
    margin-top:5px;
}

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
    font-size:16px;

}

.btn-voltar:hover{

    background:#3d566e;

}

.titulo{

    flex:1;
    text-align:center;
    margin-right:40px;

}



</style>

</head>

<body>

<div class="topo">

<button class="btn-voltar" onclick="voltarPedido()">←</button>

<span class="titulo">
Selecionar Produto
</span>

</div>


<div>

<?php foreach($produtos as $produto): ?>

<div class="produto"
onclick="abrirModal(
<?php echo $produto['id']; ?>,
'<?php echo addslashes($produto['nome']); ?>'
)">

<?php echo $produto['nome']; ?>

<br>

R$ <?php echo number_format($produto['preco'],2,',','.'); ?>

</div>

<?php endforeach; ?>

</div>

<div class="carrinho-bar"
onclick="window.location='carrinho.php?pedido_id=<?php echo $pedido_id; ?>&mesa_id=<?php echo $mesa_id; ?>'"
>

🛒 Ver Carrinho

</div>

<!-- MODAL -->

<div class="modal-bg" id="modal">

<div class="modal">

<div id="produtoNome"></div>

<div class="qtd-controls">

<button class="qtd-btn" onclick="menos()">−</button>

<span id="qtd">1</span>

<button class="qtd-btn" onclick="mais()">+</button>

</div>

<button class="add-btn" onclick="confirmar()">

Adicionar

</button>

<button class="cancel-btn" onclick="fecharModal()">

Cancelar

</button>

</div>

</div>

<script>

let produtoSelecionado = null;
let quantidade = 1;

function abrirModal(id,nome){

produtoSelecionado=id;
quantidade=1;

document.getElementById("qtd").innerText=quantidade;
document.getElementById("produtoNome").innerText=nome;

document.getElementById("modal").style.display="flex";

}

function fecharModal(){

document.getElementById("modal").style.display="none";

}

function mais(){

quantidade++;
document.getElementById("qtd").innerText=quantidade;

}

function menos(){

if(quantidade>1){

quantidade--;
document.getElementById("qtd").innerText=quantidade;

}

}

function confirmar(){

let form=new FormData();

form.append("pedido_id","<?php echo $pedido_id; ?>");
form.append("produto_id",produtoSelecionado);
form.append("quantidade",quantidade);

fetch("api/adicionar_item.php",{



method:"POST",
body:form

})
.then(res=>res.json())
.then(data=>{

    if(data.success){

        fecharModal();

    }else{

        // se pedido não existir mais, criar novo automaticamente

        if(data.erro.includes("Pedido não existe")){

            recriarPedido();

        }else{

            alert(data.erro);

        }

    }

});

}

function recriarPedido(){

let form = new FormData();

form.append("mesa_id","<?php echo $mesa_id; ?>");

fetch("api/abrir_pedido.php",{



method:"POST",
body:form

})
.then(res=>res.json())
.then(data=>{

    if(data.success){

        // redireciona para novo pedido
       window.location="pedido.php?id="+data.pedido_id;


    }else{

        alert("Erro ao recriar pedido");

    }

});

}


function voltarPedido(){

window.location="pedido.php?id=<?php echo $pedido_id; ?>&mesa_id=<?php echo $mesa_id; ?>";

}


</script>

</body>
</html>
