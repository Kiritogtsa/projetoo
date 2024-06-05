<?php
class Usuario{
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $is_vendedor;
    private $vendedor_id;
    private $saldo;
    public function _construct($id=null,$nome,$email,$senha=null,$is_vendedor=0,$vendedor_id=null) {
        
    }
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function persistir(Usuario $usuario) {
        if ($usuario->getId() !== null) {
            return $this->atualizar($usuario);
        } else {
            return $this->criar($usuario);
        }
    }
    private function criar(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (nome, email, senha, is_vendedor, vendedor_id) VALUES (:nome, :email, :senha, :is_vendedor, :vendedor_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $usuario->getNome());
        $stmt->bindParam(':email', $usuario->getEmail());
        $stmt->bindParam(':senha', $usuario->getSenha());
        $stmt->bindParam(':is_vendedor', $usuario->getIsVendedor());
        $stmt->bindParam(':vendedor_id', $usuario->getVendedorId());
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    private function atualizar(Usuario $usuario) {
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, is_vendedor = :is_vendedor, vendedor_id = :vendedor_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $usuario->getNome());
        $stmt->bindParam(':email', $usuario->getEmail());
        $stmt->bindParam(':senha', $usuario->getSenha());
        $stmt->bindParam(':is_vendedor', $usuario->getIsVendedor());
        $stmt->bindParam(':vendedor_id', $usuario->getVendedorId());
        $stmt->bindParam(':id', $usuario->getId());
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    public function buscarPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function excluir($id) {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

class vendedor extends Usuario{
    
}