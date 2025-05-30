<?php
require_once "../../includes/verificar.php";
require_once "../../config/conexao.php";
verificarLogin("professor");

$professor_id = $_SESSION['id'];

$stmtAlunos = $conn->prepare("
    SELECT DISTINCT a.id, a.nome 
    FROM mensagens m 
    JOIN alunos a ON m.aluno_id = a.id 
    WHERE m.professor_id = ?
");
$stmtAlunos->bind_param("i", $professor_id);
$stmtAlunos->execute();
$resultAlunos = $stmtAlunos->get_result();

$alunos = [];
while ($row = $resultAlunos->fetch_assoc()) {
    $alunos[] = $row;
}

$alunoSelecionado = $_GET['aluno_id'] ?? null;
$mensagens = [];

if ($alunoSelecionado) {
    // Marcar mensagens do aluno como lidas
    $marcarLidas = $conn->prepare("UPDATE mensagens SET lida = 1 WHERE professor_id = ? AND aluno_id = ? AND remetente = 'aluno'");
    $marcarLidas->bind_param("ii", $professor_id, $alunoSelecionado);
    $marcarLidas->execute();

    // Buscar mensagens trocadas
    $stmtMsg = $conn->prepare("
        SELECT * FROM mensagens 
        WHERE professor_id = ? AND aluno_id = ? 
        ORDER BY enviado_em ASC
    ");
    $stmtMsg->bind_param("ii", $professor_id, $alunoSelecionado);
    $stmtMsg->execute();
    $resultMsgs = $stmtMsg->get_result();

    while ($row = $resultMsgs->fetch_assoc()) {
        $mensagens[] = $row;
    }
}

// Enviar nova mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem']) && $alunoSelecionado) {
    $mensagem = trim($_POST['mensagem']);
    if (!empty($mensagem)) {
        $stmtEnvio = $conn->prepare("INSERT INTO mensagens (aluno_id, professor_id, mensagem, remetente, enviado_em, lida) VALUES (?, ?, ?, 'professor', NOW(), 0)");
        $stmtEnvio->bind_param("iis", $alunoSelecionado, $professor_id, $mensagem);
        $stmtEnvio->execute();
        header("Location: mensagens.php?aluno_id=$alunoSelecionado");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Mensagens - TOPFIT</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/mensagens.css">
</head>
<body>
    <button id="toggle-theme">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <a href="dashboard.php">🏠 Início</a>
        <a href="fichas.php">💪 Fichas de Treino</a>
        <a href="avaliacoes.php">📊 Avaliações</a>
        <a href="mensagens.php" class="active">💬 Mensagens</a>
        <a href="../../logout.php">🚪 Sair</a>
    </div>

    <div class="main-content">
        <h1>Mensagens com Alunos</h1>
        <div class="mensagens-container">
            <div class="lista-alunos">
                <h3>Alunos</h3>
                <ul>
                    <?php foreach ($alunos as $aluno): ?>
                        <li>
                            <a href="?aluno_id=<?= $aluno['id'] ?>" <?= ($aluno['id'] == $alunoSelecionado) ? 'class="ativo"' : '' ?>>
                                <?= htmlspecialchars($aluno['nome']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="chat-area">
                <?php if ($alunoSelecionado): ?>
                    <div class="mensagens">
                        <?php foreach ($mensagens as $msg): ?>
                            <div class="mensagem <?= $msg['remetente'] === 'professor' ? 'enviada' : 'recebida' ?>">
                                <p><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></p>
                                <span><?= date('d/m/Y H:i', strtotime($msg['enviado_em'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <form method="post" class="form-mensagem">
                        <textarea name="mensagem" required placeholder="Digite sua mensagem..."></textarea>
                        <button type="submit">Enviar</button>
                    </form>
                <?php else: ?>
                    <p class="aviso">Selecione um aluno para visualizar as mensagens.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const toggleButton = document.getElementById('toggle-theme');
        const htmlElement = document.documentElement;

        function applyTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            toggleButton.textContent = (theme === 'light') ? '🌑 Modo Escuro' : '🌙 Modo Claro';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            applyTheme(savedTheme);
            toggleButton.addEventListener('click', () => {
                applyTheme(htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
            });
        });
    </script>
</body>
</html>
