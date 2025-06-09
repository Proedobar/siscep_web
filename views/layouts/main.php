<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

// Añadir Bootstrap 5 CSS y JS
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_END]);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// Añadir Font Awesome
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css']);

// Añadir fuente Montserrat desde Google Fonts
$this->registerLinkTag(['rel' => 'stylesheet', 'href' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap']);

// Esta técnica elimina los destellos detectando el tema inmediatamente
$this->registerJs('
// Esta técnica elimina completamente los destellos aplicando el tema ANTES de cualquier renderizado
// No se debe colocar en document.ready para asegurar que se ejecute lo antes posible
try {
    var savedTheme = localStorage.getItem("darkMode");
    if (savedTheme === "true") {
        document.documentElement.classList.add("dark-mode");
        document.body.classList.add("dark-mode");
    }
} catch(e) {}
', \yii\web\View::POS_HEAD);

// Estilos inline para prevenir FOUC
$this->registerCss('
/* Estilos críticos para prevenir FOUC */
html.dark-mode { background-color: #343a40 !important; }
html.dark-mode body { background-color: #343a40 !important; color: #f8f9fa !important; }
', ['position' => \yii\web\View::POS_HEAD]);

// Estilos CSS para el FAE (Floating Action Element)
$this->registerCss('
/* Aplicar Montserrat globalmente */
html, body, p, h1, h2, h3, h4, h5, h6, div, span, button, input, select, textarea {
    font-family: "Montserrat", sans-serif !important;
}

/* Estilos para logos que cambian con el tema */
.logo-light {
    display: block;
}
.logo-dark {
    display: none;
}

html.dark-mode .logo-light,
body.dark-mode .logo-light {
    display: none !important;
}

html.dark-mode .logo-dark,
body.dark-mode .logo-dark {
    display: block !important;
}

/* Estilos para el contenedor principal */
main .container {
    border-radius: 15px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    padding: 20px;
    margin-top: 20px;
    margin-bottom: 20px;
    width: calc((100% - 15%) * 0.95);
    margin-left: auto;
    margin-right: auto;
    transition: all 0.3s ease;
    overflow-y: auto;
}

/* Personalización de la barra de scroll */
main .container::-webkit-scrollbar {
    width: 10px;
}

main .container::-webkit-scrollbar-track {
    background: transparent;
    margin: 5px 0;
    border-radius: 10px;
}

main .container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
    border: 2px solid #fff;
}

main .container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Personalización de la barra de scroll para modo oscuro */
body.dark-mode main .container::-webkit-scrollbar-thumb,
html.dark-mode main .container::-webkit-scrollbar-thumb {
    background: #4a4a4a;
    border: 2px solid #343a40;
}

body.dark-mode main .container::-webkit-scrollbar-thumb:hover,
html.dark-mode main .container::-webkit-scrollbar-thumb:hover {
    background: #5a5a5a;
}

/* Ajuste para el container cuando sidebar está colapsado */
.main-content.expanded main .container {
    width: calc((100% - 5%) * 0.95);
}

/* Altura mínima para dispositivos grandes */
@media (min-width: 992px) {
    main .container {
        min-height: 60vh;
        max-height: 65vh;
        overflow-y: auto;
    }
}

/* Ajuste para dispositivos móviles */
@media (max-width: 768px) {
    main .container {
        width: 95%;
        min-height: 58vh; /* 20% menos que 60vh */
        max-height: 58vh; /* 20% menos que 65vh */
        overflow-y: auto;
    }
}

body.dark-mode main .container,
html.dark-mode main .container {
    background-color: #343a40;
    box-shadow: 0 4px 8px rgba(0,0,0,0.4);
}

.theme-switch-wrapper {
    position: fixed;
    bottom: 70px;
    right: 40px;
    display: flex;
    flex-direction: row;
    align-items: center;
    z-index: 1000;
    height: 50px;
    width: auto;
    border-radius: 12px;
    background-color: #f8f9fa;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    overflow: visible;
}

/* Ajuste para dispositivos móviles */
@media (max-width: 768px) {
    .theme-switch-wrapper {
        bottom: 92px; /* Ajustado para estar por encima del footer móvil */
        right: 20px; /* Reducido el margen derecho en móviles */
    }
}

.theme-switch-wrapper.expanded {
    right: 45px;
}

body.dark-mode .theme-switch-wrapper,
html.dark-mode .theme-switch-wrapper {
    background-color: #343a40;
}
.theme-button, .profile-button {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 50px;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s ease;
}
.theme-button {
    border-radius: 12px 0 0 12px;
}
.profile-button {
    border-radius: 0 12px 12px 0;
}
.theme-button:hover, .profile-button:hover {
    background-color: rgba(0, 0, 0, 0.1);
}
body.dark-mode .theme-button:hover, 
body.dark-mode .profile-button:hover,
html.dark-mode .theme-button:hover, 
html.dark-mode .profile-button:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
.theme-icon, .profile-icon {
    color: #495057;
    font-size: 20px;
}
body.dark-mode .theme-icon, 
body.dark-mode .profile-icon,
html.dark-mode .theme-icon, 
html.dark-mode .profile-icon {
    color: #e9ecef;
}
.fa-sun {
    display: none;
}
.fa-moon {
    display: inline-block;
}
body.dark-mode .fa-sun,
html.dark-mode .fa-sun {
    display: inline-block;
}
body.dark-mode .fa-moon,
html.dark-mode .fa-moon {
    display: none;
}
.horizontal-separator {
    width: 1px;
    height: 30px;
    background-color: rgba(0, 0, 0, 0.2);
    margin: 0;
}
body.dark-mode .horizontal-separator,
html.dark-mode .horizontal-separator {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Estilos para el Sidebar */
.content-wrapper {
    display: flex;
    min-height: 100vh;
}
.sidebar {
    width: 15%;
    background-color: #f8f9fa;
    border-right: 1px solid #dee2e6;
    transition: all 0.3s ease;
    flex-shrink: 0;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 995;
}
.sidebar.collapsed {
    width: 5%;
}
body.dark-mode .sidebar,
html.dark-mode .sidebar {
    background-color: #343a40;
    border-right: 1px solid #495057;
}
.sidebar-hamburger-container {
    padding: 10px;
    display: flex;
    justify-content: center;
}
.sidebar-header {
    padding: 1.5rem 0.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.sidebar.collapsed .sidebar-header {
    padding: 1.5rem 0.2rem;
    justify-content: center;
}
.sidebar.collapsed .sidebar-header h5 {
    display: none;
}
.sidebar.collapsed .sidebar-header .ms-2 {
    margin-left: 0 !important;
}
body.dark-mode .sidebar-header,
html.dark-mode .sidebar-header {
    border-bottom: 1px solid #495057;
}
.sidebar-menu {
    padding: 1rem 0;
    transition: all 0.3s ease;
}
.sidebar-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.sidebar-menu li {
    margin-bottom: 0.25rem;
}
.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.25rem;
    text-decoration: none;
    color: #495057;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 0 0.5rem;
    white-space: nowrap;
    overflow: hidden;
}
.sidebar.collapsed .sidebar-menu a {
    padding: 0.75rem 0.5rem;
    justify-content: center;
    margin: 0 0.2rem;
}
.sidebar.collapsed .sidebar-menu a span {
    display: none;
}
body.dark-mode .sidebar-menu a,
html.dark-mode .sidebar-menu a {
    color: #e9ecef;
}
.sidebar-menu a:hover {
    background-color: #e9ecef;
    color: #212529;
    border-radius: 8px;
}
body.dark-mode .sidebar-menu a:hover,
html.dark-mode .sidebar-menu a:hover {
    background-color: #495057;
    color: #f8f9fa;
}
.sidebar-menu a i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
}
.sidebar.collapsed .sidebar-menu a i {
    margin-right: 0;
    font-size: 1.2rem;
}
.main-content {
    flex-grow: 1;
    margin-left: 15%;
    width: calc(100% - 15%);
    padding-bottom: 60px;
    padding-top: 56px;
    transition: all 0.3s ease;
}
.main-content.expanded {
    margin-left: 5%;
    width: calc(100% - 5%);
}
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: -100%;
        width: 100%;
        height: 100vh;
        z-index: 999;
    }
    .sidebar.collapsed {
        left: -100%;
    }
    .sidebar.show {
        left: 0;
        width: 100%;
    }
    .main-content {
        margin-left: 0;
        width: 100%;
    }
    .main-content.expanded {
        margin-left: 0;
        width: 100%;
    }
    .sidebar-toggle {
        display: flex !important;
    }
    #footer {
        width: 100% !important;
        left: 0 !important;
    }
    .sidebar-hamburger-container {
        display: none; /* Ocultar en dispositivos móviles */
    }
    
    /* Ajustar el header del sidebar para compensar el espacio */
    .sidebar-header {
        padding-top: 1rem;
        border-top: none;
    }
}
.sidebar-toggle {
    position: fixed;
    top: 10px;
    left: 10px;
    display: none; /* Por defecto estará oculto */
    z-index: 1001;
    background-color: #f8f9fa;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    align-items: center;
    justify-content: center;
}

/* Solo mostrar en dispositivos móviles */
@media (max-width: 768px) {
    .sidebar-toggle {
        display: flex !important;
    }
    
    .main-content {
        padding-top: 70px;
    }
    
    .sidebar {
        padding-top: 60px;
    }
}

/* Asegurar que está oculto en dispositivos grandes */
@media (min-width: 769px) {
    .sidebar-toggle {
        display: none !important;
    }
}

.sidebar-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
.sidebar-toggle:active {
    transform: scale(0.95);
}
.sidebar-toggle i {
    font-size: 1.2rem;
    color: #495057;
    transition: transform 0.3s ease;
}
.sidebar.show + .main-content .sidebar-toggle i {
    transform: rotate(180deg);
}
/* Ajustes para modo oscuro */
html.dark-mode .sidebar-toggle,
body.dark-mode .sidebar-toggle {
    background-color: #343a40;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
html.dark-mode .sidebar-toggle i,
body.dark-mode .sidebar-toggle i {
    color: #f8f9fa;
}
@media (max-width: 768px) {
    .sidebar-toggle {
        display: flex !important;
    }
}

/* Estilos para el footer sticky */
#footer {
    position: fixed;
    bottom: 0;
    left: calc(15% + (100% - 15%) * 0.025);
    width: calc((100% - 15%) * 0.95);
    height: 60px;
    z-index: 990;
    box-shadow: 0px -2px 10px rgba(0,0,0,0.1);
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    transition: all 0.3s ease;
}

/* Ajustes para dispositivos móviles */
@media (max-width: 768px) {
    #footer {
        height: 82px; /* 30% más compacto */
        font-size: 0.85rem;
        left: 0;
        width: 100%;
    }
    
    #footer .last-connection {
        padding: 4px 10px;
        font-size: 0.85rem;
    }
    
    #footer .container {
        padding-left: 10px;
        padding-right: 10px;
    }
}

