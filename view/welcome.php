<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
require_once("../model/bd.php");
require_once("../controller/verifylogin.php");

$usuario = unserialize($_SESSION["usuario"]);
$usuarioId = $usuario->getId();
$vendedorId = $usuario->getVendedor() ? $usuario->getVendedor()->getId(): null;
$historicoCompras = $usuario->getHistoricoCompras();
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bem-vindo</h1>
        <h3><?php echo htmlspecialchars($mensagem); ?></h3>
        <form action="../controller/logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>
    </header>

    <main class="container">
        <section>
            <h2>Produtos Disponíveis:</h2>
            <div class="product-container" id="product-container">
                <?php foreach ($produtos as $produto) : ?>
                    <div class="product" 
                         data-id="<?php echo htmlspecialchars($produto->getId()); ?>" 
                         data-nome="<?php echo htmlspecialchars($produto->getNome()); ?>" 
                         data-preco="<?php echo htmlspecialchars($produto->getPreco()); ?>" 
                         data-quant="<?php echo htmlspecialchars($produto->getQuantidade()); ?>" 
                         data-vendedor="<?php echo $produto->getVendedor() ? htmlspecialchars($produto->getVendedor()->getId()) : ''; ?>">
                        <strong><?php echo htmlspecialchars($produto->getNome()); ?></strong> 
                        <p>R$ <?php echo number_format($produto->getPreco(), 2, ',', '.'); ?></p>
                        <?php if ($vendedor && $produto->getVendedor()->getId() == $vendedorId) : ?>
                            <!-- Botão para editar o produto -->
                            <button class="edit-button" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Editar Produto</button>
                        <?php else : ?>
                            <!-- Botão para comprar o produto -->
                            <button class="buy-button" data-id="<?php echo htmlspecialchars($produto->getId()); ?>">Comprar Produto</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="purchase-history">
            <h2>Histórico de Compras:</h2>
            <?php if (empty($historicoCompras)) : ?>
                <p>Nenhuma compra realizada ainda.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($historicoCompras as $compra) : ?>
                        <li>
                            <strong>Produto:</strong> <?php echo htmlspecialchars($compra->getNome()); ?> -
                            <strong>Quantidade:</strong> <?php echo htmlspecialchars($compra->getQuantidade()); ?> -
                            <strong>Data:</strong> <?php echo htmlspecialchars($compra->getData()); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <?php if ($permicao == "vendedor") : ?>
            <a href="casdratarproduto.php">Criar Produto</a>
        <?php endif; ?>
    </main>

    <div class="details-container hidden" id="edit-container">
        <h2>Editar Produto</h2>
        <form id="edit-form" action="../controller/servidor.php" method="POST">
            <input type="hidden" id="edit-produto-id" name="produto_id">
            <label for="edit-nome">Nome:</label>
            <input type="text" id="edit-nome" name="nome">
            <label for="edit-preco">Preço:</label>
            <input type="text" id="edit-preco" name="preco">
            <label for="edit-quantidade">Quantidade:</label>
            <input type="number" id="edit-quantidade" name="quantidade" min="1">
            <button type="submit" name="btn_produto">Salvar</button>
        </form>
    </div>

    <div class="details-container hidden" id="buy-container">
        <h2>Comprar Produto</h2>
        <form id="buy-form" action="../controller/gerenciarcompra.php" method="POST">
            <input type="hidden" id="buy-produto-id" name="produto_id">
            <label for="buy-quantidade">Quantidade:</label>
            <input type="number" id="buy-quantidade" name="quantidade" min="1">
            <button type="submit">Comprar</button>
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
                    document.getElementById('edit-nome').value = this.parentElement.getAttribute('data-nome');
                    document.getElementById('edit-preco').value = this.parentElement.getAttribute('data-preco');
                    document.getElementById('edit-quantidade').value = this.parentElement.getAttribute('data-quant');
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
