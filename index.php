<?php
session_start();
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $dbname = "sgi";
    $username = "root";
    $password = "";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $email = $_POST["email"];
        $senha = $_POST["senha"];
        
        // Verificar se é um coordenador, professor ou administrador
        $stmt = $pdo->prepare("
            SELECT u.*, g.nome_grupo, 
                   CASE 
                     WHEN g.nome_grupo = 'Professores' THEN p.idprofessor
                     WHEN g.nome_grupo = 'Coordenacao' THEN c.idcoordenador
                     WHEN g.nome_grupo = 'Administradores' THEN a.idadmin
                   END as id_perfil
            FROM usuario u 
            JOIN grupo_acesso g ON u.idgrupo_acesso = g.idgrupo_acesso 
            LEFT JOIN professor p ON u.idusuario = p.idusuario AND g.nome_grupo = 'Professores'
            LEFT JOIN coordenador c ON u.idusuario = c.idusuario AND g.nome_grupo = 'Coordenacao'
            LEFT JOIN administrador a ON u.idusuario = a.idusuario AND g.nome_grupo = 'Administradores'
            WHERE u.user_login = ? AND u.senha = ? 
            AND (g.nome_grupo = 'Coordenacao' OR g.nome_grupo = 'Professores' OR g.nome_grupo = 'Administradores')
        ");
        $stmt->execute([$email, $senha]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            $_SESSION["usuario"] = $usuario["user_login"];
            $_SESSION["perfil"] = $usuario["nome_grupo"];
            $_SESSION["id_usuario"] = $usuario["idusuario"];
            $_SESSION["id_perfil"] = $usuario["id_perfil"];
            
            // Redireciona conforme o perfil
            if ($usuario["nome_grupo"] === "Coordenacao") {
                header("Location: views/homeCoordenacao.php");
            } else if ($usuario["nome_grupo"] === "Administradores") {
                header("Location: homeAdmin.php");
            } else {
                // Redireciona para o perfil do professor
                header("Location: views/perfilProfessor.php?id=" . $usuario["id_perfil"]);
            }
            exit;
        } else {
            $erro = "Email ou senha inválidos.";
        }
    } catch (PDOException $e) {
        $erro = "Erro de conexão: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestão Institucional - Login</title>
</head>
<body>
    <h1>SGI - Sistema de Gestão Institucional</h1>
    <h2>Login</h2>

    <?php if (!empty($erro)): ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin: 10px 0;">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <table>
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" id="email" name="email" required></td>
            </tr>
            <tr>
                <td><label for="senha">Senha:</label></td>
                <td><input type="password" id="senha" name="senha" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <input type="submit" value="Entrar">
                </td>
            </tr>
        </table>
    </form>
    
    <div>
        <a href="recuperarSenha.php">Recuperar Senha</a>
    </div>
    
    <div>
        <h3>Informações de Acesso:</h3>
        <ul>
            <li><strong>Professores:</strong> Acesso ao perfil pessoal</li>
            <li><strong>Coordenação:</strong> Acesso ao painel de coordenação</li>
            <li><strong>Administradores:</strong> Acesso ao painel administrativo</li>
        </ul>
    </div>
</body>
</html>
