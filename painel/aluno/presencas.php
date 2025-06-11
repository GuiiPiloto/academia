<?php
require_once "../../includes/verificar.php";
verificarLogin("aluno");
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Presenças - TopFit</title>
    <link rel="stylesheet" href="../../css/presencas-aluno.css">
</head>
<body>

        <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="presencas.php" class="active">📅 Presenças</a>
        <a href="ficha.php">💪 Ficha de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../index.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            Minhas Presenças
        </div>

        <!-- Informação sobre a marcação automática -->
        <div class="info-box">
            <h4>Informação</h4>
            <p>As presenças são registradas automaticamente ao passar pela catraca da academia. Qualquer dúvida, procure um professor.</p>
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
                        <td>09/06/2025</td>
                        <td>06:30</td>
                        <td><span class="status presente">Presença Registrada</span></td>
                    </tr>
                    <tr>
                        <td>10/06/2025</td>
                        <td>N/A</td>
                        <td><span class="status falta">Presença Não Registrada</span></td>
                    </tr>
                    <tr>
                        <td>11/06/2025</td>
                        <td>08:30</td>
                        <td><span class="status presente">Presença Registrada</span></td>
                    </tr>
                    <tr>
                        <td>12/06/2025</td>
                        <td>N/A</td>
                        <td><span class="status falta">Presença Não Registrada</span></td>
                    </tr>
                    <!-- Em breve: registros reais puxados do banco de dados -->
                </tbody>
            </table>
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

            const initialTheme = htmlElement.getAttribute('data-theme') || 'dark'; // Pega o tema do HTML ou 'dark' como fallback
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