#footer.expanded {
    left: calc(5% + (100% - 5%) * 0.025);
    width: calc((100% - 5%) * 0.95);
}

body.dark-mode #footer {
    background-color: #343a40 !important;
    color: #f8f9fa;
}

/* Estilos para el indicador de última conexión */
.last-connection {
    background-color: #f0f0f0;
    border-radius: 8px;
    padding: 8px 15px;
    font-size: 0.9em;
    transition: background-color 0.3s ease;
    margin: 0 5px;
    display: inline-block;
}

body.dark-mode .last-connection,
html.dark-mode .last-connection {
    background-color: #2c3136;
    color: #e9ecef;
}

/* Ajuste para el header */
#header {
    position: fixed;
    top: 0;
    left: 15%;
    width: calc(100% - 15%);
    z-index: 996;
    transition: all 0.3s ease;
}
#header.expanded {
    left: 5%;
    width: calc(100% - 5%);
}
@media (max-width: 768px) {
    #header {
        left: 0;
        width: 100%;
    }
    #header.expanded {
        left: 0;
        width: 100%;
    }
}

/* Estilos para el menú de perfil */
.profile-menu {
    position: absolute;
    visibility: hidden;
    opacity: 0;
    bottom: 60px;
    right: 0;
    width: 250px;
    background-color: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    overflow: hidden;
    z-index: 1010;
    transition: visibility 0.2s, opacity 0.2s linear;
    transform: translateY(10px);
}
.profile-menu.visible {
    visibility: visible;
    opacity: 1;
    transform: translateY(0);
}
body.dark-mode .profile-menu,
html.dark-mode .profile-menu {
    background-color: #343a40;
}
.profile-menu-item {
    padding: 15px;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #495057;
    transition: background-color 0.2s ease;
}
body.dark-mode .profile-menu-item,
html.dark-mode .profile-menu-item {
    color: #e9ecef;
}
.profile-menu-item:hover {
    background-color: rgba(0,0,0,0.05);
    text-decoration: none;
}
body.dark-mode .profile-menu-item:hover,
html.dark-mode .profile-menu-item:hover {
    background-color: rgba(255,255,255,0.05);
}
.profile-menu-separator {
    height: 1px;
    background-color: rgba(0,0,0,0.1);
    margin: 0;
}
body.dark-mode .profile-menu-separator,
html.dark-mode .profile-menu-separator {
    background-color: rgba(255,255,255,0.1);
}
.user-info {
    display: flex;
    justify-content: space-between;
    width: 100%;
    align-items: center;
}
.user-name {
    font-weight: 500;
}
.user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #2196F3;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}
.logout-icon {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Botón hamburguesa del sidebar */
.sidebar-hamburger-btn {
    background: transparent;
    border: none;
    font-size: 20px;
    color: #495057;
    cursor: pointer;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border-radius: 4px;
}
body.dark-mode .sidebar-hamburger-btn,
html.dark-mode .sidebar-hamburger-btn {
    color: #e9ecef;
}
.sidebar-hamburger-btn:hover {
    background-color: rgba(0,0,0,0.1);
}
body.dark-mode .sidebar-hamburger-btn:hover,
html.dark-mode .sidebar-hamburger-btn:hover {
    background-color: rgba(255,255,255,0.1);
}

/* Estilos para breadcrumbs Material You */
.breadcrumbs-container {
    margin-top: 20px;
    margin-bottom: 0;
    width: calc((100% - 15%) * 0.95);
    margin-left: auto;
    margin-right: auto;
    transition: all 0.3s ease;
}

.main-content.expanded .breadcrumbs-container {
    width: calc((100% - 5%) * 0.95);
}

@media (max-width: 768px) {
    .breadcrumbs-container {
        width: 95%;
    }
}

.material-breadcrumbs {
    background-color: #f8f9fa;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    padding: 12px 20px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 0;
    list-style: none;
    width: 100%;
    box-sizing: border-box;
}

body.dark-mode .material-breadcrumbs,
html.dark-mode .material-breadcrumbs {
    background-color: #2d3339;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.3);
}

.material-breadcrumbs > li {
    display: flex;
    align-items: center;
    color: #5f6368;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.5;
}

body.dark-mode .material-breadcrumbs > li,
html.dark-mode .material-breadcrumbs > li {
    color: #9aa0a6;
}

.material-breadcrumbs > li > a {
    color: #1a73e8;
    text-decoration: none;
    transition: color 0.2s ease;
}

body.dark-mode .material-breadcrumbs > li > a,
html.dark-mode .material-breadcrumbs > li > a {
    color: #8ab4f8;
}

.material-breadcrumbs > li > a:hover {
    color: #0d47a1;
    text-decoration: none;
}

body.dark-mode .material-breadcrumbs > li > a:hover,
html.dark-mode .material-breadcrumbs > li > a:hover {
    color: #aecbfa;
}

.material-breadcrumbs > li + li:before {
    content: "";
    display: inline-block;
    width: 24px;
    height: 24px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%235f6368\'%3E%3Cpath d=\'M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z\'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    margin: 0 4px;
    vertical-align: middle;
}

body.dark-mode .material-breadcrumbs > li + li:before,
html.dark-mode .material-breadcrumbs > li + li:before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%239aa0a6\'%3E%3Cpath d=\'M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z\'/%3E%3C/svg%3E");
}

