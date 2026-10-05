<?php
/*
    FICHEIRO: albuns.php

    OBJETIVO:
    Página principal do website do Seu Jorge em versão PHP.

    Mantemos o conteúdo visual do projeto original, mas usamos includes
    para carregar o cabeçalho, o menu dinâmico e o rodapé.
*/

session_start();
include "includes/functions.php";

$pageTitle = "Seu Jorge | Álbuns";
$pageDescription = "Discografia e álbuns do artista Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO ALBUNS -->

<section class="albums-hero">

<div class="albums-hero-content">

<h1>Álbuns</h1>
<p>Discografia de Seu Jorge</p>

</div>

</section>

<!--   DISCOS    -->

<section class="albums-section">

<div class="container">

<div class="row">

<!-- ALBUM 1 -->

<div class="col-lg-4 col-md-6">

<div class="album-card">

<img src="picture/albuns/discografia/esporte-fino.jpg" alt="Samba Esporte Fino">

<div class="album-overlay">

<h2>Samba Esporte Fino</h2>

<p>2001</p>

<a href="https://open.spotify.com" target="_blank" class="album-button">
Ouvir
</a>

</div>

</div>

</div>


<!-- ALBUM 2 -->

<div class="col-lg-4 col-md-6">

<div class="album-card">

<img src="picture/home/cards/cru.jpg" alt="Cru">

<div class="album-overlay">

<h3>Cru</h3>
<p>2004</p>

<a href="https://open.spotify.com" target="_blank" class="album-button">
Ouvir
</a>

</div>

</div>

</div>


<!-- ALBUM 3 -->

<div class="col-lg-4 col-md-6">

<div class="album-card">

<img src="picture/home/cards/america-brasil.png" alt="América Brasil">

<div class="album-overlay">

<h3>América Brasil</h3>
<p>2007</p>

<a href="https://open.spotify.com" target="_blank" class="album-button">
Ouvir
</a>

</div>

</div>

</div>

</div>

</div>

</section>

<!-- STREAMING PLATFORMS -->

<section class="streaming-section">

<div class="container">

<h2 class="streaming-title">Ouça Seu Jorge</h2>

<div class="streaming-icons">

<a href="https://open.spotify.com/" target="_blank">
<i class="fa-brands fa-spotify"></i>
<span>Spotify</span>
</a>

<a href="https://music.apple.com/" target="_blank">
<i class="fa-brands fa-apple"></i>
<span>Apple Music</span>
</a>

<a href="https://www.youtube.com/" target="_blank">
<i class="fa-brands fa-youtube"></i>
<span>YouTube</span>
</a>

<a href="https://www.deezer.com/" target="_blank">
<i class="fa-brands fa-deezer"></i>
<span>Deezer</span>
</a>

<a href="https://soundcloud.com/" target="_blank">
<i class="fa-brands fa-soundcloud"></i>
<span>SoundCloud</span>
</a>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>
