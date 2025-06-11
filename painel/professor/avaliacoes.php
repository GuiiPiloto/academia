<?php
require_once "../../includes/verificar.php";
require_once "../../config/conexao.php";
verificarLogin("professor");

$mensagem = "";
$alunoSelecionado = filter_input(INPUT_GET, "aluno_id", FILTER_VALIDATE_INT);

if (isset($_GET['delete_id'])) {
    $id_para_deletar = filter_input(INPUT_GET, 'delete_id', FILTER_VALIDATE_INT);
    if ($id_para_deletar) {
        $stmt = $conn->prepare("DELETE FROM avaliacoes WHERE id = ?");
        $stmt->bind_param("i", $id_para_deletar);
        if ($stmt->execute()) {
            $mensagem = "✅ Avaliação excluída com sucesso!";
        } else {
            $mensagem = "❌ Erro ao excluir a avaliação.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aluno_id = filter_input(INPUT_POST, "aluno_id", FILTER_VALIDATE_INT);
    $data = $_POST["data"];
    $peso = filter_input(INPUT_POST, "peso", FILTER_VALIDATE_FLOAT);
    $altura = filter_input(INPUT_POST, "altura", FILTER_VALIDATE_FLOAT);
    $gordura = filter_input(INPUT_POST, "percentual_gordura", FILTER_VALIDATE_FLOAT);
    $massa = filter_input(INPUT_POST, "massa_magra", FILTER_VALIDATE_FLOAT);
    $obs = trim($_POST["observacoes"]);
    $avaliacao_id_edit = filter_input(INPUT_POST, "avaliacao_id", FILTER_VALIDATE_INT);

    if ($aluno_id && $data && $peso !== false && $altura !== false && $gordura !== false && $massa !== false) {
        if ($avaliacao_id_edit) {
            $query = "UPDATE avaliacoes SET data_avaliacao = ?, peso = ?, altura = ?, percentual_gordura = ?, massa_magra = ?, observacoes = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("sddddsi", $data, $peso, $altura, $gordura, $massa, $obs, $avaliacao_id_edit);
                if ($stmt->execute()) {
                    $mensagem = "✅ Avaliação atualizada com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao atualizar a avaliação.";
                }
            } else {
                $mensagem = "❌ Erro na preparação da consulta de atualização.";
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO avaliacoes (aluno_id, data_avaliacao, peso, altura, percentual_gordura, massa_magra, observacoes, criado_em) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param("isdddds", $aluno_id, $data, $peso, $altura, $gordura, $massa, $obs);
                if ($stmt->execute()) {
                    $mensagem = "✅ Avaliação registrada com sucesso!";
                } else {
                    $mensagem = "❌ Erro ao salvar avaliação.";
                }
            } else {
                $mensagem = "❌ Erro na preparação da consulta de inserção.";
            }
        }
    } else {
        $mensagem = "❌ Dados inválidos. Verifique os campos.";
    }
}

$avaliacaoParaEditar = null;
if (isset($_GET['edit_id'])) {
    $id_para_editar = filter_input(INPUT_GET, 'edit_id', FILTER_VALIDATE_INT);
    if ($id_para_editar) {
        $stmt = $conn->prepare("SELECT * FROM avaliacoes WHERE id = ?");
        $stmt->bind_param("i", $id_para_editar);
        $stmt->execute();
        $result = $stmt->get_result();
        $avaliacaoParaEditar = $result->fetch_assoc();
    }
}

$alunos = [];
$queryAlunos = "SELECT id, nome FROM usuarios WHERE tipo = 'aluno' ORDER BY nome";
$resultAlunos = $conn->query($queryAlunos);
if ($resultAlunos) {
    while ($row = $resultAlunos->fetch_assoc()) {
        $alunos[] = $row;
    }
}

$avaliacoes = [];
if ($alunoSelecionado) {
    $stmt = $conn->prepare("SELECT * FROM avaliacoes WHERE aluno_id = ? ORDER BY data_avaliacao DESC");
    if ($stmt) {
        $stmt->bind_param("i", $alunoSelecionado);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $avaliacoes = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
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
        <h1><?= $avaliacaoParaEditar ? 'Editar Avaliação Física' : 'Avaliações Físicas' ?></h1>

        <?php if ($mensagem): ?>
            <p style="color: lime;"><?= $mensagem ?></p>
        <?php endif; ?>

        <div class="form-row">
            <form method="POST" action="avaliacoes.php?aluno_id=<?= $alunoSelecionado ?>" class="form-avaliacao">
                
                <?php if ($avaliacaoParaEditar): ?>
                    <input type="hidden" name="avaliacao_id" value="<?= $avaliacaoParaEditar['id'] ?>">
                <?php endif; ?>

                <label for="aluno_id">Aluno:</label>
                <select name="aluno_id" required <?= $avaliacaoParaEditar ? 'disabled' : '' ?>>
                    <option value="">Selecione</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>" 
                            <?php 
                                $idAlunoAtual = $avaliacaoParaEditar ? $avaliacaoParaEditar['aluno_id'] : ($alunoSelecionado ?? '');
                                if ($aluno['id'] == $idAlunoAtual) echo 'selected'; 
                            ?>
                        >
                            <?= htmlspecialchars($aluno['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($avaliacaoParaEditar): ?>
                    <input type="hidden" name="aluno_id" value="<?= $avaliacaoParaEditar['aluno_id'] ?>">
                <?php endif; ?>

                <label>Data:</label>
                <input type="date" name="data" required value="<?= htmlspecialchars($avaliacaoParaEditar['data_avaliacao'] ?? '') ?>">

                <label>Peso (kg):</label>
                <input type="number" step="0.01" name="peso" required value="<?= htmlspecialchars($avaliacaoParaEditar['peso'] ?? '') ?>">

                <label>Altura (m):</label>
                <input type="number" step="0.01" name="altura" required value="<?= htmlspecialchars($avaliacaoParaEditar['altura'] ?? '') ?>">

                <label>% Gordura:</label>
                <input type="number" step="0.1" name="percentual_gordura" required value="<?= htmlspecialchars($avaliacaoParaEditar['percentual_gordura'] ?? '') ?>">

                <label>Massa Magra:</label>
                <input type="number" step="0.1" name="massa_magra" required value="<?= htmlspecialchars($avaliacaoParaEditar['massa_magra'] ?? '') ?>">

                <label>Observações:</label>
                <textarea name="observacoes" rows="2"><?= htmlspecialchars($avaliacaoParaEditar['observacoes'] ?? '') ?></textarea>

                <button type="submit"><?= $avaliacaoParaEditar ? 'Atualizar Avaliação' : 'Salvar Avaliação' ?></button>
                <?php if ($avaliacaoParaEditar): ?>
                    <a href="avaliacoes.php?aluno_id=<?= $alunoSelecionado ?>" style="text-align: center; display: block; margin-top: 10px;">Cancelar Edição</a>
                <?php endif; ?>
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

                <?php if ($alunoSelecionado && count($avaliacoes) === 0 && !$mensagem): ?>
                    <p class="no-avaliacao-message">Nenhuma avaliação registrada para este aluno ainda.</p>
                <?php endif; ?>
            </form>
        </div>

        <hr>

        <?php if ($alunoSelecionado && count($avaliacoes) > 0): ?>
            <h2>Histórico de Avaliações do Aluno</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Peso (kg)</th>
                        <th>Altura (m)</th>
                        <th>% Gordura</th>
                        <th>Massa Magra</th>
                        <th>Observações</th>
                        <th>Ações</th>
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
                            <td data-label="Ações:" class="actions-cell">
                                <a href="avaliacoes.php?aluno_id=<?= $alunoSelecionado ?>&edit_id=<?= $av['id'] ?>" class="btn-edit">Editar</a>
                                <a href="avaliacoes.php?aluno_id=<?= $alunoSelecionado ?>&delete_id=<?= $av['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta avaliação? Esta ação não pode ser desfeita.');">Excluir</a>
                            </td>
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