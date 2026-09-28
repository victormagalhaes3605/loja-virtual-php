// 1. Aplica o tema imediatamente ao carregar qualquer página
const savedTheme = localStorage.getItem('loja_theme') || 'light';
const htmlElement = document.documentElement;

htmlElement.setAttribute('data-bs-theme', savedTheme);
if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
}

// 2. Lógica do clique (só executa nas páginas onde o botão existe)
document.addEventListener("DOMContentLoaded", function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    if (themeToggleBtn && themeIcon) {
        updateIcon(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            
            if (newTheme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
            
            localStorage.setItem('loja_theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                themeIcon.style.color = '#ffc107'; 
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                themeIcon.style.color = 'inherit'; 
            }
        }
    }
});