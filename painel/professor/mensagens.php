<?php
require_once "../../includes/verificar.php";
require_once "../../config/conexao.php";
verificarLogin("professor");

$professor_id = $_SESSION["id"];
$nome_professor = $_SESSION["nome"] ?? 'Professor';

$alunos_query = $conn->prepare("
    SELECT DISTINCT u.id, u.nome 
    FROM usuarios u
    JOIN mensagens m ON u.id = m.aluno_id
    WHERE m.professor_id = ? AND u.tipo = 'aluno'
");
$alunos_query->bind_param("i", $professor_id);
$alunos_query->execute();
$alunos_result = $alunos_query->get_result();

$aluno_selecionado = $_GET["aluno"] ?? null;
$mensagens = [];

if ($aluno_selecionado) {
    // Atualiza status de leitura
    $conn->prepare("UPDATE mensagens SET lida = 1 WHERE aluno_id = ? AND professor_id = ? AND remetente = 'aluno'")
        ->bind_param("ii", $aluno_selecionado, $professor_id)
        ->execute();

    // Buscar mensagens
    $mensagem_query = $conn->prepare("
        SELECT * FROM mensagens 
        WHERE aluno_id = ? AND professor_id = ?
        ORDER BY enviado_em ASC
    ");
    $mensagem_query->bind_param("ii", $aluno_selecionado, $professor_id);
    $mensagem_query->execute();
    $mensagens = $mensagem_query->get_result();
}

// Envio de nova mensagem
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["mensagem"]) && $aluno_selecionado) {
    $mensagem = trim($_POST["mensagem"]);
    if (!empty($mensagem)) {
        $inserir = $conn->prepare("
            INSERT INTO mensagens (aluno_id, professor_id, mensagem, remetente, enviado_em, lida)
            VALUES (?, ?, ?, 'professor', NOW(), 0)
        ");
        $inserir->bind_param("iis", $aluno_selecionado, $professor_id, $mensagem);
        $inserir->execute();
        header("Location: mensagens.php?aluno=$aluno_selecionado");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Mensagens - Professor</title>
    <link rel="stylesheet" href="../../css/mensagens-professor.css">
    <style>
        .chat-box {
            border: 1px solid #444;
            padding: 1rem;
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }
        .mensagem.professor {
            text-align: right;
            color: #0f0;
        }
        .mensagem.aluno {
            text-align: left;
            color: #0cf;
        }
        .form-mensagem {
            display: flex;
            gap: 0.5rem;
        }
        .form-mensagem input[type="text"] {
            flex: 1;
            padding: 0.5rem;
            border-radius: 6px;
            border: 1px solid #666;
        }
        .form-mensagem button {
            padding: 0.5rem 1rem;
        }
        .aluno-select {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>TOPFIT</h2>
    <a href="dashboard.php">🏠 Início</a>
    <a href="fichas.php">💪 Fichas de Treino</a>
    <a href="avaliacoes.php">📊 Avaliações</a>
    <a href="mensagens.php" class="active">💬 Mensagens</a>
    <a href="../../index.php">🚪 Sair</a>
</div>

<div class="main-content">
    <h1>Mensagens</h1>

    <form method="get" class="aluno-select">
        <label for="aluno">Selecionar Aluno:</label>
        <select name="aluno" id="aluno" onchange="this.form.submit()">
            <option value="">-- Escolha um aluno --</option>
            <?php while ($aluno = $alunos_result->fetch_assoc()): ?>
                <option value="<?= $aluno['id'] ?>" <?= ($aluno_selecionado == $aluno['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($aluno['nome']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($aluno_selecionado): ?>
        <div class="chat-box">
            <?php foreach ($mensagens as $msg): ?>
                <div class="mensagem <?= $msg['remetente'] ?>">
                    <strong><?= $msg['remetente'] === 'professor' ? 'Você' : 'Aluno' ?>:</strong>
                    <?= htmlspecialchars($msg['mensagem']) ?>
                    <small><br><?= date('d/m/Y H:i', strtotime($msg['enviado_em'])) ?></small>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>

        <form method="post" class="form-mensagem">
            <input type="text" name="mensagem" placeholder="Digite sua mensagem..." required>
            <button type="submit">Enviar</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
