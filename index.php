<?php
/*
    FICHEIRO: index.php

    OBJETIVO:
    Página principal do website do Seu Jorge em versão PHP.

    Mantemos o conteúdo visual do projeto original, mas usamos includes
    para carregar o cabeçalho, o menu dinâmico e o rodapé.
*/

session_start();
include "includes/functions.php";

$pageTitle = "Seu Jorge | Oficial";
$pageDescription = "Website dedicado ao artista brasileiro Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO SECTION (vamos construir depois) -->

    <main>
<section class="hero">

<div id="heroCarousel"
class="carousel slide carousel-fade"
data-bs-ride="carousel"
data-bs-interval="3000"
data-bs-pause="false">

<div class="carousel-inner">

<!-- SLIDE 1 -->

<div class="carousel-item active">

<img src="picture/home/slide/imagem-principal.jpg" class="d-block w-100 hero-image" alt="Seu Jorge cantor brasileiro em concerto">

<div class="carousel-caption hero-content">

<h1>Seu Jorge</h1>

<p>"A voz do samba soul"</p>

<div class="hero-social">

<a href="#"><i class="fa-brands fa-spotify"></i></a>
<a href="#"><i class="fa-brands fa-youtube"></i></a>
<a href="#"><i class="fa-brands fa-instagram"></i></a>
<a href="https://www.imdb.com/pt/name/nm1179580/?ref_=nv_sr_srsg_0"><i class="fa-brands fa-imdb"></i></a>
<a href="#"><i class="fa-brands fa-apple"></i></a>
<a href="#"><i class="fa-solid fa-music"></i></a>
<a href="#"><i class="fa-brands fa-amazon"></i></a>

</div>

<a href="albuns.php" class="hero-btn">Conheça a música</a>

</div>

</div>



<!-- SLIDE 2 -->

<div class="carousel-item">

<img src="picture/home/slide/band.jpg" class="d-block w-100 hero-image" alt="Jazz e Soul">

<div class="carousel-caption hero-content">

<h1>Jazz & Soul</h1>

<p>Influências que marcam o seu som</p>

<div class="hero-social">

<a href="#"><i class="fa-brands fa-spotify"></i></a>
<a href="#"><i class="fa-brands fa-youtube"></i></a>
<a href="#"><i class="fa-brands fa-instagram"></i></a>
<a href="https://www.imdb.com/pt/name/nm1179580/?ref_=nv_sr_srsg_0"><i class="fa-brands fa-imdb"></i></a>
<a href="#"><i class="fa-brands fa-apple"></i></a>
<a href="#"><i class="fa-solid fa-music"></i></a>
<a href="#"><i class="fa-brands fa-amazon"></i></a>

</div>

<a href="albuns.php" class="hero-btn">Conheça a música</a>

</div>

</div>
<!-- SLIDE 3 -->

<div class="carousel-item">

<img src="picture/home/slide/fundo-escuro.png" class="d-block w-100 hero-image" alt="Cultura brasileira">

<div class="carousel-caption hero-content">

<h1>Cultura Brasileira</h1>

<p>A música que representa o Brasil</p>

<div class="hero-social">

<a href="#"><i class="fa-brands fa-spotify"></i></a>
<a href="#"><i class="fa-brands fa-youtube"></i></a>
<a href="#"><i class="fa-brands fa-instagram"></i></a>
<a href="https://www.imdb.com/pt/name/nm1179580/?ref_=nv_sr_srsg_0"><i class="fa-brands fa-imdb"></i></a>
<a href="#"><i class="fa-brands fa-apple"></i></a>
<a href="#"><i class="fa-solid fa-music"></i></a>
<a href="#"><i class="fa-brands fa-amazon"></i></a>

</div>

<a href="albuns.php" class="hero-btn">Conheça a música</a>

</div>

</div>

<!-- SLIDE 4 -->

<div class="carousel-item">

<img src="picture/home/slide/cultura-brasileira.jpg" class="d-block w-100 hero-image" alt="Cinema">

<div class="carousel-caption hero-content">

<h1>Cinema</h1>

<p>Carreira também no grande ecrã</p>

<div class="hero-social">

