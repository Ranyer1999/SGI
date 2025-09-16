<?php
$action = $_GET['action'] ?? '';
$id     = $_GET['id'] ?? null;
$termo  = $_POST['termo'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="shortcut icon" href="static/img/icon.png">
    <meta charset="UTF-8">
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
    <h2>Home</h2>
    <form action="post" class="login-form">
        Meu Perfil
    </form>
    <form action="post" class="login-form">
        <a href="">Home</a>
        <a href="">Turmas</a>
        <a href="">Historico</a>
        <a href="">Candidaturas</a>
    </form>
</body>

</html>
<body>