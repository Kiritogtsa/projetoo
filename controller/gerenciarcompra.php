<?php
session_start();
require_once("../model/bd.php");
require_once("../model/usuario.php");
require_once("../model/produtos.php");
function getvalortotal($quant, $preco) {
    return $preco * $quant;
}

if (isset($_SESSION["usuario"])) {
    try {
        $quant = $_POST["user_quantidade"];
        $produtoDAO = new ProdutoDAO($pdo);
        $produto = $produtoDAO->buscarPorId($_POST["produto_id"]);
        $usuario = unserialize($_SESSION["usuario"]);
        $usuarioDAO = new UsuarioDAO($pdo);

        // Verificar se o usuário é um vendedor e obter o usuário real, se necessário
        if ($_SESSION["permicao"] == "vendedor") {
            $usuario = $usuarioDAO->buscarPorId($usuario->getId());
        } else {
            $usuario = $usuarioDAO->buscarPorId($usuario->getId());
        }

        // Verificar se o produto tem quantidade suficiente
        if ($produto->getQuantidade() >= $quant) {
            $vendedor = $produto->getVendedor();
            $total = getvalortotal($quant, $produto->getPreco());

            // Atualizar saldos
            $novoSaldoUsuario = $usuario->getSaldo() - $total;
            $novoSaldoVendedor = $vendedor->getSaldo() + $total;

            $usuario->setSaldo($novoSaldoUsuario);
            $vendedor->setSaldo($novoSaldoVendedor);

            // Atualizar quantidade do produto
            $produto->setQuantidade($produto->getQuantidade() - $quant);

            // Persistir as mudanças no banco de dados
            $produtoDAO->persistir($produto);
            $usuarioDAO->persistir($usuario);
            $usuarioDAO->persistir($vendedor);

            $_SESSION["mensagem"] = "Compra realizada com sucesso";
            header("Location: ../view/welcome.php");
        } else {
            echo "Não é possível comprar a quantidade desejada";
        }
    } catch (Throwable $th) {
        echo "Erro: " . $th->getMessage();
    }
} else {
    echo "Usuário não autenticado";
}
?>
