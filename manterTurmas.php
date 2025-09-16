<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="static/img/icon.png">
    <link rel="stylesheet" href="style/visual.css">
    <title>Cadastro de Turmas</title>

</head>

<body>

    <div class="top-buttons">
        <button onclick="window.location.href='inicial.php'" class="btn-inicial">Tela Inicial</button>
        <button onclick="window.location.href='login.php'" class="btn-sair">Sair</button>
    </div>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensagem = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nome = $_POST["Nome"];
        $semestre = $_POST["Semestre"];

        $sql = "INSERT INTO Turmas (Nome, Semestre) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $semestre]);

        $mensagem = "Turma cadastrada com sucesso!";
    }

    $turmas = $pdo->query("SELECT * FROM Turmas")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h2>Cadastro de Turmas</h2>
    <div class="container">
        <form method="post">
            <?php if (!empty($mensagem)): ?>
                <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <label for="Nome">Nome da Turma:</label>
            <input type="text" id="Nome" name="Nome" required>

            <label for="Semestre">Semestre:</label>
            <input type="text" id="Semestre" name="Semestre" required>

            <input type="submit" value="Cadastrar Turma">
        </form>
    </div>

    <h2>Turmas Cadastradas</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Turma</th>
            <th>Semestre</th>
        </tr>
        <?php foreach ($turmas as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['id_turma']) ?></td>
                <td><?= htmlspecialchars($t['Nome']) ?></td>
                <td><?= htmlspecialchars($t['Semestre']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>