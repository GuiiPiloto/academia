<?php
function verificarLogin($tipoEsperado) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
}

    // Verifica se o usuário está logado
    if (!isset($_SESSION["usuario_id"])) {
        header("Location: ../login.php");
        exit;
    }

    // Verifica se o tipo de usuário é o esperado (admin, professor, aluno)
    if ($_SESSION["tipo"] !== $tipoEsperado) {
        // Redireciona para o painel correspondente ao tipo correto
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
                header("Location: ../login.php");
                break;
        }
        exit;
    }
}
?>
