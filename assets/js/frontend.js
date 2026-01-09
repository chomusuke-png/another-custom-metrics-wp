/**
 * ACM Frontend
 * Lógica de ejecución para el sitio web (visitantes).
 * Depende de: acm-core.js
 */
document.addEventListener('DOMContentLoaded', () => {
    
    // Verificar que el Core esté cargado
    if (typeof window.ACM === 'undefined') {
        console.error('ACM Core no está cargado.');
        return;
    }

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Delegamos la lógica compleja al Core
                window.ACM.startAnimation(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Buscar e iniciar observación
    document.querySelectorAll('.acm-value').forEach(el => observer.observe(el));
});