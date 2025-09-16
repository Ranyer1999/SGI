<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="static/img/icon.png">
    <link rel="stylesheet" href="style/visual.css">
    <title>Cadastro de Disciplinas</title>
</head>

<body>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensagem = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nome = $_POST["Nome"];
        $carga_horaria = $_POST["carga_horaria"];

        $sql = "INSERT INTO Disciplinas (Nome, carga_horaria) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $carga_horaria]);

        $mensagem = "Disciplina cadastrada com sucesso!";
    }

    $disciplinas = $pdo->query("SELECT id_disciplina, Nome FROM Disciplinas")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="top-buttons">
        <button class="btn-home" onclick="window.location.href='inicial.php'">Tela Inicial</button>
        <button class="btn-logout" onclick="window.location.href='login.php'">Sair</button>
    </div>

    <h2>Cadastro de Disciplinas</h2>
    <form method="post">
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        Nome:<br><input type="text" name="Nome" required><br>
        Carga Horária:<br><input type="number" name="carga_horaria" required><br>
        <input type="submit" value="Cadastrar Disciplina">
    </form>

    <h2>Disciplinas cadastradas</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Carga Horária</th>
        </tr>
        <?php
        $all = $pdo->query("SELECT * FROM Disciplinas")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($all as $d) {
            echo "<tr>
                <td>{$d['id_disciplina']}</td>
                <td>" . htmlspecialchars($d['Nome']) . "</td>
                <td>{$d['carga_horaria']}</td>
              </tr>";
        }
        ?>
    </table>

</body>

</html>