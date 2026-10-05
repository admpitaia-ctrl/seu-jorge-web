<?php
/*
    FICHEIRO: admin_orders.php

    OBJETIVO:
    Gerir as encomendas feitas na loja.

    O administrador consegue visualizar as encomendas, ver um resumo dos
    produtos comprados, consultar a morada de entrega no Google Maps, gerar
    uma guia de entrega para o transportador e alterar o estado da encomenda.

    MELHORIA DE EXPERIÊNCIA:
    O botão "Guardar" do estado da encomenda também funciona por AJAX.
    Assim, quando o admin altera o estado para Pendente, A caminho ou Entregue,
    a informação é guardada na base de dados sem recarregar a página.

    Se o JavaScript estiver desativado, o formulário continua funcionando
    pelo método tradicional, com redirecionamento para admin_orders.php.

    ESTADOS DA ENTREGA:
    - pending: Pendente
    - in_transit: A caminho
    - delivered: Entregue
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

$pedidoAjax = isset($_POST["ajax"]) && $_POST["ajax"] === "1";

/*
    Função auxiliar para responder em JSON quando o pedido vem por AJAX.
*/
function responderJsonAdminOrders($sucesso, $mensagem, $dados = []){
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array_merge([
        "success" => $sucesso,
        "message" => $mensagem
    ], $dados));
    exit;
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $id = (int)($_POST["id"] ?? 0);
    $status = normalizarEstadoEncomenda($_POST["status"] ?? "pending");

    if($id <= 0){
        if($pedidoAjax){
            responderJsonAdminOrders(false, "Encomenda inválida.");
        }

        header("Location: admin_orders.php");
        exit;
    }

    $sqlUpdate = "UPDATE orders SET status = ? WHERE id = ?";
    $stmtUpdate = mysqli_prepare($conexao, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "si", $status, $id);
    $executou = mysqli_stmt_execute($stmtUpdate);

    if($pedidoAjax){
        responderJsonAdminOrders($executou, $executou ? "Estado guardado com sucesso." : "Não foi possível guardar o estado.", [
            "order_id" => $id,
            "status" => $status,
            "status_label" => traduzirEstadoEncomenda($status),
            "status_class" => classeEstadoEncomenda($status)
        ]);
    }

    header("Location: admin_orders.php");
    exit;
}

/*
    Buscamos as encomendas com:
    - dados do utilizador;
    - morada de entrega;
    - resumo dos produtos da encomenda.
*/
$sqlOrders = "SELECT
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
              ORDER BY o.order_date DESC";

$orders = mysqli_query($conexao, $sqlOrders);
$estados = estadosEncomendaDisponiveis();

