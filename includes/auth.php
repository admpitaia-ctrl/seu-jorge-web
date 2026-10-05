<?php
/*
    FICHEIRO: includes/auth.php

    OBJETIVO:
    Proteger páginas que só podem ser vistas por utilizadores autenticados.

    Se o utilizador não tiver sessão iniciada, é redirecionado para login.php.
*/

if(!isset($_SESSION["user_id"])){
    $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));
    if(basename($scriptDir) === "admin"){
        $scriptDir = dirname($scriptDir);
    }
    $scriptDir = rtrim($scriptDir, "/");
    header("Location: " . $scriptDir . "/login.php");
    exit;
}
?>
