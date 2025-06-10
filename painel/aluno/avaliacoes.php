<?php
require_once "../../includes/verificar.php";
verificarLogin("aluno");

require_once "../../config/conexao.php";

$aluno_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM avaliacoes WHERE aluno_id = ? ORDER BY data_avaliacao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Avaliações - TopFit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <link rel="stylesheet" href="../../css/avaliacoes-aluno.css">
</head>
<body>

        <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="presencas.php">📅 Presenças</a>
        <a href="ficha.php">💪 Ficha de Treino</a>
        <a href="avaliacoes.php" class="active">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../index.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            Minhas Avaliações Físicas
        </div>

        <div class="tabela-avaliacoes">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Peso (kg)</th>
                        <th>Altura (m)</th>
                        <th>% Gordura</th>
                        <th>Massa Magra</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= date("d/m/Y", strtotime($linha['data_avaliacao'])) ?></td>
                            <td><?= $linha['peso'] ?></td>
                            <td><?= $linha['altura'] ?></td>
                            <td><?= $linha['percentual_gordura'] ?? '-' ?></td>
                            <td><?= $linha['massa_magra'] ?? '-' ?></td>
                            <td><?= $linha['observacoes'] ? nl2br(htmlspecialchars($linha['observacoes'])) : '-' ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($resultado->num_rows === 0): ?>
                        <tr><td colspan="6" style="text-align:center;">Nenhuma avaliação disponível</td></tr>
                    <?php endif; ?>
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
