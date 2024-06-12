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
                <?php if ($produto->getVendedor()->getVendedor()->getId() == $vendedorId) : ?>
                    <!-- Formulário para editar o produto -->
                    <button class="edit-button" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Editar Produto</button>
                <?php else : ?>
                    <!-- Formulário para comprar o produto -->
                    <button class="buy-button" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Comprar Produto</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($permicao == "vendedor") : ?>
        <a href="./casdratarproduto.php">Criar Produto</a>
    <?php endif; ?>

    <div class="details-container" id="edit-container">
        <h2>Editar Produto</h2>
        <form id="edit-form">
            <input type="hidden" id="edit-produto-id" name="produto_id">
            <!-- Adicione aqui os campos de edição do produto -->
        </form>
    </div>

    <div class="details-container hidden" id="buy-container">
        <h2>Comprar Produto</h2>
        <form id="buy-form" action="../controller/gerenciarcompra.php" method="POST">
            <input type="hidden" id="buy-produto-id" name="produto_id">
            <!-- Adicione aqui os campos para comprar o produto -->
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editContainer = document.getElementById('edit-container');
            const buyContainer = document.getElementById('buy-container');

            const editButtons = document.querySelectorAll('.edit-button');
            const buyButtons = document.querySelectorAll('.buy-button');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    document.getElementById('edit-produto-id').value = id;
                    editContainer.classList.remove('hidden');
                    buyContainer.classList.add('hidden');
                });
            });

            buyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    document.getElementById('buy-produto-id').value = id;

                    buyContainer.classList.remove('hidden');
                    editContainer.classList.add('hidden');
                });
            });
        });
    </script>
</body>
</html>
