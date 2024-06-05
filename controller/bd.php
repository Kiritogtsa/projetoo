<?php
try {
    $dsn = 'mysql:host=localhost;port=3306;dbname=loja';
    $username = 'root';
    $password = '';
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida!";
} catch (\Throwable $th) {
    echo "Erro ao conectar ao banco de dados: " . $th->getMessage();
}
