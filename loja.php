<?php
/*
    FICHEIRO: loja.php

    OBJETIVO:
    Mostrar os produtos da loja oficial do Seu Jorge.

    Diferente da versão antiga em HTML/JavaScript, esta página busca
    os produtos diretamente na base de dados MySQL.
*/

session_start();
include "config/database.php";
include "includes/functions.php";

// Buscamos apenas produtos com stock disponível.
$sqlProdutos = "SELECT id, name, description, price, stock, image, category FROM products WHERE stock > 0 ORDER BY id ASC";
$produtos = mysqli_query($conexao, $sqlProdutos);

// Buscamos as categorias para montar o filtro visual.
$sqlCategorias = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> '' ORDER BY category ASC";
$categorias = mysqli_query($conexao, $sqlCategorias);

$pageTitle = "Seu Jorge | Loja Oficial";
$pageDescription = "Loja oficial do artista Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- LOJA -->

<section class="loja-section">

    <div class="container">

        <h1 class="loja-title">Loja Oficial</h1>
        <div class="loja-line"></div>

        <?php if(utilizadorAdmin()): ?>
            <div class="text-center mb-4">
                <a href="admin/admin_product_form.php" class="site-btn">Adicionar Produto</a>
                <a href="admin/admin_products.php" class="site-btn site-btn-secondary">Gerir Produtos</a>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET["msg"]) && $_GET["msg"] === "adicionado"): ?>
            <div class="auth-message sucesso">Produto adicionado ao carrinho.</div>
        <?php endif; ?>

        <?php if(isset($_GET["erro"])): ?>
            <div class="auth-message erro">Não foi possível adicionar este produto ao carrinho.</div>
        <?php endif; ?>

        <div id="loja-toast" class="loja-toast" aria-live="polite"></div>

        <!-- FILTRO -->

        <div class="loja-filtro">
            <select id="filtro-categoria-php">
                <option value="">Todas as categorias</option>
                <?php while($categoria = mysqli_fetch_assoc($categorias)): ?>
                    <option value="<?php echo limparTexto($categoria["category"]); ?>">
                        <?php echo limparTexto($categoria["category"]); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- BUSCA -->
        <input type="text" id="busca-produto-php" placeholder="🔍 Procurar produto..." class="form-control mb-4">

        <!-- PRODUTOS -->

        <div class="row g-4" id="lista-produtos-php">

            <?php while($produto = mysqli_fetch_assoc($produtos)): ?>

                <div class="col-md-4 produto-php" id="produto-<?php echo (int)$produto["id"]; ?>" data-categoria="<?php echo limparTexto($produto["category"]); ?>" data-nome="<?php echo strtolower(limparTexto($produto["name"])); ?>">

                    <div class="card p-3 h-100 produto-card-php"
                         role="button"
                         tabindex="0"
                         data-name="<?php echo limparTexto($produto["name"]); ?>"
                         data-description="<?php echo limparTexto($produto["description"]); ?>"
                         data-price="<?php echo formatarPreco($produto["price"]); ?>"
                         data-image="<?php echo caminhoProjeto(limparTexto($produto["image"])); ?>"
                         data-stock="<?php echo (int)$produto["stock"]; ?>"
                         data-category="<?php echo limparTexto($produto["category"]); ?>">

                        <img src="<?php echo caminhoProjeto(limparTexto($produto["image"])); ?>" alt="<?php echo limparTexto($produto["name"]); ?>" class="product-img">

                        <h2><?php echo limparTexto($produto["name"]); ?></h2>

                        <p><?php echo limparTexto($produto["description"]); ?></p>
                        <p><strong><?php echo formatarPreco($produto["price"]); ?></strong></p>
                        <p>Stock: <?php echo (int)$produto["stock"]; ?></p>

                        <p class="product-card-hint">Clique no card para ver detalhes</p>

                        <?php if(utilizadorAdmin()): ?>
                            <div class="admin-product-actions mb-3">
                                <a class="edit-link" href="admin/admin_product_form.php?id=<?php echo (int)$produto["id"]; ?>">Editar</a>
                                <a class="remove-link" href="admin/admin_products.php?delete=<?php echo (int)$produto["id"]; ?>" onclick="return confirm('Deseja remover este produto?')">Remover</a>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="actions/add_to_cart.php" class="mt-auto add-cart-form">

                            <input type="hidden" name="product_id" value="<?php echo (int)$produto["id"]; ?>">
                            <input type="hidden" name="ajax" value="1">

                            <div class="d-flex gap-2 justify-content-center align-items-center">

                                <input type="number" name="quantity" value="1" min="1" max="<?php echo (int)$produto["stock"]; ?>" class="quantity-input" required>

                                <button type="submit" class="btn btn-success btn-add">Adicionar 🛒</button>

                            </div>

                        </form>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

        <!-- LIGHTBOX DE DETALHES DO PRODUTO -->
        <div id="lightbox" class="lightbox product-lightbox-php" aria-hidden="true">
            <div class="lightbox-content">

                <button type="button" class="close-btn" aria-label="Fechar detalhes">&times;</button>

                <h2 id="lightbox-title"></h2>

                <img id="lightbox-image" src="" alt="Produto selecionado">

                <p id="lightbox-description"></p>
                <p id="lightbox-price"></p>
                <p id="lightbox-extra" class="lightbox-extra"></p>

            </div>
        </div>

        <div class="text-center mt-5">
            <a href="cart.php" class="site-btn">Ver Carrinho</a>
        </div>

    </div>

