<?php
/*
    FICHEIRO: tour.php

    OBJETIVO:
    Página principal do website do Seu Jorge em versão PHP.

    Mantemos o conteúdo visual do projeto original, mas usamos includes
    para carregar o cabeçalho, o menu dinâmico e o rodapé.
*/

session_start();
include "includes/functions.php";

$pageTitle = "Seu Jorge | Tour";
$pageDescription = "Agenda de concertos e tour do artista Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO TOUR -->


<section class="tour-hero">

<!-- CONTEÚDO DO HERO -->

<div class="tour-hero-content">

<h1>Tour</h1>
<p>Concertos e apresentações ao redor do mundo</p>

</div>

</section>


<!-- TOUR SECTION -->


<section class="tour-section">

<div class="container">

<h1 class="tour-title">Tour</h1>

<div class="tour-list">

<!-- CONCERTO -->

<div class="tour-row">

<div class="tour-date">
<span>12</span>
<p>OUT</p>
</div>

<div class="tour-location">

<h2>Porto</h2>
<p>Casa da Música</p>

</div>

<div class="tour-line"></div>

<a href="#" class="tour-btn">Bilhetes</a>

</div>


<!-- CONCERTO -->

<div class="tour-row">

<div class="tour-date">
<span>20</span>
<p>OUT</p>
</div>

<div class="tour-location">

<h3>Lisboa</h3>
<p>Coliseu dos Recreios</p>

</div>

<div class="tour-line"></div>

<a href="#" class="tour-btn">Bilhetes</a>

</div>


<!-- CONCERTO -->

<div class="tour-row">

<div class="tour-date">
<span>05</span>
<p>NOV</p>
</div>

<div class="tour-location">

<h3>Madrid</h3>
<p>Teatro Real</p>

</div>

<div class="tour-line"></div>

<a href="#" class="tour-btn">Bilhetes</a>

</div>

</div>

</div>

</section>




<!-- MAPA DA TOUR -->


<section class="tour-map-section">

<div class="container">

<!-- TÍTULO -->

<h2 class="tour-map-title">Mapa da Tour</h2>

<!-- LINHA ANIMADA -->

<div class="tour-map-line"></div>

<!-- NAVEGAÇÃO DAS CIDADES -->

<div class="tour-map-nav">

<span data-map="https://www.google.com/maps?q=Lisboa&output=embed">Lisboa</span>

<span data-map="https://www.google.com/maps?q=Porto&output=embed">Porto</span>

<span data-map="https://www.google.com/maps?q=Madrid&output=embed">Madrid</span>

</div>

<!-- MAPA -->

<div class="tour-map">

<iframe
src="https://www.google.com/maps?q=Lisboa&output=embed"
loading="lazy">
</iframe>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>
