<?php
session_start();

// Verifica se o usuário está autenticado
if(!isset($_SESSION["usuario"]) || !isset($_SESSION["permicao"])) {
    header("Location: ../controller/login.php");
    exit;
}

// Recupera os dados do usuário da sessão
$usuario = unserialize($_SESSION["usuario"]);
$permicao = $_SESSION["permicao"];

// Mensagem de boas-vindas personalizada
$mensagem = "Bem-vindo, ";
if($permicao == "vedendor"){
    $mensagem .= "Vendedor " . $usuario->getNome() . "!";
} else {
    $mensagem .= "Usuário " . $usuario->getNome() . "!";
}
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
    <div class="link">
        <?php if($permicao == "vedendor"){ ?>
        <a href="./adicionar.php">Adicionar um usuário</a>
        <?php } ?>
        <a href="../controller/logout.php">Sair</a>
        <a href="./visualizar.php">Listagem</a>
    </div>
</body>
</html>
