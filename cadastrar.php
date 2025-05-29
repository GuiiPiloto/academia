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
    <link rel="stylesheet" href="css/cadastrar.css">
</head>
<body>
    <button id="toggle-theme">🌙 Modo Claro</button>
    <div class="cadastro-container">
        <h1>Cadastro de Aluno</h1>
        <?php if ($erro): ?><p class="erro"><?php echo $erro; ?></p><?php endif; ?>
        <?php if ($sucesso): ?><p class="sucesso"><?php echo $sucesso; ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="password" name="confirma" placeholder="Confirmar senha" required>
            <button type="submit">Cadastrar</button>
        </form>
        <a href="login.php" class="voltar-login">← Voltar para o login</a>
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
