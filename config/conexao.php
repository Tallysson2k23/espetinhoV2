<?php

$host = "localhost";
$port = "5432";
$dbname = "espetinho";
$user = "postgres";
$password = "159357";

try {

    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    echo "Erro na conexão: " . $e->getMessage();
    exit;

}

?>
