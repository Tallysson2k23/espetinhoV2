<?php

session_start();

require "config/conexao.php";

$pedido_id = $_GET['pedido_id'];
$grupo_id = $_GET['grupo_id'];

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

}

.topo{

    background:#e74c3c;
    color:white;
    padding:15px;
    text-align:center;

}

.produto{

    background:white;
    padding:20px;
    border-bottom:1px solid #ddd;
    cursor:pointer;

}

</style>

</head>

<body>

<div class="topo">

Selecionar Produto

</div>

<div>

<?php foreach($produtos as $produto): ?>

<div class="produto" onclick="adicionar(<?php echo $produto['id']; ?>)">

<?php echo $produto['nome']; ?>  
R$ <?php echo $produto['preco']; ?>

</div>

<?php endforeach; ?>

</div>

<script>

function adicionar(produto_id){

    let quantidade = prompt("Quantidade:", "1");

    if(!quantidade) return;

    let form = new FormData();

    form.append("pedido_id", "<?php echo $pedido_id; ?>");
    form.append("produto_id", produto_id);
    form.append("quantidade", quantidade);

    fetch("api/adicionar_item.php",{

        method:"POST",
        body:form

    })
    .then(res=>res.json())
    .then(data=>{

        alert("Adicionado ao carrinho");

    });

}

</script>

</body>
</html>
