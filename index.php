<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Academia TopFit - Foco, Força e Superação</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plataforma completa para academias. Onde cada treino é um passo em direção à sua melhor versão.">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="./assets/img/icone.png" type="image/png">
</head>
<body>

    <header class="main-header">
        <div class="container">
            <a href="index.php" class="logo">TopFit <span>Academia</span></a>
            <nav class="main-nav">
                <a href="login.php" class="btn btn-primary">Entrar no Sistema</a>
                <button id="toggle-theme" class="theme-toggle">🌙</button>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-content">
                <div class="hero-text">
                    <h1>Eleve sua performance ao máximo</h1>
                    <p>Bem-vindo(a) à TopFit: onde cada treino é um passo em direção à sua melhor versão. Gestão completa para sua jornada fitness.</p>
                    <a href="login.php" class="btn btn-primary btn-lg">Comece Agora</a>
                </div>
                <div class="hero-image">
                    <img src="assets/img/academia.jpg" alt="Pessoas se exercitando em uma academia moderna e bem equipada.">
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="section-header">
                    <h2>Diferenciais TopFit</h2>
                    <p>Tudo que você precisa para uma gestão de alta performance.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <h3>🏋️ Estrutura Moderna</h3>
                        <p>Ambiente profissional e equipamentos de ponta para treinos de alta performance.</p>
                    </div>
                    <div class="feature-card">
                        <h3>🎯 Planos Personalizados</h3>
                        <p>Resultados reais com acompanhamento especializado e treinos sob medida.</p>
                    </div>
                    <div class="feature-card">
                        <h3>📅 Gestão Simplificada</h3>
                        <p>Controle total sobre fichas, avaliações e frequência com nosso sistema intuitivo.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> TopFit Academia. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        const toggleButton = document.getElementById("toggle-theme");
        const html = document.documentElement;

        function applyTheme(theme) {
            html.setAttribute("data-theme", theme);
            localStorage.setItem("theme", theme);
            toggleButton.textContent = theme === 'light' ? '🌑' : '🌙';
        }

        const initialTheme = localStorage.getItem("theme") || 'dark';
        applyTheme(initialTheme);

        toggleButton.addEventListener("click", () => {
            const currentTheme = html.getAttribute("data-theme");
            applyTheme(currentTheme === "dark" ? "light" : "dark");
        });
    </script>
</body>
</html>