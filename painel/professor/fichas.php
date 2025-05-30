<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("professor");

require_once "../../config/conexao.php";

// Buscar todos os alunos cadastrados
$sql = "SELECT id, nome FROM usuarios WHERE tipo = 'aluno'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Obter o ID do aluno selecionado se houver
$aluno_id = isset($_GET['aluno_id']) ? $_GET['aluno_id'] : null;

// Variáveis para a ficha
$grupo_muscular = $exercicio = $series = $repeticoes = $descanso = "";

// Verifica se já existe ficha para o aluno
$ficha = null;
if ($aluno_id) {
    $sql_ficha = "SELECT * FROM fichas WHERE aluno_id = ?";
    $stmt_ficha = $conn->prepare($sql_ficha);
    $stmt_ficha->bind_param("i", $aluno_id);
    $stmt_ficha->execute();
    $ficha_result = $stmt_ficha->get_result();
    $ficha = $ficha_result->fetch_assoc();

    if ($ficha) {
        $grupo_muscular = $ficha['grupo_muscular'];
        $exercicio = $ficha['exercicio'];
        $series = $ficha['series'];
        $repeticoes = $ficha['repeticoes'];
        $descanso = $ficha['descanso'];
    }
}

// Quando o formulário for submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grupo_muscular = $_POST['grupo_muscular'];
    $exercicio = $_POST['exercicio'];
    $series = $_POST['series'];
    $repeticoes = $_POST['repeticoes'];
    $descanso = $_POST['descanso'];

    // Verifica se a ficha já existe para esse aluno
    if ($ficha) {
        // Atualiza a ficha existente
        $sql_atualizar = "UPDATE fichas SET grupo_muscular = ?, exercicio = ?, series = ?, repeticoes = ?, descanso = ? WHERE aluno_id = ?";
        $stmt_atualizar = $conn->prepare($sql_atualizar);
        $stmt_atualizar->bind_param("sssssi", $grupo_muscular, $exercicio, $series, $repeticoes, $descanso, $aluno_id);
        $stmt_atualizar->execute();
    } else {
        // Cria uma nova ficha
        $sql_inserir = "INSERT INTO fichas (aluno_id, grupo_muscular, exercicio, series, repeticoes, descanso, criado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_inserir = $conn->prepare($sql_inserir);
        $stmt_inserir->bind_param("isssssi", $aluno_id, $grupo_muscular, $exercicio, $series, $repeticoes, $descanso, $_SESSION["usuario_id"]);
        $stmt_inserir->execute();
    }

    // Redireciona para a página de fichas
    header("Location: fichas.php?aluno_id=" . $aluno_id);
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Fichas de Treino - TOPFIT</title>
    <link rel="stylesheet" href="../../css/ficha-professor.css">
</head>
<body>

    <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="fichas.php" class="active">💪 Fichas de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../index.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1>Selecione um aluno para criar ou editar a ficha de treino</h1>
        </div>

        <div class="alunos">
            <form action="fichas.php" method="get">
                <select name="aluno_id" onchange="this.form.submit()">
                    <option value="">Escolha um aluno</option>
                    <?php while ($aluno = $result->fetch_assoc()): ?>
                        <option value="<?php echo $aluno['id']; ?>" <?php echo ($aluno['id'] == $aluno_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($aluno['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <?php if ($aluno_id): ?>
            <h2>Ficha de Treino - Aluno: <?php echo htmlspecialchars($ficha ? $ficha['aluno_id'] : $aluno_id); ?></h2>
            <form action="fichas.php" method="POST">
                <input type="hidden" name="aluno_id" value="<?php echo $aluno_id; ?>">

                <label for="grupo_muscular">Grupo Muscular</label>
                <input type="text" name="grupo_muscular" value="<?php echo htmlspecialchars($grupo_muscular); ?>" required>

                <label for="exercicio">Exercício</label>
                <input type="text" name="exercicio" value="<?php echo htmlspecialchars($exercicio); ?>" required>

                <label for="series">Séries</label>
                <input type="number" name="series" value="<?php echo htmlspecialchars($series); ?>" required>

                <label for="repeticoes">Repetições</label>
                <input type="text" name="repeticoes" value="<?php echo htmlspecialchars($repeticoes); ?>" required>

                <label for="descanso">Descanso (em segundos)</label>
                <input type="text" name="descanso" value="<?php echo htmlspecialchars($descanso); ?>" required>

                <button type="submit"><?php echo $ficha ? 'Atualizar Ficha' : 'Criar Ficha'; ?></button>
            </form>
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
