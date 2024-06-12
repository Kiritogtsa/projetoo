<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
require_once("../model/bd.php");
require_once("../controller/verifylogin.php");

$usuario = unserialize($_SESSION["usuario"]);
$usuarioId = $usuario->getId();
$vendedorId = $usuario->getVendedor()->getId();
$permicao = $_SESSION["permicao"];
$mensagem = "Bem-vindo, ";

if ($permicao == "vendedor") {
    $mensagem .= "Vendedor " . $usuario->getNome() . "!";
} else {
    $mensagem .= "Usuário " . $usuario->getNome() . "!";
}

$debug = "";
if (isset($_SESSION["mensagem"])) {
    $debug = $_SESSION["mensagem"];
}

$produtoDAO = new ProdutoDAO($pdo);
$produtos = $produtoDAO->getAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
    <style>
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .product {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            flex: 1 1 200px;
        }

        .details-container {
            display: none;
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Bem-vindo</h1>
    <h3><?php echo htmlspecialchars($mensagem); ?></h3>
    <p>Abaixo estão alguns links úteis:</p>

    <h2>Produtos Disponíveis:</h2>
    <div class="product-container" id="product-container">
        <?php foreach ($produtos as $produto) : ?>
            <div class="product" data-id="<?php echo htmlspecialchars($produto->getId()); ?>" data-nome="<?php echo htmlspecialchars($produto->getNome()); ?>" data-preco="<?php echo htmlspecialchars($produto->getPreco()); ?>" data-quant="<?php echo htmlspecialchars($produto->getQuantidade()); ?>" data-vendedor="<?php echo htmlspecialchars($produto->getVendedor()->getId()); ?>">
                <strong><?php echo htmlspecialchars($produto->getNome()); ?></strong> - R$ <?php echo number_format($produto->getPreco(), 2, ',', '.'); ?>
                <?php if ($produto->getVendedor()->getId() == $vendedorId) : ?>
                    <button class="buy-button" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Comprar</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($permicao == "vendedor") : ?>
        <a href="./casdratarproduto.php">Criar Produto</a>
    <?php endif; ?>

    <div class="details-container" id="details-container">
        <h2>Detalhes do Produto</h2>
        <p><strong>Nome:</strong> <span id="produto-nome"></span></p>
        <p><strong>Preço:</strong> R$ <span id="produto-preco"></span></p>
        <p><strong>Quantidade Disponível:</strong> <span id="produto-quant"></span></p>
        <form id="compra-form" action="../controller/gerenciarcompra.php" method="POST">
            <input type="hidden" id="produto-id" name="produto_id">
            <input type="hidden" id="produto-nome-input" name="produto_nome">
            <input type="hidden" id="produto-preco-input" name="produto_preco">
            <label for="user-quantidade">Quantidade a comprar:</label>
            <input type="number" id="user-quantidade" name="user_quantidade" min="1">
            <input type="submit" value="Comprar Produto">
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const products = document.querySelectorAll('.product');
            const buyButtons = document.querySelectorAll('.buy-button');

            products.forEach(product => {
                product.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nome = this.getAttribute('data-nome');
                    const preco = this.getAttribute('data-preco');
                    const quant = this.getAttribute('data-quant');

                    document.getElementById('produto-id').value = id;
                    document.getElementById('produto-nome-input').value = nome;
                    document.getElementById('produto-preco-input').value = preco;
                    document.getElementById('produto-nome').innerText = nome;
                    document.getElementById('produto-preco').innerText = parseFloat(preco).toFixed(2).replace('.', ',');
                    document.getElementById('produto-quant').innerText = quant;
                    document.getElementById('user-quantidade').max = quant;

                    document.getElementById('product-container').style.display = 'none';
                    document.getElementById('details-container').style.display = 'block';
                });
            });

            buyButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const id = this.getAttribute('data-id');
                    const productDiv = document.querySelector(`.product[data-id='${id}']`);
                    productDiv.click();
                });
            });
        });
    </script>
</body>
</html>
