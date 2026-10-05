<?php
/*
    FICHEIRO: login.php

    OBJETIVO:
    Criar a página de login do website.

    O utilizador pode entrar com o nome de utilizador ou email.
    A senha é verificada com password_verify, comparando a senha digitada
    com o hash guardado na base de dados.
*/

session_start();
include "config/database.php";
include "includes/functions.php";

$mensagem = "";
$tipoMensagem = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if($login === "" || $password === ""){
        $mensagem = "Preencha todos os campos.";
        $tipoMensagem = "erro";
    } else {

        // Procuramos o utilizador pelo username ou pelo email.
        $sql = "SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $login, $login);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if($utilizador = mysqli_fetch_assoc($resultado)){

            // Verificamos se a senha digitada corresponde ao hash guardado.
            if(password_verify($password, $utilizador["password"])){

                $_SESSION["user_id"] = $utilizador["id"];
                $_SESSION["username"] = $utilizador["username"];
                $_SESSION["role"] = $utilizador["role"];

                header("Location: profile.php");
                exit;
            }
        }

        $mensagem = "Dados de acesso inválidos.";
        $tipoMensagem = "erro";
    }
}

$pageTitle = "Seu Jorge | Login";
$pageDescription = "Entrar na loja oficial de Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- LOGIN -->

<section class="auth-section">

    <div class="container">

        <div class="auth-card">

            <h1>Login</h1>
            <p>Entre na sua conta para aceder ao perfil, carrinho e encomendas.</p>

            <?php if($mensagem !== ""): ?>
                <div class="auth-message <?php echo $tipoMensagem; ?>">
                    <?php echo limparTexto($mensagem); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="form-validacao">

                <p class="form-required-note">* Todos os campos são obrigatórios.</p>

                <label>Nome de utilizador ou email</label>
                <input type="text" name="login" required>

                <label>Senha</label>
                <input type="password" name="password" required>

                <button type="submit">Entrar</button>

            </form>

            <p class="auth-link">Ainda não tem conta? <a href="register.php">Criar conta</a></p>

        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>