<a href="#"><i class="fa-brands fa-spotify"></i></a>
<a href="#"><i class="fa-brands fa-youtube"></i></a>
<a href="#"><i class="fa-brands fa-instagram"></i></a>
<a href="https://www.imdb.com/pt/name/nm1179580/?ref_=nv_sr_srsg_0"><i class="fa-brands fa-imdb"></i></a>
<a href="#"><i class="fa-brands fa-apple"></i></a>
<a href="#"><i class="fa-solid fa-music"></i></a>
<a href="#"><i class="fa-brands fa-amazon"></i></a>

</div>

<a href="albuns.php" class="hero-btn">Conheça a música</a>

</div>

</div>

<!-- SLIDE 5 -->

<div class="carousel-item">

<img src="picture/home/slide/concerto.jpg" class="d-block w-100 hero-image" alt="Concertos">

<div class="carousel-caption hero-content">

<h1>Concertos</h1>

<p>Palcos do mundo inteiro</p>

<div class="hero-social">

<a href="#"><i class="fa-brands fa-spotify"></i></a>
<a href="#"><i class="fa-brands fa-youtube"></i></a>
<a href="#"><i class="fa-brands fa-instagram"></i></a>
<a href="https://www.imdb.com/pt/name/nm1179580/?ref_=nv_sr_srsg_0"><i class="fa-brands fa-imdb"></i></a>
<a href="#"><i class="fa-brands fa-apple"></i></a>
<a href="#"><i class="fa-solid fa-music"></i></a>
<a href="#"><i class="fa-brands fa-amazon"></i></a>

</div>

<a href="albuns.php" class="hero-btn">Conheça a música</a>

</div>

</div>

</div>

</div>

</section>

    </main>

<section class="artist-section reveal">

<div class="container text-center">

<h2 class="artist-title">A Voz do Samba Soul</h2>

<div class="artist-image">

<img src="picture/home/modelo/modelo-fundo-vermelho.jpg" alt="Seu Jorge artista brasileiro">

</div>

<p class="artist-text">

Seu Jorge é um cantor, compositor e ator brasileiro
reconhecido internacionalmente pela mistura única
de samba, soul e música popular brasileira.

</p>

<a href="sobre.php" class="artist-link">
Conheça a história →
</a>

</div>

</section>

<!-- DISCOGRAFIA -->

<section class="albums-section">

<div class="container">

<h2 class="section-title">Discografia</h2>

<!-- ÁLBUM 1 -->

<div class="album-item">

<div class="album-image">
<img src="picture/home/cards/cru.jpg" alt="Álbum Cru - Seu Jorge" loading="lazy">
</div>

<div class="album-info">
<h3>Cru</h3>
<p>2004</p>

<p>
Primeiro álbum de estúdio de Seu Jorge, trazendo a mistura
de samba, soul e música popular brasileira.
</p>

<a href="#" class="album-link" target="_blank">
Ouvir no Spotify →
</a>

</div>

</div>


<!-- ÁLBUM 2 -->

<div class="album-item reverse">

<div class="album-image">
<img src="picture/home/cards/america-brasil.png" alt="Álbum América Brasil" loading="lazy">
</div>

<div class="album-info">

<h3>América Brasil</h3>
<p>2007</p>

<p>
Um dos álbuns mais reconhecidos da carreira,
com forte identidade brasileira e sonoridade moderna.
</p>

<a href="#" class="album-link" target="_blank">
Ouvir no Spotify →
</a>

</div>

</div>


<!-- ÁLBUM 3 -->

<div class="album-item">

<div class="album-image">
<img src="picture/home/cards/musica-para-churrasco.png" alt="Músicas para Churrasco" loading="lazy">

</div>

<div class="album-info">

<h3>Músicas para Churrasco</h3>
<p>2011</p>

<p>
Um álbum icónico que apresenta um lado mais
descontraído e autêntico do artista.
</p>

<a href="#" class="album-link" target="_blank">
Ouvir no Spotify →
</a>

</div>

</div>


<!-- ÁLBUM 4 -->

<div class="album-item reverse">

<div class="album-image">
<img src="picture/home/cards/the-life-aquatic-song.jpg" alt="The Life Aquatic Sessions" loading="lazy">

</div>

<div class="album-info">

<h3>The Life Aquatic Sessions</h3>
<p>2005</p>

<p>
Interpretações em português de músicas de David Bowie
gravadas para o filme The Life Aquatic.
</p>

<a href="#" class="album-link" target="_blank">
Ouvir no Spotify →
</a>

</div>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>
