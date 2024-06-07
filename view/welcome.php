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
$debug="";
if(isset($_SESSION["messagem"])){
$debug = $_SESSION["messagem"];
}
echo $debug;
// Recupera os produtos do banco de dados
$produtoDAO = new ProdutoDAO();
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
    <h3><?php echo $mensagem; ?></h3>
    <p>Abaixo estão alguns links úteis:</p>

    <h2>Produtos Disponíveis:</h2>
    <div class="product-container" id="product-container">
        <?php foreach ($produtos as $produto): ?>
            <div class="product" data-id="<?php echo $produto['id']; ?>" data-nome="<?php echo $produto['nome']; ?>" data-preco="<?php echo $produto['preco']; ?>" data-quant="<?php echo $produto['quant']; ?>" data-vendedor="<?php echo $produto['vendedor_id']; ?>">
                <strong><?php echo $produto['nome']; ?></strong> - R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if($permicao == "vedendor"): ?> 
        <a href="./casdratarproduto.php">Criar Produto</a>
    <?php endif; ?>

    <div class="details-container" id="details-container">
        <h2>Detalhes do Produto</h2>
        <p><strong>Nome:</strong> <span id="produto-nome"></span></p>
        <p><strong>Preço:</strong> R$ <span id="produto-preco"></span></p>
        <p><strong>Quantidade:</strong> <span id="produto-quant"></span></p>

        <form id="update-form" class="hidden" action="../controller/servidor.php" method="POST">
            <input type="hidden" id="produto-id" name="produto_id">
            <input type="hidden" name="vendedor_id" value="<?= $usuario->getID()?>">
            <label for="update-nome">Nome:</label>
            <input type="text" id="update-nome" name="nome">
            <label for="update-preco">Preço:</label>
            <input type="number" id="update-preco" name="preco">
            <label for="update-quantidade">Quantidade:</label>
            <input type="number" id="update-quantidade" name="quantidade">
            <input type="submit" name="btn_produto">
        </form>

        <button type="button" id="comprar-button" class="hidden">Comprar Produto</button>
    </div>
    
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    const products = document.querySelectorAll('.product');
    products.forEach(product => {
        product.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nome = this.getAttribute('data-nome');
            const preco = this.getAttribute('data-preco');
            const quant = this.getAttribute('data-quant');
            const vendedorId = this.getAttribute('data-vendedor');

            document.getElementById('produto-id').value = id;
            document.getElementById('produto-nome').innerText = nome;
            document.getElementById('produto-preco').innerText = parseFloat(preco).toFixed(2).replace('.', ',');
            document.getElementById('produto-quant').innerText = quant;

            // Esconde todos os produtos e mostra a details-container
            document.getElementById('product-container').style.display = 'none';
            document.getElementById('details-container').style.display = 'block';

            // Esconde ambos os formulários inicialmente
            document.getElementById('update-form').classList.add('hidden');
            document.getElementById('comprar-button').classList.add('hidden');

            // Verifica se o usuário é o vendedor do produto
            if (<?php echo json_encode($permicao == "vedendor"); ?> && vendedorId == "<?php echo $usuario->getId(); ?>") {
                document.getElementById('update-nome').value = nome;
                document.getElementById('update-preco').value = preco;
                document.getElementById('update-quantidade').value = quant;
                document.getElementById('update-form').classList.remove('hidden');
            } else {
                document.getElementById('comprar-button').classList.remove('hidden');
            }
        });
    });
});
    </script>
</body>
</html>