.material-breadcrumbs .active {
    color: #202124;
    font-weight: 500;
}

body.dark-mode .material-breadcrumbs .active,
html.dark-mode .material-breadcrumbs .active {
    color: #e8eaed;
}
');

// Script para mostrar/ocultar el menú de perfil y cambiar el tema
$this->registerJs('
// Función para alternar la visibilidad del menú de perfil
function toggleProfileMenu() {
    var profileMenu = document.getElementById("profile-menu");
    if (profileMenu) {
        profileMenu.classList.toggle("visible");
    }
}

// Función para cambiar el tema con mejor manejo
window.toggleTheme = function() {
    // Alternar clases en ambos elementos
    document.documentElement.classList.toggle("dark-mode");
    document.body.classList.toggle("dark-mode");
    
    // Determinar si ahora estamos en modo oscuro
    var isDarkMode = document.documentElement.classList.contains("dark-mode");
    
    // Actualizar localStorage
    localStorage.setItem("darkMode", isDarkMode);
    
    // Actualizar el meta theme-color para dispositivos móviles
    var metaThemeColor = document.getElementById("theme-color-meta");
    if (metaThemeColor) {
        metaThemeColor.setAttribute("content", isDarkMode ? "#343a40" : "#f8f9fa");
    }
    
    // Actualizar los iconos de tema
    var moonIcons = document.querySelectorAll(".fa-moon");
    var sunIcons = document.querySelectorAll(".fa-sun");
    
    for (var i = 0; i < moonIcons.length; i++) {
        moonIcons[i].style.display = isDarkMode ? "none" : "inline-block";
    }
    for (var i = 0; i < sunIcons.length; i++) {
        sunIcons[i].style.display = isDarkMode ? "inline-block" : "none";
    }
};

// Agregar el evento click al botón de perfil
document.addEventListener("DOMContentLoaded", function() {
    // Actualizar el icono según el tema inicial
    if (document.documentElement.classList.contains("dark-mode") || document.body.classList.contains("dark-mode")) {
        var moonIcons = document.querySelectorAll(".fa-moon");
        var sunIcons = document.querySelectorAll(".fa-sun");
        for (var i = 0; i < moonIcons.length; i++) {
            moonIcons[i].style.display = "none";
        }
        for (var i = 0; i < sunIcons.length; i++) {
            sunIcons[i].style.display = "inline-block";
        }
    }
    
    // Toggle sidebar en dispositivos móviles
    var sidebarToggle = document.querySelector(".sidebar-toggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function() {
            document.querySelector(".sidebar").classList.toggle("show");
        });
    }
    
    // Control del menú de perfil
    var profileButton = document.getElementById("profile-button");
    if (profileButton) {
        profileButton.addEventListener("click", function(e) {
            toggleProfileMenu();
            e.stopPropagation();
        });
    }
    
    // Cerrar el menú al hacer clic en cualquier otro lugar
    document.addEventListener("click", function(e) {
        var profileMenu = document.getElementById("profile-menu");
        if (profileMenu && profileMenu.classList.contains("visible")) {
            if (!profileMenu.contains(e.target) && e.target.id !== "profile-button") {
                profileMenu.classList.remove("visible");
            }
        }
    });

    // Toggle sidebar con el botón hamburguesa
    var sidebarHamburgerBtn = document.getElementById("sidebar-toggle-btn");
    var sidebar = document.querySelector(".sidebar");
    var mainContent = document.querySelector(".main-content");
    var header = document.getElementById("header");
    var footer = document.getElementById("footer");
    var fae = document.querySelector(".theme-switch-wrapper");
    
    if (sidebarHamburgerBtn) {
        sidebarHamburgerBtn.addEventListener("click", function() {
            // En dispositivos móviles
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle("show");
            } 
            // En dispositivos de escritorio
            else {
                sidebar.classList.toggle("collapsed");
                mainContent.classList.toggle("expanded");
                header.classList.toggle("expanded");
                footer.classList.toggle("expanded");
                fae.classList.toggle("expanded");
            }
        });
    }
});

