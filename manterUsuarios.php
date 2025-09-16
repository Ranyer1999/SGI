<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="shortcut icon" href="static/img/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Cadastro de Usuários</title>
</head>

<body>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensagem = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cadastrar"])) {
        $nome = $_POST["Nome"];
        $email = $_POST["email"];
        $senha = $_POST["Senha_hash"];
        $perfil = $_POST["Perfil"];

        $validar = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
        if (!preg_match($validar, $senha)) {
            $mensagem = "<div class='mensagem error'>A senha deve ter no mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e caractere especial @$!%*?&.</div>";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (Nome, email, Senha_hash, Perfil) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $email, $senha_hash, $perfil]);
            $mensagem =  '<p>Usuário cadastrado com sucesso! Volte para a <a href="login.php" target="_self">Tela Inicial</a></p>';
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["recuperar"])) {
        $email_recuperar = $_POST["email_recuperar"];
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email_recuperar]);

        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $pdo->exec("CREATE TABLE IF NOT EXISTS recuperacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
            usado BOOLEAN DEFAULT FALSE
        )");

            $inserir = $pdo->prepare("INSERT INTO recuperacoes (email, token) VALUES (?, ?)");
            $inserir->execute([$email_recuperar, $token]);

            $link = "http://localhost/resetar_senha.php?token=$token";
            echo "<div class='mensagem success'>Link de recuperação gerado:</div>";
            echo "<div class='mensagem'><a href='$link' target='_blank'>$link</a></div>";
        } else {
            echo "<div class='mensagem error'>E-mail não encontrado.</div>";
        }
    }

    $usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h2>Cadastro de Usuários</h2>
    <form method="post">

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>


        <input type="hidden" name="cadastrar" value="1">
        Nome Completo:<br><input type="text" name="Nome" required>
        Email:<br><input type="email" name="email" required>
        Senha:<br><input type="password" name="Senha_hash" required>
        Perfil:<br>
        <select name="Perfil" required>
            <option value="admin">Admin</option>
            <option value="professor">Professor</option>
        </select>
        <input type="submit" value="Cadastrar Usuário">
    </form>