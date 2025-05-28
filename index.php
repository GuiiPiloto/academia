<!DOCTYPE html>
<html lang="pt-br" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Academia TopFit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/index.css">
    <meta name="description" content="Sistema completo de gestão para academias. Gerencie alunos, treinos, presenças e avaliações físicas com facilidade.">
    <link rel="icon" type="image/png" href="assets/img/ico.png">
</head>
<body>
    <button id="toggle-theme">🌙 Modo Claro</button>

    <header>
        <div class="container">
            <h1>TopFit <span>Academia</span></h1>
            <p>Foco. Força. Superação.</p>
        </div>
    </header>

    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <h2>Eleve sua performance</h2>
                <p>Bem-vindo à TopFit: onde cada treino é um passo mais perto do seu melhor.</p>
                <a href="pages/login.php" class="btn">Entrar no Sistema</a>
            </div>
            <div class="hero-img">
                <img src="assets/img/academia.jpg" alt="Imagem academia">
            </div>
        </div>
    </section>

    <section class="beneficios">
        <div class="container">
            <h2>O que oferecemos?</h2>
            <div class="cards">
                <div class="card">
                    <h3>🏋️ Estrutura Moderna</h3>
                    <p>Ambiente profissional para treinos de alta performance.</p>
                </div>
                <div class="card">
                    <h3>🎯 Planos Personalizados</h3>
                    <p>Resultados reais com acompanhamento especializado.</p>
                </div>
                <div class="card">
                    <h3>📅 Flexibilidade</h3>
                    <p>Acesso 24h com conforto e segurança.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 TopFit Academia. Todos os direitos reservados.</p>
    </footer>

    <script>
        const toggleButton = document.getElementById("toggle-theme");
        const html = document.documentElement;

        // Carrega tema salvo
        if (localStorage.getItem("theme") === "light") {
            html.setAttribute("data-theme", "light");
            toggleButton.textContent = "🌑 Modo Escuro";
        }

        toggleButton.addEventListener("click", () => {
            if (html.getAttribute("data-theme") === "dark") {
                html.setAttribute("data-theme", "light");
                localStorage.setItem("theme", "light");
                toggleButton.textContent = "🌑 Modo Escuro";
            } else {
                html.setAttribute("data-theme", "dark");
                localStorage.setItem("theme", "dark");
                toggleButton.textContent = "🌙 Modo Claro";
            }
        });
    </script>
</body>
</html>
