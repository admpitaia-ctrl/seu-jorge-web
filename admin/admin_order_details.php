<?php
/*
    FICHEIRO: admin_order_details.php

    OBJETIVO:
    Mostrar os detalhes completos de uma encomenda.

    Esta página melhora a área de administração porque permite ao admin
    ver quais produtos foram comprados, quantidade, preço unitário,
    subtotal e total final da encomenda.
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

$orderId = (int)($_GET["id"] ?? 0);

if($orderId <= 0){
    header("Location: admin_orders.php");
    exit;
}

/*
    Buscamos os dados principais da encomenda.
    Juntamos com a tabela users para mostrar quem fez a compra.
*/
$sqlOrder = "SELECT o.id, o.order_date, o.status, o.total,
                    o.delivery_address, o.delivery_city, o.delivery_postal_code,
                    o.delivery_country, o.delivery_maps_url, o.delivery_notes,
                    u.username, u.email
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             WHERE o.id = ?
             LIMIT 1";
$stmtOrder = mysqli_prepare($conexao, $sqlOrder);
mysqli_stmt_bind_param($stmtOrder, "i", $orderId);
mysqli_stmt_execute($stmtOrder);
$resultadoOrder = mysqli_stmt_get_result($stmtOrder);
$encomenda = mysqli_fetch_assoc($resultadoOrder);

if(!$encomenda){
    header("Location: admin_orders.php");
    exit;
}

/*
    Buscamos os produtos que pertencem a esta encomenda.
    A tabela order_items guarda a quantidade e o preço no momento da compra.
*/
$sqlItens = "SELECT oi.quantity, oi.price, p.name, p.image
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY p.name ASC";
$stmtItens = mysqli_prepare($conexao, $sqlItens);
mysqli_stmt_bind_param($stmtItens, "i", $orderId);
mysqli_stmt_execute($stmtItens);
$itens = mysqli_stmt_get_result($stmtItens);

$pageTitle = "Seu Jorge | Detalhes da Encomenda";
$pageDescription = "Detalhes completos da encomenda na área administrativa";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Detalhes da Encomenda #<?php echo (int)$encomenda["id"]; ?></h1>
            <p>Veja os produtos comprados, a morada e os dados do utilizador.</p>

            <?php if(($_GET["erro"] ?? "") === "telefone_motorista"): ?>
                <div class="auth-message erro">Informe um número de motorista válido. Pode escrever só os 9 dígitos, porque o sistema já adiciona +351.</div>
            <?php endif; ?>

            <div class="order-summary-box">
                <p><strong>Utilizador:</strong> <?php echo limparTexto($encomenda["username"]); ?></p>
                <p><strong>Email:</strong> <?php echo limparTexto($encomenda["email"]); ?></p>
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
                    <p><strong>Morada:</strong> <?php echo limparTexto($moradaCompleta); ?></p>
                    <p><a class="map-link" href="<?php echo limparTexto($mapsUrl); ?>" target="_blank" rel="noopener noreferrer">Abrir localização no Google Maps</a></p>
                <?php else: ?>
                    <p><strong>Morada:</strong> Não registada nesta encomenda.</p>
                <?php endif; ?>

                <?php if(trim($encomenda["delivery_notes"] ?? "") !== ""): ?>
                    <p><strong>Observações:</strong> <?php echo limparTexto($encomenda["delivery_notes"]); ?></p>
                <?php endif; ?>
            </div>

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

            <div class="driver-whatsapp-panel">
                <h2>Enviar guia ao motorista</h2>
                <p>Digite o número português do motorista. O sistema adiciona automaticamente o indicativo +351 e abre o WhatsApp com os dados da entrega.</p>
                <form method="POST" action="admin_send_driver_whatsapp.php" target="_blank" class="driver-whatsapp-form">
                    <input type="hidden" name="order_id" value="<?php echo (int)$encomenda["id"]; ?>">
                    <div class="phone-prefix">+351</div>
                    <input type="tel" name="driver_phone" placeholder="912345678" pattern="[0-9 ]{9,15}" required>
                    <button type="submit" class="site-btn">Enviar por WhatsApp</button>
                </form>
                <p class="small-note">Nota: o WhatsApp por link não anexa ficheiros automaticamente. A mensagem enviada inclui os dados da entrega, o link da morada no Google Maps e o link da guia imprimível.</p>
            </div>

            <div class="profile-actions">
                <a href="admin_orders.php" class="site-btn site-btn-secondary">Voltar para Encomendas</a>
                <a href="admin_delivery_note.php?id=<?php echo (int)$encomenda["id"]; ?>" target="_blank" class="site-btn">Gerar Guia PDF</a>
                <a href="admin.php" class="site-btn">Painel Admin</a>
            </div>

        </div>

    </div>

</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>
