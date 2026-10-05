<?php
/*
    FICHEIRO: admin_product_form.php

    OBJETIVO:
    Adicionar ou editar produtos da loja.

    Neste ficheiro o administrador consegue:
    - preencher nome, descrição, preço e stock;
    - escolher uma categoria já existente através de um dropdown;
    - criar uma nova categoria quando ela ainda não existir;
    - enviar uma imagem do produto pelo computador, em vez de escrever o caminho da imagem manualmente.

    Se existir um id na URL, editamos o produto.
    Se não existir id, criamos um novo produto.
*/

session_start();
include __DIR__ . "/../includes/admin_check.php";
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../includes/functions.php";

/*
    GARANTIA DA TABELA CATEGORIES

    Esta parte cria a tabela de categorias caso ela ainda não exista.
    Assim, mesmo que a base de dados antiga já tenha sido importada,
    o formulário continua a funcionar sem dar erro.
*/
$sqlCriarCategorias = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
)";
mysqli_query($conexao, $sqlCriarCategorias);

/*
    SINCRONIZAR CATEGORIAS ANTIGAS

    Como o projeto já tinha a coluna category dentro da tabela products,
    esta consulta copia as categorias existentes dos produtos para a nova
    tabela categories.
*/
$sqlSincronizarCategorias = "INSERT IGNORE INTO categories (name)
    SELECT DISTINCT category
    FROM products
    WHERE category IS NOT NULL AND category <> ''";
mysqli_query($conexao, $sqlSincronizarCategorias);

$id = (int)($_GET["id"] ?? 0);
$mensagemErro = "";

$produto = [
    "name" => "",
    "description" => "",
    "price" => "",
    "stock" => "",
    "image" => "",
    "category" => ""
];

if($id > 0){
    $sql = "SELECT * FROM products WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $produtoEncontrado = mysqli_fetch_assoc($resultado);

    if($produtoEncontrado){
        $produto = $produtoEncontrado;
    }
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $stock = (int)($_POST["stock"] ?? 0);

    /*
        CATEGORIA

        O administrador pode escolher uma categoria existente.
        Se selecionar "Criar nova categoria", usamos o texto escrito
        no campo nova_categoria.
    */
    $category = trim($_POST["category"] ?? "");
    $novaCategoria = trim($_POST["nova_categoria"] ?? "");

    if($category === "__nova__"){
        $category = $novaCategoria;
    }

    /*
        IMAGEM

        Quando estamos a editar um produto, começamos por manter a imagem antiga.
        Se o administrador enviar uma nova imagem, substituímos pelo novo ficheiro.
    */
    $image = $produto["image"];

    if(isset($_FILES["image_file"]) && $_FILES["image_file"]["error"] !== UPLOAD_ERR_NO_FILE){

        if($_FILES["image_file"]["error"] === UPLOAD_ERR_OK){

            $ficheiroTemporario = $_FILES["image_file"]["tmp_name"];
            $nomeOriginal = $_FILES["image_file"]["name"];
            $tamanhoFicheiro = (int)$_FILES["image_file"]["size"];
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

            $extensoesPermitidas = ["jpg", "jpeg", "png", "webp"];

            /*
                Validação simples:
                - aceitamos apenas imagens comuns;
                - limitamos o tamanho para evitar ficheiros muito pesados.
            */
            if(!in_array($extensao, $extensoesPermitidas)){
                $mensagemErro = "A imagem deve estar em JPG, JPEG, PNG ou WEBP.";
            } elseif($tamanhoFicheiro > 2 * 1024 * 1024){
                $mensagemErro = "A imagem deve ter no máximo 2MB.";
            } else {

                $pastaUploadFisica = __DIR__ . "/../uploads/products/";
                $pastaUploadPublica = "uploads/products/";

                if(!is_dir($pastaUploadFisica)){
                    mkdir($pastaUploadFisica, 0777, true);
                }

                /*
                    Criamos um nome único para evitar substituir imagens antigas.
                */
                $novoNome = "produto_" . time() . "_" . rand(1000, 9999) . "." . $extensao;
                $caminhoFinalFisico = $pastaUploadFisica . $novoNome;
                $caminhoFinalPublico = $pastaUploadPublica . $novoNome;

                if(move_uploaded_file($ficheiroTemporario, $caminhoFinalFisico)){
                    $image = $caminhoFinalPublico;
                } else {
                    $mensagemErro = "Não foi possível guardar a imagem enviada.";
                }
            }
        } else {
            $mensagemErro = "Ocorreu um erro ao enviar a imagem.";
        }
    }

    if($name === "" || $description === "" || $price <= 0 || $stock < 0 || $category === ""){
        $mensagemErro = "Preencha todos os campos obrigatórios corretamente.";
    }

    if($id === 0 && $image === ""){
        $mensagemErro = "Escolha uma imagem para o novo produto.";
    }

    if($mensagemErro === ""){

        /*
            Guardamos a categoria na tabela categories.
            INSERT IGNORE evita erro se a categoria já existir.
        */
        $sqlCategoria = "INSERT IGNORE INTO categories (name) VALUES (?)";
        $stmtCategoria = mysqli_prepare($conexao, $sqlCategoria);
        mysqli_stmt_bind_param($stmtCategoria, "s", $category);
        mysqli_stmt_execute($stmtCategoria);

        if($id > 0){
            $sqlUpdate = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ?, category = ? WHERE id = ?";
            $stmtUpdate = mysqli_prepare($conexao, $sqlUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "ssdissi", $name, $description, $price, $stock, $image, $category, $id);
            mysqli_stmt_execute($stmtUpdate);
        } else {
            $sqlInsert = "INSERT INTO products (name, description, price, stock, image, category) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtInsert = mysqli_prepare($conexao, $sqlInsert);
            mysqli_stmt_bind_param($stmtInsert, "ssdiss", $name, $description, $price, $stock, $image, $category);
            mysqli_stmt_execute($stmtInsert);
        }

        header("Location: admin_products.php");
        exit;
    }

    /*
        Se houve erro, mantemos os dados preenchidos no formulário
        para o administrador não precisar escrever tudo de novo.
    */
    $produto["name"] = $name;
    $produto["description"] = $description;
    $produto["price"] = $price;
    $produto["stock"] = $stock;
    $produto["image"] = $image;
    $produto["category"] = $category;
}

