<?php
class Produto {
    private $id;
    private $nome;
    private $quantidade;
    private $preco;
    private $vendedor_id;

    public function __construct($id = null, $nome, $quantidade, $preco, $vendedor_id) {
        if (empty($nome) || $quantidade === null || $preco === null || $vendedor_id === null) {
            throw new Exception("Dados insuficientes para criar um produto.");
        }

        $this->id = $id;
        $this->nome = $nome;
        $this->quantidade = $quantidade;
        $this->preco = $preco;
        $this->vendedor_id = $vendedor_id;
    }

    public function getId() {
        return $this->id;
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

    public function __construct() {
        try {
            require_once("bd.php");
            $this->pdo = $pdo;
            var_dump($pdo); // Debug da variável $pdo
        } catch (\Throwable $th) {
            $session["messagem"]  = $th->getMessage();
        }
    }

    public function persistir(Produto $produto) {
        if ($produto->getId() !== null) {
            return $this->atualizar($produto);
        } else {
            return $this->criar($produto);
        }
    }

    private function criar(Produto $produto) {
        $sql = "INSERT INTO produtos (nome, quant, preco, vededor_id) VALUES (:nome, :quant, :preco, :vededor_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome());
        $stmt->bindParam(':quant', $produto->getQuantidade());
        $stmt->bindParam(':preco', $produto->getPreco());
        $stmt->bindParam(':vededor_id', $produto->getVendedorId());
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    private function atualizar(Produto $produto) {
        $sql = "UPDATE produtos SET nome = :nome, quant = :quant, preco = :preco, vededor_id = :vededor_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome());
        $stmt->bindParam(':quant', $produto->getQuantidade());
        $stmt->bindParam(':preco', $produto->getPreco());
        $stmt->bindParam(':vededor_id', $produto->getVendedorId());
        $stmt->bindParam(':id', $produto->getId());
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
}