// Manejar la navegación AJAX del sidebar
document.addEventListener("DOMContentLoaded", function() {
    const mainContainer = document.querySelector("main .container");
    const sidebarLinks = document.querySelectorAll(".sidebar-menu a");
    
    sidebarLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            // No aplicar AJAX a enlaces específicos
            if (this.getAttribute("data-method") === "post" || 
                this.getAttribute("onclick") || 
                !this.getAttribute("href") || 
                this.getAttribute("href") === "#") {
                return;
            }
            
            e.preventDefault();
            const url = this.getAttribute("href");
            
            // Mostrar un indicador de carga
            mainContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>`;
            
            // Realizar la petición AJAX
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Parsear el HTML recibido
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");
                    
                    // Actualizar el contenido principal
                    const newContent = doc.querySelector("main .container");
                    if (newContent) {
                        mainContainer.innerHTML = newContent.innerHTML;
                    }
                    
                    // Actualizar los breadcrumbs
                    const newBreadcrumbs = doc.querySelector(".breadcrumbs-container");
                    const currentBreadcrumbsContainer = document.querySelector(".breadcrumbs-container");
                    
                    if (newBreadcrumbs) {
                        // Si hay nuevos breadcrumbs
                        if (!currentBreadcrumbsContainer) {
                            // Si no existe el contenedor, crearlo
                            const mainContent = document.querySelector(".main-content");
                            const breadcrumbsDiv = document.createElement("div");
                            breadcrumbsDiv.className = "breadcrumbs-container";
                            breadcrumbsDiv.innerHTML = newBreadcrumbs.innerHTML;
                            // Insertar antes del contenedor principal
                            mainContent.insertBefore(breadcrumbsDiv, mainContent.firstChild);
                        } else {
                            // Si existe el contenedor, actualizar su contenido
                            currentBreadcrumbsContainer.innerHTML = newBreadcrumbs.innerHTML;
                            currentBreadcrumbsContainer.style.display = "block";
                        }
                    } else {
                        // Si no hay breadcrumbs en la nueva página, ocultar el contenedor si existe
                        if (currentBreadcrumbsContainer) {
                            currentBreadcrumbsContainer.style.display = "none";
                        }
                    }
                    
                    // Actualizar la URL sin recargar
                    window.history.pushState({}, "", url);
                    
                    // Actualizar el título si está disponible
                    const newTitle = doc.querySelector("title");
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }
                })
                .catch(error => {
                    console.error("Error en la navegación AJAX:", error);
                    mainContainer.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error al cargar el contenido. Por favor, intente nuevamente.
                        </div>`;
                });
        });
    });
    
    // Manejar la navegación del navegador (botones atrás/adelante)
    window.addEventListener("popstate", function() {
        location.reload();
    });
});
', \yii\web\View::POS_END);

$this->registerCss('
/* Estilos específicos para el modal de constancias */
#constanciaModal {
    --modal-primary: #0d6efd;
    --modal-success: #198754;
    --modal-info: #0dcaf0;
    --modal-danger: #dc3545;
    --modal-background: #fff;
    --modal-text: #212529;
    --modal-border: #dee2e6;
    --modal-shadow: rgba(0,0,0,0.1);
}

/* Modo oscuro para el modal */
html.dark-mode #constanciaModal,
body.dark-mode #constanciaModal {
    --modal-background: #343a40;
    --modal-text: #f8f9fa;
    --modal-border: #495057;
    --modal-shadow: rgba(0,0,0,0.4);
}

#constanciaModal .modal-content {
    background-color: var(--modal-background);
    color: var(--modal-text);
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 20px var(--modal-shadow);
}