$pageTitle = "Seu Jorge | Gerir Encomendas";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Gerir Encomendas</h1>
            <p>Visualize produtos, morada de entrega, total, guia de transporte e estado das encomendas.</p>

            <p id="admin-orders-message" class="admin-ajax-message" aria-live="polite"></p>

            <div class="table-responsive">

                <table class="site-table admin-orders-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilizador</th>
                            <th>Produtos</th>
                            <th>Morada</th>
                            <th>Data</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($encomenda = mysqli_fetch_assoc($orders)): ?>
                            <?php
                                $estadoAtual = normalizarEstadoEncomenda($encomenda["status"]);
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
                            <tr id="order-row-<?php echo (int)$encomenda["id"]; ?>">
                                <td>#<?php echo (int)$encomenda["id"]; ?></td>
                                <td><?php echo limparTexto($encomenda["username"]); ?></td>
                                <td class="order-products-cell"><?php echo limparTexto($encomenda["produtos"] ?: "Sem produtos registados"); ?></td>
                                <td>
                                    <?php if($moradaCompleta !== ""): ?>
                                        <span class="address-text"><?php echo limparTexto($moradaCompleta); ?></span>
                                        <a class="map-link" href="<?php echo limparTexto($mapsUrl); ?>" target="_blank" rel="noopener noreferrer">Abrir Maps</a>
                                    <?php else: ?>
                                        <span class="empty-text">Morada não registada</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo limparTexto($encomenda["order_date"]); ?></td>
                                <td><?php echo formatarPreco($encomenda["total"]); ?></td>
                                <td>
                                    <form method="POST" action="admin_orders.php" class="inline-form order-status-form" data-order-id="<?php echo (int)$encomenda["id"]; ?>">
                                        <input type="hidden" name="id" value="<?php echo (int)$encomenda["id"]; ?>">
                                        <select name="status" class="status-select <?php echo classeEstadoEncomenda($estadoAtual); ?>">
                                            <?php foreach($estados as $valor => $label): ?>
                                                <option value="<?php echo limparTexto($valor); ?>" <?php if($estadoAtual === $valor) echo "selected"; ?>>
                                                    <?php echo limparTexto($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="admin-save-btn">Guardar</button>
                                        <span class="inline-save-feedback" aria-live="polite"></span>
                                    </form>
                                </td>
                                <td>
                                    <div class="table-actions-vertical">
                                        <a class="edit-link" href="admin_order_details.php?id=<?php echo (int)$encomenda["id"]; ?>">Ver detalhes</a>
                                        <a class="edit-link" href="admin_delivery_note.php?id=<?php echo (int)$encomenda["id"]; ?>" target="_blank">Guia PDF</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

            </div>

            <a href="admin.php" class="site-btn site-btn-secondary">Voltar</a>

        </div>

    </div>

</section>

<script>
/*
    ADMIN - GUARDAR ESTADO DA ENCOMENDA SEM RECARREGAR

    Usamos AJAX com fetch() para enviar o formulário do estado.
    O admin clica em "Guardar" e a base de dados é atualizada sem reiniciar a página.
*/

const adminOrdersMessage = document.getElementById("admin-orders-message");
const orderForms = document.querySelectorAll(".order-status-form");

function mostrarMensagemAdminOrders(mensagem, sucesso){
    if(!adminOrdersMessage) return;

    adminOrdersMessage.textContent = mensagem;
    adminOrdersMessage.classList.remove("erro", "sucesso");
    adminOrdersMessage.classList.add(sucesso ? "sucesso" : "erro", "admin-ajax-message-active");

    setTimeout(function(){
        adminOrdersMessage.classList.remove("admin-ajax-message-active");
    }, 2200);
}

function atualizarClasseEstado(select, novaClasse){
    select.classList.remove("status-pending", "status-in_transit", "status-delivered");
    if(novaClasse){
        select.classList.add(novaClasse);
    }
}

orderForms.forEach(function(form){
    form.addEventListener("submit", function(event){
        event.preventDefault();

        const botao = form.querySelector("button[type='submit']");
        const feedback = form.querySelector(".inline-save-feedback");
        const select = form.querySelector("select[name='status']");
        const dados = new FormData(form);

        dados.set("ajax", "1");

        if(botao){
            botao.disabled = true;
            botao.textContent = "A guardar...";
        }

        if(feedback){
            feedback.textContent = "";
        }

        fetch(form.action, {
            method: "POST",
            body: dados
        })
        .then(function(resposta){
            return resposta.json();
        })
        .then(function(respostaJson){
            mostrarMensagemAdminOrders(respostaJson.message, respostaJson.success);

            if(respostaJson.success && select){
                atualizarClasseEstado(select, respostaJson.status_class);

                if(feedback){
                    feedback.textContent = "Guardado ✓";
                }
            } else if(feedback){
                feedback.textContent = "Erro";
            }
        })
        .catch(function(){
            mostrarMensagemAdminOrders("Não foi possível guardar sem recarregar. Tente novamente.", false);

            if(feedback){
                feedback.textContent = "Erro";
            }
        })
        .finally(function(){
            if(botao){
                botao.disabled = false;
                botao.textContent = "Guardar";
            }

            if(feedback){
                setTimeout(function(){
                    feedback.textContent = "";
                }, 1800);
            }
        });
    });
});
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>
