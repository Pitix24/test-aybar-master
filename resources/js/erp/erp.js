import "./menu-principal.js";
import "../components/alertas.js";
import "../components/abrir-url.js";

document.addEventListener('alpine:init', () => {
    Alpine.data('themeSwitcher', () => ({
        theme: 'light',

        initTheme() {
            const savedTheme = localStorage.getItem('theme');

            this.theme = savedTheme ?? 'light';
            document.documentElement.setAttribute('data-theme', this.theme);
        },

        toggleTheme() {
            this.setTheme(this.theme === 'light' ? 'dark' : 'light');
        },

        setTheme(val) {
            this.theme = val;
            document.documentElement.setAttribute('data-theme', this.theme);
            localStorage.setItem('theme', this.theme);
        }
    }));

    Alpine.data('reloj', () => ({
        fecha: '',
        hora: '',

        actualizar() {
            const formateroFecha = new Intl.DateTimeFormat('es-PE', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            const formateroHora = new Intl.DateTimeFormat('es-PE', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const tick = () => {
                const ahora = new Date();
                let fechaFormateada = formateroFecha.format(ahora);
                // Capitalizar primera letra
                this.fecha = fechaFormateada.charAt(0).toUpperCase() + fechaFormateada.slice(1);
                this.hora = formateroHora.format(ahora);
            };

            tick();
            setInterval(tick, 1000);
        }
    }));
});
