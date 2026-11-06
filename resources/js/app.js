import './bootstrap';

const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebar-overlay');
const toggle = document.getElementById('sidebar-toggle');
const close = document.getElementById('sidebar-close');

function abrirSidebar() {
    sidebar?.classList.remove('-translate-x-full');
    overlay?.classList.remove('hidden');
}

function cerrarSidebar() {
    sidebar?.classList.add('-translate-x-full');
    overlay?.classList.add('hidden');
}

toggle?.addEventListener('click', abrirSidebar);
close?.addEventListener('click', cerrarSidebar);
overlay?.addEventListener('click', cerrarSidebar);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        cerrarSidebar();
    }
});

// Cierre manual de mensajes flash.
document.querySelectorAll('[data-dismiss]').forEach((boton) => {
    boton.addEventListener('click', () => {
        boton.closest('[data-flash]')?.remove();
    });
});

// Los mensajes de éxito desaparecen solos.
window.setTimeout(() => {
    document.querySelectorAll('[data-flash]').forEach((mensaje) => {
        mensaje.style.transition = 'opacity .35s ease, transform .35s ease';
        mensaje.style.opacity = '0';
        mensaje.style.transform = 'translateY(-4px)';
        window.setTimeout(() => mensaje.remove(), 350);
    });
}, 6000);
