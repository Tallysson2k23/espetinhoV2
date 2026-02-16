<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit;
}

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Apenas admin pode fechar mesa";
    exit;
}

require "config/conexao.php";

$pedido_id = $_GET['pedido_id'];

// buscar itens

$sql = "

SELECT 
produtos.nome,
pedido_itens.quantidade,
pedido_itens.preco,
(pedido_itens.quantidade * pedido_itens.preco) as total

FROM pedido_itens
JOIN produtos ON produtos.id = pedido_itens.produto_id
WHERE pedido_itens.pedido_id = :pedido_id


";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":pedido_id",$pedido_id);
$stmt->execute();

$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;

// buscar mesa

$sql = "

SELECT mesas.numero
FROM pedidos
JOIN mesas ON mesas.id = pedidos.mesa_id
WHERE pedidos.id = :pedido_id

";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":pedido_id",$pedido_id);
$stmt->execute();

$mesa = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Fechar Mesa</title>

<style>

body{
    margin:0;
    font-family:Arial;
    background:#ecf0f1;
}

/* TOPBAR */

.topbar{

    height:60px;
    background:#2c3e50;
    color:white;
    display:flex;
    align-items:center;
    padding-left:15px;
    font-size:18px;
    font-weight:bold;

}

/* CONTAINER */

.container{

    padding:20px;

}

/* CARD */

.card{

    background:white;
    padding:15px;
    border-radius:8px;
    margin-bottom:10px;
    display:flex;
    justify-content:space-between;

}

/* TOTAL */

.total{

    background:#27ae60;
    color:white;
    padding:20px;
    border-radius:8px;
    font-size:22px;
    text-align:center;
    margin-top:15px;

}

/* BUTTONS */

.btn{

    padding:15px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:16px;
    width:100%;
    margin-top:10px;

}

.btn-fechar{

    background:#e74c3c;
    color:white;

}

.btn-voltar{

    background:#7f8c8d;
    color:white;

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

.modal button{

    margin-top:10px;
    width:100%;

}

</style>

</head>

<body>

<div class="topbar">
Fechar Mesa <?php echo $mesa['numero']; ?>
</div>

<div class="container">

<?php foreach($itens as $item):

$total += $item['total'];

?>

<div class="card">

<div>
<?php echo $item['nome']; ?><br>
Qtd: <?php echo $item['quantidade']; ?>
</div>

<div>
R$ <?php echo number_format($item['total'],2,',','.'); ?>
</div>

</div>

<?php endforeach; ?>

<div class="total">

TOTAL: R$ <?php echo number_format($total,2,',','.'); ?>

</div>

<button class="btn btn-fechar" onclick="abrirModal()">
Fechar Mesa
</button>

<button class="btn btn-voltar" onclick="window.location='dashboard.php'">
Voltar
</button>

</div>

<div class="modal-bg" id="modal">

<div class="modal">

<h3>Confirmar fechamento?</h3>

<button class="btn btn-fechar" onclick="confirmarFechamento()">
Confirmar
</button>

<button class="btn btn-voltar" onclick="fecharModal()">
Cancelar
</button>

</div>

</div>

<script>

function abrirModal(){

document.getElementById("modal").style.display="flex";

}

function fecharModal(){

document.getElementById("modal").style.display="none";

}

function confirmarFechamento(){

let form = new FormData();

form.append("pedido_id","<?php echo $pedido_id; ?>");

fetch("api/fechar_mesa.php",{

method:"POST",
body:form

})
.then(res=>res.json())
.then(()=>{

window.location="dashboard.php";

});

}

</script>

</body>
</html>
