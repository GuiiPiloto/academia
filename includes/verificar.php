<?php
function verificarLogin($tipoEsperado) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo'])) {
        header("Location: ../../login.php");
        exit;
    }

    // Se o tipo de usuário não for o esperado, redireciona para o painel correto
    if ($_SESSION["tipo"] !== $tipoEsperado) {
        switch ($_SESSION["tipo"]) {
            case "admin":
                header("Location: ../admin/dashboard.php");
                break;
            case "professor":
                header("Location: ../professor/dashboard.php");
                break;
            case "aluno":
                header("Location: ../aluno/dashboard.php");
                break;
            default:
                header("Location: ../../login.php");
                break;
        }
        exit;
    }
}
?>
