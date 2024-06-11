<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuário</title>
</head>
<body>
    <h2>Login de Usuário</h2>
    <form action="../controller/servidor.php" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" required><br>
        <input type="submit" name="btn_login">
    </form>
    <a href="./casdratar.html">casdratar</a>
</body>
</html>