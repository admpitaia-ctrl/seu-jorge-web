<?php
/*
    FICHEIRO: actions/remove_from_cart.php

    OBJETIVO:
    Remover um produto específico do carrinho.
*/

session_start();

$productId = (int)($_GET["id"] ?? 0);

if(isset($_SESSION["cart"][$productId])){
    unset($_SESSION["cart"][$productId]);
}

header("Location: ../cart.php");
exit;
?>
