<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php"); // Alteração aqui
require_once("../model/bd.php");
var_dump($_POST);
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
var_dump($_SESSION);
if (isset($_POST["btn_cadastrar"])) {
    try {
        $nome = filter_var($_POST["nome"], FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $senha = filter_var($_POST["senha"], FILTER_SANITIZE_SPECIAL_CHARS);
        $is_vendedor = isset($_POST["is_vendedor"]) ? 1 : 0;

        $usuario = new Usuario($nome, $email, $senha, null);
        $usuarioDAO = new UsuarioDAO($pdo);
        $usuarioDAO->persistir($usuario, $is_vendedor);
        $_SESSION["usuario"] = serialize($usuario);
        $_SESSION["permicao"] = $usuario->getVendedor() != null ? "vendedor" : "usuario";
        header("Location: ../view/welcome.php");
        exit();
    } catch (\Throwable $th) {
        echo $th->getMessage();
        // header("Location: ../view/welcome.php");
        // exit();
        // Tratar o erro, redirecionar ou mostrar mensagem de erro
    }
} elseif (isset($_POST["btn_login"])) {
    try {
        $usuarioDAO = new UsuarioDAO($pdo);
        $senha = $_POST["senha"]; // Use senha diretamente

        // Debugging: Verificar senha inserida e usuário buscado
        echo "Senha fornecida: " . $senha . "<br>";

        $pessoa = $usuarioDAO->buscarPorEmail($_POST["email"]);
        
        // Debugging: Mostrar informações do usuário retornado
        print_r($pessoa);
        echo "<br>";

        if ($pessoa !== null && password_verify($senha, $pessoa->getSenha())) {
            $_SESSION["usuario"] = serialize($pessoa);
            $_SESSION["permicao"] = $pessoa->getVendedor() != null ? "vendedor" : "usuario";
            header("Location: ../view/welcome.php");
            exit();
        } else {
            echo "Credenciais incorretas.";
        }
    } catch (\Throwable $th) {
        echo "Erro ao tentar logar: " . $th->getMessage();
    }
} elseif (isset($_POST["btn_produto"])) {
    try {
        $nome = filter_var($_POST["nome"], FILTER_SANITIZE_STRING);
        $quantidade = filter_var($_POST["quantidade"], FILTER_SANITIZE_NUMBER_INT);
        $preco = filter_var($_POST["preco"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $vendedor_id = filter_var($_POST["vendedor_id"], FILTER_SANITIZE_NUMBER_INT);
        $usuarioDAO = new UsuarioDAO($pdo);
        // Cria um novo objeto Produto
        $vendedor = $usuarioDAO->buscarPorIdVendedor($vendedor_id); // Alteração aqui
        $produto = new Produto($nome, $quantidade, $preco, $vendedor); // Alteração aqui
        if ($_POST["produto_id"]) {
            $produto->setId($_POST["produto_id"]);
        }
        // Cria uma instância da classe ProdutoDAO
        $produtoDAO = new ProdutoDAO($pdo);

        // Chama o método persistir para adicionar o produto ao banco de dados
        $produtoDAO->persistir($produto);

        // Redireciona de volta para a página do vendedor
        header("Location: ../view/welcome.php");
        exit();
    } catch (\Throwable $th) {
        // Em caso de erro, redirecione para a página de boas-vindas com uma mensagem de erro
        $_SESSION["error_message"] = "Erro ao adicionar o produto: " . $th->getMessage();
        //header("Location: ../view/welcome.php");
        exit();
    }
}