#constanciaModal .modal-header,
#constanciaModal .modal-footer {
    background-color: var(--modal-background);
    border-color: var(--modal-border);
    padding: 1rem 1.5rem;
}

#constanciaModal .modal-header {
    border-radius: 15px 15px 0 0;
}

#constanciaModal .modal-footer {
    border-radius: 0 0 15px 15px;
}

#constanciaModal .card {
    background-color: var(--modal-background);
    border: none;
}

#constanciaModal .icon-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#constanciaModal .hover-shadow:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    transition: box-shadow 0.3s ease-in-out;
}

#constanciaModal .border-soft-primary {
    border-color: rgba(13, 109, 253, 0.68) !important;
}

#constanciaModal .border-soft-success {
    border-color: rgba(25, 135, 84, 0.69) !important;
}

#constanciaModal .border-soft-info {
    border-color: rgba(13, 202, 240, 0.68) !important;
}

#constanciaModal .border-soft-danger {
    border-color: rgba(220, 53, 70, 0.72) !important;
}

#constanciaModal .glow-primary {
    box-shadow: 0 0 20px 5px rgba(13, 109, 253, 0.2) !important;
}

#constanciaModal .glow-success {
    box-shadow: 0 0 20px 5px rgba(25, 135, 84, 0.2) !important;
}

#constanciaModal .glow-info {
    box-shadow: 0 0 20px 5px rgba(13, 202, 240, 0.2) !important;
}

#constanciaModal .glow-danger {
    box-shadow: 0 0 20px 5px rgba(220, 53, 70, 0.2) !important;
}

#constanciaModal .custom-select-wrapper {
    position: relative;
}

#constanciaModal .custom-select {
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: #f0f0f0;
    border: none;
    border-radius: 8px;
    color: var(--modal-text);
    appearance: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

#constanciaModal .select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    transition: transform 0.3s ease;
}

#constanciaModal .custom-select:focus + .select-icon {
    transform: translateY(-50%) rotate(180deg);
}

#constanciaModal .alert {
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0;
}

#constanciaModal .alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    border: none;
    color: var(--modal-danger);
}

#constanciaModal .btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

#constanciaModal .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px var(--modal-shadow);
}

/* Spinner personalizado para el modal */
#constanciaModal .spinner-border {
    width: 1.5rem;
    height: 1.5rem;
    border-width: 0.2em;
}

/* Overlay del spinner */
.spinner-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.spinner-content {
    background: transparent;
}

/* Ajustes específicos para modo oscuro */
html.dark-mode #constanciaModal .custom-select,
body.dark-mode #constanciaModal .custom-select {
    background-color: #3a3f45;
}

html.dark-mode #constanciaModal .alert-danger,
body.dark-mode #constanciaModal .alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Ajustes adicionales para coincidir con el index */
#constanciaModal .card {
    transition: box-shadow 0.3s ease-in-out;
}

#constanciaModal .card:hover {
    transform: none;
}

#constanciaModal .text-muted {
    font-size: 1.25rem !important;
}

#constanciaModal .card-title {
    font-weight: bold !important;
}

#constanciaModal .bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}
');
?>
<?php $this->beginPage() ?>
<!-- Eliminar FOUC: Aplicar tema antes de cualquier renderizado visual -->
<script>
    // Este script se ejecuta inmediatamente, sin esperar a DOMContentLoaded
    (function() {
        try {
            var savedTheme = localStorage.getItem("darkMode");
            var isDarkMode = savedTheme === "true";
            if (isDarkMode) {
                document.documentElement.classList.add("dark-mode");
            }
        } catch(e) {}
    })();
</script>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta name="theme-color" content="" id="theme-color-meta">
    <style>
        /* Estilos críticos para evitar destellos */
        html.dark-mode { background: #343a40 !important; }
        html.dark-mode body { background-color: #343a40 !important; color: #f8f9fa !important; }
    </style>
    <script>
        // Actualizar el meta theme-color para móviles
        (function() {
            var isDarkMode = document.documentElement.classList.contains('dark-mode');
            document.getElementById('theme-color-meta').setAttribute('content', 
                isDarkMode ? '#343a40' : '#f8f9fa');
        })();
    </script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
</header>

<div class="content-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-hamburger-container">
            <button class="sidebar-hamburger-btn" id="sidebar-toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="sidebar-header">
            <h5 class="m-0 text-center"><?= Yii::$app->name ?></h5>
            <div class="ms-2">
                <!-- Logo que cambia según el tema -->
                <img src="<?= Yii::getAlias('@web/logo.png') ?>" alt="Logo" class="img-fluid logo-light" style="width: 30px; height: 30px;">
                <img src="<?= Yii::getAlias('@web/logo_bn.png') ?>" alt="Logo" class="img-fluid logo-dark" style="width: 30px; height: 30px; display: none;">
            </div>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="<?= Yii::$app->homeUrl ?>"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
                
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?php 
                    $user = Yii::$app->user->identity;
                    $rol_id = $user->rol_id;
                    
                    // Opciones para todos los roles autenticados
                    if ($rol_id == 1 || $rol_id == 2 || $rol_id == 3 || $rol_id == 4): ?>
                        <li><a href="#" onclick="event.preventDefault(); const modal = new bootstrap.Modal(document.getElementById('constanciaModal')); modal.show();"><i class="fas fa-download"></i> <span>Constancia de Trabajo</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/site/recibos']) ?>"><i class="fas fa-file-alt"></i> <span>Mis Recibos de Pago</span></a></li>
                    <?php endif; ?>
                    
                    <?php // Opciones solo para Superusuario y Administrador
                    if ($rol_id == 1 || $rol_id == 2): ?>
                        <li><a href="<?= \yii\helpers\Url::to(['/nominas']) ?>"><i class="fas fa-upload"></i> <span>Gestión de Nominas</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/directores']) ?>"><i class="fas fa-user-tie"></i> <span>Directores</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/procuradores']) ?>"><i class="fas fa-user-tie"></i> <span>Procuradores</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/users']) ?>"><i class="fas fa-user"></i> <span>Usuarios</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/blocked-ips']) ?>"><i class="fas fa-shield-alt"></i> <span>IPs Bloqueadas</span></a></li>
                    <?php endif; ?>
                    
                    <?php // Opciones solo para Operador
                    if ($rol_id == 4): ?>
                        <li><a href="<?= \yii\helpers\Url::to(['/nominas']) ?>"><i class="fas fa-upload"></i> <span>Gestión de Nominas</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/directores']) ?>"><i class="fas fa-user-tie"></i> <span>Directores</span></a></li>
                        <li><a href="<?= \yii\helpers\Url::to(['/procuradores']) ?>"><i class="fas fa-user-tie"></i> <span>Procuradores</span></a></li>
                    <?php endif; ?>
                    
                    <?php // Opción Acerca de para todos los roles autenticados
                    if ($rol_id == 1 || $rol_id == 2 || $rol_id == 3 || $rol_id == 4): ?>
                        <li><a href="<?= \yii\helpers\Url::to(['/site/about']) ?>"><i class="fas fa-info-circle"></i> <span>Acerca de</span></a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?= \yii\helpers\Url::to(['/site/login']) ?>"><i class="fas fa-sign-in-alt"></i> <span>Iniciar sesión</span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>

    <!-- Contenido principal -->
    <div class="main-content">
        <button class="btn btn-sm btn-outline-primary sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Breadcrumbs con estilo Material You -->
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <div class="breadcrumbs-container">
                <?= Breadcrumbs::widget([
                    'links' => $this->params['breadcrumbs'], 
                    'options' => ['class' => 'material-breadcrumbs'],
                    'encodeLabels' => false,
                    'itemTemplate' => "<li>{link}</li>"
                ]) ?>
            </div>
        <?php endif ?>
        
        <main id="main" role="main">
            <div class="container">
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </main>
    </div>
