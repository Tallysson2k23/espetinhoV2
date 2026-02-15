<?php
session_start();

if(isset($_SESSION['usuario_id'])){
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
 
<meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Espetinho Central - Login</title>

<style>

body{
    font-family: Arial;
    background-color:#2c3e50;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.login-box{
    background:white;
    padding:30px;
    border-radius:10px;
    width:300px;
}

input{
    width:100%;
    padding:10px;
    margin-top:10px;
}

button{
    width:100%;
    padding:10px;
    margin-top:15px;
    background:#27ae60;
    color:white;
    border:none;
    cursor:pointer;
}

button:hover{
    background:#219150;
}

.erro{
    color:red;
    margin-top:10px;
}

</style>

</head>

<body>

<div class="login-box">

<h2>Espetinho Central</h2>

<form id="formLogin">

<input type="text" name="usuario" placeholder="Usuário" required>

<input type="password" name="senha" placeholder="Senha" required>

<button type="submit">Entrar</button>

<div class="erro" id="erro"></div>

</form>

</div>

<script>

document.getElementById("formLogin").addEventListener("submit", function(e){

    e.preventDefault();

    let form = new FormData(this);

    fetch("api/login.php",{
        method:"POST",
        body:form
    })
    .then(res=>res.json())
    .then(data=>{

        if(data.success){
            window.location="dashboard.php";
        }else{
            document.getElementById("erro").innerText=data.message;
        }

    });

});

</script>

</body>
</html>
