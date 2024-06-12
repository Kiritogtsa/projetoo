<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
require_once("../controller/verifylogin.php");

// Recupera os dados do usuário da sessão
$usuario = unserialize($_SESSION["usuario"]);
$permicao = $_SESSION["permicao"];
if($permicao != "vendedor"){
    header("Location: ./welcome.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Adicionar Produto</h2>

        <form action="../controller/servidor.php" method="POST">
            <label for="nome">Nome do Produto:</label><br>
            <input type="text" id="nome" name="nome" required><br>
            <label for="quantidade">Quantidade:</label><br>
            <input type="number" id="quantidade" name="quantidade" required><br>
            <label for="preco">Preço:</label><br>
            <input type="text" id="preco" name="preco" required><br>
            <input type="submit" name="btn_produto">
        </form>
    </div>
</body>
</html>
