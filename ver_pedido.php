<?php

session_start();

$nivel = $_SESSION['usuario_nivel'];

if(!isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit;
}

require "config/conexao.php";

$mesa_id = $_GET['mesa_id'];

// buscar pedido aberto

$sql = "
SELECT id
FROM pedidos
WHERE mesa_id=:mesa_id
AND status='aberto'
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":mesa_id",$mesa_id);
$stmt->execute();

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

$pedido_id = $pedido['id'];

// buscar mesa

$sql = "SELECT numero FROM mesas WHERE id=:mesa_id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":mesa_id",$mesa_id);
$stmt->execute();

$mesa = $stmt->fetch(PDO::FETCH_ASSOC);

// buscar itens

$sql = "

SELECT 
produtos.nome,
produtos.imagem,
pedido_itens.quantidade,
pedido_itens.preco,
(pedido_itens.quantidade * pedido_itens.preco) as total,
pedido_itens.status


FROM pedido_itens

JOIN produtos ON produtos.id = pedido_itens.produto_id

WHERE pedido_itens.pedido_id=:pedido_id

ORDER BY pedido_itens.id DESC

";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(":pedido_id",$pedido_id);
$stmt->execute();

$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pedidos da Mesa</title>

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

}

.container{

    padding:15px;

}

.card{

    background:white;
    padding:10px;
    border-radius:8px;
    margin-bottom:10px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    box-shadow:0 2px 6px rgba(0,0,0,0.1);

}

.card-left{

    display:flex;
    align-items:center;
    gap:10px;

}

.card img{

    width:60px;
    height:60px;
    border-radius:8px;
    object-fit:cover;
    border:1px solid #ddd;

}

.card-info{

    display:flex;
    flex-direction:column;

}


.total{

    background:#27ae60;
    color:white;
    padding:15px;
    border-radius:5px;
    font-size:18px;

}

.status{

    font-size:12px;
    color:#7f8c8d;

}

.btn{

    background:#34495e;
    color:white;
    padding:12px;
    border:none;
    width:100%;
    margin-top:10px;
    border-radius:5px;

}

.btn-fechar{

    background:#e74c3c;

}

</style>

</head>

<body>

<div class="topbar">

Mesa <?php echo $mesa['numero']; ?> - Pedidos

</div>

<div class="container">

<?php foreach($itens as $item):

$total += $item['total'];

?>

<div class="card">

<div class="card-left">

<img src="uploads/<?php echo $item['imagem'] ?: 'sem_imagem.png'; ?>">

<div class="card-info">

<strong>
<?php echo $item['nome']; ?>
</strong>

<span>
Qtd: <?php echo $item['quantidade']; ?>
</span>

<div class="status">
<?php echo $item['status']; ?>
</div>

</div>

</div>

<div>

<strong>
R$ <?php echo number_format($item['total'],2,',','.'); ?>
</strong>

</div>

</div>


<?php endforeach; ?>

<div class="total">

TOTAL: R$ <?php echo number_format($total,2,',','.'); ?>

</div>

<button class="btn" onclick="adicionarPedido()">
Adicionar Pedido
</button>

<?php if($nivel == "admin"): ?>

<button class="btn btn-fechar" onclick="fecharMesa()">
Fechar Mesa
</button>

<?php endif; ?>

<button class="btn" onclick="window.location='dashboard.php'">
Voltar
</button>



</div>


<script>

function adicionarPedido(){
    fetch("api/buscar_pedido.php?mesa_id=<?php echo $mesa_id; ?>")
    .then(res=>res.json())
    .then(data=>{
        window.location="pedido.php?id="+data.pedido_id;
    });
}

function fecharMesa(){

    let forma = prompt("Forma de pagamento:\n\npix\ndinheiro\ncredito\ndebito");

    if(!forma) return;

    fetch("api/fechar_mesa.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "pedido_id=<?php echo $pedido_id; ?>&forma_pagamento=" + forma
    })
    .then(res => res.json())
    .then(data => {

        if(data.success){
            alert("Mesa fechada com sucesso!\nTotal: R$ " + parseFloat(data.total).toFixed(2));
            window.location = "dashboard.php";
        }else{
            alert("Erro ao fechar mesa.");
        }

    });

}

</script>

</body>
</html>
