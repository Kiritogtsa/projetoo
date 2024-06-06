<?php
session_start();
require_once("../model/usuario.php");
require_once("../model/produtos.php");
var_dump($_POST);
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["btn_cadastrar"])) {
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
        //header("Location : ../view/welcome.php");
    } catch (\Throwable $th) {
        echo $th->getMessage();
        // Tratar o erro, redirecionar ou mostrar mensagem de erro
    }
}