<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['perfil'] !== 'Coordenacao') {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$dbname = "sgi";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT * FROM professor ORDER BY nome_prof");
    $stmt->execute();
    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $erro = "Erro ao carregar professores: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Professores</title>
</head>
<body>
    <h1>Professores Cadastrados</h1>
    <a href="homeCoordenacao.php">Voltar para Home</a>
    
    <?php if (isset($erro)): ?>
        <div><?php echo $erro; ?></div>
    <?php endif; ?>
    
    <?php if (count($professores) > 0): ?>
        <ul>
            <?php foreach ($professores as $professor): ?>
                <li>
                    <a href="perfilProfessor.php?id=<?php echo $professor['idprofessor']; ?>">
                        <?php echo $professor['nome_prof']; ?>
                    </a>
                    - <?php echo $professor['email_prof']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nenhum professor cadastrado.</p>
    <?php endif; ?>
</body>
</html>