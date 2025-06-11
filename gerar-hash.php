<?php
// Coloque aqui a senha temporária que você escolheu
$senhaTemporaria = '123456789';

// Gera o hash da senha
$hash = password_hash($senhaTemporaria, PASSWORD_DEFAULT);

// Exibe o hash na tela
echo "Senha temporária: " . htmlspecialchars($senhaTemporaria) . "<br><br>";
echo "Copie este hash para o banco de dados:<br><br>";
echo "<strong>" . $hash . "</strong>";
?>