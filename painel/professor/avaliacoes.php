<?php
require_once "../../includes/verificar.php";
require_once "../../config/conexao.php";
verificarLogin("professor");

$alunos = [];
$stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE tipo = 'aluno' ORDER BY nome");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $alunos[] = $row;
}

$mensagem = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["aluno_id"])) {
    $aluno_id = $_POST["aluno_id"];
    $data = $_POST["data"];
    $peso = $_POST["peso"];
    $altura = $_POST["altura"];
    $gordura = $_POST["percentual_gordura"];
    $massa = $_POST["massa_magra"];
    $obs = $_POST["observacoes"];

    $stmt = $conn->prepare("INSERT INTO avaliacoes (aluno_id, data_avaliacao, peso, altura, percentual_gordura, massa_magra, observacoes, criado_em) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isdddds", $aluno_id, $data, $peso, $altura, $gordura, $massa, $obs);
    if ($stmt->execute()) {
        $mensagem = "✅ Avaliação registrada com sucesso!";
    } else {
        $mensagem = "❌ Erro ao salvar avaliação.";
    }
}

$avaliacoes = [];
$alunoSelecionado = $_GET["aluno_id"] ?? null;
if ($alunoSelecionado) {
    $stmt = $conn->prepare("SELECT * FROM avaliacoes WHERE aluno_id = ? ORDER BY data_avaliacao DESC");
    $stmt->bind_param("i", $alunoSelecionado);
    $stmt->execute();
    $avaliacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Avaliações Físicas - TOPFIT</title>
    <link rel="stylesheet" href="../../css/avaliacoes-professor.css">
</head>
<body>
    <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="fichas.php">💪 Fichas de Treino</a>
        <a href="avaliacoes.php" class="active">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../index.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1>Avaliações Físicas</h1>

        <?php if ($mensagem): ?>
            <p style="color: lime;"><?= $mensagem ?></p>
        <?php endif; ?>

        <div class="form-row">
            <form method="POST" class="form-avaliacao">
                <label for="aluno_id">Aluno:</label>
                <select name="aluno_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>"><?= htmlspecialchars($aluno['nome']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Data:</label>
                <input type="date" name="data" required>

                <label>Peso (kg):</label>
                <input type="number" step="0.01" name="peso" required>

                <label>Altura (m):</label>
                <input type="number" step="0.01" name="altura" required>

                <label>% Gordura:</label>
                <input type="number" step="0.1" name="percentual_gordura" required>

                <label>Massa Magra:</label>
                <input type="number" step="0.1" name="massa_magra" required>

                <label>Observações:</label>
                <textarea name="observacoes" rows="2"></textarea>

                <button type="submit">Salvar Avaliação</button>
            </form>
            
            <form method="GET" class="form-filtro">
                <label for="aluno_id">Filtrar por Aluno:</label>
                <select name="aluno_id" onchange="this.form.submit()">
                    <option value="">Selecione</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>" <?= ($alunoSelecionado == $aluno['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($alunoSelecionado && count($avaliacoes) === 0): ?>
                    <p class="no-avaliacao-message">Nenhuma avaliação registrada para este aluno ainda.</p>
                <?php endif; ?>
            </form>
        </div>

        <hr>

        <?php if ($alunoSelecionado && count($avaliacoes) > 0): ?>
            <h2>Avaliações do Aluno</h2>
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
                    <?php foreach ($avaliacoes as $av): ?>
                        <tr>
                            <td data-label="Data:"><?= date('d/m/Y', strtotime($av['data_avaliacao'])) ?></td>
                            <td data-label="Peso (kg):"><?= $av['peso'] ?></td>
                            <td data-label="Altura (m):"><?= $av['altura'] ?></td>
                            <td data-label="% Gordura:"><?= $av['percentual_gordura'] ?>%</td>
                            <td data-label="Massa Magra:"><?= $av['massa_magra'] ?></td>
                            <td data-label="Observações:"><?= htmlspecialchars($av['observacoes']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        const toggleButton = document.getElementById('toggle-theme');
        const htmlElement = document.documentElement;
        toggleButton.addEventListener('click', () => {
            const newTheme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            toggleButton.textContent = newTheme === 'dark' ? '🌙 Modo Claro' : '🌑 Modo Escuro';
        });
        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('theme') || 'dark';
            htmlElement.setAttribute('data-theme', saved);
            toggleButton.textContent = saved === 'dark' ? '🌙 Modo Claro' : '🌑 Modo Escuro';
        });
    </script>
</body>
</html>