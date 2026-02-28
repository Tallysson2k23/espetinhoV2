<?php

require "../config/conexao.php";

$pedido_id = $_POST['pedido_id'] ?? null;
$forma_pagamento = $_POST['forma_pagamento'] ?? null;

if (!$pedido_id || !$forma_pagamento) {
    echo json_encode([
        "success" => false,
        "message" => "Dados inválidos."
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    // 1️⃣ Calcular total do pedido
    $sqlTotal = "
        SELECT COALESCE(SUM(quantidade * preco),0) as total
        FROM pedido_itens
        WHERE pedido_id = :pedido_id
    ";

    $stmt = $pdo->prepare($sqlTotal);
    $stmt->bindParam(":pedido_id", $pedido_id);
    $stmt->execute();

    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2️⃣ Atualizar pedido
    $sqlUpdatePedido = "
        UPDATE pedidos
        SET 
            total = :total,
            forma_pagamento = :forma_pagamento,
            status = 'fechado',
            fechado_em = NOW()
        WHERE id = :pedido_id
    ";

    $stmt = $pdo->prepare($sqlUpdatePedido);
    $stmt->bindParam(":total", $total);
    $stmt->bindParam(":forma_pagamento", $forma_pagamento);
    $stmt->bindParam(":pedido_id", $pedido_id);
    $stmt->execute();

    // 3️⃣ Liberar mesa
    $sqlLiberarMesa = "
        UPDATE mesas
        SET status = 'livre'
        WHERE id = (
            SELECT mesa_id FROM pedidos WHERE id = :pedido_id
        )
    ";

    $stmt = $pdo->prepare($sqlLiberarMesa);
    $stmt->bindParam(":pedido_id", $pedido_id);
    $stmt->execute();

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "total" => $total
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        "success" => false,
        "message" => "Erro ao fechar pedido."
    ]);
}