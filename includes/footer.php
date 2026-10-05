<?php
/*
    FICHEIRO: includes/footer.php

    OBJETIVO:
    Guardar o rodapé do site e os scripts finais.

    O footer é repetido em várias páginas, por isso fica separado
    para manter o projeto mais limpo e profissional.
*/
?>

<!-- FOOTER -->

<footer class="footer">

    <div class="container text-center">

        <div class="footer-logo">
            <img src="<?php echo caminhoProjeto('picture/Logo/logo-seu-jorge.png'); ?>" alt="Seu Jorge Logo" loading="lazy">
        </div>

        <div class="footer-social">
            <a href="#"><i class="fa-brands fa-spotify"></i></a>
            <a href="#"><i class="fa-brands fa-youtube"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.imdb.com/pt/name/nm1179580/?ref_=nv_sr_srsg_0" target="_blank">
                <i class="fa-brands fa-imdb"></i>
            </a>
        </div>

        <p class="footer-copy">© 2025 Seu Jorge</p>

    </div>

</footer>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS -->
<script src="<?php echo caminhoProjeto('js/script.js'); ?>"></script>
<script src="<?php echo caminhoProjeto('js/address-autocomplete.js'); ?>"></script>

</body>
</html>
