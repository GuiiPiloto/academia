<?php
session_start();
require_once "../../includes/verificar.php";
verificarLogin("professor");
require_once "../../config/conexao.php";

$aluno_id = isset($_GET['aluno_id']) ? intval($_GET['aluno_id']) : 0;
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = isset($_POST['aluno_id']) ? intval($_POST['aluno_id']) : 0;
    $grupo_muscular = $_POST['grupo_muscular'];
    $exercicio = $_POST['exercicio'];
    $series = $_POST['series'];
    $repeticoes = $_POST['repeticoes'];
    $descanso = $_POST['descanso'];

    if ($aluno_id > 0) {
        $stmt = $conn->prepare("SELECT id FROM fichas WHERE aluno_id = ?");
        $stmt->bind_param("i", $aluno_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE fichas SET grupo_muscular=?, exercicio=?, series=?, repeticoes=?, descanso=? WHERE aluno_id=?");
            $stmt->bind_param("sssssi", $grupo_muscular, $exercicio, $series, $repeticoes, $descanso, $aluno_id);
            $stmt->execute();
            $mensagem = "Ficha atualizada com sucesso!";
        } else {
            $stmt = $conn->prepare("INSERT INTO fichas (aluno_id, grupo_muscular, exercicio, series, repeticoes, descanso, criado_por) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssi", $aluno_id, $grupo_muscular, $exercicio, $series, $repeticoes, $descanso, $_SESSION['usuario_id']);
            $stmt->execute();
            $mensagem = "Ficha cadastrada com sucesso!";
        }
    } else {
        $mensagem = "Erro: nenhum aluno selecionado.";
    }
}

// busca os alunos
$alunos = $conn->query("SELECT id, nome FROM usuarios WHERE tipo = 'aluno' ORDER BY nome");
?>

<!DOCTYPE html>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
<html lang="pt-BR">
    <link rel="stylesheet" href="../../css/ficha-professor.css">
<head>
    <meta charset="UTF-8">
    <title>Fichas</title>
</head>
<body>
    <h1>Cadastro de Fichas</h1>

    <?php if ($mensagem): ?>
        <p><?php echo $mensagem; ?></p>
    <?php endif; ?>

    <form method="get">
        <label for="aluno_id">Selecione o aluno:</label>
        <select name="aluno_id" id="aluno_id" onchange="this.form.submit()" required>
            <option value="">-- Escolha um aluno --</option>
            <?php while($aluno = $alunos->fetch_assoc()): ?>
                <option value="<?php echo $aluno['id']; ?>" <?php if ($aluno_id == $aluno['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($aluno['nome']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php
    if ($aluno_id > 0):
        $stmt = $conn->prepare("SELECT * FROM fichas WHERE aluno_id = ?");
        $stmt->bind_param("i", $aluno_id);
        $stmt->execute();
        $ficha = $stmt->get_result()->fetch_assoc();
    ?>

    <form method="post">
        <input type="hidden" name="aluno_id" value="<?php echo $aluno_id; ?>">

        <label>Grupo Muscular:</label>
        <input type="text" name="grupo_muscular" required value="<?php echo $ficha['grupo_muscular'] ?? ''; ?>"><br>

        <label>Exercício:</label>
        <input type="text" name="exercicio" required value="<?php echo $ficha['exercicio'] ?? ''; ?>"><br>

        <label>Séries:</label>
        <input type="text" name="series" required value="<?php echo $ficha['series'] ?? ''; ?>"><br>

        <label>Repetições:</label>
        <input type="text" name="repeticoes" required value="<?php echo $ficha['repeticoes'] ?? ''; ?>"><br>

        <label>Descanso:</label>
        <input type="text" name="descanso" required value="<?php echo $ficha['descanso'] ?? ''; ?>"><br>

        <button type="submit">Salvar</button>
    </form>

    <?php endif; ?>
</body>
</html>
