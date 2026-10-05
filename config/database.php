<?php
/*
    FICHEIRO: config/database.php

    OBJETIVO:
    Criar a ligação entre o projeto PHP e a base de dados MySQL.

    Este ficheiro será chamado por todas as páginas que precisam
    buscar, inserir, atualizar ou apagar informações na base de dados.

    IMPORTANTE:
    Se o seu MySQL estiver a usar outra porta, altere o valor de $porta.
    No XAMPP normalmente é 3306, mas no seu caso pode ser 3307.
*/

$host = "127.0.0.1";
$porta = "3307";
$utilizador = "root";
$senha = "";
$baseDados = "seu_jorge_store";

// Criamos a conexão com o MySQL usando mysqli.
$conexao = mysqli_connect($host, $utilizador, $senha, $baseDados, $porta);

// Garantimos que os acentos e caracteres especiais aparecem corretamente.
if($conexao){
    mysqli_set_charset($conexao, "utf8mb4");
}



/*
    ATUALIZAÇÕES AUTOMÁTICAS DA BASE DE DADOS

    OBJETIVO:
    Como o projeto evoluiu depois da primeira importação SQL, este bloco garante
    que as colunas novas de morada/endereço existem mesmo que o aluno já tenha
    importado uma versão anterior da base de dados.

    Isto evita ter que apagar a base e importar tudo novamente.
*/
function colunaExiste($conexao, $baseDados, $tabela, $coluna){

    $sql = "SELECT COUNT(*) AS total
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?";

    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $baseDados, $tabela, $coluna);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $linha = mysqli_fetch_assoc($resultado);

    return (int)$linha["total"] > 0;
}

function adicionarColunaSeNaoExistir($conexao, $baseDados, $tabela, $coluna, $definicao){

    if(!colunaExiste($conexao, $baseDados, $tabela, $coluna)){
        mysqli_query($conexao, "ALTER TABLE $tabela ADD COLUMN $coluna $definicao");
    }
}

if($conexao){

    // Dados de morada do utilizador.
    adicionarColunaSeNaoExistir($conexao, $baseDados, "users", "address", "VARCHAR(255) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "users", "city", "VARCHAR(100) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "users", "postal_code", "VARCHAR(20) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "users", "country", "VARCHAR(80) NULL DEFAULT 'Portugal'");

    // Morada gravada no momento da encomenda.
    adicionarColunaSeNaoExistir($conexao, $baseDados, "orders", "delivery_address", "VARCHAR(255) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "orders", "delivery_city", "VARCHAR(100) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "orders", "delivery_postal_code", "VARCHAR(20) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "orders", "delivery_country", "VARCHAR(80) NULL DEFAULT 'Portugal'");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "orders", "delivery_maps_url", "VARCHAR(500) NULL");
    adicionarColunaSeNaoExistir($conexao, $baseDados, "orders", "delivery_notes", "TEXT NULL");

    /*
        Atualizamos os estados da encomenda para um fluxo mais real:
        Pendente -> A caminho -> Entregue.

        A versão anterior usava "completed". Antes de alterar o ENUM,
        convertemos completed para delivered para não perder informações.
    */
    mysqli_query($conexao, "UPDATE orders SET status = 'delivered' WHERE status = 'completed'");
    mysqli_query($conexao, "ALTER TABLE orders MODIFY status ENUM('pending', 'in_transit', 'delivered') DEFAULT 'pending'");
}


// Se a conexão falhar, mostramos uma mensagem simples para facilitar o teste.
if(!$conexao){
    die("Erro ao conectar à base de dados: " . mysqli_connect_error());
}
?>
