<?php
/*
    FICHEIRO: includes/admin_check.php

    OBJETIVO:
    Proteger páginas da área de administração.

    Apenas utilizadores com role igual a admin podem aceder.
*/

if(!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin"){
    $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));
    if(basename($scriptDir) === "admin"){
        $scriptDir = dirname($scriptDir);
    }
    $scriptDir = rtrim($scriptDir, "/");
    header("Location: " . $scriptDir . "/login.php");
    exit;
}
?>
