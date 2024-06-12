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
        $quant = $_POST["quantidade"];
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
            echo "chega aqui";
            // Atualizar saldos

            $novoSaldoUsuario = $usuario->getSaldo() - $total;
            if($novoSaldoUsuario < 0){
                $_SESSION["mensagem"] = "dinheiro insuficiente";
                throw new Exception("nao tem dinheiro para comprar");
            }
            $novoSaldoVendedor = $vendedor->getSaldo() + $total;
            $usuario->setSaldo($novoSaldoUsuario);
            $vendedor->setSaldo($novoSaldoVendedor);

            // Atualizar quantidade do produto
            $produto->setQuantidade($produto->getQuantidade() - $quant);

            // Persistir as mudanças no banco de dados
            $produtoDAO->persistir($produto);
            $usuarioDAO->persistir($usuario);
            $usuarioDAO->persistir($vendedor);
            $usuarioDAO->adicionarCompra($usuario->getId(),$produto->getId(),$quant);
            $usuario->setHistoricoCompras($usuarioDAO->buscarHistoricoCompras($usuario->getId()));
            $_SESSION["mensagem"] = "Compra realizada com sucesso";
            header("Location: ../view/welcome.php");
        } else {
            echo "Não é possível comprar a quantidade desejada";
            header("Location: ../view/welcome.php");
        }
    } catch (Throwable $th) {
        echo "Erro: " . $th->getMessage();
        header("Location: ../view/welcome.php");
    }
} else {
    echo "Usuário não autenticado";
}
?>
