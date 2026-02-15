<?php

session_start();

require "config/conexao.php";

$pedido_id = $_GET['pedido_id'];

$sql = "

SELECT 
pedido_itens.id,
produtos.nome,
pedido_itens.quantidade,
pedido_itens.preco,
(pedido_itens.quantidade * pedido_itens.preco) as total

FROM pedido_itens

JOIN produtos ON produtos.id = pedido_itens.produto_id

WHERE pedido_itens.pedido_id = :pedido_id
AND pedido_itens.status = 'carrinho'

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

<title>Carrinho</title>

<style>

body{

    font-family:Arial;
    background:#ecf0f1;

}

.item{

    background:white;
    padding:15px;
    margin:5px;

}

.total{

    background:#27ae60;
    color:white;
    padding:20px;
    font-size:20px;

}

.botao{

    background:#e74c3c;
    color:white;
    padding:15px;
    text-align:center;
    margin-top:10px;
    cursor:pointer;

}

</style>

</head>

<body>

<h2>Carrinho</h2>

<?php foreach($itens as $item): 

$total += $item['total'];

?>

<div class="item">

<?php echo $item['nome']; ?><br>

Qtd: <?php echo $item['quantidade']; ?><br>

R$ <?php echo $item['total']; ?>

</div>

<?php endforeach; ?>

<div class="total">

Total: R$ <?php echo $total; ?>

</div>

<div class="botao" onclick="voltar()">

Voltar ao pedido

</div>
<div class="botao" onclick="enviarPedido()">

Enviar Pedido

</div>

<script>

function enviarPedido(){

    if(confirm("Enviar pedido para cozinha?")){

        let form = new FormData();

        form.append("pedido_id", "<?php echo $pedido_id; ?>");

        fetch("api/enviar_pedido.php",{

            method:"POST",
            body:form

        })
        .then(res=>res.json())
        .then(data=>{

            alert("Pedido enviado com sucesso");

            window.location="pedido.php?id=<?php echo $pedido_id; ?>";

        });

    }

}

</script>


</body>

<script>

function voltar(){

window.history.back();

}

</script>

</html>
