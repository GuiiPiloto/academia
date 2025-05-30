<?php
session_start();
require_once "config/conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $senha = sha1($_POST["senha"]);

    $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["tipo"] = $usuario["tipo"];

            // Redireciona com base no tipo
            switch ($usuario["tipo"]) {
                case 'aluno':
                    header("Location: painel/aluno/dashboard.php");
                    break;
                case 'professor':
                    header("Location: painel/professor/dashboard.php");
                    break;
                case 'admin':
                    header("Location: painel/admin/dashboard.php");
                    break;
                default:
                    $erro = "Tipo de usuário inválido.";
                    session_destroy();
                    exit;
            }
            exit;
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    } else {
        $erro = "Erro na conexão com o banco.";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Login - TopFit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/img/icone.png" type="image/png">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <button id="toggle-theme">🌙 Modo Claro</button>
    <div class="login-container">
        <h1>Login TopFit</h1>
        <?php if ($erro): ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <a href="index.php" class="btn-voltar">← Voltar à Página Inicial</a>
        <p class="link-cadastro">
    Ainda não tem conta? <a href="cadastrar.php">Cadastre-se</a>
        </p>
        <p class="creditos">© 2025 TopFit Academia</p>
    </div>

    <script>
        const toggleButton = document.getElementById("toggle-theme");
        const html = document.documentElement;

        if (localStorage.getItem("theme") === "light") {
            html.setAttribute("data-theme", "light");
            toggleButton.textContent = "🌑 Modo Escuro";
        }

        toggleButton.addEventListener("click", () => {
            if (html.getAttribute("data-theme") === "dark") {
                html.setAttribute("data-theme", "light");
                localStorage.setItem("theme", "light");
                toggleButton.textContent = "🌑 Modo Escuro";
            } else {
                html.setAttribute("data-theme", "dark");
                localStorage.setItem("theme", "dark");
                toggleButton.textContent = "🌙 Modo Claro";
            }
        });
    </script>
</body>
</html>
