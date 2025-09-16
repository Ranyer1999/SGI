<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="static/img/icon.png">
    <link rel="stylesheet" href="style/visual.css">
    <title>Cadastro de Horários</title>
</head>

<body>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mensagem = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $dia_semana     = $_POST["dia_semana"];
        $turno          = $_POST["turno"];
        $horario_inicio = $_POST["horario_inicio"];
        $horario_fim    = $_POST["horario_fim"];

        if ($horario_fim <= $horario_inicio) {
            $mensagem = "<p style='color:red; text-align:center;'>Horário de fim deve ser após o horário de início.</p>";
        } else {
            $sql = "INSERT INTO Horarios (dia_semana, turno, horario_inicio, horario_fim)
                VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$dia_semana, $turno, $horario_inicio, $horario_fim]);
            $mensagem = "<p style='color:green; text-align:center;'>Horário cadastrado com sucesso!</p>";
        }
    }

    // Buscar horários cadastrados para exibir
    $horarios = $pdo->query("SELECT * FROM Horarios ORDER BY dia_semana, turno, horario_inicio")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="top-buttons">
        <button class="btn-home" onclick="window.location.href='homeSgi.php'">Tela Inicial</button>
        <button class="btn-logout" onclick="window.location.href='loginSgi.php'">Sair</button>
    </div>

    <h2>Cadastro de Horários</h2>
    <div class="container">
        <?= $mensagem ?>

        <form method="post">
            <label for="dia_semana">Dia da Semana:</label>
            <select name="dia_semana" id="dia_semana" required>
                <option value="">Selecione...</option>
                <option value="Segunda">Segunda</option>
                <option value="Terça">Terça</option>
                <option value="Quarta">Quarta</option>
                <option value="Quinta">Quinta</option>
                <option value="Sexta">Sexta</option>
                <option value="Sábado">Sábado</option>
            </select>

            <label for="turno">Turno:</label>
            <select name="turno" id="Turno" required>
                <option value="">Selecione...</option>
                <option value="Manhã">Manhã</option>
                <option value="Tarde">Tarde</option>
                <option value="Noite">Noite</option>
            </select>

            <label for="horario_inicio">Horário Início:</label>
            <input type="time" name="horario_inicio" id="horario_inicio" required>

            <label for="horario_fim">Horário Fim:</label>
            <input type="time" name="horario_fim" id="horario_fim" required>

            <input type="submit" value="Cadastrar Horário">
        </form>
    </div>

    <?php if ($horarios): ?>
        <h2>Horários Cadastrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dia da Semana</th>
                    <th>Turno</th>
                    <th>Horário Início</th>
                    <th>Horário Fim</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horarios as $h): ?>
                    <tr>
                        <td><?= $h['id_horario'] ?></td>
                        <td><?= htmlspecialchars($h['dia_semana']) ?></td>
                        <td><?= htmlspecialchars($h['turno']) ?></td>
                        <td><?= htmlspecialchars(substr($h['horario_inicio'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars(substr($h['horario_fim'], 0, 5)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">Nenhum horário cadastrado ainda.</p>
    <?php endif; ?>

</body>

</html>