</div>

<!-- FAE (Floating Action Element) -->
<div class="theme-switch-wrapper">
    <button type="button" class="theme-button" onclick="window.toggleTheme()">
        <span class="theme-icon">
            <i class="fas fa-moon"></i>
            <i class="fas fa-sun"></i>
        </span>
    </button>
    <div class="horizontal-separator"></div>
    <button type="button" id="profile-button" class="profile-button">
        <span class="profile-icon">
            <i class="fas fa-user"></i>
        </span>
    </button>
    
    <div id="profile-menu" class="profile-menu">
        <?php if (!Yii::$app->user->isGuest): ?>
            <a href="<?= \yii\helpers\Url::to(['/site/perfil']) ?>" class="profile-menu-item">
                <div class="user-info">
                    <div class="user-name"><?= Yii::$app->user->identity->username ?></div>
                    <?php if ($user->foto_perfil): ?>
                        <img src="/siscep/web<?= $user->foto_perfil ?>" class="rounded-circle" style="width: 30px; height: 30px; border: 1px solid #dee2e6; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-user fa-4x text-secondary"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
            <div class="profile-menu-separator"></div>
            <a href="<?= \yii\helpers\Url::to(['/site/logout']) ?>" data-method="post" class="profile-menu-item">
                <i class="fas fa-sign-out-alt logout-icon"></i>
                <span>Cerrar sesión</span>
            </a>
        <?php else: ?>
            <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="profile-menu-item">
                <i class="fas fa-sign-in-alt logout-icon"></i>
                <span>Iniciar sesión</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<footer id="footer" class="py-0 bg-light">
    <div class="container h-100">
        <div class="row h-100 text-muted d-flex align-items-center">
            <div class="col-md-6 text-center text-md-start small">
                &copy; PGEB - Direccion de Informatica y Comunicación Corporativa
            </div>
            <div class="col-md-6 text-center text-md-end">
                <?php if (!Yii::$app->user->isGuest): ?>
                <span class="last-connection small">
                    Última Conexión: <?= Yii::$app->formatter->asDatetime(Yii::$app->user->identity->ultima_vez, 'php:d/m/Y H:i') ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>

<!-- Modal de Constancia de Trabajo -->
<div class="modal fade" id="constanciaModal" tabindex="-1" aria-labelledby="constanciaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="constanciaModalLabel">Generar Constancia de Trabajo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?php
                    $user = Yii::$app->user->identity;
                    $empleado = $user->empleado;
                    
                    if ($empleado):
                        $nominas = \app\models\DetallesNomina::find()
                            ->select(['nominas.nomina_id', 'nominas.nomina'])
                            ->innerJoin('nominas', 'nominas.nomina_id = detalles_nomina.nomina_id')
                            ->where(['detalles_nomina.empleado_id' => $empleado->empleado_id])
                            ->groupBy(['nominas.nomina_id', 'nominas.nomina'])
                            ->asArray()
                            ->all();
                    ?>
                    <div class="card border-0">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card h-100 border-start border-soft-primary border-3 rounded-3 hover-shadow glow-primary">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <h5 class="card-title fw-bold m-0">Nombre del Empleado</h5>
                                            </div>
                                            <p class="card-text fs-5 text-muted"><?= Html::encode($empleado->nombre ?? 'No disponible') ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100 border-start border-soft-success border-3 rounded-3 hover-shadow glow-success">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="icon-box bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="fas fa-id-card text-success"></i>
                                                </div>
                                                <h5 class="card-title fw-bold m-0">Cédula de Identidad</h5>
                                            </div>
                                            <p class="card-text fs-5 text-muted">V-<?= Html::encode($empleado->ci ?? 'No disponible') ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100 border-start border-soft-info border-3 rounded-3 hover-shadow glow-info">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="icon-box bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="fas fa-file-alt text-info"></i>
                                                </div>
                                                <h5 class="card-title fw-bold m-0">Nómina a Generar</h5>
                                            </div>
                                            <div class="custom-select-wrapper">
                                                <select class="form-select fs-5 custom-select" id="nominaSelect">
                                                    <option value="">Seleccione una nómina</option>
                                                    <?php if (!empty($nominas)): ?>
                                                        <?php foreach ($nominas as $nomina): ?>
                                                            <option value="<?= $nomina['nomina_id'] ?>"><?= Html::encode($nomina['nomina']) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="" disabled>No hay nóminas disponibles</option>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="select-icon">
                                                    <i class="fas fa-chevron-down text-info"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card h-100 border-start border-soft-danger border-3 rounded-3 hover-shadow glow-danger">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="icon-box bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="fas fa-calendar-alt text-danger"></i>
                                                </div>
                                                <h5 class="card-title fw-bold m-0">Fecha de Emisión</h5>
                                            </div>
                                            <p class="card-text fs-5 text-muted"><?= date('d/m/Y') ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-danger mt-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                * Deberá consignar el documento a recursos humanos para su validación y sellado
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No se encontró información del empleado asociado a su cuenta.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Debe iniciar sesión para generar una constancia de trabajo.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarConstancia">
                    <span class="btn-text">
                        <i class="fas fa-file-download me-2"></i>Generar Constancia
                    </span>
                    <span class="spinner d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Generando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Overlay del Spinner -->
