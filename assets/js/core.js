/**
 * ACM Core
 * Biblioteca de funciones compartidas para formatos y animaciones.
 * Expone el objeto global window.ACM
 */
window.ACM = window.ACM || {};

(function(exports) {
    'use strict';

    // --- 1. UTILIDADES DE FORMATO ---

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

    const formatNumberOnly = (value, format, decimals) => {
        if (value < 0) value = 0;
        switch (format) {
            case 'number': return value.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            case 'money': return '$ ' + value.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            case 'percent': return value.toFixed(decimals) + '%';
            case 'compact': return formatCompactGeneric(value, decimals);
            case 'money_compact': return '$ ' + formatCompactGeneric(value, decimals);
            case 'weight': return formatWeight(value, decimals);
            default: return Math.floor(value);
        }
    };

    // --- 2. MOTORES DE ANIMACIÓN ---

    const animateSlotMachine = (element, finalString, duration) => {
        element.innerHTML = '';
        element.style.display = 'inline-flex';
        element.style.overflow = 'hidden';
        element.style.height = '1.2em';
        element.style.alignItems = 'flex-start';

        const chars = finalString.split('');
        chars.forEach((char) => {
            const wrapper = document.createElement('span');
            wrapper.style.display = 'inline-block';
            wrapper.style.lineHeight = '1.2em';
            
            if (/\d/.test(char)) {
                const digit = parseInt(char, 10);
                const column = document.createElement('span');
                column.style.display = 'flex';
                column.style.flexDirection = 'column';
                column.style.transition = `transform ${duration}ms cubic-bezier(0.1, 0.7, 0.1, 1)`;
                
                let content = '';
                // Crear cinta de números
                for(let i=0; i<=digit; i++) { content += `<span>${i}</span>`; }
                
                column.innerHTML = content;
                wrapper.appendChild(column);
                element.appendChild(wrapper);

                // Disparar animación
                requestAnimationFrame(() => {
                    column.style.transform = `translateY(-${digit * 1.2}em)`;
                });
            } else {
                wrapper.textContent = char;
                element.appendChild(wrapper);
            }
        });
    };

    const animateCountUp = (element, rawTarget, format, decimals, prefix, suffix, duration) => {
        const startTime = performance.now();
        
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 4); // Ease Out Quart
            
            const currentVal = rawTarget * ease;
            element.textContent = prefix + formatNumberOnly(currentVal, format, decimals) + suffix;

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                element.textContent = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
            }
        };
        requestAnimationFrame(step);
    };

    const animateCssEffect = (element, rawTarget, format, decimals, prefix, suffix, effectClass) => {
        element.textContent = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
        // Reiniciar animación CSS
        element.classList.remove(effectClass);
        void element.offsetWidth; // Force Reflow
        element.classList.add(effectClass);
    };

    // --- 3. FUNCIÓN PÚBLICA PRINCIPAL ---

    exports.startAnimation = (element) => {
        const rawTarget = parseFloat(element.getAttribute('data-acm-value'));
        const format    = element.getAttribute('data-acm-format');
        const decimals  = parseInt(element.getAttribute('data-acm-decimals')) || 0;
        const prefix    = element.getAttribute('data-acm-prefix') || '';
        const suffix    = element.getAttribute('data-acm-suffix') || '';
        const animType  = element.getAttribute('data-acm-anim') || 'count';
        
        let durationSec = parseFloat(element.getAttribute('data-acm-duration'));
        if (isNaN(durationSec)) durationSec = 2.5;
        const duration = durationSec * 1000;

        if (isNaN(rawTarget) || format === 'date') return;

        // Limpieza previa
        element.classList.remove('acm-effect-blur', 'acm-effect-bounce');
        element.style.display = '';

        // Router de animaciones
        if (animType === 'slot') {
            const finalStr = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
            animateSlotMachine(element, finalStr, duration);
        } else if (animType === 'blur') {
            animateCssEffect(element, rawTarget, format, decimals, prefix, suffix, 'acm-effect-blur');
        } else if (animType === 'bounce') {
            animateCssEffect(element, rawTarget, format, decimals, prefix, suffix, 'acm-effect-bounce');
        } else {
            animateCountUp(element, rawTarget, format, decimals, prefix, suffix, duration);
        }
    };

})(window.ACM);