<?php
/*
    FICHEIRO: admin_send_driver_whatsapp.php

    OBJETIVO:
    Preparar uma mensagem de WhatsApp para enviar a guia de entrega
    ao motorista/transportador.

    IMPORTANTE:
    Um link normal do WhatsApp não consegue anexar automaticamente um PDF.
    Por isso, este ficheiro envia os dados principais da entrega e o link da
    guia imprimível. Se o projeto estiver online, o motorista conseguirá abrir
    o link. Em localhost, o link serve apenas para teste no computador.
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

$orderId = (int)($_POST["order_id"] ?? $_GET["order_id"] ?? 0);
$telefoneMotorista = trim($_POST["driver_phone"] ?? $_GET["driver_phone"] ?? "");

if($orderId <= 0){
    header("Location: admin_orders.php");
    exit;
}

/*
    Normalizamos o número do motorista.
    A regra definida para Portugal é iniciar sempre com +351.
    O WhatsApp usa apenas números no link, então +351 912 345 678 vira 351912345678.
*/
$numeroLimpo = preg_replace('/\D+/', '', $telefoneMotorista);

if(strpos($numeroLimpo, "351") !== 0){
    $numeroLimpo = "351" . $numeroLimpo;
}

if(strlen($numeroLimpo) < 12){
    header("Location: admin_order_details.php?id=" . $orderId . "&erro=telefone_motorista");
    exit;
}

$sqlOrder = "SELECT o.id, o.order_date, o.status, o.total,
                    o.delivery_address, o.delivery_city, o.delivery_postal_code,
                    o.delivery_country, o.delivery_maps_url, o.delivery_notes,
                    u.username, u.email
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             WHERE o.id = ?
             LIMIT 1";
$stmtOrder = mysqli_prepare($conexao, $sqlOrder);
mysqli_stmt_bind_param($stmtOrder, "i", $orderId);
mysqli_stmt_execute($stmtOrder);
$resultadoOrder = mysqli_stmt_get_result($stmtOrder);
$encomenda = mysqli_fetch_assoc($resultadoOrder);

if(!$encomenda){
    header("Location: admin_orders.php");
    exit;
}

$sqlItens = "SELECT oi.quantity, p.name
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY p.name ASC";
$stmtItens = mysqli_prepare($conexao, $sqlItens);
mysqli_stmt_bind_param($stmtItens, "i", $orderId);
mysqli_stmt_execute($stmtItens);
$resultadoItens = mysqli_stmt_get_result($stmtItens);

$listaProdutos = [];
while($item = mysqli_fetch_assoc($resultadoItens)){
    $listaProdutos[] = "- " . $item["name"] . " x" . (int)$item["quantity"];
}

$moradaCompleta = montarMoradaCompleta(
    $encomenda["delivery_address"],
    $encomenda["delivery_city"],
    $encomenda["delivery_postal_code"],
    $encomenda["delivery_country"]
);

$mapsUrl = $encomenda["delivery_maps_url"] ?: gerarLinkGoogleMaps(
    $encomenda["delivery_address"],
    $encomenda["delivery_city"],
    $encomenda["delivery_postal_code"],
    $encomenda["delivery_country"]
);

/*
    Montamos a URL da guia de entrega.
    Em ambiente real, esta URL deve ser o domínio do site.
*/
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER["HTTP_HOST"] ?? "localhost";
$pastaAtual = rtrim(dirname($_SERVER["SCRIPT_NAME"] ?? ""), "/\\");
$linkGuia = $protocolo . "://" . $host . $pastaAtual . "/admin_delivery_note.php?id=" . $orderId;

$mensagem = "Olá, segue uma nova entrega da loja Seu Jorge.\n\n";
$mensagem .= "Encomenda: #" . $encomenda["id"] . "\n";
$mensagem .= "Cliente: " . $encomenda["username"] . "\n";
$mensagem .= "Morada: " . ($moradaCompleta ?: "Morada não registada") . "\n";
$mensagem .= "Google Maps: " . ($mapsUrl ?: "Não disponível") . "\n\n";
$mensagem .= "Produtos:\n" . implode("\n", $listaProdutos) . "\n\n";
$mensagem .= "Total: " . formatarPreco($encomenda["total"]) . "\n";

if(trim($encomenda["delivery_notes"] ?? "") !== ""){
    $mensagem .= "Observações: " . $encomenda["delivery_notes"] . "\n";
}

$mensagem .= "\nGuia de entrega: " . $linkGuia . "\n";
$mensagem .= "Após a entrega, solicitar a confirmação ao cliente.";

$linkWhatsapp = "https://wa.me/" . $numeroLimpo . "?text=" . rawurlencode($mensagem);

header("Location: " . $linkWhatsapp);
exit;
?>