<div id="spinnerOverlay" class="spinner-overlay d-none">
    <div class="spinner-content">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <div class="mt-3 text-white fw-light">Generando constancia...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el modal
    const constanciaModal = new bootstrap.Modal(document.getElementById('constanciaModal'));
    const btnGenerar = document.getElementById('btnGenerarConstancia');
    const spinnerOverlay = document.getElementById('spinnerOverlay');
    
    // Función para mostrar el spinner
    function showSpinner() {
        btnGenerar.disabled = true;
        btnGenerar.querySelector('.btn-text').classList.add('d-none');
        btnGenerar.querySelector('.spinner').classList.remove('d-none');
        spinnerOverlay.classList.remove('d-none');
    }
    
    // Función para ocultar el spinner
    function hideSpinner() {
        btnGenerar.disabled = false;
        btnGenerar.querySelector('.btn-text').classList.remove('d-none');
        btnGenerar.querySelector('.spinner').classList.add('d-none');
        spinnerOverlay.classList.add('d-none');
    }
    
    // Manejar el clic en el botón de generar constancia
    btnGenerar.addEventListener('click', function() {
        const nominaId = document.getElementById('nominaSelect').value;
        if (!nominaId) {
            alert('Por favor, seleccione una nómina');
            return;
        }
        
        // Mostrar spinner
        showSpinner();
        
        // Esperar 3 segundos
        setTimeout(function() {
            // Ocultar spinner y modal
            hideSpinner();
            constanciaModal.hide();
            
            // Redirigir a la página de generación
            window.location.href = '<?= \yii\helpers\Url::to(['/site/generar-constancia']) ?>?nomina_id=' + nominaId;
        }, 3000);
    });
});
</script>

<!-- Modal de Alerta de Directores/Procuradores -->
<div class="modal fade" id="alertaModal" tabindex="-1" aria-labelledby="alertaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertaModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Alerta del Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="alert alert-warning mb-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h5 class="alert-heading mb-2">¡Atención!</h5>
                            <p class="mb-0" id="alertaModalMensaje">
                                No se encontraron directores o procuradores activos en el sistema.
                                Por favor, contacte al administrador.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar directores y procuradores activos al cargar la página
    verificarDirectoresProcuradores();
    
    // Función para verificar directores y procuradores activos
    function verificarDirectoresProcuradores() {
        fetch('<?= \yii\helpers\Url::to(['/site/verificar-activos']) ?>')
            .then(response => response.json())
            .then(data => {
                if (!data.hayDirectoresActivos || !data.hayProcuradoresActivos) {
                    let mensaje = '';
                    if (!data.hayDirectoresActivos && !data.hayProcuradoresActivos) {
                        mensaje = 'No se encontraron directores ni procuradores activos en el sistema.';
                    } else if (!data.hayDirectoresActivos) {
                        mensaje = 'No se encontraron directores activos en el sistema.';
                    } else {
                        mensaje = 'No se encontraron procuradores activos en el sistema.';
                    }
                    
                    document.getElementById('alertaModalMensaje').textContent = mensaje;
                    const alertaModal = new bootstrap.Modal(document.getElementById('alertaModal'));
                    alertaModal.show();
                }
            })
            .catch(error => {
                console.error('Error al verificar directores y procuradores:', error);
            });
    }
});
</script>

<style>
.modal-content {
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    background-color: #f8f9fa;
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.1);
    background-color: #f8f9fa;
    border-radius: 0 0 15px 15px;
}

.form-control-static {
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    color: #495057;
}

html.dark-mode .modal-content,
body.dark-mode .modal-content {
    background-color: #343a40;
    color: #f8f9fa;
}

html.dark-mode .modal-header,
body.dark-mode .modal-header,
html.dark-mode .modal-footer,
body.dark-mode .modal-footer {
    background-color: #2c3136;
    border-color: #495057;
}

html.dark-mode .form-control-static,
body.dark-mode .form-control-static {
    background-color: #2c3136;
    border-color: #495057;
    color: #f8f9fa;
}

html.dark-mode .form-select,
body.dark-mode .form-select {
    background-color: #2c3136;
    border-color: #495057;
    color: #f8f9fa;
}

html.dark-mode .form-select option,
body.dark-mode .form-select option {
    background-color: #343a40;
    color: #f8f9fa;
}

.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: all 0.3s ease;
}

.spinner-content {
    text-align: center;
    background: transparent;
    padding: 2rem;
}

.spinner-content .spinner-border {
    filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
}

.spinner-content .text-white {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    font-size: 1.1rem;
    letter-spacing: 0.5px;
}

/* Ajustes para modo oscuro */
html.dark-mode .spinner-overlay,
body.dark-mode .spinner-overlay {
    background-color: rgba(0, 0, 0, 0.5);
}

/* Animaciones mejoradas */
.spinner-overlay.d-none {
    opacity: 0;
    visibility: hidden;
}

