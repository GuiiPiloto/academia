<?php
require_once "../../includes/verificar.php";
require_once "../../config/conexao.php";
verificarLogin("aluno");

$nome_aluno = $_SESSION["nome"] ?? 'Aluno';
$aluno_id = $_SESSION["usuario_id"];

$sql_presencas = "SELECT COUNT(*) AS total FROM presencas WHERE aluno_id = ? AND MONTH(data_presenca) = MONTH(CURDATE()) AND YEAR(data_presenca) = YEAR(CURDATE())";
$stmt_presencas = $conn->prepare($sql_presencas);
$stmt_presencas->bind_param("i", $aluno_id);
$stmt_presencas->execute();
$presencas_no_mes = $stmt_presencas->get_result()->fetch_assoc()['total'] ?? 0;

$sql_mensagens = "SELECT COUNT(*) AS total FROM mensagens WHERE aluno_id = ? AND lida = 0 AND remetente = 'professor'";
$stmt_mensagens = $conn->prepare($sql_mensagens);
$stmt_mensagens->bind_param("i", $aluno_id);
$stmt_mensagens->execute();
$mensagens_nao_lidas = $stmt_mensagens->get_result()->fetch_assoc()['total'] ?? 0;

$sql_ficha = "SELECT grupo_muscular FROM fichas WHERE aluno_id = ? ORDER BY criado_em DESC LIMIT 1";
$stmt_ficha = $conn->prepare($sql_ficha);
$stmt_ficha->bind_param("i", $aluno_id);
$stmt_ficha->execute();
$foco_treino = $stmt_ficha->get_result()->fetch_assoc()['grupo_muscular'] ?? 'Nenhum';

?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Dashboard do Aluno - TOPFIT</title>
    <link rel="stylesheet" href="../../css/dashboard-aluno.css"> 
</head>
<body>

    <button id="toggle-theme">🌙 Modo Claro</button>

    <aside class="sidebar">
        <h2>TOPFIT</h2>
        <nav>
            <a href="dashboard.php" class="active">🏠 Início</a>
            <a href="presencas.php">📅 Presenças</a>
            <a href="ficha.php">💪 Ficha de Treino</a>
            <a href="avaliacoes.php">📊 Avaliações</a>
            <a href="mensagens.php">💬 Mensagens</a>
            <a href="../../logout.php">🚪 Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="dashboard-header">
            <h1>Bem-vindo, <?php echo htmlspecialchars($nome_aluno); ?>!</h1>
            <p>Seu progresso começa agora. Vamos lá!</p>
        </header>

        <section class="cards">
            <a href="presencas.php" class="card">
                <h3>📅 Presenças</h3>
                <p>Ver meu histórico</p>
            </a>
            <a href="ficha.php" class="card">
                <h3>💪 Ficha de Treino</h3>
                <p>Acessar meu treino</p>
            </a>
            <a href="avaliacoes.php" class="card">
                <h3>📊 Avaliações</h3>
                <p>Ver meus resultados</p>
            </a>
            <a href="mensagens.php" class="card">
                <h3>💬 Mensagens</h3>
                <p>Falar com professor</p>
            </a>
        </section>

        <section class="infos">
            <div class="info-box">
                <h4>📈 Presenças no Mês</h4>
                <p class="info-dado">4</p>  <!-- FUTURAMENTE ADICIONAR A PRESENÇA NA CATRACA COLOCAR ESSE CÓDIGO
                                            <p class="info-dado"><?php echo $presencas_no_mes;?></p> -->
            </div>
            <div class="info-box">
                <h4>🎯 Foco do Treino</h4>
                <p class="info-dado small-text"><?php echo htmlspecialchars($foco_treino); ?></p>
            </div>
            <div class="info-box">
                <h4>📨 Mensagens Não Lidas</h4>
                <p class="info-dado"><?php echo $mensagens_nao_lidas; ?></p>
            </div>
             <div class="info-box">
                <h4>🔥 Dica do Dia</h4>
                <p class="info-dado small-text">“A dor é temporária, a glória é eterna.”</p>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('toggle-theme');
            const htmlElement = document.documentElement;

            function applyTheme(theme) {
                htmlElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                toggleButton.textContent = (theme === 'light') ? '🌑 Modo Escuro' : '🌙 Modo Claro';
            }

            const savedTheme = localStorage.getItem('theme') || 'dark';
            applyTheme(savedTheme);

            toggleButton.addEventListener('click', () => {
                const newTheme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        });
    </script>

</body>
</html>