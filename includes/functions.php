<?php
/*
    FICHEIRO: includes/functions.php

    OBJETIVO:
    Guardar funções auxiliares usadas em várias páginas do projeto.

    Assim evitamos repetir o mesmo código em vários ficheiros.
*/


function caminhoProjeto($caminho = ""){
    /*
        Cria caminhos absolutos a partir da pasta principal do projeto.

        Esta função permite que páginas dentro da pasta admin/ continuem
        chamando corretamente CSS, JS, imagens e páginas principais.
        Exemplo: caminhoProjeto("loja.php") funciona tanto na raiz como em admin/.
    */
    $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));

    if(basename($scriptDir) === "admin"){
        $scriptDir = dirname($scriptDir);
    }

    $scriptDir = rtrim($scriptDir, "/");

    if($scriptDir === "" || $scriptDir === "."){
        $scriptDir = "";
    }

    return $scriptDir . "/" . ltrim($caminho, "/");
}

function limparTexto($valor){
    /*
        Esta função protege a saída de textos no HTML.
        Evita que código indevido seja executado no navegador.
    */
    return htmlspecialchars($valor ?? "", ENT_QUOTES, "UTF-8");
}

function formatarPreco($valor){
    /*
        Esta função transforma um número em preço no formato europeu.
        Exemplo: 47.99 fica €47,99.
    */
    return "€" . number_format((float)$valor, 2, ",", ".");
}

function utilizadorLogado(){
    /*
        Verifica se existe uma sessão com o id do utilizador.
        Se existir, significa que o utilizador fez login.
    */
    return isset($_SESSION["user_id"]);
}

function utilizadorAdmin(){
    /*
        Verifica se o utilizador logado tem a função de administrador.
    */
    return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}

function montarMoradaCompleta($address, $city, $postalCode, $country){
    /*
        Junta os campos de morada num texto único.
        Isto é usado tanto no checkout como nos links do Google Maps.
    */
    $partes = [];

    foreach([$address, $postalCode, $city, $country] as $parte){
        $parte = trim($parte ?? "");
        if($parte !== ""){
            $partes[] = $parte;
        }
    }

    return implode(", ", $partes);
}

function gerarLinkGoogleMaps($address, $city, $postalCode, $country){
    /*
        Cria um link de pesquisa no Google Maps com a morada da encomenda.
        Não depende de API Key, por isso funciona bem em XAMPP/local.
    */
    $moradaCompleta = montarMoradaCompleta($address, $city, $postalCode, $country);

    if($moradaCompleta === ""){
        return "";
    }

    return "https://www.google.com/maps/search/?api=1&query=" . rawurlencode($moradaCompleta);
}

function contarItensCarrinho(){
    /*
        Conta quantos itens existem no carrinho.
        Esta função é usada na navbar para mostrar uma badge no carrinho,
        melhorando a experiência do utilizador.
    */
    if(!isset($_SESSION["cart"]) || !is_array($_SESSION["cart"])){
        return 0;
    }

    $total = 0;

    foreach($_SESSION["cart"] as $quantidade){
        $total += (int)$quantidade;
    }

    return $total;
}

function normalizarEstadoEncomenda($status){
    /*
        Mantém compatibilidade com a versão anterior do projeto.
        Antes existia o estado "completed". Agora a loja usa estados mais
        claros para acompanhar a entrega: pending, in_transit e delivered.
    */
    if($status === "completed"){
        return "delivered";
    }

    if(in_array($status, ["pending", "in_transit", "delivered"], true)){
        return $status;
    }

    return "pending";
}

function traduzirEstadoEncomenda($status){
    /*
        Mostra o estado da encomenda em português para o utilizador e admin.
    */
    $status = normalizarEstadoEncomenda($status);

    $estados = [
        "pending" => "Pendente",
        "in_transit" => "A caminho",
        "delivered" => "Entregue"
    ];

    return $estados[$status] ?? "Pendente";
}

function classeEstadoEncomenda($status){
    /*
        Define uma classe CSS diferente para cada fase da entrega.
    */
    $status = normalizarEstadoEncomenda($status);

    return "status-" . $status;
}

function estadosEncomendaDisponiveis(){
    /*
        Lista oficial de estados usados no admin.
    */
    return [
        "pending" => "Pendente",
        "in_transit" => "A caminho",
        "delivered" => "Entregue"
    ];
}

?>
