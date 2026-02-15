<?php

session_start();

if($_SESSION['usuario_nivel'] != 'admin'){
    echo "Acesso negado";
    exit;
}

require "../config/conexao.php";

// buscar grupos
$sql = "SELECT * FROM grupos ORDER BY nome";
$stmt = $pdo->query($sql);
$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// buscar produtos
$sql = "SELECT produtos.*, grupos.nome as grupo_nome 
        FROM produtos
        LEFT JOIN grupos ON grupos.id = produtos.grupo_id
        ORDER BY produtos.id DESC";

$stmt = $pdo->query($sql);
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
    padding:20px;
}

input, select, button{
    padding:10px;
    margin-top:10px;
    width:100%;
}

.produto{
    background:#ecf0f1;
    padding:10px;
    margin-top:5px;
}

</style>

</head>

<body>

<h2>Cadastrar Produto</h2>

<select id="grupo">

<option value="">Selecione grupo</option>

<?php foreach($grupos as $grupo): ?>

<option value="<?php echo $grupo['id']; ?>">
<?php echo $grupo['nome']; ?>
</option>

<?php endforeach; ?>

</select>

<input type="text" id="nome" placeholder="Nome do produto">

<input type="number" id="preco" placeholder="Preço" step="0.01">

<button onclick="salvar()">Salvar</button>

<h3>Lista de produtos:</h3>

<div>

<?php foreach($produtos as $produto): ?>

<div class="produto">

<b><?php echo $produto['nome']; ?></b><br>

Grupo: <?php echo $produto['grupo_nome']; ?><br>

Preço: R$ <?php echo $produto['preco']; ?>

</div>

<?php endforeach; ?>

</div>

<script>

function salvar(){

    let grupo = document.getElementById("grupo").value;
    let nome = document.getElementById("nome").value;
    let preco = document.getElementById("preco").value;

    let form = new FormData();

    form.append("grupo_id", grupo);
    form.append("nome", nome);
    form.append("preco", preco);

    fetch("../api/salvar_produto.php",{

        method:"POST",
        body:form

    })
    .then(res=>res.json())
    .then(data=>{

        location.reload();

    });

}

</script>

</body>
</html>
