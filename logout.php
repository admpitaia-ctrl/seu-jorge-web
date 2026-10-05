<?php
/*
    FICHEIRO: logout.php

    OBJETIVO:
    Terminar a sessão do utilizador.

    Ao clicar em sair, apagamos os dados da sessão e enviamos o utilizador
    novamente para a página de login.
*/

session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;
?>
