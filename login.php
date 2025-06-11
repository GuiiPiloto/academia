<?php
session_start();
require_once "config/conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $senhaDigitada = $_POST["senha"];

    $sql = "SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $hashSalvoNoBanco = $usuario['senha'];

            if (password_verify($senhaDigitada, $hashSalvoNoBanco)) {
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["nome"] = $usuario["nome"];
                $_SESSION["tipo"] = $usuario["tipo"];

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
    <button id="toggle-theme" class="theme-toggle">🌙</button>

    <main class="form-page-container">
        <div class="form-wrapper">
            <header class="form-header">
                <a href="index.php" class="logo">TOPFIT <span>Academia</span></a>
                <h1>Acesse sua Conta</h1>
                <p>Bem-vindo(a) de volta! Entre com seus dados.</p>
            </header>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>

            <section class="form-section">
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="seu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="Sua senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Entrar</button>
                </form>
            </section>

            <footer class="form-footer">
                <p>Ainda não tem conta? <a href="cadastrar.php" class="link-destaque">Cadastre-se</a></p>
                <a href="index.php" class="voltar-link">← Voltar à Página Inicial</a>
            </footer>
        </div>
    </main>

    <script>
        const toggleButton = document.getElementById("toggle-theme");
        const html = document.documentElement;

        function applyTheme(theme) {
            html.setAttribute("data-theme", theme);
            localStorage.setItem("theme", theme);
            toggleButton.textContent = theme === 'light' ? '🌑' : '🌙';
        }

        const initialTheme = localStorage.getItem("theme") || 'dark';
        applyTheme(initialTheme);

        toggleButton.addEventListener("click", () => {
            const currentTheme = html.getAttribute("data-theme");
            applyTheme(currentTheme === "dark" ? "light" : "dark");
        });
    </script>
</body>
</html>