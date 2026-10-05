<?php
/*
    FICHEIRO: profile.php

    OBJETIVO:
    Mostrar a área pessoal do utilizador autenticado.

    Esta página é protegida. Apenas utilizadores com sessão iniciada
    conseguem aceder ao perfil.
*/

session_start();
include "includes/auth.php";
include "config/database.php";
include "includes/functions.php";

$userId = $_SESSION["user_id"];

// Buscamos os dados do utilizador, incluindo a morada guardada.
$sqlUser = "SELECT username, email, address, city, postal_code, country FROM users WHERE id = ? LIMIT 1";
$stmtUser = mysqli_prepare($conexao, $sqlUser);
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$resultadoUser = mysqli_stmt_get_result($stmtUser);
$dadosUtilizador = mysqli_fetch_assoc($resultadoUser) ?: [];

// Buscamos as encomendas do utilizador logado.
$sql = "SELECT id, order_date, status, total, delivery_address, delivery_city, delivery_postal_code, delivery_country FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$encomendas = mysqli_stmt_get_result($stmt);

$pageTitle = "Seu Jorge | Perfil";
$pageDescription = "Perfil do utilizador na loja oficial de Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- PERFIL -->

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Perfil</h1>
            <p>Bem-vindo, <?php echo limparTexto($_SESSION["username"]); ?>.</p>

            <div class="profile-actions">
                <a href="loja.php" class="site-btn">Ir para a Loja</a>
                <a href="cart.php" class="site-btn site-btn-secondary">Ver Carrinho</a>
            </div>

            <?php
                $moradaPerfil = montarMoradaCompleta(
                    $dadosUtilizador["address"] ?? "",
                    $dadosUtilizador["city"] ?? "",
                    $dadosUtilizador["postal_code"] ?? "",
                    $dadosUtilizador["country"] ?? ""
                );
                $mapsPerfil = gerarLinkGoogleMaps(
                    $dadosUtilizador["address"] ?? "",
                    $dadosUtilizador["city"] ?? "",
                    $dadosUtilizador["postal_code"] ?? "",
                    $dadosUtilizador["country"] ?? ""
                );
            ?>

            <div class="order-summary-box">
                <h2>Morada Guardada</h2>
                <?php if($moradaPerfil !== ""): ?>
                    <p><?php echo limparTexto($moradaPerfil); ?></p>
                    <p><a class="map-link" href="<?php echo limparTexto($mapsPerfil); ?>" target="_blank" rel="noopener noreferrer">Ver morada no Google Maps</a></p>
                    <p class="form-helper-text">Pode confirmar ou alterar esta morada no checkout antes de finalizar uma compra.</p>
                <?php else: ?>
                    <p class="empty-text">Ainda não existe morada guardada. Ela será pedida no checkout.</p>
                <?php endif; ?>
            </div>

            <h2>Minhas Encomendas</h2>

            <?php if(mysqli_num_rows($encomendas) === 0): ?>
                <p class="empty-text">Ainda não existem encomendas associadas à sua conta.</p>
            <?php else: ?>

                <div class="table-responsive">

                    <table class="site-table">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Morada</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while($encomenda = mysqli_fetch_assoc($encomendas)): ?>
                                <tr>
                                    <td>#<?php echo $encomenda["id"]; ?></td>
                                    <td><?php echo limparTexto($encomenda["order_date"]); ?></td>
                                    <td><span class="status-pill <?php echo classeEstadoEncomenda($encomenda["status"]); ?>"><?php echo traduzirEstadoEncomenda($encomenda["status"]); ?></span></td>
                                    <td><?php echo formatarPreco($encomenda["total"]); ?></td>
                                    <td>
                                        <?php
                                            $moradaEncomenda = montarMoradaCompleta(
                                                $encomenda["delivery_address"],
                                                $encomenda["delivery_city"],
                                                $encomenda["delivery_postal_code"],
                                                $encomenda["delivery_country"]
                                            );
                                        ?>
                                        <?php echo limparTexto($moradaEncomenda ?: "Não registada"); ?>
                                    </td>
                                    <td><a class="edit-link" href="order_details.php?id=<?php echo (int)$encomenda["id"]; ?>">Ver detalhes</a></td>
                                </tr>
                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>
