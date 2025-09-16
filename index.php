<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="static/img/icon.png">
    <title>Cadastro de Professores - Login</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<header>

        <nav class="menu">
            <img src="public/img/logo.png" alt="logo" id="logo">
            <ul>
                <li></li>
            </ul>
        </nav>
</header>
<form method="post" class="login-form">
    <h1>SGI</h1>
    <h2>Login</h2>

    <?php if (!empty($erro)): ?>
        <div class="error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    Email:<br><input type="email" name="email" required><br>
    Senha:<br><input type="password" name="senha" required><br>
    <input type="submit" value="Entrar">
    <a href="views/recuperarSenha.php">Recuperar Senha</a>
</form>



    <?php
    session_start();
    $pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $erro = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = $_POST["email"];
        $senha = $_POST["senha"];

        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario["Senha_hash"])) {
            $_SESSION["usuario"] = $usuario["Nome"];
            header("Location: views/homeSgi.php");
            exit;
        } else {
            $erro = "Email ou senha inválidos.";
        }
    }
    ?>


</body>

</html>