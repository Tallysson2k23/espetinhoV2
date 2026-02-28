<?php
require "config/conexao.php";



$grupo_id = intval($_GET['grupo_id'] ?? 0);
$mesa_id  = intval($_GET['mesa_id'] ?? 0);
$pedido_id = intval($_GET['pedido_id'] ?? 0);

if($grupo_id <= 0){
    echo "Grupo inválido";
    exit;
}

/* BUSCAR PRODUTOS DO GRUPO */

$sql = "
SELECT id, nome, preco, imagem
FROM produtos
WHERE grupo_id = :grupo_id
AND ativo = TRUE
ORDER BY nome
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":grupo_id"=>$grupo_id
]);

$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<style>

/* ================= DESKTOP ================= */
/* automático - cria quantas colunas couberem */

.produtos-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(160px, 1fr));
    gap:15px;
}

.produto-card{
    background:white;
    padding:10px;
    border-radius:10px;
    text-align:center;
    cursor:pointer;
    box-shadow:0 3px 8px rgba(0,0,0,0.1);
    transition:0.2s;
}

.produto-card:hover{
    transform:scale(1.03);
}

.produto-img{
    width:100%;
    height:110px;
    object-fit:cover;
    border-radius:8px;
    margin-bottom:6px;
}

/* ================= MOBILE ================= */
/* força exatamente 2 colunas */

@media(max-width:768px){

    .produtos-grid{
        grid-template-columns:repeat(2, 1fr);
        gap:10px;
    }

    .produto-card{
        padding:8px;
    }

    .produto-img{
        height:85px;
    }

    .produto-card strong{
        font-size:13px;
        display:block;
    }

    .produto-card div{
        font-size:12px;
    }

}

</style>



<div class="produtos-grid">

<?php foreach($produtos as $produto): ?>

    <div class="produto-card"
         onclick="adicionarProduto(<?php echo $produto['id']; ?>)">

        <img class="produto-img"
             src="uploads/<?php echo $produto['imagem'] ?: 'sem_imagem.png'; ?>">

        <div><strong><?php echo $produto['nome']; ?></strong></div>
        <div>R$ <?php echo number_format($produto['preco'],2,',','.'); ?></div>

    </div>

<?php endforeach; ?>

</div>

<script>

function voltarGrupos(){
    location.reload();
}

function adicionarProduto(produto_id){

    fetch("api/adicionar_item.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:new URLSearchParams({
            mesa_id:"<?php echo $mesa_id; ?>",
            produto_id:produto_id,
            quantidade:1
        })
    })
    .then(res=>res.json())
    .then(data=>{

        if(data.success){
            alert("Produto adicionado!");
        }else{
            alert("Erro ao adicionar");
        }

    });

}

</script>