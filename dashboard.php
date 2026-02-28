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

/* TOAST NOTIFICATION */

.toast{
    position:fixed;
    top:20px;
    right:-400px;
    width:300px;
    background:#27ae60;
    color:white;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,0.25);
    overflow:hidden;
    z-index:5000;
    animation:slideIn 0.4s forwards;
}

.toast-content{
    padding:15px;
    font-weight:bold;
}

.toast-bar{
    height:4px;
    background:rgba(255,255,255,0.7);
    width:100%;
    animation:progressBar 2s linear forwards;
}

@keyframes slideIn{
    from{ right:-400px; }
    to{ right:20px; }
}

@keyframes slideOut{
    from{ right:20px; opacity:1; }
    to{ right:-400px; opacity:0; }
}

@keyframes progressBar{
    from{ width:100%; }
    to{ width:0%; }
}

</style>

</head>

<body>
<?php if(isset($_GET['msg']) && $_GET['msg'] == 'enviado'): ?>
<div id="toast" class="toast">
    <div class="toast-content">
        ✔ Pedido enviado com sucesso!
    </div>
    <div class="toast-bar"></div>
</div>
<?php endif; ?>


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

<div class="mesa <?php echo $mesa['status']; ?>" 
     data-id="<?php echo $mesa['id']; ?>"
     onclick="abrirPDV(<?php echo $mesa['id']; ?>)">

    Mesa <?php echo $mesa['numero']; ?>

    <div id="timer-<?php echo $mesa['id']; ?>" 
         style="font-size:12px;margin-top:5px;">
        ⏱ 00:00:00
    </div>

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

function abrirMesaDireto(mesa_id){

    console.log("Clicou na mesa:", mesa_id);

    let form = new FormData();
    form.append("mesa_id", mesa_id);

    fetch("api/abrir_pedido.php",{
        method:"POST",
        body:form
    })
    .then(res=>res.text())
    .then(texto=>{

        console.log("Resposta bruta da API:", texto);

        try{
            let data = JSON.parse(texto);

            if(data.success){
                window.location = "pedido.php?mesa_id=" + mesa_id;
            }else{
                alert("Erro da API: " + texto);
            }

        }catch(e){
            alert("API não retornou JSON válido.\nVeja o console (F12).");
        }

    })
    .catch(err=>{
        alert("Erro de conexão com API");
        console.log(err);
    });

}

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





function atualizarTimers(){

    Object.keys(horariosMesas).forEach(id=>{

        let abertura = horariosMesas[id];

        let timerDiv = document.getElementById("timer-"+id);

        if(!timerDiv) return;

        if(!abertura){
            timerDiv.innerHTML = "⏱ 00:00:00";
            return;
        }

        let dataAbertura = new Date(abertura.replace(' ', 'T'));
        let agora = new Date();

        let diff = Math.floor((agora - dataAbertura)/1000);

        let horas = Math.floor(diff/3600);
        let minutos = Math.floor((diff%3600)/60);
        let segundos = diff%60;

        timerDiv.innerHTML =
            "⏱ " +
            String(horas).padStart(2,'0') + ":" +
            String(minutos).padStart(2,'0') + ":" +
            String(segundos).padStart(2,'0');

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

let horariosMesas = {};

function buscarHorarios(){

    fetch("api/listar_mesas.php")
    .then(res=>res.json())
    .then(mesas=>{

        mesas.forEach(mesa=>{

            let mesaDiv = document.querySelector(
    `.mesa[data-id="${mesa.id}"]`
);

            if(!mesaDiv) return;

            // Atualiza cor da mesa
            mesaDiv.classList.remove("livre","ocupada");
            mesaDiv.classList.add(mesa.status);

            // Atualiza horário
            if(mesa.status == "ocupada" && mesa.data_abertura){
                horariosMesas[mesa.id] = mesa.data_abertura;
            } else {
                horariosMesas[mesa.id] = null;
            }

        });

    });

}



buscarHorarios();
atualizarContador();

setInterval(buscarHorarios,5000);
setInterval(atualizarTimers,1000);
setInterval(atualizarContador,5000);
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


<script>
document.addEventListener("DOMContentLoaded", function(){

let toast = document.getElementById("toast");

if(toast){

    // remove parâmetro da URL
    if(window.history.replaceState){
        let url = new URL(window.location);
        url.searchParams.delete("msg");
        window.history.replaceState({}, document.title, url.pathname);
    }

    // após 3s faz animação de saída
    setTimeout(function(){
        toast.style.animation = "slideOut 0.4s forwards";
        setTimeout(()=> toast.remove(),400);
    },2000);

}

});
function abrirPDV(mesa_id){

    fetch("pedido_modal.php?mesa_id=" + mesa_id)
    .then(res=>res.text())
    .then(html=>{

        document.getElementById("pdvConteudo").innerHTML = html;
        document.getElementById("pdvTitulo").innerText = "Mesa " + mesa_id;
        document.getElementById("pdvOverlay").style.display = "flex";

    });

}
function fecharPDV(){
    document.getElementById("pdvOverlay").style.display = "none";
    document.getElementById("pdvConteudo").innerHTML = "";
}

// TORNA FUNÇÃO GLOBAL PARA O MODAL
window.abrirGrupoPDV = function(grupo_id){

    // pega mesa atual do título
    let titulo = document.getElementById("pdvTitulo").innerText;
    let mesa_id = titulo.replace("Mesa ","");

    fetch("api/verificar_ou_criar_pedido.php?mesa_id=" + mesa_id)
    .then(res => res.json())
    .then(data => {

        if(data.success){

            fetch("produtos_modal.php?pedido_id=" + data.pedido_id +
                  "&mesa_id=" + mesa_id +
                  "&grupo_id=" + grupo_id)
            .then(res => res.text())
            .then(html => {
document.getElementById("areaProdutosPDV").innerHTML = html; });

        }else{
            alert("Erro ao abrir pedido");
        }

    });

};

</script>

<!-- ========================= -->
<!-- MODAL PDV CENTRAL        -->
<!-- ========================= -->

<div id="pdvOverlay" style="
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
display:none;
justify-content:center;
align-items:center;
z-index:5000;
">

    <div id="pdvJanela" style="
    width:90%;
    max-width:1200px;
    height:95%;
    background:white;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 40px rgba(0,0,0,0.4);
    display:flex;
    flex-direction:column;
    ">

        <!-- TOPO -->
        <div style="
        background:#1f3a5c;
        color:white;
        padding:15px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        font-weight:bold;
        ">
            <div id="pdvTitulo">
                PDV
            </div>

            <button onclick="fecharPDV()" style="
            background:#e74c3c;
            border:none;
            padding:6px 12px;
            color:white;
            border-radius:6px;
            cursor:pointer;
            ">
                Fechar
            </button>
        </div>

        <!-- CONTEÚDO DINÂMICO -->
        <div id="pdvConteudo" style="
        flex:1;
        overflow:auto;
        padding:15px;
        background:#f4f6f8;
        ">
        </div>

    </div>

</div>



</body>
</html>
