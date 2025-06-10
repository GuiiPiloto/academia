<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("professor");

$nome_professor = $_SESSION["nome"] ?? 'Professor';
$usuario_id = $_SESSION["usuario_id"];

require_once "../../config/conexao.php";

$sql_fichas = "SELECT COUNT(*) AS fichas_ultimo_mes FROM fichas WHERE criado_em >= CURDATE() - INTERVAL 1 MONTH AND criado_por = ?";
$stmt_fichas = $conn->prepare($sql_fichas);
$stmt_fichas->bind_param("i", $usuario_id);
$stmt_fichas->execute();
$fichas_result = $stmt_fichas->get_result()->fetch_assoc();
$fichas_ultimo_mes = $fichas_result['fichas_ultimo_mes'];

$sql_mensagens = "SELECT COUNT(*) AS mensagens_nao_lidas FROM mensagens WHERE professor_id = ? AND lida = 0 AND remetente = 'aluno'";
$stmt_mensagens = $conn->prepare($sql_mensagens);
$stmt_mensagens->bind_param("i", $usuario_id);
$stmt_mensagens->execute();
$mensagens_result = $stmt_mensagens->get_result()->fetch_assoc();
$mensagens_nao_lidas = $mensagens_result['mensagens_nao_lidas'];

?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Dashboard do Professor - TOPFIT</title>
    <link rel="stylesheet" href="../../css/dashboard-professor.css">
</head>
<body>

    <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="index.php" class="active">🏠 Início</a>
        <a href="fichas.php">💪 Fichas de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="logout.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            Bem-vindo, Professor <?php echo htmlspecialchars($nome_professor); ?>!
        </div>

        <div class="cards">
            <div class="card">
                <h3>Fichas de Treino</h3>
                <a href="fichas.php">Criar/Editar fichas</a>
            </div>

            <div class="card">
                <h3>Avaliações</h3>
                <a href="avaliacoes.php">Ver/Registrar avaliações</a>
            </div>

            <div class="card">
                <h3>Mensagens</h3>
                <a href="mensagens.php">Ver mensagens</a>
            </div>
        </div>

        <div class="infos">
            <div class="info-box">
                <h4>📝 Fichas Criadas (Último Mês)</h4>
                <p><?php echo $fichas_ultimo_mes; ?></p>
            </div>
            <div class="info-box">
                <h4>💬 Mensagens Não Lidas</h4>
                <p><?php echo $mensagens_nao_lidas; ?></p>
            </div>
            <div class="info-box">
                <h4>🌟 Dica do Dia</h4>
                <p>“A Prática Sempre Leva a Perfeição!”</p>
            </div>
        </div>
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

            if (savedTheme) {
                applyTheme(savedTheme);
            } else {
                const initialTheme = htmlElement.getAttribute('data-theme') || 'dark';
                applyTheme(initialTheme);
            }

            toggleButton.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        });
    </script>

</body>
</html>