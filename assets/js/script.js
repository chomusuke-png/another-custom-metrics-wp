/**
 * ACM Script
 * Maneja animaciones: Count Up, Slot Machine, Blur, Bounce.
 */
document.addEventListener('DOMContentLoaded', () => {

    // --- UTILS FORMATO ---
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

    // --- ANIMACIÓN: SLOT MACHINE (TRAGAMONEDAS) ---
    const animateSlotMachine = (element, finalString, duration) => {
        element.innerHTML = ''; // Limpiar
        element.style.display = 'inline-flex';
        element.style.overflow = 'hidden';
        element.style.height = '1.2em'; // Altura fija basada en fuente
        element.style.alignItems = 'flex-start'; // Alinear arriba para que la cinta baje

        // Analizar cada caracter
        const chars = finalString.split('');
        
        chars.forEach((char, index) => {
            const wrapper = document.createElement('span');
            wrapper.style.display = 'inline-block';
            wrapper.style.lineHeight = '1.2em';
            
            if (/\d/.test(char)) {
                // Es un número: crear cinta 0..char
                const digit = parseInt(char, 10);
                const column = document.createElement('span');
                column.style.display = 'flex';
                column.style.flexDirection = 'column';
                column.style.transition = `transform ${duration}ms cubic-bezier(0.1, 0.7, 0.1, 1)`; // Easing suave
                
                // Generar números 0..digit (o 0..9 para efecto completo si quisiéramos)
                // Para slot machine simple, vamos de 0 hasta el número objetivo
                // Un truco visual: añadir números random antes para dar efecto de giro
                let content = '';
                // Pequeño ciclo extra para dar sensación de giro
                for(let i=0; i<=digit; i++) {
                    content += `<span>${i}</span>`;
                }
                column.innerHTML = content;
                wrapper.appendChild(column);
                element.appendChild(wrapper);

                // Forzar reflow
                requestAnimationFrame(() => {
                    // Mover la columna hacia arriba para mostrar el dígito final
                    // La altura de cada número es 1.2em. Si el digito es 5, hay 6 items (0,1,2,3,4,5).
                    // Queremos mostrar el último. TranslateY debe ser -(count-1) * 100% o ems
                    column.style.transform = `translateY(-${digit}00%)`; // Como cada span mide 100% de alto del padre si flex.. wait
                    // Mejor usar ems ya que lineHeight es 1.2em
                    column.style.transform = `translateY(-${digit * 1.2}em)`;
                });

            } else {
                // Símbolo estático ($, ., k, %)
                wrapper.textContent = char;
                element.appendChild(wrapper);
            }
        });
    };

    // --- ANIMACIÓN: COUNT UP (NORMAL) ---
    const animateCountUp = (element, rawTarget, format, decimals, prefix, suffix, duration) => {
        const startTime = performance.now();
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 4);
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

    // --- ANIMACIÓN: CSS BASED (BLUR, BOUNCE) ---
    const animateCssEffect = (element, rawTarget, format, decimals, prefix, suffix, effectClass) => {
        // Poner valor final inmediatamente
        element.textContent = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
        
        // Resetear animación removiendo clase y forzando reflow
        element.classList.remove(effectClass);
        void element.offsetWidth; // Trigger reflow
        element.classList.add(effectClass);
    };

    // --- ROUTER PRINCIPAL ---
    const startAnimation = (element) => {
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

        // Limpiar clases previas
        element.classList.remove('acm-effect-blur', 'acm-effect-bounce');
        element.style.display = ''; // Reset slot styles

        if (animType === 'slot') {
            // Generar string final primero
            const finalStr = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
            animateSlotMachine(element, finalStr, duration);
        } else if (animType === 'blur') {
            animateCssEffect(element, rawTarget, format, decimals, prefix, suffix, 'acm-effect-blur');
        } else if (animType === 'bounce') {
            animateCssEffect(element, rawTarget, format, decimals, prefix, suffix, 'acm-effect-bounce');
        } else {
            // Count (Default)
            animateCountUp(element, rawTarget, format, decimals, prefix, suffix, duration);
        }
    };


    // --- OBSERVER ---
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startAnimation(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.acm-value').forEach(el => observer.observe(el));


    // --- PREVIEW ADMIN ---
    const previewContainer = document.getElementById('acm-admin-preview');
    if (previewContainer) {
        const updatePreview = () => {
            const val      = document.getElementById('acm_value').value;
            const label    = document.getElementById('acm_label').value;
            const color    = document.getElementById('acm_color').value;
            const format   = document.getElementById('acm_format').value;
            const decimals = document.getElementById('acm_decimals').value || 0;
            const prefix   = document.getElementById('acm_prefix').value;
            const suffix   = document.getElementById('acm_suffix').value;
            const duration = document.getElementById('acm_duration').value || 2.5;
            const anim     = document.getElementById('acm_anim').value;

            let isNumeric = !isNaN(parseFloat(val)) && format !== 'date';
            let dataAttrs = '';

            if (isNumeric) {
                dataAttrs = `
                    data-acm-value="${val}" 
                    data-acm-format="${format}" 
                    data-acm-decimals="${decimals}" 
                    data-acm-prefix="${prefix}" 
                    data-acm-suffix="${suffix}"
                    data-acm-duration="${duration}"
                    data-acm-anim="${anim}"
                `;
            }
            const styleAttr = color ? `style="border-color: ${color}; color: ${color};"` : '';
            
            // HTML base (Render inicial simple)
            let html = `
                <div class="acm-widget-card" style="margin:0;">
                    <div class="acm-value" ${styleAttr} ${dataAttrs}>
                        ${prefix}${val}${suffix} 
                    </div>
                    <div class="acm-label">${label}</div>
                </div>
            `;
            previewContainer.innerHTML = html;

            if (isNumeric) {
                startAnimation(previewContainer.querySelector('.acm-value'));
            }
        };

        const inputs = ['acm_value', 'acm_label', 'acm_color', 'acm_format', 'acm_decimals', 'acm_prefix', 'acm_suffix', 'acm_duration', 'acm_anim'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', updatePreview);
                el.addEventListener('change', updatePreview);
            }
        });
        updatePreview();
    }
});