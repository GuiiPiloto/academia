<?php include("config.php"); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Usuário</title>
  <!-- Link para o arquivo CSS específico da página de cadastro -->
  <link rel="stylesheet" href="css/cadastro_aluno.css">
  <!-- Importando a fonte Roboto -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="cadastro-page">
  <div class="container">
    <h2>Cadastro de Usuário</h2>
    <div class="form-group">
      <span class="icon">👤</span>
      <input type="text" name="nome" placeholder="Nome" required>
    </div>
    <div class="form-group">
      <span class="icon">✉️</span>
      <input type="email" name="email" placeholder="Email" required>
    </div>
    <div class="form-group">
      <span class="icon">🔒</span>
      <input type="password" name="senha" placeholder="Senha" required>
    </div>
    <div class="form-group">
      <span class="icon">📜</span>
      <input type="text" name="cpf" placeholder="CPF (000.000.000-00)" required>
    </div>
    <div class="form-group">
      <span class="icon">📞</span>
      <input type="tel" name="telefone" placeholder="Telefone ((DD) 99999-9999)" required>
    </div>
    <div class="form-group">
      <span class="icon">📅</span>
      <input type="date" name="data_cadastro" required>
    </div>
    <button type="submit" formaction="backend.php">Cadastrar</button>
    <div class="login-link">
      <p>Já tem uma conta? <a href="#">Faça login</a></p>
    </div>
  </div>
</body>
</html>

<?php
if (isset($_POST['cadastrar'])) {
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $telefone = $_POST['telefone'];
  $data_nasc = $_POST['data_nasc'];
  $endereco = $_POST['endereco'];

  $sql = "INSERT INTO alunos (nome, email, telefone, data_nasc, endereco)
          VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $nome, $email, $telefone, $data_nasc, $endereco);

  if ($stmt->execute()) {
    echo "<script>alert('Aluno cadastrado com sucesso!'); window.location.href='cadastrar_aluno.php';</script>";
  } else {
    echo "<script>alert('Erro ao cadastrar aluno!');</script>";
  }

  $stmt->close();
}
?>