.spinner-overlay:not(.d-none) {
    opacity: 1;
    visibility: visible;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.spinner-content {
    animation: fadeIn 0.3s ease;
}

/* Ajustes adicionales para el modal */
.modal-content {
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border: none;
    animation: modalShow 0.3s ease;
}

@keyframes modalShow {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Ajustes para el contenido detrás del modal/spinner */
.main-content.blur {
    filter: blur(5px);
    transition: filter 0.3s ease;
}

/* Estilos para el backdrop del modal */
.modal-backdrop {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-backdrop.show {
    opacity: 1;
}

/* Animaciones del modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out, opacity 0.3s ease;
    transform: scale(0.95);
    opacity: 0;
}

.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

/* Estilos refinados para el spinner overlay */
.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: all 0.3s ease;
}

.spinner-content {
    text-align: center;
    background: transparent;
    padding: 2rem;
    animation: fadeIn 0.3s ease;
}

.spinner-content .spinner-border {
    filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
}

.spinner-content .text-white {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    font-size: 1.1rem;
    letter-spacing: 0.5px;
}

/* Ajustes para modo oscuro */
html.dark-mode .modal-backdrop,
body.dark-mode .modal-backdrop,
html.dark-mode .spinner-overlay,
body.dark-mode .spinner-overlay {
    background-color: rgba(0, 0, 0, 0.5);
}

/* Animaciones */
.spinner-overlay.d-none {
    opacity: 0;
    visibility: hidden;
}

.spinner-overlay:not(.d-none) {
    opacity: 1;
    visibility: visible;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Ajustes para el contenido detrás del modal/spinner */
.main-content.blur {
    filter: blur(5px);
    transition: filter 0.3s ease;
}

/* Ajustes adicionales para el modal */
.modal-content {
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border: none;
    animation: modalShow 0.3s ease;
}

@keyframes modalShow {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}

/* Estilos adicionales para el modal */
.border-soft-info {
    border-color: rgba(13, 202, 240, 0.68) !important;
}

.glow-info {
    box-shadow: 0 0 20px 5px rgba(13, 202, 240, 0.2) !important;
}

/* Ajustes para el select dentro del modal */
.form-select {
    border: none;
    background-color: transparent;
    color: #6c757d;
    padding-left: 0;
}

html.dark-mode .form-select,
body.dark-mode .form-select {
    color: #adb5bd;
}

.form-select:focus {
    box-shadow: none;
    border: none;
}

/* Ajustes para el modal en modo oscuro */
html.dark-mode .card,
body.dark-mode .card {
    background-color: #343a40;
}

html.dark-mode .card-text,
body.dark-mode .card-text {
    color: #adb5bd !important;
}

html.dark-mode .form-select option,
body.dark-mode .form-select option {
    background-color: #343a40;
    color: #adb5bd;
}

/* Estilos para el select personalizado */
.custom-select-wrapper {
    position: relative;
    width: 100%;
}

.custom-select {
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: rgba(13, 202, 240, 0.1);
    border: 2px solid rgba(13, 202, 240, 0.2);
    border-radius: 8px;
    color: #495057;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-select:focus {
    outline: none;
    border-color: rgba(13, 202, 240, 0.5);
    box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
}

.select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    transition: transform 0.3s ease;
}

.custom-select:focus + .select-icon {
    transform: translateY(-50%) rotate(180deg);
}

/* Estilos para modo oscuro */
html.dark-mode .custom-select,
body.dark-mode .custom-select {
    background-color: rgba(13, 202, 240, 0.1);
    border-color: rgba(13, 202, 240, 0.2);
    color: #e9ecef;
}

html.dark-mode .custom-select option,
body.dark-mode .custom-select option {
    background-color: #343a40;
    color: #e9ecef;
}

html.dark-mode .custom-select:focus,
body.dark-mode .custom-select:focus {
    border-color: rgba(13, 202, 240, 0.5);
}

/* Hover state */
.custom-select:hover {
    border-color: rgba(13, 202, 240, 0.4);
    background-color: rgba(13, 202, 240, 0.15);
}

/* Placeholder color */
.custom-select option[value=""] {
    color: #6c757d;
}

html.dark-mode .custom-select option[value=""],
body.dark-mode .custom-select option[value=""] {
    color: #adb5bd;
}

/* Estilos para el modal */
.modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
    margin: 0.5rem auto;
}

.modal-content {
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border: none;
    animation: modalShow 0.3s ease;
    max-height: calc(100vh - 2rem);
    display: flex;
    flex-direction: column;
}

.modal-header, .modal-footer {
    flex-shrink: 0;
    position: sticky;
    background-color: #f8f9fa;
    z-index: 1020;
}

.modal-header {
    top: 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    bottom: 0;
    border-top: 1px solid rgba(0,0,0,0.1);
    border-radius: 0 0 15px 15px;
}

.modal-body {
    flex: 1 1 auto;
    overflow-y: auto;
    position: relative;
    /* Personalización del scrollbar */
    scrollbar-width: thin;
    scrollbar-color: #c1c1c1 transparent;
}

/* Estilos del scrollbar para WebKit */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: transparent;
    margin: 5px 0;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Ajustes para modo oscuro */
html.dark-mode .modal-header,
body.dark-mode .modal-header,
html.dark-mode .modal-footer,
body.dark-mode .modal-footer {
    background-color: #2c3136;
    border-color: #495057;
}

html.dark-mode .modal-body::-webkit-scrollbar-thumb,
body.dark-mode .modal-body::-webkit-scrollbar-thumb {
    background: #4a4a4a;
}

html.dark-mode .modal-body::-webkit-scrollbar-thumb:hover,
body.dark-mode .modal-body::-webkit-scrollbar-thumb:hover {
    background: #5a5a5a;
}

/* Ajuste para el contenedor de las tarjetas */
.modal-body .card-body {
    padding: 1.25rem;
}

.modal-body .row.g-4 {
    margin: -0.5rem;
}

.modal-body .row.g-4 > [class*="col-"] {
    padding: 0.5rem;
}
</style>

</html>
<?php $this->endPage() ?>
