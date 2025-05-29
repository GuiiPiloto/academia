<?php
require_once "../../includes/verificar.php";
verificarLogin("aluno");
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno - TopFit</title>
    <link rel="stylesheet" href="../../css/dashboard-aluno.css">
</head>
<body>
    <div class="sidebar">
        <h2>TopFit</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="presencas.php">📅 Presenças</a>
        <a href="ficha.php">💪 Ficha de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../logout.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            Bem-vindo, <?php echo $_SESSION["nome"] ?? 'Aluno'; ?>!
        </div>

        <div class="cards">
            <div class="card">
                <h3>Presenças</h3>
                <a href="presencas.php">Ver minhas presenças</a>
            </div>

            <div class="card">
                <h3>Minha Ficha</h3>
                <a href="ficha.php">Ver ficha de treino</a>
            </div>

            <div class="card">
                <h3>Avaliações</h3>
                <a href="avaliacoes.php">Ver avaliações físicas</a>
            </div>

            <div class="card">
                <h3>Mensagens</h3>
                <a href="mensagens.php">Ver mensagens</a>
            </div>
        </div>

        <div class="infos">
            <div class="info-box">
                <h4>📅 Último login</h4>
                <p><?php echo date('d/m/Y'); ?></p>
            </div>
            <div class="info-box">
                <h4>📈 Presença no mês</h4>
                <p>3 de 20 dias</p>
            </div>
            <div class="info-box">
                <h4>🏋️ Progresso da Ficha</h4>
                <p>Etapa 4 de 4 concluída</p>
            </div>
            <div class="info-box">
                <h4>🔥 Motivação do dia</h4>
                <p>“Cada treino é um passo mais perto do seu objetivo.”</p>
            </div>
        </div>
    </div>
</body>
</html>
