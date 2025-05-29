<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("aluno");
require_once "../../config/conexao.php";

$aluno_id = $_SESSION['usuario_id'];
$mensagens = [];
$professor_id = isset($_GET['professor_id']) ? (int)$_GET['professor_id'] : 0;

// Buscar professores disponíveis
$professores = [];
$sql = "SELECT id, nome FROM usuarios WHERE tipo = 'professor'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $professores[] = $row;
}

// Buscar mensagens se professor for selecionado
if ($professor_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM mensagens 
        WHERE aluno_id = ? AND professor_id = ? 
        ORDER BY enviado_em ASC
    ");
    $stmt->bind_param("ii", $aluno_id, $professor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $mensagens[] = $row;
    }
}

// Enviar nova mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'], $_POST['professor_id'])) {
    $mensagem = trim($_POST['mensagem']);
    $professor_id = (int)$_POST['professor_id'];
    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO mensagens (aluno_id, professor_id, mensagem, remetente) VALUES (?, ?, ?, 'aluno')");
        $stmt->bind_param("iis", $aluno_id, $professor_id, $mensagem);
        $stmt->execute();
        header("Location: mensagens.php?professor_id=$professor_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Mensagens - TopFit</title>
    <link rel="stylesheet" href="../../css/mensagens-aluno.css">
</head>
<body>
<div class="sidebar">
    <h2>TopFit</h2>
    <a href="dashboard.php">🏠 Início</a>
    <a href="presencas.php">📅 Presenças</a>
    <a href="ficha.php">💪 Ficha de Treino</a>
    <a href="avaliacoes.php">📊 Avaliações</a>
    <a href="mensagens.php" class="active">💬 Mensagens</a>
    <a href="../../logout.php">🚪 Sair</a>
</div>

<div class="main-content">
    <div class="dashboard-header">Mensagens com o Professor</div>

    <form method="GET" class="form-professor">
        <label for="professor_id">Escolha o professor:</label>
        <select name="professor_id" onchange="this.form.submit()">
            <option value="">-- Selecione --</option>
            <?php foreach ($professores as $prof) : ?>
                <option value="<?= $prof['id'] ?>" <?= $prof['id'] == $professor_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prof['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($professor_id): ?>
        <div class="chat-box">
            <?php foreach ($mensagens as $msg): ?>
                <div class="mensagem <?= $msg['remetente'] == 'aluno' ? 'aluno' : 'professor' ?>">
                    <span><?= htmlspecialchars($msg['mensagem']) ?></span>
                    <small><?= date('d/m H:i', strtotime($msg['enviado_em'])) ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="form-chat">
            <input type="hidden" name="professor_id" value="<?= $professor_id ?>">
            <input type="text" name="mensagem" placeholder="Digite sua mensagem..." required>
            <button type="submit">Enviar</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
