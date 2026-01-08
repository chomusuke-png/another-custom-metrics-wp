/**
 * ACM Script
 * Maneja la animación con soporte dinámico de decimales, prefijos y sufijos.
 *
 */
document.addEventListener('DOMContentLoaded', () => {

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    /**
     * Utilidades de Formato
     */
    const formatCompactGeneric = (value, decimals) => {
        if (value === 0) return '0';
        const suffixes = ['', 'k', 'M', 'B', 'T'];
        const suffixNum = Math.floor(Math.log10(Math.abs(value)) / 3);
        const safeSuffixNum = Math.min(Math.max(0, suffixNum), suffixes.length - 1);
        const suffix = suffixes[safeSuffixNum];
        let shortValue = value / Math.pow(1000, safeSuffixNum);
        return shortValue.toFixed(decimals) + suffix;
    };

    const formatWeight = (value, decimals) => {
        if (value <= 0) return '0 g';
        const suffixes = ['g', 'kg', 't'];
        const suffixNum = Math.floor(Math.log10(value) / 3);
        const safeSuffixNum = Math.min(Math.max(0, suffixNum), suffixes.length - 1);
        const suffix = suffixes[safeSuffixNum];
        let shortValue = value / Math.pow(1000, safeSuffixNum);
        return shortValue.toFixed(decimals) + ' ' + suffix;
    };

    /**
     * Router Principal de Formatos (Solo devuelve el número formateado)
     */
    const formatNumberOnly = (value, format, decimals) => {
        if (value < 0) value = 0;

        switch (format) {
            case 'number':
                return value.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            case 'money':
                return '$ ' + value.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            case 'percent':
                return value.toFixed(decimals) + '%';
            case 'compact':
                return formatCompactGeneric(value, decimals);
            case 'money_compact':
                return '$ ' + formatCompactGeneric(value, decimals);
            case 'weight':
                return formatWeight(value, decimals);
            default:
                return Math.floor(value);
        }
    };

    /**
     * Animación
     */
    const animateCounter = (element) => {
        const rawTarget = parseFloat(element.getAttribute('data-acm-value'));
        const format    = element.getAttribute('data-acm-format');
        const decimals  = parseInt(element.getAttribute('data-acm-decimals')) || 0;
        
        // Nuevos atributos
        const prefix    = element.getAttribute('data-acm-prefix') || '';
        const suffix    = element.getAttribute('data-acm-suffix') || '';
        
        if (isNaN(rawTarget) || format === 'date') return;

        const duration = 2500;
        const startTime = performance.now();

        // Estado inicial
        element.textContent = prefix + formatNumberOnly(0, format, decimals) + suffix;
        
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 4);
            const currentVal = rawTarget * ease;
            
            // Construimos la cadena completa
            element.textContent = prefix + formatNumberOnly(currentVal, format, decimals) + suffix;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Estado final
                element.textContent = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
            }
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.acm-value').forEach(el => observer.observe(el));
});