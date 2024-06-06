<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
require_once("../controller/verifylogin.php");

// Recupera os dados do usuário da sessão
$usuario = unserialize($_SESSION["usuario"]);
$permicao = $_SESSION["permicao"];
if($permicao != "vedendor"){
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
</head>
<body>
    <h2>Adicionar Produto</h2>

    <form action="../controller/servidor.php" method="POST">
        <input type="hidden" id="vendedor_id" name="vendedor_id" value="<?php echo $usuario->getId(); ?>">
        <label for="nome">Nome do Produto:</label><br>
        <input type="text" id="nome" name="nome" required><br>
        <label for="quantidade">Quantidade:</label><br>
        <input type="number" id="quantidade" name="quantidade" required><br>
        <label for="preco">Preço:</label><br>
        <input type="text" id="preco" name="preco" required><br>
        <input type="submit" name="btn_produto" value="novo">
    </form>
</body>
</html>