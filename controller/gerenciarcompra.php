<?php
session_start();
require_once("../model/bd.php");
require_once("../model/usuario.php");
require_once("../model/produtos.php");
function getvalortotal($quant,$preco){
    $total = 0;
    $total = $preco * $quant;
    return $total;
}

if(isset($_SESSION["usuario"])){
    try {
        $quant = $_POST["user_quantidade"];
        $produtoDAO = new ProdutoDAO($pdo);
        $produto = $produtoDAO->buscarPorId($_POST["produto_id"]);
        $usuario = unserialize($_SESSION["usuario"]);
        $usuarioDAO = new usuarioDAO($pdo);
        echo "<br>";
        echo "<br>";
        var_dump($produto);
        echo "<br>";
        echo "<br>";
        if($produto->getQuantidade() > 0 && $produto->getQuantidade() - $quant > 0){
            if($_SESSION["permicao"] == "vedendor"){
                $usuario = $usuario->getUsuario();
                $usuario=$usuarioDAO->buscarPorId($usuario->getId());
            }else{
                $usuario=$usuarioDAO->buscarPorId($usuario->getId());
            }
            $vendedordao = new vendedorDAO($pdo);
            $vendedorid = $vendedordao->buscarPorId($produto->getVendedorId());
            $vendedor = $usuarioDAO->buscarPorId($vendedorid);
            $total = getvalortotal($quant,$produto->getPreco());
            $usuariovalor = $usuario->getSaldo() - $total;
            $vendedor->setSaldo($vendedor->getSaldo() + $total);
            $usuario->setSaldo($usuariovalor);
            $produto->setQuantidade($produto->getQuantidade() - $quant);
            $produtoDAO->persistir($produto);
            $usuarioDAO->persistir($usuario);
            $usuarioDAO->persistir($vendedor);
            
            $_SESSION["messagem"] = "compra com sucesso";
            //header("Location: ../view/welcome.php");
        }else{
            echo "nao pode comprar";
        }
        // if($produto->getVendedorId() == $usuario->getVendedorId()){
        //     $_SESSION["messagem"] = "vc consegui bular o html mais nao o php ";
        //     header("Location: /");
        // }
        
    } catch (\Throwable $th) {
        //throw $th;
        echo "". $th->getMessage() ."";
    }
}