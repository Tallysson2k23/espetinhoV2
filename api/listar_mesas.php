<?php

require "../config/conexao.php";

$sql = "
SELECT 
    m.id,
    m.numero,
    m.status,
    p.data_abertura
FROM mesas m
LEFT JOIN pedidos p 
    ON p.mesa_id = m.id 
    AND p.status = 'aberto'
ORDER BY m.numero
";

$stmt = $pdo->query($sql);

$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($mesas);