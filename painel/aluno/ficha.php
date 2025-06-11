<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("aluno");

require_once "../../config/conexao.php"; 

$aluno_id = $_SESSION['usuario_id']; 
$fichas = [];

// Buscar ficha do aluno agrupada por grupo muscular
$stmt = $conn->prepare("SELECT grupo_muscular, exercicio, series, repeticoes, descanso FROM fichas WHERE aluno_id = ? ORDER BY grupo_muscular");
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $fichas[$row['grupo_muscular']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Ficha de Treino - TopFit</title>
    <link rel="stylesheet" href="../../css/ficha-aluno.css">
</head>
<body>

        <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="presencas.php">📅 Presenças</a>
        <a href="ficha.php" class="active">💪 Ficha de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../index.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            Minha Ficha de Treino
        </div>

        <?php if (empty($fichas)) : ?>
            <p class="no-data">Nenhuma ficha de treino cadastrada ainda.</p>
        <?php else : ?>
            <?php foreach ($fichas as $grupo => $exercicios) : ?>
                <div class="grupo-muscular">
                    <h3><?= htmlspecialchars($grupo) ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Exercício</th>
                                <th>Séries</th>
                                <th>Repetições</th>
                                <th>Descanso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exercicios as $ex) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($ex['exercicio']) ?></td>
                                    <td><?= htmlspecialchars($ex['series']) ?></td>
                                    <td><?= htmlspecialchars($ex['repeticoes']) ?></td>
                                    <td><?= htmlspecialchars($ex['descanso']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
