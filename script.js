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
    