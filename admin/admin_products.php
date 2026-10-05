<?php
/*
    FICHEIRO: admin_products.php

    OBJETIVO:
    Gerir os produtos da loja.

    O administrador pode adicionar, editar e remover produtos.

    MELHORIA DE EXPERIÊNCIA:
    A remoção de produtos também funciona por AJAX.
    Assim, ao clicar em "Remover", a linha desaparece sem recarregar a página.

    Se o JavaScript estiver desativado, o link normal continua funcionando.
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

$pedidoAjaxGet = isset($_GET["ajax"]) && $_GET["ajax"] === "1";

/*
    Função auxiliar para responder em JSON quando o admin remove por AJAX.
*/
function responderJsonAdminProducts($sucesso, $mensagem, $dados = []){
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array_merge([
        "success" => $sucesso,
        "message" => $mensagem
    ], $dados));
    exit;
}

if(isset($_GET["delete"])){

    $id = (int)$_GET["delete"];

    $sqlDelete = "DELETE FROM products WHERE id = ?";
    $stmtDelete = mysqli_prepare($conexao, $sqlDelete);
    mysqli_stmt_bind_param($stmtDelete, "i", $id);
    $executou = mysqli_stmt_execute($stmtDelete);

    if($pedidoAjaxGet){
        responderJsonAdminProducts($executou, $executou ? "Produto removido com sucesso." : "Não foi possível remover o produto.", [
            "product_id" => $id
        ]);
    }

    header("Location: admin_products.php");
    exit;
}

$products = mysqli_query($conexao, "SELECT id, name, price, stock, category FROM products ORDER BY id ASC");

$pageTitle = "Seu Jorge | Gerir Produtos";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Gerir Produtos</h1>
            <p>Adicione, edite ou remova produtos da loja.</p>

            <p id="admin-products-message" class="admin-ajax-message" aria-live="polite"></p>

            <div class="profile-actions">
                <a href="admin_product_form.php" class="site-btn">Adicionar Produto</a>
                <a href="admin.php" class="site-btn site-btn-secondary">Voltar</a>
            </div>

            <div class="table-responsive">

                <table class="site-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Stock</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($produto = mysqli_fetch_assoc($products)): ?>
                            <tr id="product-row-<?php echo (int)$produto["id"]; ?>">
                                <td><?php echo (int)$produto["id"]; ?></td>
                                <td><?php echo limparTexto($produto["name"]); ?></td>
                                <td><?php echo limparTexto($produto["category"]); ?></td>
                                <td><?php echo formatarPreco($produto["price"]); ?></td>
                                <td><?php echo (int)$produto["stock"]; ?></td>
                                <td>
                                    <a class="edit-link" href="admin_product_form.php?id=<?php echo (int)$produto["id"]; ?>">Editar</a>
                                    |
                                    <a class="remove-link ajax-delete-product" href="admin_products.php?delete=<?php echo (int)$produto["id"]; ?>" data-product-id="<?php echo (int)$produto["id"]; ?>">Remover</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

            </div>

        </div>

    </div>

</section>

<script>
/*
    ADMIN - REMOVER PRODUTO SEM RECARREGAR

    O link "Remover" usa AJAX para apagar o produto e retirar a linha da tabela.
    Mantemos o href normal como fallback caso o JavaScript esteja desativado.
*/

const adminProductsMessage = document.getElementById("admin-products-message");
const deleteProductLinks = document.querySelectorAll(".ajax-delete-product");

function mostrarMensagemAdminProducts(mensagem, sucesso){
    if(!adminProductsMessage) return;

    adminProductsMessage.textContent = mensagem;
    adminProductsMessage.classList.remove("erro", "sucesso");
    adminProductsMessage.classList.add(sucesso ? "sucesso" : "erro", "admin-ajax-message-active");

    setTimeout(function(){
        adminProductsMessage.classList.remove("admin-ajax-message-active");
    }, 2200);
}

deleteProductLinks.forEach(function(link){
    link.addEventListener("click", function(event){
        event.preventDefault();

        if(!confirm("Deseja remover este produto?")){
            return;
        }

        const productId = link.dataset.productId;
        const row = document.getElementById("product-row-" + productId);
        const url = link.href + "&ajax=1";

        link.textContent = "A remover...";

        fetch(url)
        .then(function(resposta){
            return resposta.json();
        })
        .then(function(respostaJson){
            mostrarMensagemAdminProducts(respostaJson.message, respostaJson.success);

            if(respostaJson.success && row){
                row.classList.add("row-removing");
                setTimeout(function(){
                    row.remove();
                }, 250);
            } else {
                link.textContent = "Remover";
            }
        })
        .catch(function(){
            mostrarMensagemAdminProducts("Não foi possível remover sem recarregar. Tente novamente.", false);
            link.textContent = "Remover";
        });
    });
});
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>
