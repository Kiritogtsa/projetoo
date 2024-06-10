<?php

require_once("./model/bd.php");
$sql = "SELECT * FROM produtos where id = 1";
$stmt = $pdo->prepare($sql);
// $stmt->bindParam(":id", $id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($result);