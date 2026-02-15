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

// buscar itens enviados

$sql = "

SELECT 
produtos.nome,
pedido_itens.quantidade,
pedido_itens.preco,
(pedido_itens.quantidade * pedido_itens.preco) as total

FROM pedido_itens

JOIN produtos ON produtos.id = pedido_itens.produto_id

WHERE pedido_itens.pedido_id = :pedido_id
AND pedido_itens.status='enviado'

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

    font-family:Arial;
    background:#ecf0f1;
    padding:20px;

}

.item{

    background:white;
    padding:10px;
    margin-top:5px;

}

.total{

    background:#27ae60;
    color:white;
    padding:20px;
    font-size:20px;
    margin-top:10px;

}

.botao{

    background:#e74c3c;
    color:white;
    padding:20px;
    text-align:center;
    margin-top:20px;
    cursor:pointer;

}

</style>

</head>

<body>

<h2>Fechar Mesa <?php echo $mesa['numero']; ?></h2>

<?php foreach($itens as $item): 

$total += $item['total'];

?>

<div class="item">

<?php echo $item['nome']; ?>

Qtd: <?php echo $item['quantidade']; ?>

R$ <?php echo $item['total']; ?>

</div>

<?php endforeach; ?>

<div class="total">

Total: R$ <?php echo $total; ?>

</div>

<div class="botao" onclick="fecharMesa()">

FECHAR MESA

</div>

<script>

function fecharMesa(){

    if(confirm("Confirmar fechamento da mesa?")){

        let form = new FormData();

        form.append("pedido_id","<?php echo $pedido_id; ?>");

        fetch("api/fechar_mesa.php",{

            method:"POST",
            body:form

        })
        .then(res=>res.json())
        .then(data=>{

            alert("Mesa fechada");

            window.location="dashboard.php";

        });

    }

}

</script>

</body>
</html>
