<?php
session_start();
require_once '../../config/conexao.php';
require_once '../../includes/verificar.php';

verificarLogin('admin');

$erro = '';
$sucesso = '';

if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id_para_excluir = intval($_GET['id']);
    
    if ($id_para_excluir == ($_SESSION['usuario_id'] ?? 0)) {
        header("Location: usuarios.php?erro=3");
        exit();
    }

    $conn->begin_transaction();

    try {
        $stmt_mensagens_aluno = $conn->prepare("DELETE FROM mensagens WHERE aluno_id = ?");
        $stmt_mensagens_aluno->bind_param("i", $id_para_excluir);
        $stmt_mensagens_aluno->execute();
        $stmt_mensagens_aluno->close();
        
        $stmt_mensagens_prof = $conn->prepare("DELETE FROM mensagens WHERE professor_id = ?");
        $stmt_mensagens_prof->bind_param("i", $id_para_excluir);
        $stmt_mensagens_prof->execute();
        $stmt_mensagens_prof->close();

        $stmt_fichas_aluno = $conn->prepare("DELETE FROM fichas WHERE aluno_id = ?");
        $stmt_fichas_aluno->bind_param("i", $id_para_excluir);
        $stmt_fichas_aluno->execute();
        $stmt_fichas_aluno->close();
        
        $stmt_fichas_prof = $conn->prepare("DELETE FROM fichas WHERE criado_por = ?");
        $stmt_fichas_prof->bind_param("i", $id_para_excluir);
        $stmt_fichas_prof->execute();
        $stmt_fichas_prof->close();

        $stmt_avaliacoes = $conn->prepare("DELETE FROM avaliacoes WHERE aluno_id = ?");
        $stmt_avaliacoes->bind_param("i", $id_para_excluir);
        $stmt_avaliacoes->execute();
        $stmt_avaliacoes->close();
        
        $stmt_presencas_aluno = $conn->prepare("DELETE FROM presencas WHERE aluno_id = ?");
        $stmt_presencas_aluno->bind_param("i", $id_para_excluir);
        $stmt_presencas_aluno->execute();
        $stmt_presencas_aluno->close();
        
        $stmt_presencas_prof = $conn->prepare("DELETE FROM presencas WHERE registrada_por = ?");
        $stmt_presencas_prof->bind_param("i", $id_para_excluir);
        $stmt_presencas_prof->execute();
        $stmt_presencas_prof->close();
        
        $stmt_delete_user = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt_delete_user->bind_param("i", $id_para_excluir);
        $stmt_delete_user->execute();
        $stmt_delete_user->close();
        
        $conn->commit();
        
        header("Location: usuarios.php?sucesso=3");
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        
        header("Location: usuarios.php?erro=4"); 
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar_usuario'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
        $erro = "Para cadastrar, todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Formato de email inválido.";
    } else {
        $sql_check = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $erro = "Este email já está cadastrado.";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nome, $email, $senha_hash, $tipo);
            if ($stmt_insert->execute()) {
                header("Location: usuarios.php?sucesso=1");
                exit();
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_usuario'])) {
    $id = $_POST['usuario_id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tipo = $_POST['tipo'];
    $senha = $_POST['senha'];

    if (empty($nome) || empty($email) || empty($tipo) || empty($id)) {
        $erro = "Para editar, nome, email e tipo são obrigatórios.";
    } else {
        $sql_update = "UPDATE usuarios SET nome = ?, email = ?, tipo = ?";
        $params = [$nome, $email, $tipo];
        $types = "sss";
        
        if (!empty($senha)) {
            $sql_update .= ", senha = ?";
            $params[] = password_hash($senha, PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        $sql_update .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param($types, ...$params);

        if ($stmt_update->execute()) {
            header("Location: usuarios.php?sucesso=2");
            exit();
        } else {
            $erro = "Erro ao atualizar o usuário.";
        }
        $stmt_update->close();
    }
}

if(isset($_GET['sucesso'])){
    if($_GET['sucesso'] == '1') $sucesso = "Usuário cadastrado com sucesso!";
    if($_GET['sucesso'] == '2') $sucesso = "Usuário atualizado com sucesso!";
    if($_GET['sucesso'] == '3') $sucesso = "Usuário excluído com sucesso!";
}
if(isset($_GET['erro'])){
    if($_GET['erro'] == '3') $erro = "Você não pode excluir seu próprio usuário.";
    if($_GET['erro'] == '4') $erro = "Ocorreu um erro ao tentar excluir o usuário.";
}

$sql_select = "SELECT id, nome, email, tipo FROM usuarios ORDER BY nome ASC";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->execute();
$result = $stmt_select->get_result();
$usuarios = $result->fetch_all(MYSQLI_ASSOC);
$stmt_select->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Gerenciar Usuários - TOPFIT</title>
    <link rel="stylesheet" href="../../css/usuarios-admin.css">
</head>
<body>

    <button id="toggle-theme" class="theme-toggle">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <nav>
            <a href="dashboard.php">🏠 Início</a>
            <a href="usuarios.php" class="active">👤 Usuários</a>
            <a href="cameras.php">📹 Câmeras</a>
            <a href="../../logout.php">🚪 Sair</a>
        </nav>
    </div>

    <main class="main-content">
        <header class="page-header">
            <h1>Gerenciar Usuários</h1>
            <button id="btnAbrirModalCadastro" class="btn btn-primary">Cadastrar Novo Usuário</button>
        </header>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <section class="table-wrapper">
            <div class="filter-container">
                <input type="text" id="filtro-usuarios" placeholder="Buscar por nome, email ou tipo...">
            </div>

            <table id="tabela-usuarios">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr 
                            data-id="<?= $usuario['id']; ?>"
                            data-nome="<?= htmlspecialchars($usuario['nome']); ?>"
                            data-email="<?= htmlspecialchars($usuario['email']); ?>"
                            data-tipo="<?= $usuario['tipo']; ?>">
                            <td data-label="Nome"><?= htmlspecialchars($usuario['nome']); ?></td>
                            <td data-label="Email"><?= htmlspecialchars($usuario['email']); ?></td>
                            <td data-label="Tipo"><?= htmlspecialchars(ucfirst($usuario['tipo'])); ?></td>
                            <td data-label="Ações">
                                <div class="actions-cell">
                                    <button class="btn btn-edit btnAbrirModalEditar">Editar</button>
                                    <a href="usuarios.php?acao=excluir&id=<?= $usuario['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.');">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <div id="modalUsuario" class="modal">
        <div class="modal-content">
            <header class="modal-header">
                <h2 id="modal-titulo"></h2>
                <span class="close-button">&times;</span>
            </header>
            <main class="modal-body">
                <form id="formUsuario" action="usuarios.php" method="POST">
                    <input type="hidden" name="usuario_id" id="usuario_id">
                    
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" name="nome" id="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Nova Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="Deixe em branco para não alterar">
                        <small id="senha-aviso"></small>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Usuário</label>
                        <select name="tipo" id="tipo" required>
                            <option value="" disabled>Selecione um tipo</option>
                            <option value="aluno">Aluno</option>
                            <option value="professor">Professor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" id="modal-submit-button" class="btn btn-primary"></button>
                </form>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('toggle-theme');
            const htmlElement = document.documentElement;

            function applyTheme(theme) {
                htmlElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                toggleButton.textContent = theme === 'light' ? '🌑 Modo Escuro' : '🌙 Modo Claro';
            }

            const savedTheme = localStorage.getItem('theme') || 'dark';
            applyTheme(savedTheme);

            toggleButton.addEventListener('click', () => {
                const newTheme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });

            const filtroInput = document.getElementById('filtro-usuarios');
            if (filtroInput) {
                filtroInput.addEventListener('keyup', filtrarTabela);
            }

            function filtrarTabela() {
                const input = document.getElementById("filtro-usuarios");
                const filter = input.value.toUpperCase();
                const table = document.getElementById("tabela-usuarios");
                const tr = table.getElementsByTagName("tr");
                for (let i = 1; i < tr.length; i++) {
                    let rowVisible = false;
                    const td = tr[i].getElementsByTagName("td");
                    for (let j = 0; j < td.length - 1; j++) {
                        if (td[j] && td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                            rowVisible = true;
                            break;
                        }
                    }
                    tr[i].style.display = rowVisible ? "" : "none";
                }
            }
            
            const modal = document.getElementById("modalUsuario");
            const form = document.getElementById("formUsuario");
            const modalTitulo = document.getElementById("modal-titulo");
            const modalSubmitButton = document.getElementById("modal-submit-button");
            const senhaInput = document.getElementById("senha");
            const usuarioIdInput = document.getElementById("usuario_id");
            const btnAbrirModalCadastro = document.getElementById("btnAbrirModalCadastro");
            const btnsAbrirModalEditar = document.querySelectorAll(".btnAbrirModalEditar");
            const spanClose = modal.querySelector(".close-button");

            btnAbrirModalCadastro.onclick = function() {
                form.reset();
                form.querySelector('input[name="cadastrar_usuario"]')?.remove();
                form.querySelector('input[name="editar_usuario"]')?.remove();
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'cadastrar_usuario';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);

                modalTitulo.innerText = "Cadastrar Novo Usuário";
                senhaInput.required = true;
                senhaInput.placeholder = "Senha obrigatória";
                modalSubmitButton.innerText = "Cadastrar";
                modal.style.display = "flex";
            }

            btnsAbrirModalEditar.forEach(button => {
                button.onclick = function() {
                    form.reset();
                    form.querySelector('input[name="cadastrar_usuario"]')?.remove();
                    form.querySelector('input[name="editar_usuario"]')?.remove();
                    
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'editar_usuario';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);

                    const tr = this.closest('tr');
                    usuarioIdInput.value = tr.dataset.id;
                    document.getElementById('nome').value = tr.dataset.nome;
                    document.getElementById('email').value = tr.dataset.email;
                    document.getElementById('tipo').value = tr.dataset.tipo;

                    modalTitulo.innerText = "Editar Usuário";
                    senhaInput.required = false;
                    senhaInput.placeholder = "Deixe em branco para não alterar";
                    modalSubmitButton.innerText = "Salvar Alterações";
                    modal.style.display = "flex";
                }
            });
            
            spanClose.onclick = function() { modal.style.display = "none"; }
            
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
</body>
</html>