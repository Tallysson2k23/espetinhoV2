<?php

require "../config/conexao.php";

$pedido_id = $_POST['pedido_id'];

// 1️⃣ Atualiza status
$sql = "
UPDATE pedido_itens
SET status='enviado'
WHERE pedido_id=:pedido_id
AND status='carrinho'
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":pedido_id" => $pedido_id
]);

// 2️⃣ Buscar dados do pedido
$sql = "
SELECT 
    pi.quantidade,
    pi.observacao,
    p.nome as produto,
    g.nome as grupo,
    pe.mesa_id,
    u.nome as garcom
FROM pedido_itens pi
JOIN produtos p ON p.id = pi.produto_id
JOIN grupos g ON g.id = p.grupo_id
JOIN pedidos pe ON pe.id = pi.pedido_id
JOIN usuarios u ON u.id = pe.usuario_id
WHERE pi.pedido_id = :pedido_id
AND pi.status = 'enviado'
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":pedido_id" => $pedido_id
]);

$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "itens" => $itens
]);