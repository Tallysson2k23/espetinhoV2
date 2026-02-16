<?php

require "../config/conexao.php";

$pedido_id = intval($_GET['pedido_id']);

$sql = "

SELECT
produtos.nome,
pedido_itens.quantidade,
pedido_itens.preco,
(pedido_itens.quantidade * pedido_itens.preco) as total

FROM pedido_itens

JOIN produtos ON produtos.id = pedido_itens.produto_id

WHERE pedido_itens.pedido_id=:pedido_id
AND pedido_itens.status='carrinho'

ORDER BY pedido_itens.id DESC

";

$stmt = $pdo->prepare($sql);

$stmt->execute([
":pedido_id"=>$pedido_id
]);

$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($itens as &$item){

$item['total'] = number_format($item['total'],2,',','.');

}

echo json_encode($itens);
