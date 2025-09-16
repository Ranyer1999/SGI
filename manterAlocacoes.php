<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="static/img/icon.png">
    <link rel="stylesheet" href="style/visual.css">
    <title>Cadastro de Alocações</title>
</head>

<body>

    <div class="top-buttons">
        <button class="btn-home" onclick="window.location.href='homeSgi.php'">Tela Inicial</button>
        <button class="btn-logout" onclick="window.location.href='login.php'">Sair</button>
    </div>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=sgbd;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cadastro = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $professor = $_POST["id_professor"];
        $disciplina = $_POST["id_disciplina"];
        $turma = $_POST["id_turma"];

        // Verificação para evitar duplicata
        $check = $pdo->prepare("SELECT COUNT(*) FROM alocacoes WHERE id_professor = ? AND id_disciplina = ? AND id_turma = ?");
        $check->execute([$professor, $disciplina, $turma]);

        if ($check->fetchColumn() > 0) {
            $cadastro = "Essa alocação já existe!";
        } else {
            $sql = "INSERT INTO alocacoes (id_professor, id_disciplina, id_turma) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$professor, $disciplina, $turma]);
            $cadastro = "Alocação cadastrada com sucesso!";
        }
    }

    $professores = $pdo->query("SELECT id_professor, Nome FROM Professores")->fetchAll(PDO::FETCH_ASSOC);
    $disciplinas = $pdo->query("SELECT id_disciplina, Nome FROM Disciplinas")->fetchAll(PDO::FETCH_ASSOC);
    $turmas = $pdo->query("SELECT id_turma, Nome FROM Turmas")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h2>Cadastro de Alocações</h2>
    <form method="post">

        <?php if (!empty($cadastro)): ?>
        <div class="cadastro"><?= htmlspecialchars($cadastro) ?></div>
        <?php endif; ?>

        <label>Professor:</label>
        <select name="id_professor" required>
            <option value="">Selecione...</option>
            <?php foreach ($professores as $p): ?>
            <option value="<?= $p['id_professor'] ?>"><?= htmlspecialchars($p['Nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Disciplina:</label>
        <select name="id_disciplina" required>
            <option value="">Selecione...</option>
            <?php foreach ($disciplinas as $d): ?>
            <option value="<?= $d['id_disciplina'] ?>"><?= htmlspecialchars($d['Nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Turma:</label>
        <select name="id_turma" required>
            <option value="">Selecione...</option>
            <?php foreach ($turmas as $t): ?>
            <option value="<?= $t['id_turma'] ?>"><?= htmlspecialchars($t['Nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Cadastrar Alocação">
    </form>

    <h2>Alocações Cadastradas</h2>
    <table>
        <tr>
            <th>Professor</th>
            <th>Disciplina</th>
            <th>Turma</th>
        </tr>
        <?php
        $all = $pdo->query("
        SELECT 
            p.nome AS professor, 
            d.nome AS disciplina, 
            t.nome AS turma
        FROM alocacoes a
        JOIN Professores p ON a.id_professor = p.id_professor
        JOIN Disciplinas d ON a.id_disciplina = d.id_disciplina
        JOIN Turmas t ON a.id_turma = t.id_turma
    ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($all as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['professor']) ?></td>
            <td><?= htmlspecialchars($r['disciplina']) ?></td>
            <td><?= htmlspecialchars($r['turma']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>