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

$nivel = $_SESSION['usuario_nivel'];
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Painel de Mesas</title>


<style>

body{
    margin:0;
    font-family:Arial;
    background:#ecf0f1;
}

/* BOTÃO MENU */

.menuBtn{

    position:fixed;
    top:10px;
    left:10px;
    font-size:22px;
    background:#2c3e50;
    color:white;
    border:none;
    padding:8px 12px;
    cursor:pointer;
    border-radius:5px;
    z-index:1200;

}

/* MENU LATERAL */

.sidebar{

    width:220px;
    height:100vh;
    background:#2c3e50;
    color:white;
    position:fixed;
    left:-220px;
    top:0;
    transition:0.3s;
    z-index:1300;

}

.sidebar.active{

    left:0;

}

.sidebar h2{

    padding:15px;
    margin:0;
    background:#1a252f;

}

.usuarioBox{

    padding:15px;
    background:#22313f;
    border-bottom:1px solid rgba(255,255,255,0.2);

}

.usuarioNome{

    font-weight:bold;

}

.usuarioNivel{

    font-size:12px;
    opacity:0.7;

}

.sidebar a{

    display:block;
    padding:15px;
    color:white;
    text-decoration:none;
    border-bottom:1px solid rgba(255,255,255,0.1);

}

.sidebar a:hover{

    background:#34495e;

}

/* AREA PRINCIPAL */

.main{
    padding:70px 15px 15px 15px;
}


/* MESAS */

.mesas{

    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap:10px;

}

.mesa{

    padding:20px;
    border-radius:8px;
    color:white;
    font-weight:bold;
    cursor:pointer;
    text-align:center;

}

.livre{

    background:#27ae60;

}

.ocupada{

    background:#e74c3c;

}

/* OVERLAY */

.overlay{

    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.3);
    display:none;
    z-index:1250;

}

.overlay.active{

    display:block;

}

/* BARRA SUPERIOR */

.topbar{

    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:60px;
    background:#2c3e50;
    color:white;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:18px;
    font-weight:bold;

    z-index:1100;

}

.topbar span{

    pointer-events:none;

}

.topbar{

    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:60px;
    background:#2c3e50;
    color:white;

    display:flex;
    align-items:center;

    font-size:18px;
    font-weight:bold;

    z-index:1100;

}

.modal-bg{

    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);

    display:none;
    justify-content:center;
    align-items:center;

    z-index:2000;

}

.modal{

    background:white;
    padding:20px;
    border-radius:8px;
    width:250px;

}

.modal-title{

    font-size:20px;
    margin-bottom:15px;
    text-align:center;

}

.modal-btn{

    width:100%;
    padding:10px;
    margin-top:10px;
    border:none;
    border-radius:5px;
    cursor:pointer;

}

.abrir{

    background:#27ae60;
    color:white;

}

.fechar{

    background:#e74c3c;
    color:white;

}

.cancelar{

    background:#7f8c8d;
    color:white;

}

.ver{
    background:#3498db;
    color:white;
}

.topo{
    text-align: center;
    margin-top: 20px;
}

.btn-sair{
    background: #e74c3c;
    color: white;
    padding: 2px 10px;
    text-decoration: none;
    border-radius: 6px;
}



</style>

</head>

<body>


<div class="topbar">

    <div style="position:absolute; left:60px;">
        Espetinho Central
    </div>

    <div class="topbar-right" id="contadorMesas" style="position:absolute; right:15px;">
    </div>

</div>



<button class="menuBtn" onclick="toggleMenu(event)">☰</button>

<div class="overlay" id="overlay" onclick="fecharMenu()"></div>

<div class="sidebar" id="sidebar">

<h2>Espetinho</h2>

<div class="usuarioBox">

<div class="usuarioNome">
<?php echo $_SESSION['usuario_nome']; ?>
</div>

<div class="usuarioNivel">
<?php echo ucfirst($_SESSION['usuario_nivel']); ?>
</div>

</div>

<a href="dashboard.php">Mesas</a>

<?php if($nivel == "admin"): ?>
<a href="admin/index.php">Admin</a>
<?php endif; ?>

<?php if($nivel == "admin"): ?>

<a href="admin/usuarios.php">Usuários</a>

<?php endif; ?>



<a href="logout.php">Sair</a>

</div>

<div class="main">

<div class="mesas" id="areaMesas">

<?php foreach($mesas as $mesa): ?>

<div class="mesa <?php echo $mesa['status']; ?>" onclick="clicarMesa(<?php echo $mesa['id']; ?>)">

Mesa <?php echo $mesa['numero']; ?>

</div>

<?php endforeach; ?>

</div>

</div>

