<?php
session_start();
require "config/conexao.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Monitor de Impressão</title>
</head>
<body style="font-family:Arial;background:#111;color:#0f0;">

<h2>🖨 Monitor de Impressão Ativo</h2>
<div id="status">Aguardando pedidos...</div>

<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.3/qz-tray.js"></script>

<script>

qz.websocket.connect()
.then(() => {
    console.log("Conectado ao QZ");
})
.catch(err => {
    console.error("Erro ao conectar QZ:", err);
});

let processando = false;

function verificarPedidos(){

if(processando) return; // 🔒 impede execução simultânea

processando = true;

fetch("api/verificar_impressoes.php")
.then(res => res.json())
.then(data => {

if(!data.success || data.itens.length === 0){
document.getElementById("status").innerText = "Aguardando pedidos...";
processando = false;
return;
}

console.log("Pedido encontrado:", data.itens);

imprimirPedidos(data.itens);

})
.catch(()=>{
processando = false;
});

}

function getPrinterByGroup(grupo){

grupo = grupo.toUpperCase();

if(grupo === "ALMOÇO" || grupo === "ESPETOS"){
    return "COZINHA";
}
if(grupo === "PORÇÕES"){
    return "PORCOES";
}
if(grupo === "BEBIDAS" || grupo === "CERVEJAS"){
    return "BEBIDAS";
}
if(grupo === "SUCOS"){
    return "SUCOS";
}

return null;
}

function imprimirPedidos(itens){

let grupos = {};

// Agrupar por grupo
itens.forEach(item => {
    if(!grupos[item.grupo]){
        grupos[item.grupo] = [];
    }
    grupos[item.grupo].push(item);
});

let promessas = [];

Object.keys(grupos).forEach(nomeGrupo => {

    let impressora = getPrinterByGroup(nomeGrupo);
    if(!impressora) return;

    let texto = gerarCupom(nomeGrupo, grupos[nomeGrupo]);

    console.log("==== CUPOM ====");
    console.log(texto);
    console.log("===============");

    let ids = grupos[nomeGrupo].map(item => item.id);

    let promessa = qz.print(
        qz.configs.create(impressora),
        [{
            type: 'raw',
            data: texto
        }]
    )
    .then(() => {

        console.log("Impresso com sucesso:", ids);

        return fetch("api/marcar_impresso.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: ids.map(id => "ids[]=" + id).join("&")
        });

    });

    promessas.push(promessa);

});

// 🔥 Espera TODAS as impressões terminarem
Promise.all(promessas)
.then(() => {
    console.log("Todos grupos impressos");
    processando = false;
})
.catch(err => {
    console.error("Erro impressão:", err);
    processando = false;
});

}

function gerarCupom(nomeTopo, itensGrupo){

let mesa = itensGrupo[0].mesa_id;
let atendimento = itensGrupo[0].pedido_id;
let garcom = itensGrupo[0].garcom.toUpperCase();

let agora = new Date();

let dataHora =
agora.getDate().toString().padStart(2,'0') + "/" +
(agora.getMonth()+1).toString().padStart(2,'0') + "/" +
agora.getFullYear().toString().slice(-2) + " " +
agora.getHours().toString().padStart(2,'0') + ":" +
agora.getMinutes().toString().padStart(2,'0') + ":" +
agora.getSeconds().toString().padStart(2,'0');

const largura = 32;

function centralizar(texto){
let espacos = Math.floor((largura - texto.length) / 2);
return " ".repeat(Math.max(0, espacos)) + texto + "\n";
}

function linha(){
return "-".repeat(largura) + "\n";
}

let texto = "";

texto += centralizar(nomeTopo.toUpperCase());
texto += linha();
texto += "Mesa: " + mesa + "\n";
texto += "Atendimento: " + atendimento + "\n";
texto += linha();
texto += "QTD  DESCRIÇÃO\n";
texto += linha();

itensGrupo.forEach(item => {

let qtd = item.quantidade.toString().padEnd(4, " ");
let nome = item.produto;

if(nome.length > 26){
nome = nome.substring(0, 26);
}

texto += qtd + nome + "\n";

// Observação
if(item.observacao && item.observacao.trim() !== ""){

let obs = item.observacao.trim();

while(obs.length > 26){
texto += "     * " + obs.substring(0, 23) + "\n";
obs = obs.substring(23);
}

texto += "     * " + obs + "\n";
}

});

texto += linha();
texto += "Garçom: " + garcom + "\n";
texto += "Impresso: " + dataHora + "\n\n\n";

return texto;
}

setInterval(verificarPedidos, 2000);

</script>

</body>
</html>