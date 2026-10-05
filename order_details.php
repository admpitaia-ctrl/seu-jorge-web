<?php
/*
    FICHEIRO: order_details.php

    OBJETIVO:
    Mostrar ao utilizador normal os detalhes de uma encomenda própria.

    Esta página é protegida. O utilizador só consegue ver encomendas
    que pertencem à sua própria conta.
*/

session_start();
include "includes/auth.php";
include "config/database.php";
include "includes/functions.php";

$orderId = (int)($_GET["id"] ?? 0);
$userId = (int)$_SESSION["user_id"];

if($orderId <= 0){
    header("Location: profile.php");
    exit;
}

/*
    Buscamos a encomenda apenas se ela pertencer ao utilizador logado.
    Isto evita que um utilizador veja encomendas de outra conta.
*/
$sqlOrder = "SELECT id, order_date, status, total,
                    delivery_address, delivery_city, delivery_postal_code,
                    delivery_country, delivery_maps_url, delivery_notes
             FROM orders
             WHERE id = ? AND user_id = ?
             LIMIT 1";
$stmtOrder = mysqli_prepare($conexao, $sqlOrder);
mysqli_stmt_bind_param($stmtOrder, "ii", $orderId, $userId);
mysqli_stmt_execute($stmtOrder);
$resultadoOrder = mysqli_stmt_get_result($stmtOrder);
$encomenda = mysqli_fetch_assoc($resultadoOrder);

if(!$encomenda){
    header("Location: profile.php");
    exit;
}

/*
    CONFIRMAÇÃO DE ENTREGA PELO CLIENTE

    Quando o admin altera o estado para "A caminho", o utilizador pode confirmar
    que recebeu a encomenda. Isso muda o estado para "Entregue".
*/
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirm_delivery"])){

    if(normalizarEstadoEncomenda($encomenda["status"]) === "in_transit"){
        $novoEstado = "delivered";
        $sqlConfirmar = "UPDATE orders SET status = ? WHERE id = ? AND user_id = ?";
        $stmtConfirmar = mysqli_prepare($conexao, $sqlConfirmar);
        mysqli_stmt_bind_param($stmtConfirmar, "sii", $novoEstado, $orderId, $userId);
        mysqli_stmt_execute($stmtConfirmar);

        header("Location: order_details.php?id=" . $orderId . "&confirmado=1");
        exit;
    }
}

$sqlItens = "SELECT oi.quantity, oi.price, p.name, p.image
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY p.name ASC";
$stmtItens = mysqli_prepare($conexao, $sqlItens);
mysqli_stmt_bind_param($stmtItens, "i", $orderId);
mysqli_stmt_execute($stmtItens);
$itens = mysqli_stmt_get_result($stmtItens);

$pageTitle = "Seu Jorge | Detalhes da Minha Encomenda";
$pageDescription = "Detalhes de uma encomenda do utilizador";
include "includes/header.php";
include "includes/navbar.php";
?>

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Minha Encomenda #<?php echo (int)$encomenda["id"]; ?></h1>
            <p>Consulte os produtos comprados nesta encomenda.</p>

            <?php if(isset($_GET["confirmado"])): ?>
                <div class="auth-message sucesso">Entrega confirmada com sucesso. Obrigado!</div>
            <?php endif; ?>

            <div class="order-summary-box">
                <p><strong>Data:</strong> <?php echo limparTexto($encomenda["order_date"]); ?></p>
                <p><strong>Estado:</strong> <span class="status-pill <?php echo classeEstadoEncomenda($encomenda["status"]); ?>"><?php echo traduzirEstadoEncomenda($encomenda["status"]); ?></span></p>
                <p><strong>Total:</strong> <?php echo formatarPreco($encomenda["total"]); ?></p>

                <?php
                    $moradaCompleta = montarMoradaCompleta(
                        $encomenda["delivery_address"],
                        $encomenda["delivery_city"],
                        $encomenda["delivery_postal_code"],
                        $encomenda["delivery_country"]
                    );

                    $mapsUrl = $encomenda["delivery_maps_url"] ?: gerarLinkGoogleMaps(
                        $encomenda["delivery_address"],
                        $encomenda["delivery_city"],
                        $encomenda["delivery_postal_code"],
                        $encomenda["delivery_country"]
                    );
                ?>

                <?php if($moradaCompleta !== ""): ?>
                    <p><strong>Morada de entrega:</strong> <?php echo limparTexto($moradaCompleta); ?></p>
                    <p><a class="map-link" href="<?php echo limparTexto($mapsUrl); ?>" target="_blank" rel="noopener noreferrer">Ver no Google Maps</a></p>
                <?php endif; ?>

                <?php if(trim($encomenda["delivery_notes"] ?? "") !== ""): ?>
                    <p><strong>Observações:</strong> <?php echo limparTexto($encomenda["delivery_notes"]); ?></p>
                <?php endif; ?>
            </div>

            <?php $estadoAtual = normalizarEstadoEncomenda($encomenda["status"]); ?>
            <div class="delivery-steps">
                <div class="delivery-step <?php echo in_array($estadoAtual, ["pending", "in_transit", "delivered"], true) ? "active" : ""; ?>">
                    <span>1</span>
                    <p>Pendente</p>
                </div>
                <div class="delivery-step <?php echo in_array($estadoAtual, ["in_transit", "delivered"], true) ? "active" : ""; ?>">
                    <span>2</span>
                    <p>A caminho</p>
                </div>
                <div class="delivery-step <?php echo $estadoAtual === "delivered" ? "active" : ""; ?>">
                    <span>3</span>
                    <p>Entregue</p>
                </div>
            </div>

            <?php if($estadoAtual === "in_transit"): ?>
                <form method="POST" action="order_details.php?id=<?php echo (int)$encomenda["id"]; ?>" class="confirm-delivery-box">
                    <p>Recebeu esta encomenda?</p>
                    <input type="hidden" name="confirm_delivery" value="1">
                    <button type="submit" class="site-btn">Confirmar Receção</button>
                </form>
            <?php endif; ?>

            <div class="table-responsive">

                <table class="site-table">

                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($itens)): ?>
                            <?php $subtotal = $item["price"] * $item["quantity"]; ?>
                            <tr>
                                <td>
                                    <img src="<?php echo caminhoProjeto(limparTexto($item["image"])); ?>" alt="<?php echo limparTexto($item["name"]); ?>" class="cart-img">
                                    <?php echo limparTexto($item["name"]); ?>
                                </td>
                                <td><?php echo (int)$item["quantity"]; ?></td>
                                <td><?php echo formatarPreco($item["price"]); ?></td>
                                <td><?php echo formatarPreco($subtotal); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

            </div>

            <a href="profile.php" class="site-btn site-btn-secondary">Voltar para o Perfil</a>

        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>
