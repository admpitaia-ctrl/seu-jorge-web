<?php
/*
    FICHEIRO: cart.php

    OBJETIVO:
    Mostrar o carrinho de compras do utilizador.

    O carrinho é guardado em sessões PHP e permite alterar quantidades,
    remover produtos e avançar para o checkout.
*/

session_start();
include "config/database.php";
include "includes/functions.php";

$carrinho = $_SESSION["cart"] ?? [];
$produtosCarrinho = [];
$total = 0;

if(!empty($carrinho)){

    $ids = array_keys($carrinho);
    $idsSeguros = array_map("intval", $ids);
    $idsTexto = implode(",", $idsSeguros);

    $sql = "SELECT id, name, price, stock, image FROM products WHERE id IN ($idsTexto)";
    $resultado = mysqli_query($conexao, $sql);

    while($produto = mysqli_fetch_assoc($resultado)){

        $quantidade = $carrinho[$produto["id"]];
        $subtotal = $produto["price"] * $quantidade;
        $total += $subtotal;

        $produto["quantity"] = $quantidade;
        $produto["subtotal"] = $subtotal;

        $produtosCarrinho[] = $produto;
    }
}

$pageTitle = "Seu Jorge | Carrinho";
$pageDescription = "Carrinho de compras da loja oficial de Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- CARRINHO -->

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Carrinho</h1>
            <p>Revise os produtos antes de finalizar a compra.</p>

            <?php if(empty($produtosCarrinho)): ?>

                <p class="empty-text">O carrinho está vazio.</p>
                <a href="loja.php" class="site-btn">Voltar para a Loja</a>

            <?php else: ?>

                <form method="POST" action="actions/update_cart.php" id="cart-auto-form">

                    <div class="table-responsive">

                        <table class="site-table">

                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach($produtosCarrinho as $produto): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo caminhoProjeto(limparTexto($produto["image"])); ?>" alt="<?php echo limparTexto($produto["name"]); ?>" class="cart-img">
                                            <?php echo limparTexto($produto["name"]); ?>
                                        </td>
                                        <td><?php echo formatarPreco($produto["price"]); ?></td>
                                        <td>
                                            <input type="number" name="quantities[<?php echo (int)$produto["id"]; ?>]" value="<?php echo (int)$produto["quantity"]; ?>" min="1" max="<?php echo (int)$produto["stock"]; ?>" class="quantity-input cart-auto-quantity" data-product-id="<?php echo (int)$produto["id"]; ?>" required>
                                        </td>
                                        <td class="cart-subtotal" data-product-id="<?php echo (int)$produto["id"]; ?>"><?php echo formatarPreco($produto["subtotal"]); ?></td>
                                        <td>
                                            <a href="actions/remove_from_cart.php?id=<?php echo (int)$produto["id"]; ?>" class="remove-link">Remover</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <p id="cart-auto-message" class="cart-auto-message" aria-live="polite"></p>

                    <h2 class="cart-total">Total: <span id="cart-total-value"><?php echo formatarPreco($total); ?></span></h2>

                    <div class="profile-actions">
                        <noscript><button type="submit" class="site-btn site-btn-secondary">Atualizar Carrinho</button></noscript>
                        <a href="checkout.php" class="site-btn">Finalizar Compra</a>
                    </div>

                </form>

            <?php endif; ?>

        </div>

    </div>

</section>


<script>
/*
    CARRINHO COM ATUALIZAÇÃO AUTOMÁTICA

    Ao alterar a quantidade, o carrinho é atualizado por AJAX.
    Assim, o utilizador não precisa clicar num botão para recalcular o total.
*/

const cartAutoForm = document.getElementById("cart-auto-form");
const cartAutoMessage = document.getElementById("cart-auto-message");
const cartTotalValue = document.getElementById("cart-total-value");
const cartBadgeCartPage = document.getElementById("cart-badge");
let cartUpdateTimer = null;

function mostrarMensagemCarrinho(mensagem){
    if(!cartAutoMessage) return;

    cartAutoMessage.textContent = mensagem;
    cartAutoMessage.classList.add("cart-auto-message-active");

    setTimeout(function(){
        cartAutoMessage.classList.remove("cart-auto-message-active");
    }, 1600);
}

function atualizarCarrinhoAutomaticamente(){
    if(!cartAutoForm) return;

    const dados = new FormData(cartAutoForm);
    dados.set("ajax", "1");

    fetch(cartAutoForm.action, {
        method: "POST",
        body: dados
    })
    .then(function(resposta){
        return resposta.json();
    })
    .then(function(dadosResposta){
        if(!dadosResposta.success) return;

        if(cartTotalValue){
            cartTotalValue.textContent = dadosResposta.total;
        }

        if(cartBadgeCartPage && typeof dadosResposta.cart_count !== "undefined"){
            cartBadgeCartPage.textContent = dadosResposta.cart_count;
            if(dadosResposta.cart_count > 0){
                cartBadgeCartPage.classList.remove("cart-badge-empty");
            } else {
                cartBadgeCartPage.classList.add("cart-badge-empty");
            }
        }

        if(dadosResposta.items){
            Object.keys(dadosResposta.items).forEach(function(productId){
                const subtotalCell = document.querySelector(".cart-subtotal[data-product-id='" + productId + "']");
                const input = document.querySelector(".cart-auto-quantity[data-product-id='" + productId + "']");

                if(subtotalCell){
                    subtotalCell.textContent = dadosResposta.items[productId].subtotal;
                }

                if(input){
                    input.value = dadosResposta.items[productId].quantity;
                }
            });
        }

        mostrarMensagemCarrinho("Carrinho atualizado automaticamente.");
    })
    .catch(function(){
        mostrarMensagemCarrinho("Não foi possível atualizar automaticamente.");
    });
}

if(cartAutoForm){
    cartAutoForm.querySelectorAll(".cart-auto-quantity").forEach(function(input){
        input.addEventListener("input", function(){
            clearTimeout(cartUpdateTimer);
            cartUpdateTimer = setTimeout(atualizarCarrinhoAutomaticamente, 450);
        });

        input.addEventListener("change", atualizarCarrinhoAutomaticamente);
    });
}
</script>

<?php include "includes/footer.php"; ?>
