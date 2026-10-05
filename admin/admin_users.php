<?php
/*
    FICHEIRO: admin_users.php

    OBJETIVO:
    Listar, editar a função e remover utilizadores da loja.

    Esta página pertence à área de administração e só pode ser usada
    por utilizadores com role admin.

    MELHORIA DE EXPERIÊNCIA:
    O botão "Guardar" da função do utilizador agora usa AJAX.
    Assim, o administrador altera user/admin e guarda sem recarregar a página.

    A remoção de utilizadores também foi melhorada:
    se o JavaScript estiver ativo, a linha desaparece da tabela sem reiniciar a página.
    Se o JavaScript estiver desativado, o link normal continua funcionando.
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

$pedidoAjaxGet = isset($_GET["ajax"]) && $_GET["ajax"] === "1";
$pedidoAjaxPost = isset($_POST["ajax"]) && $_POST["ajax"] === "1";

/*
    Função auxiliar para devolver JSON nos pedidos AJAX.
*/
function responderJsonAdminUsers($sucesso, $mensagem, $dados = []){
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array_merge([
        "success" => $sucesso,
        "message" => $mensagem
    ], $dados));
    exit;
}

if(isset($_GET["delete"])){

    $id = (int)$_GET["delete"];

    // Impedimos que o administrador apague a própria conta durante o teste.
    if($id === (int)$_SESSION["user_id"]){
        if($pedidoAjaxGet){
            responderJsonAdminUsers(false, "Não pode remover a conta que está autenticada.");
        }

        header("Location: admin_users.php");
        exit;
    }

    $sqlDelete = "DELETE FROM users WHERE id = ?";
    $stmtDelete = mysqli_prepare($conexao, $sqlDelete);
    mysqli_stmt_bind_param($stmtDelete, "i", $id);
    $executou = mysqli_stmt_execute($stmtDelete);

    if($pedidoAjaxGet){
        responderJsonAdminUsers($executou, $executou ? "Utilizador removido com sucesso." : "Não foi possível remover o utilizador.", [
            "user_id" => $id
        ]);
    }

    header("Location: admin_users.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $id = (int)($_POST["id"] ?? 0);
    $role = ($_POST["role"] ?? "user") === "admin" ? "admin" : "user";

    if($id <= 0){
        if($pedidoAjaxPost){
            responderJsonAdminUsers(false, "Utilizador inválido.");
        }

        header("Location: admin_users.php");
        exit;
    }

    $sqlUpdate = "UPDATE users SET role = ? WHERE id = ?";
    $stmtUpdate = mysqli_prepare($conexao, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "si", $role, $id);
    $executou = mysqli_stmt_execute($stmtUpdate);

    if($pedidoAjaxPost){
        responderJsonAdminUsers($executou, $executou ? "Função guardada com sucesso." : "Não foi possível guardar a função.", [
            "user_id" => $id,
            "role" => $role
        ]);
    }

    header("Location: admin_users.php");
    exit;
}

$users = mysqli_query($conexao, "SELECT id, username, email, role FROM users ORDER BY id ASC");

$pageTitle = "Seu Jorge | Gerir Utilizadores";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>

<section class="auth-section">

    <div class="container">

        <div class="auth-card auth-card-large">

            <h1>Gerir Utilizadores</h1>
            <p>Liste, altere a função e remova utilizadores.</p>

            <p id="admin-users-message" class="admin-ajax-message" aria-live="polite"></p>

            <div class="table-responsive">

                <table class="site-table">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilizador</th>
                            <th>Email</th>
                            <th>Função</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($user = mysqli_fetch_assoc($users)): ?>
                            <tr id="user-row-<?php echo (int)$user["id"]; ?>">
                                <td><?php echo (int)$user["id"]; ?></td>
                                <td><?php echo limparTexto($user["username"]); ?></td>
                                <td><?php echo limparTexto($user["email"]); ?></td>
                                <td>
                                    <form method="POST" action="admin_users.php" class="inline-form user-role-form" data-user-id="<?php echo (int)$user["id"]; ?>">
                                        <input type="hidden" name="id" value="<?php echo (int)$user["id"]; ?>">
                                        <select name="role">
                                            <option value="user" <?php if($user["role"] === "user") echo "selected"; ?>>user</option>
                                            <option value="admin" <?php if($user["role"] === "admin") echo "selected"; ?>>admin</option>
                                        </select>
                                        <button type="submit" class="admin-save-btn">Guardar</button>
                                        <span class="inline-save-feedback" aria-live="polite"></span>
                                    </form>
                                </td>
                                <td>
                                    <?php if((int)$user["id"] !== (int)$_SESSION["user_id"]): ?>
                                        <a class="remove-link ajax-delete-user" href="admin_users.php?delete=<?php echo (int)$user["id"]; ?>" data-user-id="<?php echo (int)$user["id"]; ?>">Remover</a>
                                    <?php else: ?>
                                        Conta atual
                                    <?php endif; ?>
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
    ADMIN - UTILIZADORES SEM RECARREGAR

    Guardar função e remover utilizador foram melhorados com AJAX.
    O objetivo é manter o admin no mesmo ponto da página, sem reiniciar a navegação.
*/

const adminUsersMessage = document.getElementById("admin-users-message");
const userRoleForms = document.querySelectorAll(".user-role-form");
const deleteUserLinks = document.querySelectorAll(".ajax-delete-user");

function mostrarMensagemAdminUsers(mensagem, sucesso){
    if(!adminUsersMessage) return;

    adminUsersMessage.textContent = mensagem;
    adminUsersMessage.classList.remove("erro", "sucesso");
    adminUsersMessage.classList.add(sucesso ? "sucesso" : "erro", "admin-ajax-message-active");

    setTimeout(function(){
        adminUsersMessage.classList.remove("admin-ajax-message-active");
    }, 2200);
}

userRoleForms.forEach(function(form){
    form.addEventListener("submit", function(event){
        event.preventDefault();

        const botao = form.querySelector("button[type='submit']");
        const feedback = form.querySelector(".inline-save-feedback");
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
            mostrarMensagemAdminUsers(respostaJson.message, respostaJson.success);

            if(feedback){
                feedback.textContent = respostaJson.success ? "Guardado ✓" : "Erro";
            }
        })
        .catch(function(){
            mostrarMensagemAdminUsers("Não foi possível guardar sem recarregar. Tente novamente.", false);

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

deleteUserLinks.forEach(function(link){
    link.addEventListener("click", function(event){
        event.preventDefault();

        if(!confirm("Deseja remover este utilizador?")){
            return;
        }

        const userId = link.dataset.userId;
        const row = document.getElementById("user-row-" + userId);
        const url = link.href + "&ajax=1";

        link.textContent = "A remover...";

        fetch(url)
        .then(function(resposta){
            return resposta.json();
        })
        .then(function(respostaJson){
            mostrarMensagemAdminUsers(respostaJson.message, respostaJson.success);

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
            mostrarMensagemAdminUsers("Não foi possível remover sem recarregar. Tente novamente.", false);
            link.textContent = "Remover";
        });
    });
});
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>
