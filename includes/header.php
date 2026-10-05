<?php
/*
    FICHEIRO: includes/header.php

    OBJETIVO:
    Guardar a parte inicial do HTML usada nas páginas PHP.

    Aqui ficam o DOCTYPE, a tag head, as metatags, o Bootstrap,
    o Font Awesome, o Google Fonts e o ficheiro CSS do projeto.
*/

if(!isset($pageTitle)){
    $pageTitle = "Seu Jorge | Oficial";
}

if(!isset($pageDescription)){
    $pageDescription = "Website dedicado ao artista brasileiro Seu Jorge";
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo limparTexto($pageTitle); ?></title>

    <!-- SEO -->
    <meta name="description" content="<?php echo limparTexto($pageDescription); ?>">
    <meta name="keywords" content="Seu Jorge, música brasileira, samba soul, artista brasileiro, loja oficial">
    <meta name="author" content="Hasan Marouf">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo caminhoProjeto('css/style.css'); ?>">

</head>

<body>
