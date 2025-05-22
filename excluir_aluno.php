<?php
include("config.php");

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $sql = "DELETE FROM alunos WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "<script>alert('Aluno excluído com sucesso!'); window.location.href='listar_alunos.php';</script>";
  } else {
    echo "<script>alert('Erro ao excluir aluno!'); window.location.href='listar_alunos.php';</script>";
  }

  $stmt->close();
} else {
  header("Location: listar_alunos.php");
}
