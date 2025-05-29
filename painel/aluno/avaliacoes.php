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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/ico.png">
    <title>Avaliações - TopFit</title>
    <link rel="stylesheet" href="../../css/avaliacoes-aluno.css">
</head>
<body>
    <div class="sidebar">
        <h2>TopFit</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="presencas.php">📅 Presenças</a>
        <a href="ficha.php">💪 Ficha de Treino</a>
        <a href="avaliacoes.php" class="active">📊 Avaliações</a>
        <a href="mensagens.php">💬 Mensagens</a>
        <a href="../../logout.php">🚪 Sair</a>
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
</body>
</html>
