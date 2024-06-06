<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
if (isset($_POST["btn_cadastrar"])) {
    try {
        echo "entra aqui";
        $nome = filter_var($_POST["nome"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $senha = filter_var($_POST["senha"], FILTER_SANITIZE_STRING);
        $is_vendedor = isset($_POST["is_vendedor"]) ? 1 : 0;
        $usuario = new Usuario($nome, $email, $senha, $is_vendedor);        
        $usuarioDAO = new UsuarioDAO();
        $usuarioDAO->persistir($usuario);
        if($usuario->getIsVendedor()==1){
            $_SESSION["usuario"]=serialize(new vendedor($usuario));
            $_SESSION["permicao"]="vedendor";
        }else{
            $_SESSION["usuario"]=serialize($usuario);
            $_SESSION["permicao"]="usuario";
        }
        header("Location: ../view/welcome.php");
        exit();
    } catch (\Throwable $th) {
        header("Location: ../view/welcome.php");
        exit();
        // Tratar o erro, redirecionar ou mostrar mensagem de erro
    }
}else if(isset($_POST["btn_login"])) {
    try {
        $usuarioDAO = new UsuarioDAO();
        $pessoa = $usuarioDAO->buscarPorEmail($_POST["email"]);
        if ($pessoa !== null && password_verify($_POST["senha"], $pessoa->getSenha())) {
            if($pessoa->getIsVendedor()==1){
                $_SESSION["usuario"]=serialize(new vendedor($pessoa));
                $_SESSION["permicao"]="vedendor";
            }else{
                $_SESSION["usuario"]=serialize($pessoa);
                $_SESSION["permicao"]="usuario";
            }
            header("Location: ../view/welcome.php");
            exit();
        }        
    } catch (\Throwable $th) {
        header("Location: ../view/welcome.php");
        exit();
    }
}else if(isset($_POST["btn_produto"])) {
    try {
        $nome = filter_var($_POST["nome"], FILTER_SANITIZE_STRING);
        $quantidade = filter_var($_POST["quantidade"], FILTER_SANITIZE_NUMBER_INT);
        $preco = filter_var($_POST["preco"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $vendedor_id = filter_var($_POST["vendedor_id"], FILTER_SANITIZE_NUMBER_INT);

        // Cria um novo objeto Produto
        $produto = new Produto(null, $nome, $quantidade, $preco, $vendedor_id);

        // Cria uma instância da classe ProdutoDAO
        $produtoDAO = new ProdutoDAO();

        // Chama o método persistir para adicionar o produto ao banco de dados
        $produtoDAO->persistir($produto);

        // Redireciona de volta para a página do vendedor
        header("Location: ../view/welcome.php");
        exit();
    } catch (\Throwable $th) {
        // Em caso de erro, redirecione para a página de boas-vindas com uma mensagem de erro
        $_SESSION["error_message"] = "Erro ao adicionar o produto: " . $th->getMessage();
        header("Location: ../view/welcome.php");
        exit();
    }
} 