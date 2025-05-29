<?php
require_once "../../includes/verificar.php";
verificarLogin("aluno");
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Presenças - TopFit</title>
    <link rel="stylesheet" href="../../css/presencas-aluno.css">
</head>
<body>
    <div class="sidebar">
        <h2>TopFit</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="presencas.php" class="active">📅 Presenças</a>
        <a href="ficha.php">💪 Ficha de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../logout.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            Minhas Presenças
        </div>

        <div class="presenca-actions">
            <button class="btn-neon">Marcar Presença de Hoje</button>
        </div>

        <div class="tabela-presencas">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>29/05/2025</td>
                        <td>10:00</td>
                        <td><span class="status presente">Presente</span></td>
                    </tr>
                    <tr>
                        <td>28/05/2025</td>
                        <td>09:45</td>
                        <td><span class="status presente">Presente</span></td>
                    </tr>
                    <tr>
                        <td>27/05/2025</td>
                        <td>Faltou</td>
                        <td><span class="status falta">Falta</span></td>
                    </tr>
                    <!-- Exemplo, depois vamos puxar do banco -->
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
