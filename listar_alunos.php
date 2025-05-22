<?php include("config.php"); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Alunos</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="list-container">
  <h2>Alunos Cadastrados</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th>Email</th>
      <th>Telefone</th>
      <th>Data Nasc.</th>
      <th>Endereço</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $result = $conn->query("SELECT * FROM alunos ORDER BY id DESC");

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['email']}</td>
                <td>{$row['telefone']}</td>
                <td>" . date("d/m/Y", strtotime($row['data_nasc'])) . "</td>
                <td>{$row['endereco']}</td>
                <td>
                  <a class='btn-edit' href='editar_aluno.php?id={$row['id']}'>Editar</a>
                  <a class='btn-delete' href='excluir_aluno.php?id={$row['id']}' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>Excluir</a>
                </td>
              </tr>";
      }
    } else {
      echo "<tr><td colspan='7'>Nenhum aluno cadastrado.</td></tr>";
    }
    ?>
  </tbody>
</table>
</div>

</body>
</html>
