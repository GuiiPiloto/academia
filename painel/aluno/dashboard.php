<?php
session_start();
require_once "includes/verificar.php"; // Garante que o usuário está logado
verificarLogin("aluno"); // Garante que é aluno

// Aqui você pode buscar as presenças no banco quando o sistema estiver integrado

$nome = $_SESSION["nome"];
?>
<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Presenças - TopFit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/dashboard_aluno.css">
</head>
<body>
    <div class="container">
        <button class="btn-voltar" onclick="window.location.href='dashboard.php'">⬅ Voltar</button>
        <h1>Presenças de <?php echo $nome; ?></h1>

        <div class="presencas-lista">
            <!-- Conteúdo fictício por enquanto -->
            <div class="presenca">
                <p>📅 27/05/2025</p>
                <span class="status presente">Presente</span>
            </div>
            <div class="presenca">
                <p>📅 28/05/2025</p>
                <span class="status falta">Falta</span>
            </div>
            <div class="presenca">
                <p>📅 29/05/2025</p>
                <span class="status presente">Presente</span>
            </div>
        </div>

        <p class="creditos">© 2025 TopFit Academia</p>
    </div>

    <script>
        // Tema claro/escuro
        const html = document.documentElement;
        if (localStorage.getItem("theme") === "light") {
            html.setAttribute("data-theme", "light");
        }
    </script>
</body>
</html>
