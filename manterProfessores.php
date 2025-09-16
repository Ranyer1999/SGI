<?php
$pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["Nome"];
    $cpf = $_POST["CPF"];
    $email = $_POST["email"];
    $formacao = $_POST["Formacao"];

    $sql = "INSERT INTO Professores (Nome, CPF, email, Formacao) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $cpf, $email, $formacao]);

    $mensagem = "Professor cadastrado com sucesso!";
}

$professores = $pdo->query("SELECT * FROM Professores")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="static/img/icon.png">
    <link rel="stylesheet" href="style/visual.css">
    <title>Cadastro de Professores</title>
</head>

<body>

    <div class="botoes">
        <button onclick="window.location.href='homeSgi.php'">Tela Inicial</button>
        <button class="sair" onclick="window.location.href='loginSgi.php'">Sair</button>
    </div>

    <h2>Cadastro de Professores</h2>

    <form method="post">
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        Nome:<br><input type="text" name="Nome" required><br>
        CPF:<br><input type="text" name="CPF" required><br>
        Email:<br><input type="email" name="email" required><br>
        Formação:<br><input type="text" name="Formacao" required><br>
        <input type="submit" value="Cadastrar Professor">
    </form>

    <h2>Professores cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Email</th>
            <th>Formação</th>
        </tr>
        <?php foreach ($professores as $p): ?>
            <tr>
                <td><?= $p['id_professor'] ?></td>
                <td><?= htmlspecialchars($p['Nome']) ?></td>
                <td><?= $p['CPF'] ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['Formacao']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>