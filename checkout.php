<?php
/*
    FICHEIRO: checkout.php

    OBJETIVO:
    Finalizar a compra do utilizador.

    Para finalizar a compra, o utilizador precisa estar autenticado.
    Ao confirmar, gravamos a encomenda nas tabelas orders e order_items.
*/

session_start();
include "includes/auth.php";
include "config/database.php";
include "includes/functions.php";

$carrinho = $_SESSION["cart"] ?? [];
$produtosCarrinho = [];
$total = 0;
$mensagem = "";
$tipoMensagem = "";
$userId = (int)$_SESSION["user_id"];

/*
    Buscamos a morada guardada no perfil do utilizador.
    No checkout, o utilizador confirma ou corrige essa morada antes de comprar.
*/
$sqlUser = "SELECT address, city, postal_code, country FROM users WHERE id = ? LIMIT 1";
$stmtUser = mysqli_prepare($conexao, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$resultadoUser = mysqli_stmt_get_result($stmtUser);
$dadosUtilizador = mysqli_fetch_assoc($resultadoUser) ?: [];

$address = trim($dadosUtilizador["address"] ?? "");
$city = trim($dadosUtilizador["city"] ?? "");
$postalCode = trim($dadosUtilizador["postal_code"] ?? "");
$country = trim($dadosUtilizador["country"] ?? "Portugal");
$deliveryNotes = "";

// Buscamos os produtos do carrinho para revisão.
if(!empty($carrinho)){

    $ids = array_keys($carrinho);
    $idsSeguros = array_map("intval", $ids);
    $idsTexto = implode(",", $idsSeguros);

    $sql = "SELECT id, name, price, stock FROM products WHERE id IN ($idsTexto)";
    $resultado = mysqli_query($conexao, $sql);

    while($produto = mysqli_fetch_assoc($resultado)){

        $quantidade = $carrinho[$produto["id"]];

        if($quantidade > $produto["stock"]){
            $quantidade = $produto["stock"];
        }

        $subtotal = $produto["price"] * $quantidade;
        $total += $subtotal;

        $produto["quantity"] = $quantidade;
        $produto["subtotal"] = $subtotal;

        $produtosCarrinho[] = $produto;
    }
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    // Recebemos a morada confirmada no checkout.
    $address = trim($_POST["address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $postalCode = trim($_POST["postal_code"] ?? "");
    $country = trim($_POST["country"] ?? "Portugal");
    $deliveryNotes = trim($_POST["delivery_notes"] ?? "");
    $mapsUrl = gerarLinkGoogleMaps($address, $city, $postalCode, $country);

    if(empty($produtosCarrinho)){
        $mensagem = "O carrinho está vazio.";
        $tipoMensagem = "erro";
    } elseif($address === "" || $city === "" || $postalCode === "" || $country === ""){
        $mensagem = "Confirme a morada completa antes de finalizar a compra.";
        $tipoMensagem = "erro";
    } else {

        mysqli_begin_transaction($conexao);

        try{

            // Atualizamos a morada do utilizador com a última morada confirmada.
            $sqlUpdateUser = "UPDATE users SET address = ?, city = ?, postal_code = ?, country = ? WHERE id = ?";
            $stmtUpdateUser = mysqli_prepare($conexao, $sqlUpdateUser);
            mysqli_stmt_bind_param($stmtUpdateUser, "ssssi", $address, $city, $postalCode, $country, $userId);
            mysqli_stmt_execute($stmtUpdateUser);

            // Criamos a encomenda principal com a morada confirmada no momento da compra.
            $sqlOrder = "INSERT INTO orders (user_id, total, status, delivery_address, delivery_city, delivery_postal_code, delivery_country, delivery_maps_url, delivery_notes) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?)";
            $stmtOrder = mysqli_prepare($conexao, $sqlOrder);
            mysqli_stmt_bind_param($stmtOrder, "idssssss", $userId, $total, $address, $city, $postalCode, $country, $mapsUrl, $deliveryNotes);
            mysqli_stmt_execute($stmtOrder);

            $orderId = mysqli_insert_id($conexao);

            // Inserimos cada produto dentro da tabela order_items.
            foreach($produtosCarrinho as $produto){

                $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmtItem = mysqli_prepare($conexao, $sqlItem);
                mysqli_stmt_bind_param($stmtItem, "iiid", $orderId, $produto["id"], $produto["quantity"], $produto["price"]);
                mysqli_stmt_execute($stmtItem);

                // Atualizamos o stock do produto.
                $sqlStock = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmtStock = mysqli_prepare($conexao, $sqlStock);
                mysqli_stmt_bind_param($stmtStock, "ii", $produto["quantity"], $produto["id"]);
                mysqli_stmt_execute($stmtStock);
            }

            mysqli_commit($conexao);

            // Depois da compra, limpamos o carrinho.
            unset($_SESSION["cart"]);

            header("Location: profile.php");
            exit;

        } catch(Exception $erro){

            mysqli_rollback($conexao);
            $mensagem = "Erro ao finalizar compra.";
            $tipoMensagem = "erro";
        }
    }
}

$pageTitle = "Seu Jorge | Checkout";
$pageDescription = "Finalizar compra na loja oficial de Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- CHECKOUT -->

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Checkout</h1>
            <p>Confirme os produtos e a morada de entrega antes de finalizar a compra.</p>

            <?php if($mensagem !== ""): ?>
                <div class="auth-message <?php echo $tipoMensagem; ?>">
                    <?php echo limparTexto($mensagem); ?>
                </div>
            <?php endif; ?>

            <?php if(empty($produtosCarrinho)): ?>

                <p class="empty-text">O carrinho está vazio.</p>
                <a href="loja.php" class="site-btn">Voltar para a Loja</a>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="site-table">

                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($produtosCarrinho as $produto): ?>
                                <tr>
                                    <td><?php echo limparTexto($produto["name"]); ?></td>
                                    <td><?php echo (int)$produto["quantity"]; ?></td>
                                    <td><?php echo formatarPreco($produto["subtotal"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>

                <h2 class="cart-total">Total: <?php echo formatarPreco($total); ?></h2>

                <form method="POST" action="checkout.php" class="form-validacao checkout-address-form">

                    <h2 class="form-subtitle">Confirmar Morada de Entrega</h2>
                    <p class="form-helper-text">Pode usar as sugestões automáticas ou escrever uma morada nova. Esta informação ficará gravada na encomenda.</p>

                    <label>Endereço</label>
                    <div class="address-autocomplete-wrap">
                        <input type="text" name="address" data-address-field="address" data-address-autocomplete="1" list="enderecosSugeridos" value="<?php echo limparTexto($address); ?>" placeholder="Ex: Rua de Santa Catarina, 100" required>
                    </div>

                    <datalist id="enderecosSugeridos">
                        <option value="Rua de Santa Catarina, Porto"></option>
                        <option value="Avenida dos Aliados, Porto"></option>
                        <option value="Rua Augusta, Lisboa"></option>
                        <option value="Avenida da Liberdade, Lisboa"></option>
                        <option value="Rua Ferreira Borges, Coimbra"></option>
                    </datalist>

                    <label>Cidade</label>
                    <input type="text" name="city" data-address-field="city" list="cidadesSugeridas" value="<?php echo limparTexto($city); ?>" placeholder="Ex: Porto" required>

                    <datalist id="cidadesSugeridas">
                        <option value="Porto"></option>
                        <option value="Lisboa"></option>
                        <option value="Coimbra"></option>
                        <option value="Braga"></option>
                        <option value="Aveiro"></option>
                    </datalist>

                    <label>Código Postal</label>
                    <input type="text" name="postal_code" data-address-field="postal_code" value="<?php echo limparTexto($postalCode); ?>" placeholder="Ex: 4000-000" required>

                    <label>País</label>
                    <input type="text" name="country" data-address-field="country" value="<?php echo limparTexto($country); ?>" required>

                    <label>Observações para entrega</label>
                    <textarea name="delivery_notes" placeholder="Ex: tocar à campainha, entregar na receção, horário preferencial..."><?php echo limparTexto($deliveryNotes); ?></textarea>

                    <?php $previewMaps = gerarLinkGoogleMaps($address, $city, $postalCode, $country); ?>
                    <?php if($previewMaps !== ""): ?>
                        <p class="maps-preview">
                            <strong>Pré-visualização:</strong>
                            <a href="<?php echo limparTexto($previewMaps); ?>" target="_blank" rel="noopener noreferrer">Ver morada no Google Maps</a>
                        </p>
                    <?php endif; ?>

                    <button type="submit" class="site-btn">Confirmar Compra</button>
                </form>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>
