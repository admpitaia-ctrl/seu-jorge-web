<?php
/*
    FICHEIRO: sobre.php

    OBJETIVO:
    Página principal do website do Seu Jorge em versão PHP.

    Mantemos o conteúdo visual do projeto original, mas usamos includes
    para carregar o cabeçalho, o menu dinâmico e o rodapé.
*/

session_start();
include "includes/functions.php";

$pageTitle = "Seu Jorge | Sobre";
$pageDescription = "Biografia e carreira do artista Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO SOBRE -->

<section class="about-hero">

<div class="about-hero-content">

<h1>Seu Jorge</h1>
<p>A história por trás da música</p>

</div>

</section>

  <!-- BIOGRAFIA -->

  <section class="bio-section">

<div class="container">

<div class="row align-items-center">

<!-- FOTO -->

<div class="col-lg-5 bio-image">
<img src="picture/Sobre/Biografia.jpg" alt="Seu Jorge retrato artístico">
</div>

<!-- TEXTO -->

<div class="col-lg-7 bio-content">

<h2 class="bio-title">Biografia</h2>

<p>
Seu Jorge é um cantor, compositor e ator brasileiro reconhecido
internacionalmente pela sua mistura única de samba, soul e música
popular brasileira. Com uma voz marcante e estilo inconfundível,
construiu uma carreira sólida que atravessa música, cinema e cultura.
</p>

<p>
Ao longo de sua trajetória, colaborou com artistas internacionais,
participou de filmes aclamados e tornou-se um dos representantes
mais autênticos da música brasileira contemporânea.
</p>

</div>

</div>

</div>

</section>


<!-- TRAJETÓRIA -->

<section class="career-section">

<div class="container">

<h2 class="career-title">A Trajetória</h2>

<div class="career-timeline">

<div class="career-item">
<span class="career-year">1998</span>
<p>Início da carreira musical.</p>
</div>

<div class="career-item">
<span class="career-year">2002</span>
<p>Participação no filme Cidade de Deus.</p>
</div>

<div class="career-item">
<span class="career-year">2004</span>
<p>Lançamento do álbum Cru.</p>
</div>

<div class="career-item">
<span class="career-year">2012</span>
<p>Reconhecimento internacional.</p>
</div>

</div>

</div>

</section>

<!-- Cinema & Cultura -->

<section class="cinema-section">

<div class="container">

<h2 class="cinema-title">Cinema & Cultura</h2>

<div class="row">

<!-- FILME 1 -->

<div class="col-lg-4 col-md-6 col-sm-12">

<div class="cinema-card">

<img class="film-poster"
src="picture/Sobre/filme/cidade-de-deus.jpg"
alt="Cidade de Deus">

<video class="film-video" muted loop playsinline>

<source src="videos/cidade-de-deus.mp4" type="video/mp4">

</video>

<h3>Cidade de Deus</h3>
<p>2002</p>

</div>

</div>


<!-- FILME 2 -->

<div class="col-lg-4 col-md-6 col-sm-12">

<div class="cinema-card">

<img class="film-poster"
src="picture/Sobre/filme/the-life-aquatic.jpg"
alt="The Life Aquatic">

<video class="film-video" muted loop playsinline>

<source src="videos/life-aquatic.mp4" type="video/mp4">

</video>

<h3>The Life Aquatic</h3>
<p>2004</p>

</div>

</div>


<!-- FILME 3 -->

<div class="col-lg-4 col-md-6 col-sm-12">

<div class="cinema-card">

<img class="film-poster"
src="picture/Sobre/filme/marighella.jpg"
alt="Marighella">

<video class="film-video" muted loop playsinline>

<source src="videos/marighella.mp4" type="video/mp4">

</video>

<h3>Marighella</h3>
<p>2019</p>

</div>

</div>

</div>

</div>

</section>


<!-- CITAÇÃO ARTÍSTICA -->


<section class="quote-section">

<div class="container">

<h2 class="visually-hidden">Citação artística</h2>

<blockquote class="artist-quote reveal-quote">
"A música é a forma que encontrei de contar a minha história."
</blockquote>

<p class="quote-author">
— Seu Jorge
</p>

</div>

</section>


<!-- DISCografia CTA -->


<section class="discography-cta">

<div class="container text-center">

<h2 class="cta-title">
Explore a discografia completa
</h2>

</div>

<div class="cta-button-wrapper">

<a href="albuns.php" class="cta-button">
Ver Álbuns
</a>

</div>

</section>

<?php include "includes/footer.php"; ?>
