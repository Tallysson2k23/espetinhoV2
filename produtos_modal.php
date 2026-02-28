<?php
require "config/conexao.php";

$grupo_id = intval($_GET['grupo_id'] ?? 0);
$mesa_id  = intval($_GET['mesa_id'] ?? 0);

if($grupo_id <= 0){
    echo "Grupo inválido";
    exit;
}

$sql = "
SELECT id, nome, preco, imagem
FROM produtos
WHERE grupo_id = :grupo_id
AND ativo = TRUE
ORDER BY nome
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":grupo_id"=>$grupo_id]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="produtos-grid">

<?php foreach($produtos as $produto): ?>

<div class="produto-card"
data-id="<?php echo $produto['id']; ?>"
     data-nome="<?php echo addslashes($produto['nome']); ?>">
    <img class="produto-img"
         src="uploads/<?php echo $produto['imagem'] ?: 'sem_imagem.png'; ?>">

    <div><strong><?php echo $produto['nome']; ?></strong></div>
    <div>R$ <?php echo number_format($produto['preco'],2,',','.'); ?></div>

</div>

<?php endforeach; ?>

</div>