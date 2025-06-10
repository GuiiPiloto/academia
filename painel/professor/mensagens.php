<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("professor");

require_once "../../config/conexao.php";

$professor_id = $_SESSION["usuario_id"];

// Buscar todos os alunos
$sql = "SELECT id, nome FROM usuarios WHERE tipo = 'aluno'";
$result = $conn->query($sql);

// Aluno selecionado
$aluno_id = isset($_GET["aluno_id"]) ? intval($_GET["aluno_id"]) : null;

// Buscar o nome do aluno selecionado
$aluno_nome = '';
if ($aluno_id) {
    $stmt_aluno = $conn->prepare("SELECT nome FROM usuarios WHERE id = ? AND tipo = 'aluno'");
    $stmt_aluno->bind_param("i", $aluno_id);
    $stmt_aluno->execute();
    $result_aluno = $stmt_aluno->get_result();
    if ($result_aluno->num_rows > 0) {
        $aluno_data = $result_aluno->fetch_assoc();
        $aluno_nome = htmlspecialchars($aluno_data['nome']);
    }
}


// Enviar nova mensagem
if ($_SERVER["REQUEST_METHOD"] === "POST" && $aluno_id) {
    $mensagem = trim($_POST["mensagem"]);
    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO mensagens (aluno_id, professor_id, mensagem, remetente, enviado_em, lida)
                                 VALUES (?, ?, ?, 'professor', NOW(), 0)");
        $stmt->bind_param("iis", $aluno_id, $professor_id, $mensagem);
        $stmt->execute();
    }
    // Continuamos com o header Location para recarregar a página e mostrar a nova mensagem
    // Para um chat real-time, isso seria substituído por uma resposta JSON e JS para atualizar o DOM
    header("Location: mensagens.php?aluno_id=" . $aluno_id);
    exit;
}

// Marcar mensagens como lidas
if ($aluno_id) {
    $conn->query("UPDATE mensagens SET lida = 1 WHERE aluno_id = $aluno_id AND professor_id = $professor_id AND remetente = 'aluno'");
}

// Buscar mensagens
$mensagens = [];
if ($aluno_id) {
    $stmt = $conn->prepare("SELECT * FROM mensagens
                            WHERE aluno_id = ? AND professor_id = ?
                            ORDER BY enviado_em ASC");
    $stmt->bind_param("ii", $aluno_id, $professor_id);
    $stmt->execute();
    $result_mensagens = $stmt->get_result();
    while ($msg = $result_mensagens->fetch_assoc()) {
        $mensagens[] = $msg;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - TOPFIT</title>
    <link rel="stylesheet" href="../../css/mensagens-professor.css">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
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
        <div class="dashboard-header">
            <h1>Mensagens com Alunos</h1>
        </div>

        <div class="alunos">
            <form method="get">
                <select name="aluno_id" onchange="this.form.submit()">
                    <option value="">Escolha um aluno</option>
                    <?php while ($aluno = $result->fetch_assoc()): ?>
                        <option value="<?= $aluno['id'] ?>" <?= ($aluno['id'] == $aluno_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <?php if ($aluno_id): ?>
            <div class="chat">
                <div class="chat-header-aluno">
                    <span>Conversa com: </span>
                    <span class="aluno-nome-chat"><?= $aluno_nome ?></span>
                </div>

                <div class="mensagens" id="mensagens-box">
                    <?php foreach ($mensagens as $msg): ?>
                        <div class="<?= $msg['remetente'] === 'professor' ? 'msg-professor' : 'msg-aluno' ?>">
                            <p><?= htmlspecialchars($msg['mensagem']) ?></p>
                            <span><?= date("d/m/Y H:i", strtotime($msg['enviado_em'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="post" class="form-msg">
                    <input type="text" name="mensagem" placeholder="Digite sua mensagem..." required>
                    <button type="submit">Enviar</button>
                </form>
            </div>
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

            const box = document.getElementById("mensagens-box");
            if (box) box.scrollTop = box.scrollHeight;
        });
    </script>

</body>
</html>