</section>

<script>
/*
    FILTRO DA LOJA EM PHP

    Este pequeno script mantém a experiência da versão antiga:
    permite filtrar por categoria e pesquisar pelo nome do produto.
*/

const filtroPhp = document.getElementById("filtro-categoria-php");
const buscaPhp = document.getElementById("busca-produto-php");
const produtosPhp = document.querySelectorAll(".produto-php");
const addCartForms = document.querySelectorAll(".add-cart-form");
const lojaToast = document.getElementById("loja-toast");
const cartBadge = document.getElementById("cart-badge");
const cardsProdutoPhp = document.querySelectorAll(".produto-card-php");
const lightboxPhp = document.getElementById("lightbox");
const lightboxTitlePhp = document.getElementById("lightbox-title");
const lightboxImagePhp = document.getElementById("lightbox-image");
const lightboxDescriptionPhp = document.getElementById("lightbox-description");
const lightboxPricePhp = document.getElementById("lightbox-price");
const lightboxExtraPhp = document.getElementById("lightbox-extra");
const closeLightboxPhp = document.querySelector(".product-lightbox-php .close-btn");

function filtrarProdutosPhp(){

    const categoria = filtroPhp.value;
    const busca = buscaPhp.value.toLowerCase();

    produtosPhp.forEach(function(produto){

        const produtoCategoria = produto.dataset.categoria;
        const produtoNome = produto.dataset.nome;

        const categoriaOk = categoria === "" || produtoCategoria === categoria;
        const buscaOk = produtoNome.includes(busca);

        produto.style.display = categoriaOk && buscaOk ? "block" : "none";

    });
}

if(filtroPhp && buscaPhp){
    filtroPhp.addEventListener("change", filtrarProdutosPhp);
    buscaPhp.addEventListener("input", filtrarProdutosPhp);
}

/*
    DETALHES DO PRODUTO AO CLICAR NO CARD

    Recupera o comportamento visual da versão original da loja: o utilizador
    pode clicar no card do produto e abrir uma janela com as informações
    apenas daquele item, sem sair da página.
*/

