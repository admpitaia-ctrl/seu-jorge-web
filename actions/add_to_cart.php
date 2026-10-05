<?php
/*
    FICHEIRO: actions/add_to_cart.php

    OBJETIVO:
    Adicionar produtos ao carrinho de compras usando sessões PHP.

    O carrinho fica guardado em $_SESSION['cart'], conforme pedido no exercício.

    MELHORIA DE EXPERIÊNCIA:
    Este ficheiro também responde em JSON quando o pedido vem por AJAX.
    Assim, ao clicar em "Adicionar", a página não precisa recarregar nem subir ao topo.
*/

session_start();
include "../config/database.php";

function responderAjax($sucesso, $mensagem, $totalCarrinho = 0){
    /*
        Quando o JavaScript envia o formulário por AJAX, devolvemos uma
        resposta em JSON para a página atualizar o carrinho sem recarregar.
    */
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => $sucesso,
        "message" => $mensagem,
        "cart_count" => $totalCarrinho
    ]);
    exit;
}

function totalCarrinhoAtual(){
    /*
        Conta a quantidade total de itens guardados na sessão do carrinho.
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

$pedidoAjax = isset($_POST["ajax"]) && $_POST["ajax"] === "1";

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    if($pedidoAjax){
        responderAjax(false, "Pedido inválido.", totalCarrinhoAtual());
    }

    header("Location: ../loja.php");
    exit;
}

$productId = (int)($_POST["product_id"] ?? 0);
$quantity = (int)($_POST["quantity"] ?? 1);

if($quantity < 1){
    $quantity = 1;
}

// Verificamos se o produto existe e se possui stock.
$sql = "SELECT id, stock FROM products WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$produto = mysqli_fetch_assoc($resultado);

if(!$produto){
    if($pedidoAjax){
        responderAjax(false, "Este produto já não está disponível.", totalCarrinhoAtual());
    }

    header("Location: ../loja.php?erro=produto_indisponivel");
    exit;
}

if((int)$produto["stock"] <= 0){
    if($pedidoAjax){
        responderAjax(false, "Produto sem stock disponível.", totalCarrinhoAtual());
    }

    header("Location: ../loja.php?erro=sem_stock");
    exit;
}

if($quantity > (int)$produto["stock"]){
    $quantity = (int)$produto["stock"];
}

// Criamos o carrinho caso ele ainda não exista.
if(!isset($_SESSION["cart"])){
    $_SESSION["cart"] = [];
}

// Se o produto já estiver no carrinho, somamos a quantidade.
if(isset($_SESSION["cart"][$productId])){
    $_SESSION["cart"][$productId] += $quantity;
} else {
    $_SESSION["cart"][$productId] = $quantity;
}

// Não deixamos ultrapassar o stock disponível.
if($_SESSION["cart"][$productId] > (int)$produto["stock"]){
    $_SESSION["cart"][$productId] = (int)$produto["stock"];
}

if($pedidoAjax){
    responderAjax(true, "Produto adicionado ao carrinho.", totalCarrinhoAtual());
}

/*
    Fallback sem JavaScript:
    Se o navegador não usar AJAX, voltamos para a loja com uma âncora do produto.
    Isso reduz o problema da página voltar ao topo.
*/
header("Location: ../loja.php?msg=adicionado&produto=" . $productId . "#produto-" . $productId);
exit;
?>