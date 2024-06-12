<?php
class Produto {
    private $id;
    private $nome;
    private $quantidade;
    private $preco;
    private $vendedor;

    public function __construct($nome, $quantidade, $preco, $vendedor, $id = null) {
        if(!$vendedor){
            throw new Exception("erro ao acessar o produto");
        }
        $this->nome = $nome;
        $this->quantidade = $quantidade;
        $this->preco = $preco;
        $this->vendedor = $vendedor;
        $this->id = $id;
    }

    // Getters and Setters
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

    public function getVendedor() {
        return $this->vendedor;
    }

    public function setVendedor($vendedor) {
        $this->vendedor = $vendedor;
    }
}

class ProdutoDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function persistir(Produto $produto) {
        if (!$produto->getId()) {
            return $this->criar($produto);
        } else {
            return $this->atualizar($produto);
        }
    }

    private function criar(Produto $produto) {
        $sql = "INSERT INTO produtos (nome, quant, preco, vendedor_id ) VALUES (:nome, :quant, :preco, :vendedor_id )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome());
        $stmt->bindParam(':quant', $produto->getQuantidade());
        $stmt->bindParam(':preco', $produto->getPreco());
        $stmt->bindParam(':vendedor_id ', $produto->getVendedor()->getId());
        $stmt->execute();
        $produto->setId($this->pdo->lastInsertId());
        return $produto->getId();
    }

    private function atualizar(Produto $produto) {
        $sql = "UPDATE produtos SET nome = :nome, quant = :quant, preco = :preco WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $produto->getNome());
        $stmt->bindParam(':quant', $produto->getQuantidade());
        $stmt->bindParam(':preco', $produto->getPreco());
        $stmt->bindParam(':id', $produto->getId());
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            throw new Exception("Produto não encontrado com o ID: " . $id);
        }

        $usuarioDAO = new UsuarioDAO($this->pdo); // Alteração aqui
        $vendedorDAO = new VendedorDAO($this->pdo);
        $vendedor = $vendedorDAO->buscarPorId($dados["vendedor_id "]);
        $usuario = $usuarioDAO->buscarPorId($vendedor->getUsuarioId()); // Alteração aqui

        $produto = new Produto($dados["nome"], $dados["quant"], $dados["preco"], $usuario); // Alteração aqui
        $produto->setId($dados["id"]);
        return $produto;
    }

    public function buscarPorVendedorId($usuario_id) {
        $sql = "SELECT * FROM produtos WHERE usuario_id = :usuario_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $usuarioDAO = new UsuarioDAO($this->pdo); // Alteração aqui
        $usuario = $usuarioDAO->buscarPorId($usuario_id); // Alteração aqui

        $produtos = [];
        foreach ($dados as $produtoData) {
            $produto = new Produto($produtoData["nome"], $produtoData["quant"], $produtoData["preco"], $usuario); // Alteração aqui
            $produto->setId($produtoData["id"]);
            $produtos[] = $produto;
        }
        return $produtos;
    }

    public function excluir($id) {
        $sql = "DELETE FROM produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getAll() {
        $sql = "SELECT * FROM produtos";
        $stmt = $this->pdo->query($sql);
        $dados =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        $usuarioDAO = new UsuarioDAO($this->pdo);
        $produtos = array();
        foreach ($dados as $dado) {
            $usuario = $usuarioDAO->buscarPorIdVendedor($dado["vendedor_id"]);
            $produto = new Produto($dado["nome"], $dado["quant"], $dado["preco"], $usuario); 
            $produto->setId($dado["id"]);
            $produtos[] = $produto;
        }
        return $produtos;
    }
}