function abrirDetalhesProduto(card){

    if(!lightboxPhp) return;

    lightboxTitlePhp.textContent = card.dataset.name || "Produto";
    lightboxImagePhp.src = card.dataset.image || "";
    lightboxImagePhp.alt = card.dataset.name || "Produto selecionado";
    lightboxDescriptionPhp.textContent = card.dataset.description || "";
    lightboxPricePhp.textContent = card.dataset.price || "";

    if(lightboxExtraPhp){
        lightboxExtraPhp.textContent = "Categoria: " + (card.dataset.category || "Sem categoria") + " | Stock: " + (card.dataset.stock || "0");
    }

    lightboxPhp.style.display = "flex";
    lightboxPhp.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
}

function fecharDetalhesProduto(){
    if(!lightboxPhp) return;

    lightboxPhp.style.display = "none";
    lightboxPhp.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "auto";
}

cardsProdutoPhp.forEach(function(card){

    card.addEventListener("click", function(event){

        /*
            Se o clique for num botão, input, link ou formulário,
            o sistema mantém a ação normal: adicionar ao carrinho,
            editar produto ou remover produto.
        */
        if(event.target.closest("button, input, a, form, select, textarea")){
            return;
        }

        abrirDetalhesProduto(card);

    });

    card.addEventListener("keydown", function(event){
        if(event.key === "Enter" || event.key === " "){
            event.preventDefault();
            abrirDetalhesProduto(card);
        }
    });

});

if(closeLightboxPhp){
    closeLightboxPhp.addEventListener("click", fecharDetalhesProduto);
}

if(lightboxPhp){
    lightboxPhp.addEventListener("click", function(event){
        if(event.target === lightboxPhp){
            fecharDetalhesProduto();
        }
    });
}

document.addEventListener("keydown", function(event){
    if(event.key === "Escape"){
        fecharDetalhesProduto();
    }
});


/*
    ADICIONAR AO CARRINHO SEM RECARREGAR A PÁGINA

    Antes, ao clicar em "Adicionar", o formulário enviava para o PHP,
    a página recarregava e voltava ao topo. Agora usamos fetch/AJAX:
    - o produto entra no carrinho;
    - o utilizador continua exatamente no mesmo ponto da loja;
    - aparece uma mensagem pequena de confirmação;
    - a badge do carrinho é atualizada.
*/

function mostrarToastLoja(mensagem, sucesso = true){

    if(!lojaToast) return;

    lojaToast.textContent = mensagem;
    lojaToast.classList.remove("loja-toast-success", "loja-toast-error", "loja-toast-active");
    lojaToast.classList.add(sucesso ? "loja-toast-success" : "loja-toast-error");

    setTimeout(function(){
        lojaToast.classList.add("loja-toast-active");
    }, 20);

    setTimeout(function(){
        lojaToast.classList.remove("loja-toast-active");
    }, 2600);

}

addCartForms.forEach(function(form){

    form.addEventListener("submit", function(event){

        event.preventDefault();

        const botao = form.querySelector("button[type='submit']");
        const textoOriginal = botao ? botao.textContent : "";
        const dados = new FormData(form);

        dados.set("ajax", "1");

        if(botao){
            botao.disabled = true;
            botao.textContent = "A adicionar...";
        }

        fetch(form.action, {
            method: "POST",
            body: dados
        })
        .then(function(resposta){
            return resposta.json();
        })
        .then(function(dadosResposta){

            mostrarToastLoja(dadosResposta.message || "Produto adicionado ao carrinho.", dadosResposta.success === true);

            if(cartBadge && typeof dadosResposta.cart_count !== "undefined"){
                cartBadge.textContent = dadosResposta.cart_count;
                cartBadge.classList.remove("cart-badge-empty");
            }

            if(botao){
                botao.textContent = dadosResposta.success ? "Adicionado ✓" : textoOriginal;
            }

            setTimeout(function(){
                if(botao){
                    botao.disabled = false;
                    botao.textContent = textoOriginal;
                }
            }, 900);

        })
        .catch(function(){

            mostrarToastLoja("Não foi possível adicionar agora. Tente novamente.", false);

            if(botao){
                botao.disabled = false;
                botao.textContent = textoOriginal;
            }

        });

    });

});

</script>

<?php include "includes/footer.php"; ?>
