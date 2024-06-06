<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
require_once("../controller/verifylogin.php");

// Recupera os dados do usuário da sessão
$usuario = unserialize($_SESSION["usuario"]);
$permicao = $_SESSION["permicao"];

// Mensagem de boas-vindas personalizada
$mensagem = "Bem-vindo, ";
if($permicao == "vedendor"){
    $mensagem .= "Vendedor " . $usuario->getUsuario()->getNome() . "!";
} else {
    $mensagem .= "Usuário " . $usuario->getNome() . "!";
}

// Recupera os produtos do banco de dados
$produtoDAO = new ProdutoDAO();
$produtos = $produtoDAO->getAll();
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <title>Bem-vindo</title>
</head>
<body>
    <h1>Bem-vindo</h1>
    <h3><?php echo $mensagem; ?></h3>
    <p>Abaixo estão alguns links úteis:</p>

    <h2>Produtos Disponíveis:</h2>
    <ul>
        <?php foreach ($produtos as $produto): ?>
            <li><?php echo $produto['nome']; ?> - R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if($permicao=="vedendor"){
        ?> 
            <a href="./casdratarproduto.php">criar produtos</a>
        <?php
    }
    ?>
</body>
</html>
