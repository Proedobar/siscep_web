document.addEventListener('DOMContentLoaded', function() {
    // Botón para cambiar tema
    const themeToggle = document.getElementById('theme-toggle');
    
    // Verificar el tema guardado en localStorage
    const currentTheme = localStorage.getItem('theme');
    
    // Si hay un tema guardado, aplicarlo
    if (currentTheme) {
        document.body.classList.add(currentTheme);
        if (currentTheme === 'dark-mode' && themeToggle) {
            themeToggle.checked = true;
        }
    } else {
        // Detectar preferencia del sistema
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark-mode');
            if (themeToggle) themeToggle.checked = true;
            localStorage.setItem('theme', 'dark-mode');
        }
    }
    
    // Función para cambiar el tema
    function toggleTheme() {
        if (document.body.classList.contains('dark-mode')) {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme', '');
        } else {
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark-mode');
        }
    }
    
    // Agregar evento al botón si existe
    if (themeToggle) {
        themeToggle.addEventListener('change', toggleTheme);
    }
    
    // Exponer la función globalmente para usarla desde cualquier lugar
    window.toggleTheme = toggleTheme;
}); 