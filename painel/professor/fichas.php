<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("professor");

require_once "../../config/conexao.php";

$mensagem = "";
$aluno_id = isset($_GET['aluno_id']) ? intval($_GET['aluno_id']) : null;

if (isset($_GET['excluir_id'])) {
    $excluir_id = intval($_GET['excluir_id']);
    $sql_delete = "DELETE FROM fichas WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $excluir_id);
    if ($stmt_delete->execute()) {
        $mensagem = "✅ Exercício excluído com sucesso!";
    } else {
        $mensagem = "❌ Erro ao excluir o exercício.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = isset($_POST['aluno_id']) ? intval($_POST['aluno_id']) : null;
    $grupo_muscular = $_POST['grupo_muscular'];
    $exercicio = $_POST['exercicio'];
    $series = $_POST['series'];
    $repeticoes = $_POST['repeticoes'];
    $descanso = $_POST['descanso'];

    if (!$aluno_id) {
        $mensagem = "❌ Erro: Nenhum aluno selecionado.";
    } else {
        $sql_insert = "INSERT INTO fichas (aluno_id, grupo_muscular, exercicio, series, repeticoes, descanso, criado_por) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isssssi", $aluno_id, $grupo_muscular, $exercicio, $series, $repeticoes, $descanso, $_SESSION["usuario_id"]);
        
        if ($stmt_insert->execute()) {
            $mensagem = "✅ Exercício adicionado com sucesso!";
        } else {
            $mensagem = "❌ Erro ao adicionar exercício.";
        }
    }
}

$sql_alunos = "SELECT id, nome FROM usuarios WHERE tipo = 'aluno'";
$stmt_alunos = $conn->prepare($sql_alunos);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->get_result();

$fichas = [];
if ($aluno_id) {
    $sql_fichas = "SELECT * FROM fichas WHERE aluno_id = ?";
    $stmt_fichas = $conn->prepare($sql_fichas);
    $stmt_fichas->bind_param("i", $aluno_id);
    $stmt_fichas->execute();
    $fichas_result = $stmt_fichas->get_result();
    while ($linha = $fichas_result->fetch_assoc()) {
        $fichas[] = $linha;
    }
}

$aluno_nome = "";
if ($aluno_id) {
    $sql_nome = "SELECT nome FROM usuarios WHERE id = ?";
    $stmt_nome = $conn->prepare($sql_nome);
    $stmt_nome->bind_param("i", $aluno_id);
    $stmt_nome->execute();
    $stmt_nome->bind_result($aluno_nome);
    $stmt_nome->fetch();
    $stmt_nome->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichas de Treino - TOPFIT</title>
    <link rel="stylesheet" href="../../css/ficha-professor.css">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
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
            <h2>Selecione um aluno para criar ou editar a ficha de treino</h2>
        </div>

        <div class="alunos">
            <form action="fichas.php" method="get" id="filtro-aluno-form">
                <select name="aluno_id" onchange="document.getElementById('filtro-aluno-form').submit()" required>
                    <option value="">Escolha um aluno</option>
                    <?php while ($aluno = $result_alunos->fetch_assoc()): ?>
                        <option value="<?php echo $aluno['id']; ?>" <?php echo ($aluno['id'] == $aluno_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($aluno['nome']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <?php if ($aluno_id): ?>
            <h2>Ficha de Treino - <?php echo htmlspecialchars($aluno_nome); ?></h2>
            <form action="fichas.php?aluno_id=<?php echo $aluno_id; ?>" method="POST">
                <input type="hidden" name="aluno_id" value="<?php echo $aluno_id; ?>">

                <label for="grupo_muscular">Grupo Muscular</label>
                <input type="text" name="grupo_muscular" required>

                <label for="exercicio">Exercício</label>
                <input type="text" name="exercicio" required>

                <label for="series">Séries</label>
                <input type="number" name="series" required>

                <label for="repeticoes">Repetições</label>
                <input type="text" name="repeticoes" required>

                <label for="descanso">Descanso (em segundos)</label>
                <input type="text" name="descanso" required>

                <button type="submit">Adicionar Exercício</button>
            </form>

            <?php if (!empty($fichas)): ?>
                <h2>📄 Relatório da Ficha de Treino</h2>
                <?php
                $agrupado = [];
                foreach ($fichas as $item) {
                    $grupo = $item['grupo_muscular'];
                    if (!isset($agrupado[$grupo])) {
                        $agrupado[$grupo] = [];
                    }
                    $agrupado[$grupo][] = $item;
                }
                ?>

                <?php foreach ($agrupado as $grupo => $exercicios): ?>
                    <h3><?php echo htmlspecialchars($grupo); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Exercício</th>
                                <th>Séries</th>
                                <th>Repetições</th>
                                <th>Descanso (s)</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exercicios as $ex): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ex['exercicio']); ?></td>
                                    <td><?php echo htmlspecialchars($ex['series']); ?></td>
                                    <td><?php echo htmlspecialchars($ex['repeticoes']); ?></td>
                                    <td><?php echo htmlspecialchars($ex['descanso']); ?></td>
                                    <td>
                                        <a href="fichas.php?aluno_id=<?php echo $aluno_id; ?>&excluir_id=<?php echo $ex['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este exercício?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <div id="container-notificacoes"></div>

    <script>
        const toggleButton = document.getElementById('toggle-theme');
        const htmlElement = document.documentElement;

        function applyTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            toggleButton.textContent = (theme === 'light') ? '🌑 Modo Escuro' : '🌙 Modo Claro';
        }

        function mostrarNotificacao(mensagem, tipo = 'sucesso') {
            const container = document.getElementById('container-notificacoes');
            const notificacaoDiv = document.createElement('div');
            notificacaoDiv.className = `notificacao ${tipo}`;
            notificacaoDiv.textContent = mensagem;
            container.appendChild(notificacaoDiv);
            setTimeout(() => {
                notificacaoDiv.remove();
            }, 4000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            applyTheme(savedTheme);
            
            toggleButton.addEventListener('click', () => {
                const newTheme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        });
    </script>

    <?php
    if (!empty($mensagem)) {
        $tipo = (strpos($mensagem, '✅') !== false) ? 'sucesso' : 'erro';
        echo "<script>mostrarNotificacao('" . addslashes($mensagem) . "', '" . $tipo . "');</script>";
    }
    ?>
</body>
</html>