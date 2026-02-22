<?php

require "../config/conexao.php";

$sql = "
SELECT 
    pi.id,
    pi.pedido_id,
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
WHERE pi.status = 'enviado'
AND pi.impresso = FALSE
ORDER BY pi.id ASC
LIMIT 50
";

$stmt = $pdo->query($sql);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "itens" => $itens
]);