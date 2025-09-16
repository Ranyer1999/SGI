<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/sgi/public/css/style.css">
    <title>SGI</title>
</head>
<header>

        <nav class="menu">
            <img src="/public/img/logo.png" alt="logo" id="logo">
            <ul>
                <li><button onclick="window.location.href='loginSgi.php'">Sair</button></li>
            </ul>
        </nav>
</header>
<body>
<form action="post" class="login-form">
    <h2>Cadastrar novo Docente</h2>
    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" required>
    <label for="cpf">CPF:</label>
    <input type="text" id="cpf" name="cpf" required>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <label for="data">Data de Nascimento:</label>
    <input type="date" id="data" name="data" required>
    <label for="telefone">Telefone:</label>
    <input type="tel" id="telefone" name="telefone" required>
    <button onclick="window.location.href='loginSgi.php'">Cancelar</button>
    <button onclick="window.location.href='loginSgi.php'">Cadastrar</button>
</form>
</body>
</html>