<script>

function toggleMenu(event){

    event.stopPropagation();

    document.getElementById("sidebar").classList.add("active");
    document.getElementById("overlay").classList.add("active");

}

function fecharMenu(){

    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("active");

}

let mesaSelecionada = null;

function clicarMesa(id){

    mesaSelecionada = id;

    fetch("api/listar_mesas.php")
    .then(res=>res.json())
    .then(mesas=>{

        let mesa = mesas.find(m=>m.id == id);

        if(!mesa){

            alert("Mesa não encontrada");
            return;

        }

        document.getElementById("modalTitulo").innerText =
            "Mesa " + mesa.numero;

        let nivel = "<?php echo $_SESSION['usuario_nivel']; ?>";

        // botão ver pedidos
        if(mesa.status == "ocupada"){

            document.getElementById("btnVerPedidos").style.display="block";

        }else{

            document.getElementById("btnVerPedidos").style.display="none";

        }

        // botão fechar mesa
        if(mesa.status=="ocupada" && nivel=="admin"){

            document.getElementById("btnFecharMesa").style.display="block";

        }else{

            document.getElementById("btnFecharMesa").style.display="none";

        }

        document.getElementById("modalBg").style.display="flex";

    });

}


function fecharModal(){

    document.getElementById("modalBg").style.display = "none";

}

function abrirPedidoModal(){

    if(!mesaSelecionada){

        alert("Mesa não selecionada");
        return;

    }

    let form = new FormData();

    form.append("mesa_id", String(mesaSelecionada));

    fetch("api/abrir_pedido.php",{

        method:"POST",
        body:form

    })
    .then(res=>res.json())
    .then(data=>{

        console.log(data);

        if(data.success){

window.location="pedido.php?id="+data.pedido_id+"&mesa_id="+mesaSelecionada;

        }else{

            alert(data.erro);

        }

    })
    .catch(error=>{

        console.log(error);
        alert("Erro de conexão");

    });

}


function fecharMesaModal(){

    fetch("api/buscar_pedido.php?mesa_id="+mesaSelecionada)
    .then(res=>res.json())
    .then(data=>{

        window.location="fechar_mesa.php?pedido_id="+data.pedido_id;

    });

}


function buscarPedido(mesa_id){

    fetch("api/buscar_pedido.php?mesa_id="+mesa_id)
    .then(res=>res.json())
    .then(data=>{

        if(data.success){

            window.location="fechar_mesa.php?pedido_id="+data.pedido_id;

        }

    });

}

function abrirMesa(mesa_id){

    let form = new FormData();
    form.append("mesa_id", mesa_id);

    fetch("api/abrir_pedido.php",{

        method:"POST",
        body:form

    })
    .then(res=>res.json())
    .then(data=>{

        if(data.success){

            window.location="pedido.php?id="+data.pedido_id;

        }

    });

}

function carregarMesas(){

    fetch("api/listar_mesas.php")
    .then(res=>res.json())
    .then(mesas=>{

        let html="";

        mesas.forEach(mesa=>{

            html+=`
            <div class="mesa ${mesa.status}" onclick="clicarMesa(${mesa.id})">
            Mesa ${mesa.numero}
            </div>
            `;

        });

        document.getElementById("areaMesas").innerHTML=html;

    });

}

function atualizarContador(){

    fetch("api/listar_mesas.php")
    .then(res=>res.json())
    .then(mesas=>{

        let livres = 0;
        let ocupadas = 0;

        mesas.forEach(m=>{

            if(m.status == "livre") livres++;
            else ocupadas++;

        });

        document.getElementById("contadorMesas").innerHTML =
            "🟢 " + livres + " | 🔴 " + ocupadas;

    });

}

setInterval(atualizarContador,2000);

atualizarContador();

function verPedidosModal(){

window.location="ver_pedido.php?mesa_id="+mesaSelecionada;

}


setInterval(carregarMesas,2000);

</script>
<div class="modal-bg" id="modalBg">

    <div class="modal">

        <div class="modal-title" id="modalTitulo">
            Mesa
        </div>

        <button class="modal-btn abrir" onclick="abrirPedidoModal()">
            Abrir Pedido
        </button>

<button class="modal-btn ver" id="btnVerPedidos" onclick="verPedidosModal()">
Ver Pedidos
</button>



        <button class="modal-btn fechar" id="btnFecharMesa" onclick="fecharMesaModal()">
            Fechar Mesa
        </button>

        <button class="modal-btn cancelar" onclick="fecharModal()">
            Cancelar
        </button>

    </div>

</div>
<div class="topo">
    <a href="logout.php" class="btn-sair">Sair</a>
</div>


</body>
</html>
