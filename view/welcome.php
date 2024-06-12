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
    <header>
        <h1>Bem-vindo</h1>
        <h3><?php echo htmlspecialchars($mensagem); ?></h3>
        <nav>
            <ul>
                <li><a href="#produtos-disponiveis">Produtos Disponíveis</a></li>
                <?php if ($permicao == "vendedor") : ?>
                    <li><a href="./casdratarproduto.php">Criar Produto</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section id="produtos-disponiveis">
            <h2>Produtos Disponíveis</h2>
            <div class="product-container" id="product-container">
                <?php foreach ($produtos as $produto) : ?>
                    <article class="product" data-id="<?php echo htmlspecialchars($produto->getId()); ?>" data-nome="<?php echo htmlspecialchars($produto->getNome()); ?>" data-preco="<?php echo htmlspecialchars($produto->getPreco()); ?>" data-quant="<?php echo htmlspecialchars($produto->getQuantidade()); ?>" data-vendedor="<?php echo htmlspecialchars($produto->getVendedor()->getId()); ?>">
                        <h3><?php echo htmlspecialchars($produto->getNome()); ?></h3>
                        <p><strong>Preço:</strong> R$ <?php echo number_format($produto->getPreco(), 2, ',', '.'); ?></p>
                        <?php if ($produto->getVendedor()->getVendedor()->getId() == $vendedorId) : ?>
                            <!-- Link para editar o produto -->
                            <a href="#" class="edit-link" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Editar Produto</a>
                        <?php else : ?>
                            <!-- Link para comprar o produto -->
                            <a href="#" class="buy-link" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Comprar Produto</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="details-container" id="edit-container">
            <h2>Editar Produto</h2>
            <form id="edit-form">
                <input type="hidden" id="edit-produto-id" name="produto_id">
                <!-- Adicione aqui os campos de edição do produto -->
            </form>
        </section>

        <section class="details-container hidden" id="buy-container">
            <h2>Comprar Produto</h2>
            <form id="buy-form" action="../controller/gerenciarcompra.php" method="POST">
                <input type="hidden" id="buy-produto-id" name="produto_id">
                <!-- Adicione aqui os campos para comprar o produto -->
            </form>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editContainer = document.getElementById('edit-container');
            const buyContainer = document.getElementById('buy-container');

            const editLinks = document.querySelectorAll('.edit-link');
            const buyLinks = document.querySelectorAll('.buy-link');

            editLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const id = this.getAttribute('data-id');
                    document.getElementById('edit-produto-id').value = id;
                    editContainer.classList.remove('hidden');
                    buyContainer.classList.add('hidden');
                    document.getElementById('product-container').classList.add('hidden');
                });
            });

            buyLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const id = this.getAttribute('data-id');
                    document.getElementById('buy-produto-id').value = id;
                    buyContainer.classList.remove('hidden');
                    editContainer.classList.add('hidden');
                    document.getElementById('product-container').classList.add('hidden');
                });
            });
        });
    </script>
</body>
</html>