/*
    Buscamos as categorias para preencher o dropdown.
*/
$categorias = mysqli_query($conexao, "SELECT name FROM categories ORDER BY name ASC");

$pageTitle = $id > 0 ? "Seu Jorge | Editar Produto" : "Seu Jorge | Adicionar Produto";
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/navbar.php";
?>

<section class="auth-section">

    <div class="container">

        <div class="auth-card">

            <h1><?php echo $id > 0 ? "Editar Produto" : "Adicionar Produto"; ?></h1>

            <?php if($mensagemErro !== ""): ?>
                <div class="auth-message erro"><?php echo limparTexto($mensagemErro); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-validacao">

                <p class="form-required-note">* Campos obrigatórios. As caixas vazias ficam contornadas a vermelho.</p>

                <label>Nome</label>
                <input type="text" name="name" value="<?php echo limparTexto($produto["name"]); ?>" required>

                <label>Descrição</label>
                <textarea name="description" rows="4" required><?php echo limparTexto($produto["description"]); ?></textarea>

                <label>Preço</label>
                <input type="number" step="0.01" min="0.01" name="price" value="<?php echo limparTexto($produto["price"]); ?>" required>

                <label>Stock</label>
                <input type="number" min="0" name="stock" value="<?php echo limparTexto($produto["stock"]); ?>" required>

                <label>Imagem do produto</label>

                <?php if($produto["image"] !== ""): ?>
                    <div class="admin-image-preview">
                        <p>Imagem atual:</p>
                        <img src="<?php echo caminhoProjeto(limparTexto($produto["image"])); ?>" alt="Imagem atual do produto">
                    </div>
                <?php endif; ?>

                <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" <?php echo $id === 0 ? "required" : ""; ?>>
                <small class="form-help">Escolha uma imagem JPG, PNG ou WEBP com até 2MB.</small>

                <label>Categoria</label>
                <select name="category" id="category-select" required>
                    <option value="">Selecione uma categoria</option>

                    <?php while($categoria = mysqli_fetch_assoc($categorias)): ?>
                        <option value="<?php echo limparTexto($categoria["name"]); ?>" <?php echo $produto["category"] === $categoria["name"] ? "selected" : ""; ?>>
                            <?php echo limparTexto($categoria["name"]); ?>
                        </option>
                    <?php endwhile; ?>

                    <option value="__nova__">+ Criar nova categoria</option>
                </select>

                <div id="nova-categoria-box" class="nova-categoria-box">
                    <label>Nova categoria</label>
                    <input type="text" name="nova_categoria" id="nova-categoria-input" placeholder="Exemplo: acessórios, livros, roupa...">
                </div>

                <button type="submit">Guardar Produto</button>

            </form>

            <p class="auth-link"><a href="admin_products.php">Voltar para produtos</a></p>

        </div>

    </div>

</section>

<script>
/*
    DROPDOWN DE CATEGORIAS

    Quando o administrador escolhe "+ Criar nova categoria",
    mostramos um campo extra para escrever a nova categoria.
*/

const categoriaSelect = document.getElementById("category-select");
const novaCategoriaBox = document.getElementById("nova-categoria-box");
const novaCategoriaInput = document.getElementById("nova-categoria-input");

function controlarNovaCategoria(){
    if(categoriaSelect.value === "__nova__"){
        novaCategoriaBox.style.display = "block";
        novaCategoriaInput.required = true;
    } else {
        novaCategoriaBox.style.display = "none";
        novaCategoriaInput.required = false;
        novaCategoriaInput.value = "";
    }
}

if(categoriaSelect){
    categoriaSelect.addEventListener("change", controlarNovaCategoria);
    controlarNovaCategoria();
}
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>
