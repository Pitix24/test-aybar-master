import "./menu-principal.js";
import "../components/alertas.js";

document.addEventListener('alpine:init', () => {
    Alpine.data('themeSwitcher', () => ({
        theme: 'light',

        initTheme() {
            const savedTheme = localStorage.getItem('theme');

            this.theme = savedTheme ?? 'light';
            document.documentElement.setAttribute('data-theme', this.theme);
        },

        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', this.theme);
            localStorage.setItem('theme', this.theme);
        },
    }));
});
