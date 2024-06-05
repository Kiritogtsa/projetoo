<?php
class Usuario{
    protected $user_id;
    protected $nome;
    protected $email;
    protected $senha;
    protected $is_vendedor;
    protected $vendedor_id;
    protected $saldo;
    public function _construct($user_id=null,$nome,$email,$senha=null,$is_vendedor=0,$vendedor_id=null) {
        if($nome=="" && $email== "" ){
            throw new Exception("nao foi passado um erro");
        }
        $this->nome=$nome;
        $this->email=$email;
        if(!$senha== ""){
            $this->senha=$senha;
        }
        if(!$is_vendedor==null || $is_vendedor!=1){
          $this->is_vendedor=0;
        }else{
            $this->is_vendedor=$is_vendedor;
        }
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
        $senhaHash = password_hash($usuario->getSenha(), PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $senhaHash);
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
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
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
class vendedor{
    private Usuario $usuario;
    private $vede_id;
    private $produtos;
    public function __construct(Usuario $usuario){
        if($usuario==null){
            throw new Exception('nao tem um usuario');
        }
        $this->usuario = $usuario;
        $this->vede_id = $this->usuario->getVendedorId();
    }
    public function getID() {
        return $this->vede_id;
    }

    public function getProdutos() {
        return $this->produtos;
    }

    public function setNome($produtos) {
        $this->produtos = $produtos;
    }
}

class vendedorDAO{

}