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

$erro = '';
$sucesso = '';

// Buscar dados necessários para os selects
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar cursos ativos
    $stmtCursos = $pdo->prepare("SELECT idcurso, nome_curso FROM curso WHERE idstatus_curso = (SELECT idstatus_curso FROM status_curso WHERE nome_stcurso = 'Ativo')");
    $stmtCursos->execute();
    $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar turnos
    $stmtTurnos = $pdo->prepare("SELECT idturno, nome_turno FROM turno");
    $stmtTurnos->execute();
    $turnos = $stmtTurnos->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar status de turma
    $stmtStatus = $pdo->prepare("SELECT idstatus_turma, nome_stturma FROM status_turma");
    $stmtStatus->execute();
    $status_turmas = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar grades curriculares
    $stmtGrades = $pdo->prepare("SELECT idgrade_curricular, status_grade, dt_vigencia FROM grade_curricular WHERE status_grade = 'Ativo'");
    $stmtGrades->execute();
    $grades = $stmtGrades->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro de conexão: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $nome_turma = $_POST["nome_turma"];
        $dt_inicio = $_POST["dt_inicio"];
        $dt_fim = $_POST["dt_fim"];
        $idstatus_turma = $_POST["idstatus_turma"];
        $idturno = $_POST["idturno"];
        $idgrade_curricular = $_POST["idgrade_curricular"];
        $idcurso = $_POST["idcurso"];
        
        // Validar datas
        if ($dt_inicio >= $dt_fim) {
            $erro = "A data de início deve ser anterior à data de fim.";
        } else {
            // Inserir a turma
            $stmt = $pdo->prepare("
                INSERT INTO turma (nome_turma, dt_inicio, dt_fim, idstatus_turma, idturno, idgrade_curricular, idcurso) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nome_turma, $dt_inicio, $dt_fim, $idstatus_turma, $idturno, $idgrade_curricular, $idcurso]);
            
            $sucesso = "Turma cadastrada com sucesso!";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar turma: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Turma</title>
</head>
<body>
    <h1>Cadastrar Nova Turma</h1>
    <a href="homeCoordenacao.php">Voltar para Home</a>

    <?php if (!empty($erro)): ?>
        <div style="color: red;"><?php echo $erro; ?></div>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
        <div style="color: green;"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form method="post">
        <div>
            <label for="nome_turma">Nome da Turma:</label>
            <input type="text" id="nome_turma" name="nome_turma" required>
        </div>

        <div>
            <label for="dt_inicio">Data de Início:</label>
            <input type="date" id="dt_inicio" name="dt_inicio" required>
        </div>

        <div>
            <label for="dt_fim">Data de Término:</label>
            <input type="date" id="dt_fim" name="dt_fim" required>
        </div>

        <div>
            <label for="idstatus_turma">Status da Turma:</label>
            <select id="idstatus_turma" name="idstatus_turma" required>
                <option value="">Selecione o status</option>
                <?php foreach ($status_turmas as $status): ?>
                    <option value="<?php echo $status['idstatus_turma']; ?>"><?php echo $status['nome_stturma']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="idturno">Turno:</label>
            <select id="idturno" name="idturno" required>
                <option value="">Selecione o turno</option>
                <?php foreach ($turnos as $turno): ?>
                    <option value="<?php echo $turno['idturno']; ?>"><?php echo $turno['nome_turno']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="idgrade_curricular">Grade Curricular:</label>
            <select id="idgrade_curricular" name="idgrade_curricular" required>
                <option value="">Selecione a grade curricular</option>
                <?php foreach ($grades as $grade): ?>
                    <option value="<?php echo $grade['idgrade_curricular']; ?>">
                        Grade (Vigência: <?php echo date('d/m/Y', strtotime($grade['dt_vigencia'])); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="idcurso">Curso:</label>
            <select id="idcurso" name="idcurso" required>
                <option value="">Selecione o curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['idcurso']; ?>"><?php echo $curso['nome_curso']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="submit" value="Cadastrar Turma">
    </form>
</body>
</html>