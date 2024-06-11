<?php
class Produto {
    private $id;
    private $nome;
    private $quantidade;
    private $preco;
    private $vendedor_id;

    public function __construct($nome, $quantidade, $preco, $vendedor_id) {
        if (empty($nome) || $quantidade === null || $preco === null || $vendedor_id === null) {
            throw new Exception("Dados insuficientes para criar um produto.");
        }
        $this->nome = $nome;
        $this->quantidade = $quantidade;
        $this->preco = $preco;
        $this->vendedor_id = $vendedor_id;
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

    public function getQuantidade() {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade) {
        $this->quantidade = $quantidade;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }

    public function getVendedorId() {
        return $this->vendedor_id;
    }

    public function setVendedorId($vendedor_id) {
        $this->vendedor_id = $vendedor_id;
    }
}
class ProdutoDAO {
    private $pdo;

    public function __construct($pdo) {
        try {
            $this->pdo = $pdo;
        } catch (\Throwable $th) {
            $session["messagem"]  = $th->getMessage();
        }
    }

    public function persistir(Produto $produto) {
        if (!$produto->getId()) {
            return $this->criar($produto);
            
        } else {
            return $this->atualizar($produto);
        }
    }

    private function criar(Produto $produto) {
        $sql = "INSERT INTO produtos (nome, quant, preco, vendedor_id) VALUES (:nome, :quant, :preco, :vendedor_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome());
        $stmt->bindParam(':quant', $produto->getQuantidade());
        $stmt->bindParam(':preco', $produto->getPreco());
        $stmt->bindParam(':vendedor_id', $produto->getVendedorId());
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    private function atualizar(Produto $produto) {
        if(!$produto){
            throw new Exception("Error Processing Request", 1);
        }
        $sql = "UPDATE produtos SET nome = :nome, quant = :quant, preco = :preco WHERE id = :id";
        $nome = $produto->getNome();
        $quantidade = $produto->getQuantidade();
        $preco = $produto->getPreco();
        $id = $produto->getId();
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':quant', $quantidade);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    public function getProduto($id){
        require_once("../teste.php");
        // $sql = "SELECT * FROM produtos where id = :id";
        // $stmt = $this->pdo->prepare($sql);
        // $stmt->bindParam(":id", $id);
        // $stmt->execute();
        // return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM produtos WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $produto = new Produto($result["nome"],$result["quant"],$result["preco"],$result["vendedor_id"]);
            $produto->setId($result["id"]);
            return $produto;
        } catch (PDOException $e) {
            echo "Erro ao buscar produto: " . $e->getMessage();
            return null;
        }   
    }
    
    

    public function excluir($id) {
        $sql = "DELETE FROM produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function buscarPorVendedorId($vendedor_id) {
        $sql = "SELECT * FROM produtos WHERE vededor_id = :vededor_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':vededor_id', $vendedor_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAll() {
        $sql = "SELECT * FROM produtos";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
