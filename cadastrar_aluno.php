<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastrar Aluno</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <section class="form-wrapper">
            <h1 class="title">Cadastrar Aluno</h1>
            <form action="processa_cadastro.php" method="POST" novalidate>
                
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required autocomplete="name" placeholder="Digite o nome completo" />
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email" placeholder="exemplo@dominio.com" />
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" pattern="[0-9]{10,11}" placeholder="Somente números, ex: 11999999999" />
                </div>

                <div class="form-group">
                    <label for="nascimento">Data de Nascimento</label>
                    <input type="date" id="nascimento" name="nascimento" required />
                </div>

                <div class="form-group">
                    <label for="plano">Plano</label>
                    <select id="plano" name="plano" required>
                        <option value="" disabled selected>Selecione o plano</option>
                        <option value="basico">Básico</option>
                        <option value="premium">Premium</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Cadastrar</button>
            </form>
        </section>
    </main>
</body>
</html>
