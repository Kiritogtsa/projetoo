<?php

class Usuario
{
    private $user_id;
    private $nome;
    private $email;
    private $senha;
    private $saldo;

    private ?Vendedor $vendedor;

    public function __construct($nome, $email, $senha, $vendedor = null, $id = null, $saldo = 0.0)
    {
        if (empty($nome) || empty($email) || empty($senha)) {
            throw new Exception("Os campos nome, email e senha são obrigatórios.");
        }
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->user_id = $id;
        $this->saldo = $saldo;
        $this->vendedor = $vendedor;
    }

    // Getters and Setters
    public function getId()
    {
        return $this->user_id;
    }

    public function setId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
    }

    public function getVendedor()
    {
        return $this->vendedor;
    }

    public function setVendedor(Vendedor $vendedor)
    {
        $this->vendedor = $vendedor;
    }
}

class Vendedor
{
    private $vende_id;
    private $usuario_id; // Alteração aqui
    private $produtos;

    public function __construct($usuario_id, $id = null, $produtos = array())
    { // Alteração no construtor
        $this->usuario_id = $usuario_id; // Alteração aqui
        $this->vende_id = $id;
        $this->produtos = $produtos;
    }

    // Getters and Setters
    public function getId()
    {
        return $this->vende_id;
    }

    public function setId($id)
    {
        $this->vende_id = $id;
    }

    public function getProdutos()
    {
        return $this->produtos;
    }

    public function setProdutos($produtos)
    {
        $this->produtos = $produtos;
    }

    public function getUsuarioId()
    { // Método para obter o ID do usuário
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    { // Método para definir o ID do usuário
        $this->usuario_id = $usuario_id;
    }
}

class UsuarioDAO
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function persistir(Usuario $usuario, $is_vendedor = null)
    {
        $is_vendedor = $is_vendedor == null ? 0 : 1;
        if (!$usuario->getId()) {
            return $this->criar($usuario, $is_vendedor);
        } else {
            return $this->atualizar($usuario);
        }
    }

    private function criar(Usuario $usuario, $is_vendedor)
    {
        $sql = "INSERT INTO usuario (nome, email, senha, vendedor, saldo) VALUES (:nome, :email, :senha,:vendedor, :saldo)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':nome', $usuario->getNome());
        $stmt->bindParam(':email', $usuario->getEmail());
        $senhaHash = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':vendedor', $is_vendedor);
        $stmt->bindParam(':saldo', $usuario->getSaldo());
        $stmt->execute();
        $usuario->setId($this->pdo->lastInsertId());
        if ($is_vendedor == 1) {
            $vendedorDAO = new VendedorDAO($this->pdo);
            $vendedor =  new Vendedor($usuario->getId());
            $vendedor = $vendedorDAO->persistir($vendedor);
            $usuario->setVendedor($vendedor);
            $this->salvaridVededor($usuario);
        }
        return $usuario;
    }
    private function salvaridVededor(Usuario $user){
        if(!$user->getVendedor()){
            throw new Exception("Não foi possível adicionar o ID do vendedor");
        }
        $sql = "UPDATE usuario SET vendedor_id = :vendedor_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":vendedor_id", $user->getVendedor()->getId());
        $stmt->bindParam(":id", $user->getId()); // Este bind estava faltando
        $stmt->execute();
    }

    private function atualizar(Usuario $usuario)
    {
        $sql = "UPDATE usuario SET nome = :nome, email = :email, senha = :senha, saldo = :saldo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':nome', $usuario->getNome());
        $stmt->bindParam(':email', $usuario->getEmail());
        $stmt->bindParam(':senha', $usuario->getSenha());
        $stmt->bindParam(':saldo', $usuario->getSaldo());
        $stmt->bindParam(':id', $usuario->getId());
        $stmt->execute();

        if ($usuario->getVendedor()) {
            $vendedorDAO = new VendedorDAO($this->pdo);
            $vendedor = $usuario->getVendedor();
            $vendedor->setUsuarioId($usuario->getId()); // Alteração aqui
            $vendedorDAO->persistir($vendedor);
        }
        return $usuario;
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        echo "<br>";
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<br>";
        var_dump($dados);
        if (!$dados) {
            throw new Exception("Usuário não encontrado com o email: " . $email);
        }
        $vendedorDAO = new VendedorDAO($this->pdo);
        // echo "<br>";
        // var_dump($_SESSION);

        $vendedor = $dados["vendedor_id"] ? $vendedorDAO->buscarPorUsuarioId($dados["id"]) : null;


        return new Usuario($dados["nome"], $dados["email"], $dados["senha"], $vendedor, $dados["id"], $dados["saldo"]);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Usuário não encontrado com o ID: " . $id);
        }

        $vendedorDAO = new VendedorDAO($this->pdo);
        $vendedor = $dados["id"] ? $vendedorDAO->buscarPorUsuarioId($dados["id"]) : null;

        return new Usuario($dados["nome"], $dados["email"], $dados["senha"], $vendedor, $dados["id"], $dados["saldo"]);
    }
    public function buscarPorIdVendedor($id)
    {
        $sql = "SELECT u.* FROM usuario u JOIN vendedores v ON u.id = v.usuario_id WHERE v.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Usuário não encontrado com o ID do vendedor: " . $id);
        }

        $vendedorDAO = new VendedorDAO($this->pdo);
        $vendedor = $vendedorDAO->buscarPorId($id); // Busca o vendedor pelo ID

        return new Usuario($dados["nome"], $dados["email"], $dados["senha"], $vendedor, $dados["id"], $dados["saldo"]);
    }

    public function excluir($id)
    {
        $sql = "DELETE FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

class VendedorDAO
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function persistir(Vendedor $vendedor)
    {
        if (!$vendedor->getId()) {
            return $this->criar($vendedor);
        } else {
            return $this->atualizar($vendedor);
        }
    }

    private function criar(Vendedor $vendedor)
    {
        $sql = "INSERT INTO vendedores (usuario_id, produtos) VALUES (:usuario_id, :produtos)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $vendedor->getUsuarioId()); // Alteração aqui
        $stmt->bindParam(':produtos', serialize($vendedor->getProdutos()));
        $stmt->execute();
        $vendedor->setId($this->pdo->lastInsertId());
        return $vendedor;
    }

    private function atualizar(Vendedor $vendedor)
    {
        $sql = "UPDATE vendedores SET produtos = :produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':produtos', serialize($vendedor->getProdutos()));
        $stmt->bindParam(':id', $vendedor->getId());
        $stmt->execute();
        return $vendedor;
    }

    public function buscarPorUsuarioId($usuario_id)
    {
        $sql = "SELECT * FROM vendedores WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Vendedor não encontrado com o usuário ID: " . $usuario_id);
        }

        return new Vendedor($usuario_id, $dados['id'], $dados['produtos']); // Alteração aqui
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM vendedores WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Vendedor não encontrado com o ID: " . $id);
        }

        return new Vendedor($dados['usuario_id'], $dados['id'], $dados['produtos']); // Alteração aqui
    }


    public function excluir($id)
    {
        $sql = "DELETE FROM vendedores WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
