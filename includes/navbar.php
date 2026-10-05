<?php
/*
    FICHEIRO: includes/navbar.php

    OBJETIVO:
    Guardar a barra de navegação do website.

    Como várias páginas usam o mesmo menu, colocamos tudo aqui.
    Assim, se for necessário alterar o menu, alteramos apenas este ficheiro.
*/
?>

<!-- NAVBAR -->

<header>

    <nav class="navbar navbar-expand-lg fixed-top">

        <div class="container">

            <!-- LOGO -->

            <a class="navbar-brand" href="<?php echo caminhoProjeto('index.php'); ?>">
                <img src="<?php echo caminhoProjeto('picture/Logo/logo-seu-jorge.png'); ?>" alt="Seu Jorge Logo" height="45">
            </a>

            <!-- BOTÃO MOBILE -->

            <button class="navbar-toggler custom-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu"
                aria-controls="menu"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>

            <!-- MENU -->

            <div class="collapse navbar-collapse justify-content-end" id="menu">

                <ul class="navbar-nav">

                    <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('index.php'); ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('sobre.php'); ?>">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('albuns.php'); ?>">Álbuns</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('tour.php'); ?>">Tour</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('contactos.php'); ?>">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('loja.php'); ?>">Loja</a></li>
                    <li class="nav-item">
                        <a class="nav-link cart-link cart-link-icon-only" href="<?php echo caminhoProjeto('cart.php'); ?>" aria-label="Abrir carrinho de compras">
                            <span class="cart-icon" aria-hidden="true">🛒</span>
                            <span class="cart-hover-text">Carrinho</span>
                            <?php $totalCarrinho = contarItensCarrinho(); ?>
                            <span id="cart-badge" class="cart-badge <?php echo $totalCarrinho > 0 ? '' : 'cart-badge-empty'; ?>">
                                <?php echo $totalCarrinho; ?>
                            </span>
                        </a>
                    </li>

                    <?php if(utilizadorLogado()): ?>

                        <?php if(utilizadorAdmin()): ?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo caminhoProjeto('admin/admin.php'); ?>">Admin</a></li>
                        <?php endif; ?>

                        <!--
                            MENU DE CONTA DO UTILIZADOR

                            Mantemos o mesmo estilo leve usado para Login/Registar:
                            Perfil em cima, uma linha de separação e Sair em baixo.

                            Esta organização ajuda a navbar a ficar mais limpa e deixa claro
                            que Perfil e Sair pertencem à conta que está autenticada.
                        -->
                        <li class="nav-item auth-menu-item">
                            <div class="auth-mini-box">
                                <a class="auth-mini-link auth-mini-profile" href="<?php echo caminhoProjeto('profile.php'); ?>">Perfil</a>
                                <span class="auth-mini-line"></span>
                                <a class="auth-mini-link auth-mini-logout" href="<?php echo caminhoProjeto('logout.php'); ?>">Sair</a>
                            </div>
                        </li>

                    <?php else: ?>
                        <li class="nav-item auth-menu-item">
                            <div class="auth-mini-box">
                                <a class="auth-mini-link auth-mini-login" href="<?php echo caminhoProjeto('login.php'); ?>">Login</a>
                                <span class="auth-mini-line"></span>
                                <a class="auth-mini-link auth-mini-register" href="<?php echo caminhoProjeto('register.php'); ?>">Registar</a>
                            </div>
                        </li>
                    <?php endif; ?>

                </ul>

            </div>

        </div>

    </nav>

</header>
