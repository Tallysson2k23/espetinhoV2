<?php
session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location: index.php");
    exit;
}

require "config/conexao.php";

$sql = "SELECT * FROM mesas ORDER BY numero";
$stmt = $pdo->query($sql);
$mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Painel de Mesas</title>

<style>

body{
    font-family:Arial;
    background:#ecf0f1;
}

.topo{
    background:#2c3e50;
    color:white;
    padding:15px;
}

.mesas{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap:10px;
    padding:10px;
}

.mesa{

    padding:20px;
    text-align:center;
    border-radius:8px;
    color:white;
    font-weight:bold;
    cursor:pointer;

}

.livre{
    background:#27ae60;
}

.ocupada{
    background:#e74c3c;
}

</style>

</head>

<body>

<div class="topo">

Usuário: <?php echo $_SESSION['usuario_nome']; ?>

</div>

<div class="mesas">

<?php foreach($mesas as $mesa): ?>

<div class="mesa <?php echo $mesa['status']; ?>">

Mesa <?php echo $mesa['numero']; ?>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
