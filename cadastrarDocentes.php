<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['perfil'] !== 'Coordenacao') {
    header("Location: /sgi/index.php");
    exit;
}

$erro = '';
$sucesso = '';

// Função para validar a força da senha
function validarSenha($senha)
{
    // Verificar se tem pelo menos uma letra maiúscula
    if (!preg_match('/[A-Z]/', $senha)) {
        return "A senha deve conter pelo menos uma letra maiúscula";
    }

    // Verificar se tem pelo menos um número
    if (!preg_match('/[0-9]/', $senha)) {
        return "A senha deve conter pelo menos um número";
    }

    // Verificar se tem pelo menos um caractere especial
    if (!preg_match('/[^a-zA-Z0-9]/', $senha)) {
        return "A senha deve conter pelo menos um caractere especial";
    }

    // Verificar comprimento mínimo
    if (strlen($senha) < 6) {
        return "A senha deve ter pelo menos 6 caracteres";
    }

    return true;
}

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

        // Validar a senha
        $validacaoSenha = validarSenha($senha_prof);
        if ($validacaoSenha !== true) {
            $erro = $validacaoSenha;
        } else {
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
    <style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
    }

    h1 {
        color: #2c3e50;
        text-align: center;
    }

    form {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    div {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="date"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #3498db;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    input[type="submit"]:hover {
        background-color: #2980b9;
    }

    .erro {
        color: #e74c3c;
        background-color: #fadbd8;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .sucesso {
        color: #27ae60;
        background-color: #d5f4e6;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .requisitos-senha {
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    a {
        color: #3498db;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <h1>Cadastro de Professor</h1>
    <a href="homeCoordenacao.php">Voltar para Home</a>

    <?php if (!empty($erro)): ?>
    <div class="erro"><?php echo $erro; ?></div>
    <?php endif; ?>

    <?php if (!empty($sucesso)): ?>
    <div class="sucesso"><?php echo $sucesso; ?></div>
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
            <div class="requisitos-senha">
                A senha deve conter pelo menos:
                <ul>
                    <li>6 caracteres</li>
                    <li>1 letra maiúscula</li>
                    <li>1 número</li>
                    <li>1 caractere especial (ex: @, #, $, etc.)</li>
                </ul>
            </div>
        </div>

        <input type="submit" value="Cadastrar Professor">
    </form>

    <script>
    // Validação em tempo real da senha
    document.getElementById('senha_prof').addEventListener('input', function() {
        const senha = this.value;
        const requisitos = {
            maiuscula: /[A-Z]/.test(senha),
            numero: /[0-9]/.test(senha),
            especial: /[^a-zA-Z0-9]/.test(senha),
            comprimento: senha.length >= 6
        };

        // Atualizar visualmente os requisitos (opcional)
        const itens = document.querySelectorAll('.requisitos-senha li');
        itens[0].style.color = requisitos.comprimento ? 'green' : 'red';
        itens[1].style.color = requisitos.maiuscula ? 'green' : 'red';
        itens[2].style.color = requisitos.numero ? 'green' : 'red';
        itens[3].style.color = requisitos.especial ? 'green' : 'red';
    });
    </script>
</body>

</html>
