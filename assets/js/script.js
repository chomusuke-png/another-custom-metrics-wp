/**
 * ACM Script
 * Maneja la animación de conteo numérico cuando el elemento entra en el viewport.
 */
document.addEventListener('DOMContentLoaded', () => {
    
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    /**
     * Formatea el número en JS similar a la lógica de PHP.
     * @param {number} value - Valor actual de la animación.
     * @param {string} format - Formato (money, compact, number, etc).
     * @returns {string} - Valor formateado.
     */
    const formatValue = (value, format) => {
        if (format === 'money') {
            return '$ ' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        
        if (format === 'compact') {
            const suffixes = ['', 'k', 'M', 'B', 'T'];
            const suffixNum = Math.floor(('' + parseInt(value)).length / 3);
            
            let shortValue = parseFloat((suffixNum !== 0 ? (value / Math.pow(1000, suffixNum)) : value).toPrecision(3));
            if (shortValue % 1 !== 0) {
                shortValue = shortValue.toFixed(1);
            }
            
            return shortValue + suffixes[suffixNum];
        }

        if (format === 'number') {
            return Math.floor(value).toLocaleString();
        }

        // Por defecto o raw
        return Math.floor(value);
    };

    /**
     * Función de animación frame a frame.
     * @param {HTMLElement} element - Elemento DOM a animar.
     */
    const animateCounter = (element) => {
        const rawValue = parseFloat(element.getAttribute('data-acm-value'));
        const format = element.getAttribute('data-acm-format');
        
        // Si no es un número válido o es fecha, no animamos
        if (isNaN(rawValue) || format === 'date') return;

        const duration = 2000; // 2 segundos
        const startTime = performance.now();
        
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function (easeOutExpo) para suavizar el final
            const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            
            const currentVal = rawValue * ease;
            element.textContent = formatValue(currentVal, format);

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Asegurar valor final exacto formateado
                // Para compact puede diferir levemente por redondeo JS vs PHP, pero es aceptable visualmente
                element.textContent = formatValue(rawValue, format); 
            }
        };

        requestAnimationFrame(step);
    };

    // Inicializar Observer
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                animateCounter(element);
                observer.unobserve(element); // Animar solo una vez
            }
        });
    }, observerOptions);

    // Buscar todos los elementos de valor
    const metricValues = document.querySelectorAll('.acm-value');
    metricValues.forEach(el => observer.observe(el));
});