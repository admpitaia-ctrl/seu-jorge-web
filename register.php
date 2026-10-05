<?php
/*
    FICHEIRO: register.php

    OBJETIVO:
    Criar a página de registo de novos utilizadores.

    Nesta página o visitante informa nome de utilizador, email e senha.
    A senha é protegida com password_hash antes de ser guardada no MySQL.
*/

session_start();
include "config/database.php";
include "includes/functions.php";

$mensagem = "";
$tipoMensagem = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    // Recebemos os dados enviados pelo formulário.
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";
    $address = trim($_POST["address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $postalCode = trim($_POST["postal_code"] ?? "");
    $country = trim($_POST["country"] ?? "Portugal");

    // Validamos os campos principais antes de enviar para a base de dados.
    if($username === "" || $email === "" || $password === "" || $confirmar === "" || $address === "" || $city === "" || $postalCode === "" || $country === ""){
        $mensagem = "Preencha todos os campos, incluindo a morada de entrega.";
        $tipoMensagem = "erro";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $mensagem = "Insira um email válido.";
        $tipoMensagem = "erro";
    } elseif($password !== $confirmar){
        $mensagem = "As senhas não são iguais.";
        $tipoMensagem = "erro";
    } elseif(strlen($password) < 6){
        $mensagem = "A senha deve ter pelo menos 6 caracteres.";
        $tipoMensagem = "erro";
    } else {

        // Verificamos se já existe utilizador com o mesmo username ou email.
        $sqlVerificar = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
        $stmtVerificar = mysqli_prepare($conexao, $sqlVerificar);
        mysqli_stmt_bind_param($stmtVerificar, "ss", $username, $email);
        mysqli_stmt_execute($stmtVerificar);
        $resultadoVerificar = mysqli_stmt_get_result($stmtVerificar);

        if(mysqli_num_rows($resultadoVerificar) > 0){
            $mensagem = "Este nome de utilizador ou email já está registado.";
            $tipoMensagem = "erro";
        } else {

            // Criamos o hash seguro da senha.
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Inserimos o novo utilizador na base de dados.
            // O registo cria sempre role user. O admin não é criado pelo formulário público.
            $sql = "INSERT INTO users (username, email, password, role, address, city, postal_code, country) VALUES (?, ?, ?, 'user', ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conexao, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $username, $email, $passwordHash, $address, $city, $postalCode, $country);

            if(mysqli_stmt_execute($stmt)){
                $mensagem = "Registo criado com sucesso. Agora já pode fazer login.";
                $tipoMensagem = "sucesso";
            } else {
                $mensagem = "Erro ao criar registo.";
                $tipoMensagem = "erro";
            }
        }
    }
}

$pageTitle = "Seu Jorge | Registo";
$pageDescription = "Criar conta na loja oficial de Seu Jorge";
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- REGISTO -->

<section class="auth-section">

    <div class="container">

        <div class="auth-card">

            <h1>Criar Conta</h1>
            <p>Registe-se para comprar produtos oficiais e acompanhar as suas encomendas.</p>

            <?php if($mensagem !== ""): ?>
                <div class="auth-message <?php echo $tipoMensagem; ?>">
                    <?php echo limparTexto($mensagem); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="form-validacao">

                <p class="form-required-note">* Todos os campos são obrigatórios.</p>

                <label>Nome de utilizador</label>
                <input type="text" name="username" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Senha</label>
                <input type="password" name="password" required>

                <label>Confirmar senha</label>
                <input type="password" name="confirmar" required>

                <h2 class="form-subtitle">Morada de Entrega</h2>
                <p class="form-helper-text">Esta morada será usada para confirmar o local da encomenda no checkout.</p>

                <label>Endereço</label>
                <div class="address-autocomplete-wrap">
                    <input type="text" name="address" data-address-field="address" data-address-autocomplete="1" list="enderecosSugeridos" placeholder="Ex: Rua de Santa Catarina, 100" required>
                </div>

                <datalist id="enderecosSugeridos">
                    <option value="Rua de Santa Catarina, Porto"></option>
                    <option value="Avenida dos Aliados, Porto"></option>
                    <option value="Rua Augusta, Lisboa"></option>
                    <option value="Avenida da Liberdade, Lisboa"></option>
                    <option value="Rua Ferreira Borges, Coimbra"></option>
                </datalist>

                <label>Cidade</label>
                <input type="text" name="city" data-address-field="city" list="cidadesSugeridas" placeholder="Ex: Porto" required>

                <datalist id="cidadesSugeridas">
                    <option value="Porto"></option>
                    <option value="Lisboa"></option>
                    <option value="Coimbra"></option>
                    <option value="Braga"></option>
                    <option value="Aveiro"></option>
                </datalist>

                <label>Código Postal</label>
                <input type="text" name="postal_code" data-address-field="postal_code" placeholder="Ex: 4000-000" required>

                <label>País</label>
                <input type="text" name="country" data-address-field="country" value="Portugal" required>

                <button type="submit">Criar Registo</button>

            </form>

            <p class="auth-link">Já tem conta? <a href="login.php">Entrar</a></p>

        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>
