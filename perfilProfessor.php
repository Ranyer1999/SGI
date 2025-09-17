<?php
session_start();
// Permitir acesso tanto para coordenadores quanto para o próprio professor
if (!isset($_SESSION['usuario'])) {
    header("Location: /sgi/index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listarDocentes.php");
    exit;
}

$idProfessor = $_GET['id'];
$host = "localhost";
$dbname = "sgi";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar dados do professor
    $stmt = $pdo->prepare("SELECT * FROM professor WHERE idprofessor = ?");
    $stmt->execute([$idProfessor]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$professor) {
        header("Location: lista_professores.php");
        exit;
    }
    
    // Buscar formações do professor
    $stmtFormacoes = $pdo->prepare("SELECT * FROM formacao_professor WHERE idprofessor = ? ORDER BY ano_conclusao DESC");
    $stmtFormacoes->execute([$idProfessor]);
    $formacoes = $stmtFormacoes->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar perfil: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Professor - <?php echo $professor['nome_prof']; ?></title>
</head>
<body>
    <h1>Perfil do Professor</h1>
    
    <?php if ($_SESSION['perfil'] === 'Coordenacao'): ?>
        <a href="lista_professores.php">Voltar para Lista</a>
    <?php else: ?>
        <a href="homeProfessor.php">Voltar para Home</a>
    <?php endif; ?>
    
    <?php if (isset($erro)): ?>
        <div><?php echo $erro; ?></div>
    <?php else: ?>
        <h2><?php echo $professor['nome_prof']; ?></h2>
        <p><strong>CPF:</strong> <?php echo $professor['cpf']; ?></p>
        <p><strong>Telefone:</strong> <?php echo $professor['telefone']; ?></p>
        <p><strong>Data de Nascimento:</strong> <?php echo date('d/m/Y', strtotime($professor['dt_nascimento'])); ?></p>
        <p><strong>Email:</strong> <?php echo $professor['email_prof']; ?></p>
        
        <h3>Formações</h3>
        <?php if (count($formacoes) > 0): ?>
            <ul>
                <?php foreach ($formacoes as $formacao): ?>
                    <li>
                        <strong><?php echo $formacao['nome_formacao']; ?></strong> - 
                        <?php echo $formacao['tipo_formacao']; ?> - 
                        <?php echo $formacao['instituicao']; ?> - 
                        Conclusão: <?php echo $formacao['ano_conclusao']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhuma formação cadastrada.</p>
        <?php endif; ?>
        
        <?php if ($_SESSION['perfil'] === 'Coordenacao'): ?>
            <p>
                <a href="editar_professor.php?id=<?php echo $professor['idprofessor']; ?>">Editar Professor</a> | 
                <a href="adicionar_formacao.php?id=<?php echo $professor['idprofessor']; ?>">Adicionar Formação</a>
            </p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>