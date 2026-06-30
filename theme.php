<?php
// Carregar funções necessárias
require_once("listfiles.php");
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 IntraConnect - Social Intranet</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">  
    
    <!-- CSS Personalizado separado -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>IntraConnect
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-house-fill me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-fill me-1"></i>Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-chat-fill me-1"></i>Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bell-fill me-1"></i>Notifications</a></li>
                    <li class="nav-item me-2">
                        <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
                            <i class="bi bi-sun-fill" id="themeIcon"></i>
                            <div class="toggle-track">
                                <div class="toggle-thumb"></div>
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <div class="row g-4">
            <?php require_once("home_sidebar_left.php"); ?>
            <?php require_once("home_sidebar_center.php"); ?>
            <?php require_once("home_sidebar_right.php"); ?>
        </div>
    </main>

    <!-- MODAL DO EDITOR -->
    <?php require_once("editor-modal.php"); ?>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="editor.js"></script>
    
    <script>
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            const icon = document.getElementById('themeIcon');
            if (newTheme === 'light') {
                icon.className = 'bi bi-moon-fill';
            } else {
                icon.className = 'bi bi-sun-fill';
            }
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            const icon = document.getElementById('themeIcon');
            if (savedTheme === 'light') {
                icon.className = 'bi bi-moon-fill';
            } else {
                icon.className = 'bi bi-sun-fill';
            }
        });
    </script>

    <!-- No theme.php, antes do fechamento do </body> -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>    

</body>
</html>