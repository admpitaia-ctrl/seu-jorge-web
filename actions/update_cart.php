<?php
/*
    FICHEIRO: actions/update_cart.php

    OBJETIVO:
    Atualizar as quantidades dos produtos dentro do carrinho.

    MELHORIA DE EXPERIÊNCIA:
    Este ficheiro também pode responder em JSON quando o carrinho é atualizado
    automaticamente por JavaScript. Assim o utilizador altera a quantidade e o
    total muda sem precisar clicar em "Atualizar Carrinho".
*/

session_start();
include "../config/database.php";
include "../includes/functions.php";

function responderJsonCarrinho($conexao){
    $carrinho = $_SESSION["cart"] ?? [];
    $total = 0;
    $itens = [];

    if(!empty($carrinho)){
        $ids = array_map("intval", array_keys($carrinho));
        $idsTexto = implode(",", $ids);

        if($idsTexto !== ""){
            $sql = "SELECT id, price, stock FROM products WHERE id IN ($idsTexto)";
            $resultado = mysqli_query($conexao, $sql);

            while($produto = mysqli_fetch_assoc($resultado)){
                $id = (int)$produto["id"];
                $quantidade = (int)($carrinho[$id] ?? 0);
                $stock = (int)$produto["stock"];

                if($quantidade > $stock){
                    $quantidade = $stock;
                    $_SESSION["cart"][$id] = $stock;
                }

                if($quantidade <= 0){
                    unset($_SESSION["cart"][$id]);
                    continue;
                }

                $subtotal = (float)$produto["price"] * $quantidade;
                $total += $subtotal;

                $itens[$id] = [
                    "quantity" => $quantidade,
                    "subtotal" => formatarPreco($subtotal)
                ];
            }
        }
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => true,
        "message" => "Carrinho atualizado.",
        "cart_count" => contarItensCarrinho(),
        "total" => formatarPreco($total),
        "items" => $itens
    ]);
    exit;
}

$pedidoAjax = isset($_POST["ajax"]) && $_POST["ajax"] === "1";

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["quantities"])){

    foreach($_POST["quantities"] as $productId => $quantity){

        $productId = (int)$productId;
        $quantity = (int)$quantity;

        if($quantity <= 0){
            unset($_SESSION["cart"][$productId]);
        } else {
            $_SESSION["cart"][$productId] = $quantity;
        }
    }

    if($pedidoAjax){
        responderJsonCarrinho($conexao);
    }
}

if($pedidoAjax){
    responderJsonCarrinho($conexao);
}

header("Location: ../cart.php");
exit;
?>
