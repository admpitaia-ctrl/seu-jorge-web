<?php
/*
    FICHEIRO: admin.php

    OBJETIVO:
    Criar a área de administração do website.

    Esta página só pode ser acedida por utilizadores com role admin.
    Aqui o administrador consegue visualizar utilizadores, produtos e encomendas.
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

// Contadores gerais do dashboard.
$totalUsers = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) AS total FROM users"))["total"];
$totalProducts = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) AS total FROM products"))["total"];
$totalOrders = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) AS total FROM orders"))["total"];

$ultimasEncomendas = mysqli_query($conexao, "SELECT
    o.id,
    o.order_date,
    o.status,
    o.total,
    o.delivery_address,
    o.delivery_city,
    o.delivery_postal_code,
    o.delivery_country,
    o.delivery_maps_url,
    u.username,
    GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS produtos
FROM orders o
INNER JOIN users u ON u.id = o.user_id
LEFT JOIN order_items oi ON oi.order_id = o.id
LEFT JOIN products p ON p.id = oi.product_id
GROUP BY o.id, o.order_date, o.status, o.total, o.delivery_address, o.delivery_city, o.delivery_postal_code, o.delivery_country, o.delivery_maps_url, u.username
ORDER BY o.order_date DESC
LIMIT 5");

$pageTitle = "Seu Jorge | Administração";
$pageDescription = "Área de administração da loja oficial de Seu Jorge";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>

<!-- ADMIN -->

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Administração</h1>
            <p>Gestão da loja oficial do Seu Jorge.</p>

            <div class="admin-top-actions">
                <a href="<?php echo caminhoProjeto('loja.php'); ?>" class="site-btn site-btn-secondary">Voltar para a loja</a>
                <a href="<?php echo caminhoProjeto('logout.php'); ?>" class="site-btn">Sair do Admin</a>
            </div>

            <div class="admin-grid">

                <div class="admin-card">
                    <span><?php echo $totalUsers; ?></span>
                    <p>Utilizadores</p>
                    <a href="admin_users.php">Gerir</a>
                </div>

                <div class="admin-card">
                    <span><?php echo $totalProducts; ?></span>
                    <p>Produtos</p>
                    <a href="admin_products.php">Gerir</a>
                </div>

                <div class="admin-card">
                    <span><?php echo $totalOrders; ?></span>
                    <p>Encomendas</p>
                    <a href="admin_orders.php">Gerir</a>
                </div>

            </div>

            <h2>Últimas Encomendas</h2>

            <div class="table-responsive">

                <table class="site-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilizador</th>
                            <th>Data</th>
                            <th>Produtos</th>
                            <th>Morada</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($encomenda = mysqli_fetch_assoc($ultimasEncomendas)): ?>
                            <tr>
                                <td>#<?php echo $encomenda["id"]; ?></td>
                                <td><?php echo limparTexto($encomenda["username"]); ?></td>
                                <td><?php echo limparTexto($encomenda["order_date"]); ?></td>
                                <td><?php echo limparTexto($encomenda["produtos"] ?: "Sem produtos registados"); ?></td>
                                <td>
                                    <?php
                                        $moradaResumo = montarMoradaCompleta(
                                            $encomenda["delivery_address"],
                                            $encomenda["delivery_city"],
                                            $encomenda["delivery_postal_code"],
                                            $encomenda["delivery_country"]
                                        );
                                        $mapsResumo = $encomenda["delivery_maps_url"] ?: gerarLinkGoogleMaps(
                                            $encomenda["delivery_address"],
                                            $encomenda["delivery_city"],
                                            $encomenda["delivery_postal_code"],
                                            $encomenda["delivery_country"]
                                        );
                                    ?>
                                    <?php if($moradaResumo !== ""): ?>
                                        <a class="map-link" href="<?php echo limparTexto($mapsResumo); ?>" target="_blank" rel="noopener noreferrer">Abrir Maps</a>
                                    <?php else: ?>
                                        Não registada
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-pill <?php echo classeEstadoEncomenda($encomenda["status"]); ?>"><?php echo traduzirEstadoEncomenda($encomenda["status"]); ?></span></td>
                                <td><?php echo formatarPreco($encomenda["total"]); ?></td>
                                <td><a class="edit-link" href="admin_order_details.php?id=<?php echo (int)$encomenda["id"]; ?>">Detalhes</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>
