<?php
class Usuario{
    protected $user_id;
    protected $nome;
    protected $email;
    protected $senha;
    protected $is_vendedor;
    protected $vendedor_id;
    protected $saldo;
    public function __construct($nome,$email,$senha,$is_vendedor,$vendedor_id=null,$id=null) {
        if($nome=="" || $email== "" || $senha==""){
            throw new Exception("nao foi passado um erro");
        }
        $this->nome=$nome;
        $this->email=$email;
        if(!$senha== ""){
            $this->senha=$senha;
        }
        if ($id !== null) {
            $this->user_id = $id;
        }
        $this->is_vendedor=$is_vendedor;
        $this->vendedor_id=$vendedor_id;
    }
    public function getId() {
        return $this->user_id;
    }
    public function setId($user_id) {
        $this->user_id = $user_id;
    }
    public function getNome() {
        return $this->nome;
    }
    public function setNome($nome) {
        $this->nome = $nome;
    }
    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function getSenha() {
        return $this->senha;
    }
    public function setSenha($senha) {
        $this->senha = $senha;
    }
    public function getIsVendedor() {
        return $this->is_vendedor;
    }
    public function setIsVendedor($is_vendedor) {
        $this->is_vendedor = $is_vendedor;
    }

    public function getVendedorId() {
        return $this->vendedor_id;
    }

    public function setVendedorId($vendedor_id) {
        $this->vendedor_id = $vendedor_id;
    }
    public function getSaldo() {
        return $this->saldo;
    }
    public function setSaldo($saldo) {
        $this->saldo = $saldo;
    }
}
class UsuarioDAO {
    private $pdo;
    public function __construct() {
        try {
            require_once('bd.php');
            $this->pdo = $pdo;
        } catch (\Throwable $th) {
            $session["messagem"]  = $th->getMessage();
        }
    }
    // arumar as logicas de persit ta tudo errado menos a do produto
    public function persistir(Usuario $usuario) {
        if ($usuario->getId() !== null) {
            return $this->atualizar($usuario);
        } else {
            return $this->criar($usuario);
        }
    }

    private function criar(Usuario $usuario) {
        $sql = "INSERT INTO usuario (nome, email, senha, vendedor, vendedor_id) VALUES (:nome, :email, :senha, :is_vendedor, :vendedor_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $usuario->getNome());
        $stmt->bindParam(':email', $usuario->getEmail());
        $senhaHash = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':is_vendedor', $usuario->getIsVendedor());
        $stmt->bindParam(':vendedor_id', $usuario->getVendedorId());
        $stmt->execute();

        $usuarioId = $this->pdo->lastInsertId();
        $usuario->setId($usuarioId);

        if ($usuario->getIsVendedor() == 1) {
            $vendedorId = $this->criarVendedor($usuarioId);
            $this->atualizarVendedorId($usuarioId, $vendedorId);
            $usuario->setVendedorId($vendedorId);
        }
        return $usuario;
    }

    private function criarVendedor($usuarioId) {
        $sql = "INSERT INTO vendedores (usuario_id, produtos) VALUES (:usuario_id, :produtos)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->bindValue(':produtos', 0);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    private function atualizarVendedorId($usuarioId, $vendedorId) {
        $sql = "UPDATE usuario SET vendedor_id = :vendedor_id WHERE id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':vendedor_id', $vendedorId);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();
    }

    private function atualizar(Usuario $usuario) {
        $sql = "UPDATE usuario SET nome = :nome, email = :email, vendedor = :is_vendedor, vendedor_id = :vendedor_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $usuario->getNome());
        $stmt->bindParam(':email', $usuario->getEmail());
        $stmt->bindParam(':is_vendedor', $usuario->getIsVendedor());
        $stmt->bindParam(':vendedor_id', $usuario->getVendedorId());
        $stmt->bindParam(':id', $usuario->getId());
        $stmt->execute();
        return $usuario;
    }

    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Usuario($dados["nome"],$dados["email"],$dados["senha"],$dados["vendedor"],$dados["vendedor_id"],$dados["id"]);
    }

    public function excluir($id) {
        $sql = "DELETE FROM usuario WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
class vendedor{
    private Usuario $usuario;
    private $vede_id;
    private $produtos;

    public function __construct(Usuario $usuario) {
        if ($usuario == null) {
            throw new Exception('O vendedor deve ter um usuário associado');
        }
        if ($usuario->getId() == null) {
            throw new Exception('O usuário associado deve ter um ID');
        }
        $this->usuario = $usuario;
        $this->vede_id = $usuario->getVendedorId();
        $this->produtos = 0; // valor padrão
    }

    public function getID() {
        return $this->vede_id;
    }

    public function getProdutos() {
        return $this->produtos;
    }

    public function setProdutos($produtos) {
        $this->produtos = $produtos;
    }

    public function getUsuario() {
        return $this->usuario;
    }
}

class vendedorDAO{
    private $pdo;
    public function __construct() {
        try {
            require_once('bd.php');
            $this->pdo = $pdo;
        } catch (\Throwable $th) {
            $session["messagem"]  = $th->getMessage();
        }
    }
    public function persistir(Vendedor $vendedor) {
        $usuario = $vendedor->getUsuario();
        if ($usuario->getId() == null) {
            throw new Exception('O usuário associado deve ter um ID');
        }

        if ($vendedor->getID() !== null) {
            return $this->atualizar($vendedor);
        } else {
            return $this->criar($vendedor);
        }
    }

    private function criar(Vendedor $vendedor) {
        $sql = "INSERT INTO vendedores (usuario_id, produtos) VALUES (:usuario_id, :produtos)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $vendedor->getUsuario()->getId());
        $stmt->bindParam(':produtos', $vendedor->getProdutos());
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    private function atualizar(Vendedor $vendedor) {
        $sql = "UPDATE vendedores SET produtos = :produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':produtos', $vendedor->getProdutos());
        $stmt->bindParam(':id', $vendedor->getID());
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function buscarPorUsuarioId($usuario_id) {
        $sql = "SELECT * FROM vendedores WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluir($id) {
        $sql = "DELETE FROM vendedores WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}