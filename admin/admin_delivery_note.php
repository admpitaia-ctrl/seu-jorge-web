<?php
/*
    FICHEIRO: admin_delivery_note.php

    OBJETIVO:
    Criar uma guia de entrega imprimível para o transportador.

    Esta página foi criada para que o administrador consiga abrir a encomenda,
    imprimir ou guardar como PDF pelo navegador e entregar o documento ao
    transportador.
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

$sqlItens = "SELECT oi.quantity, oi.price, p.name
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY p.name ASC";
$stmtItens = mysqli_prepare($conexao, $sqlItens);
mysqli_stmt_bind_param($stmtItens, "i", $orderId);
mysqli_stmt_execute($stmtItens);
$itens = mysqli_stmt_get_result($stmtItens);

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
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Entrega #<?php echo (int)$encomenda["id"]; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111;
            background: #f4f4f4;
            margin: 0;
            padding: 24px;
        }

        .delivery-sheet {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            padding: 32px;
            border: 1px solid #ddd;
        }

        .delivery-header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 2px solid #111;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }

        h1, h2 {
            margin: 0 0 10px;
        }

        h1 {
            font-size: 26px;
        }

        h2 {
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
            margin-top: 24px;
        }

        p {
            margin: 6px 0;
            line-height: 1.45;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-top: 48px;
        }

        .signature-line {
            border-top: 1px solid #111;
            padding-top: 8px;
            text-align: center;
        }

        .screen-actions {
            max-width: 850px;
            margin: 0 auto 18px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .screen-actions button,
        .screen-actions a {
            border: 0;
            background: #111;
            color: #fff;
            padding: 10px 14px;
            text-decoration: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .delivery-map-btn {
            display: inline-block;
            background: #111;
            color: #fff;
            padding: 9px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 6px;
        }

        .driver-whatsapp-box {
            max-width: 850px;
            margin: 0 auto 18px;
            background: #fff;
            border: 1px solid #ddd;
            padding: 14px;
        }

        .driver-whatsapp-box form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .driver-whatsapp-box label {
            font-weight: bold;
        }

        .driver-whatsapp-box span {
            background: #eee;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .driver-whatsapp-box input {
            flex: 1;
            min-width: 180px;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 5px;
        }

        .driver-whatsapp-box button {
            border: 0;
            background: #198754;
            color: #fff;
            padding: 10px 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .delivery-sheet {
                max-width: none;
                border: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="screen-actions no-print">
        <a href="admin_orders.php">Voltar</a>
        <button type="button" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    <div class="driver-whatsapp-box no-print">
        <form method="POST" action="admin_send_driver_whatsapp.php" target="_blank">
            <input type="hidden" name="order_id" value="<?php echo (int)$encomenda["id"]; ?>">
            <label for="driver_phone_note">Enviar ao motorista:</label>
            <span>+351</span>
            <input type="tel" id="driver_phone_note" name="driver_phone" placeholder="912345678" pattern="[0-9 ]{9,15}" required>
            <button type="submit">Enviar guia por WhatsApp</button>
        </form>
    </div>

    <main class="delivery-sheet">

        <div class="delivery-header">
            <div>
                <h1>Guia de Entrega</h1>
                <p><strong>Loja:</strong> Website Seu Jorge</p>
                <p><strong>Encomenda:</strong> #<?php echo (int)$encomenda["id"]; ?></p>
            </div>
            <div>
                <p><strong>Data da encomenda:</strong> <?php echo limparTexto($encomenda["order_date"]); ?></p>
                <p><strong>Estado:</strong> <?php echo traduzirEstadoEncomenda($encomenda["status"]); ?></p>
                <p><strong>Total:</strong> <?php echo formatarPreco($encomenda["total"]); ?></p>
            </div>
        </div>

        <section>
            <h2>Dados do cliente</h2>
            <p><strong>Nome de utilizador:</strong> <?php echo limparTexto($encomenda["username"]); ?></p>
            <p><strong>Email:</strong> <?php echo limparTexto($encomenda["email"]); ?></p>
        </section>

        <section>
            <h2>Morada de entrega</h2>
            <p><?php echo limparTexto($moradaCompleta ?: "Morada não registada."); ?></p>
            <?php if($mapsUrl !== ""): ?>
                <p class="no-print"><a class="delivery-map-btn" href="<?php echo limparTexto($mapsUrl); ?>" target="_blank" rel="noopener noreferrer">Abrir localização no Google Maps</a></p>
            <?php endif; ?>
            <?php if(trim($encomenda["delivery_notes"] ?? "") !== ""): ?>
                <p><strong>Observações:</strong> <?php echo limparTexto($encomenda["delivery_notes"]); ?></p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Produtos para entrega</h2>
            <table>
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
                        <?php $subtotal = $item["quantity"] * $item["price"]; ?>
                        <tr>
                            <td><?php echo limparTexto($item["name"]); ?></td>
                            <td><?php echo (int)$item["quantity"]; ?></td>
                            <td><?php echo formatarPreco($item["price"]); ?></td>
                            <td><?php echo formatarPreco($subtotal); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Confirmação da entrega</h2>
            <p>Este documento pode acompanhar a encomenda. Após a entrega, o cliente também pode confirmar a receção pela área da sua encomenda no website.</p>
            <div class="signature-grid">
                <div class="signature-line">Assinatura do transportador</div>
                <div class="signature-line">Assinatura do cliente</div>
            </div>
        </section>

    </main>

</body>
</html>
