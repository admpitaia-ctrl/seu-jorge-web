<?php
/*
    FICHEIRO: contactos.php

    OBJETIVO:
    Página principal do website do Seu Jorge em versão PHP.

    Mantemos o conteúdo visual do projeto original, mas usamos includes
    para carregar o cabeçalho, o menu dinâmico e o rodapé.
*/

session_start();
include "includes/functions.php";

$pageTitle = "Seu Jorge | Contactos";
$pageDescription = "Página de contacto do website Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO CONTACTO -->

<section class="contact-hero">

<div class="contact-hero-content">

<h1>Contacto</h1>
<p>Entre em contacto para concertos, eventos ou colaborações</p>

</div>

</section>


<!-- FORMULÁRIO DE CONTACTO -->

<section class="contact-section">

<div class="container">

<h2 class="contact-title">Enviar Mensagem</h2>

<div class="contact-line"></div>

<div class="contact-container">

<!-- FORMULÁRIO -->

<form class="contact-form form-validacao" id="contactForm">

<p class="form-required-note">* Todos os campos são obrigatórios. O contacto é permitido apenas para maiores de 18 anos.</p>

<input type="text" id="nome" name="nome" placeholder="Nome" minlength="3" autocomplete="given-name" required>
<small class="field-feedback" data-feedback-for="nome"></small>

<input type="text" id="apelido" name="apelido" placeholder="Apelido" minlength="3" autocomplete="family-name" required>
<small class="field-feedback" data-feedback-for="apelido"></small>

<label class="contact-label" for="nascimento">Data de nascimento</label>

<input type="date" id="nascimento" name="nascimento" required>
<small class="field-feedback" data-feedback-for="nascimento"></small>

<input type="email" id="email" name="email" placeholder="Email" autocomplete="email" required>
<small class="field-feedback" data-feedback-for="email"></small>

<input type="tel" id="telefone" name="telefone" placeholder="Telefone" autocomplete="tel" inputmode="tel" required>
<small class="field-feedback" data-feedback-for="telefone"></small>

<textarea id="mensagem" name="mensagem" placeholder="Mensagem" rows="5" minlength="10" required></textarea>
<small class="field-feedback" data-feedback-for="mensagem"></small>

<button type="submit">Enviar Mensagem</button>

<p id="formMessage" class="auth-message contact-feedback" aria-live="polite"></p>

</form>


<!-- INFORMAÇÕES -->

<div class="contact-info">

<h3>Management</h3>

<p>Email: management@seujorge.com</p>

<p>Booking: booking@seujorge.com</p>

<p>Press: press@seujorge.com</p>

<div class="contact-social">

<a href="#">Spotify</a>
<a href="#">YouTube</a>
<a href="#">Instagram</a>

</div>

</div>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>
