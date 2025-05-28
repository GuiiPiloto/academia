<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "academia_db";

// Conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>
