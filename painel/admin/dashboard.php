<?php
session_start();
require_once '../../config/conexao.php';
require_once '../../includes/verificar.php';

verificarLogin('admin');

$nome_admin = $_SESSION["nome"] ?? 'Admin';

$sql_alunos = "SELECT COUNT(id) FROM usuarios WHERE tipo = 'aluno'";
$result_alunos = $conn->query($sql_alunos);
$total_alunos = $result_alunos->fetch_row()[0] ?? 0;

$sql_professores = "SELECT COUNT(id) FROM usuarios WHERE tipo = 'professor'";
$result_professores = $conn->query($sql_professores);
$total_professores = $result_professores->fetch_row()[0] ?? 0;

$sql_avaliacoes = "SELECT COUNT(id) FROM avaliacoes";
$result_avaliacoes = $conn->query($sql_avaliacoes);
$total_avaliacoes = $result_avaliacoes->fetch_row()[0] ?? 0;

$sql_fichas = "SELECT COUNT(id) FROM fichas";
$result_fichas = $conn->query($sql_fichas);
$total_fichas = $result_fichas->fetch_row()[0] ?? 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Dashboard do Admin - TOPFIT</title>
    <link rel="stylesheet" href="../../css/dashboard-admin.css">
</head>
<body>

    <button id="toggle-theme" class="theme-toggle">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <nav>
            <a href="dashboard.php" class="active">🏠 Início</a>
            <a href="usuarios.php">👤 Usuários</a>
            <a href="cameras.php">📹 Câmeras</a>
            <a href="../../logout.php">🚪 Sair</a>
        </nav>
    </div>

    <div class="main-content">
        <header class="dashboard-header">
            <h1>Bem-vindo(a), ADMIN <?php echo htmlspecialchars($nome_admin); ?>!</h1>
        </header>

        <main class="cards-container">
            <div class="card">
                <h3>Total de Alunos</h3>
                <p><?= $total_alunos; ?></p>
            </div>
            <div class="card">
                <h3>Total de Professores</h3>
                <p><?= $total_professores; ?></p>
            </div>
            <div class="card">
                <h3>Avaliações Registradas</h3>
                <p><?= $total_avaliacoes; ?></p>
            </div>
            <div class="card">
                <h3>Fichas Criadas</h3>
                <p><?= $total_fichas; ?></p>
            </div>
        </main>
    </div>

    <script>
        const toggleButton = document.getElementById('toggle-theme');
        const htmlElement = document.documentElement;

        function applyTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);

            if (theme === 'light') {
                toggleButton.textContent = '🌑 Modo Escuro';
            } else {
                toggleButton.textContent = '🌙 Modo Claro';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            const initialTheme = savedTheme || htmlElement.getAttribute('data-theme') || 'dark';
            applyTheme(initialTheme);

            toggleButton.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        });
</script>
</body>
</html>