<?php
require_once "config/conexao.php";
$sucesso = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    $confirma = $_POST["confirma"];

    if ($senha !== $confirma) {
        $erro = "As senhas não coincidem.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $tipo = "aluno";

        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);

        if ($stmt->execute()) {
            $sucesso = "Cadastro realizado com sucesso! Faça o login.";
        } else {
            $erro = "Erro ao cadastrar. Esse e-mail já pode estar em uso.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar - TopFit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/img/icone.png" type="image/png">
    <link rel="stylesheet" href="css/cadastrar.css">
</head>
<body>
    <button id="toggle-theme" class="theme-toggle">🌙</button>

    <main class="form-page-container">
        <div class="form-wrapper">
            <header class="form-header">
                <a href="index.php" class="logo">TopFit <span>Academia</span></a>
               <h1>Crie sua Conta</h1>
                <p>Junte-se a nós e comece sua jornada fitness hoje mesmo.</p>
            </header>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <?php endif; ?>

            <section class="form-section">
                <form method="POST">
                    <div class="form-group">
                        <label for="nome">Nome completo</label>
                        <input type="text" name="nome" id="nome" placeholder="Seu nome completo" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="seu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="Crie uma senha forte" required>
                    </div>
                    <div class="form-group">
                        <label for="confirma">Confirmar senha</label>
                        <input type="password" name="confirma" id="confirma" placeholder="Repita a senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Cadastrar</button>
                </form>
            </section>

            <footer class="form-footer">
                <a href="login.php" class="voltar-link">← Já tem uma conta? Faça o login</a>
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