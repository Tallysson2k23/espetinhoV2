<?php

session_start();
header('Content-Type: application/json');

require "../config/conexao.php";

$id = intval($_POST['id']);

$sql = "
DELETE FROM pedido_itens
WHERE id=:id
AND status='carrinho'
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
":id"=>$id
]);

echo json_encode([
"success"=>true
]);
