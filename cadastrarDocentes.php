<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['perfil'] !== 'Coordenacao') {
    header("Location: /sgi/login.php");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $dbname = "sgi";
    $username = "root";
    $password = "";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $nome_prof = $_POST["nome_prof"];
        $cpf = $_POST["cpf"];
        $telefone = $_POST["telefone"];
        $dt_nascimento = $_POST["dt_nascimento"];
        $email_prof = $_POST["email_prof"];
        $senha_prof = $_POST["senha_prof"];
        
        // Verificar se o email já existe
        $stmtVerifica = $pdo->prepare("SELECT idusuario FROM usuario WHERE user_login = ?");
        $stmtVerifica->execute([$email_prof]);
        
        if ($stmtVerifica->fetch()) {
            $erro = "Este email já está cadastrado no sistema.";
        } else {
            // Primeiro, criar um usuário para o professor
            $stmtUsuario = $pdo->prepare("
                INSERT INTO usuario (idgrupo_acesso, user_login, senha, dt_criacao) 
                VALUES (
                    (SELECT idgrupo_acesso FROM grupo_acesso WHERE nome_grupo = 'Professores'), 
                    ?, 
                    ?, 
                    NOW()
                )
            ");
            $stmtUsuario->execute([$email_prof, $senha_prof]);
            $idUsuario = $pdo->lastInsertId();
            
            // Inserir o professor
            $stmtProfessor = $pdo->prepare("
                INSERT INTO professor (nome_prof, cpf, telefone, dt_nascimento, email_prof, idusuario) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtProfessor->execute([$nome_prof, $cpf, $telefone, $dt_nascimento, $email_prof, $idUsuario]);
            $idProfessor = $pdo->lastInsertId();
            
            
            $sucesso = "Professor cadastrado com sucesso! <a href='perfilProfessor.php?id=$idProfessor'>Ver perfil do professor</a>";

        }
        
    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar professor: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Professor</title>
</head>
<body>
    <h1>Cadastro de Professor</h1>
    <a href="homeCoordenacao.php">Voltar para Home</a>
    
    <?php if (!empty($erro)): ?>
        <div><?php echo $erro; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($sucesso)): ?>
        <div><?php echo $sucesso; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div>
            <label for="nome_prof">Nome Completo:</label>
            <input type="text" id="nome_prof" name="nome_prof" required>
        </div>
        
        <div>
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
        </div>
        
        <div>
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>
        </div>
        
        <div>
            <label for="dt_nascimento">Data de Nascimento:</label>
            <input type="date" id="dt_nascimento" name="dt_nascimento" required>
        </div>
        
        <div>
            <label for="email_prof">Email:</label>
            <input type="email" id="email_prof" name="email_prof" required>
        </div>
        
        <div>
            <label for="senha_prof">Senha:</label>
            <input type="password" id="senha_prof" name="senha_prof" required minlength="6">
        </div>
        
        <input type="submit" value="Cadastrar Professor">
    </form>
</body>
</html>
