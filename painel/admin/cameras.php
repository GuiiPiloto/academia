<?php
session_start();
require_once '../../config/conexao.php';
require_once '../../includes/verificar.php';

verificarLogin('admin');

$caminho_da_pasta_relativo = '../../assets/videos/';
$caminho_da_pasta_web = '/academia/assets/videos/';

$arquivos_de_video_path = glob($caminho_da_pasta_relativo . '*.{mp4,webm,ogg}', GLOB_BRACE);
$videos = [];
if (is_array($arquivos_de_video_path)) {
    foreach ($arquivos_de_video_path as $filepath) {
        $videos[] = basename($filepath);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/academia/assets/img/icone.png" type="image/png">
    <title>Câmeras de Segurança - TOPFIT</title>
    <link rel="stylesheet" href="../../css/cameras-admin.css">
</head>
<body>

    <button id="toggle-theme" class="theme-toggle">🌙 Modo Claro</button>

    <div class="sidebar">
        <h2>TOPFIT</h2>
        <nav>
            <a href="dashboard.php">🏠 Início</a>
            <a href="usuarios.php">👤 Usuários</a>
            <a href="cameras.php" class="active">📹 Câmeras</a>
            <a href="../../logout.php">🚪 Sair</a>
        </nav>
    </div>

    <main class="main-content">
        <header class="page-header">
            <h1>Câmeras de Segurança</h1>
        </header>

        <section class="camera-viewer">
            <?php if (!empty($videos)): ?>
                <div class="camera-container">
                    <?php foreach ($videos as $index => $video_filename): ?>
                        <div class="video-slide <?= $index === 0 ? 'active' : '' ?>">
                            <video autoplay muted loop playsinline src="<?= $caminho_da_pasta_web . $video_filename ?>"></video>
                            <div class="camera-title">Câmera <?= $index + 1 ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="camera-navigation">
                    <button class="nav-arrow prev" onclick="mudarSlide(-1)">&#10094;</button>
                    <button class="nav-arrow next" onclick="mudarSlide(1)">&#10095;</button>
                </div>
            <?php else: ?>
                <div class="no-cameras">
                    <p>Nenhuma câmera (vídeo) encontrada na pasta de vídeos.</p>
                    <p>Adicione arquivos .mp4, .webm ou .ogg na pasta <code>assets/videos/</code>.</p>
                </div>
            <?php endif; ?>
        </section>  
    </main>

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

            const slides = document.querySelectorAll(".video-slide");
            if (slides.length > 0) {
                let slideIndex = 0;
                
                function mostrarSlide(n) {
                    slides.forEach((slide, index) => {
                        slide.classList.remove('active');
                        // Pausar todos os vídeos para economizar recursos
                        slide.querySelector('video').pause(); 
                    });
                    slideIndex = (n + slides.length) % slides.length;
                    slides[slideIndex].classList.add('active');
                    // Tocar apenas o vídeo ativo
                    slides[slideIndex].querySelector('video').play(); 
                }

                window.mudarSlide = function(n) {
                    mostrarSlide(slideIndex + n);
                }
                
                mostrarSlide(slideIndex);
            }
        });
    </script>
</body>
</html>