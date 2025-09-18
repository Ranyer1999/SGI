<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['perfil'] !== 'Coordenacao') {
    header("Location: /sgi/index.php");
    exit;
}

$host = "localhost";
$dbname = "sgi";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar todas as turmas com informações relacionadas
    $stmt = $pdo->prepare("
        SELECT 
            t.*,
            c.nome_curso,
            tu.nome_turno,
            st.nome_stturma,
            g.dt_vigencia
        FROM turma t
        JOIN curso c ON t.idcurso = c.idcurso
        JOIN turno tu ON t.idturno = tu.idturno
        JOIN status_turma st ON t.idstatus_turma = st.idstatus_turma
        JOIN grade_curricular g ON t.idgrade_curricular = g.idgrade_curricular
        ORDER BY t.dt_inicio DESC, t.nome_turma
    ");
    $stmt->execute();
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar turmas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Turmas</title>
</head>
<body>
    <h1>Turmas Cadastradas</h1>
    <a href="homeCoordenacao.php">Voltar para Home</a>
    <a href="cadastrarTurma.php">Cadastrar Nova Turma</a>
    
    <?php if (isset($erro)): ?>
        <div><?php echo $erro; ?></div>
    <?php endif; ?>
    
    <?php if (count($turmas) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Nome da Turma</th>
                    <th>Curso</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    <th>Turno</th>
                    <th>Status</th>
                    <th>Vigência da Grade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turmas as $turma): ?>
                    <tr>
                        <td><?php echo $turma['nome_turma']; ?></td>
                        <td><?php echo $turma['nome_curso']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($turma['dt_inicio'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($turma['dt_fim'])); ?></td>
                        <td><?php echo $turma['nome_turno']; ?></td>
                        <td><?php echo $turma['nome_stturma']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($turma['dt_vigencia'])); ?></td>
                        <td>
                            <a href="editarTurma.php?id=<?php echo $turma['idturma']; ?>">Editar</a>
                            <a href="visualizarTurma.php?id=<?php echo $turma['idturma']; ?>">Visualizar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma turma cadastrada.</p>
    <?php endif; ?>
</body>
</html>