/**
 * ACM Script
 * Maneja la animación y la vista previa en admin.
 */
document.addEventListener('DOMContentLoaded', () => {

    // --- LÓGICA DE FORMATO (COMPARTIDA) ---
    
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

    // --- ANIMACIÓN ---

    const animateCounter = (element) => {
        const rawTarget = parseFloat(element.getAttribute('data-acm-value'));
        const format    = element.getAttribute('data-acm-format');
        const decimals  = parseInt(element.getAttribute('data-acm-decimals')) || 0;
        const prefix    = element.getAttribute('data-acm-prefix') || '';
        const suffix    = element.getAttribute('data-acm-suffix') || '';
        
        // Obtener duración en segundos y convertir a ms
        let durationSec = parseFloat(element.getAttribute('data-acm-duration'));
        if (isNaN(durationSec)) durationSec = 2.5; // fallback
        const duration = durationSec * 1000; 

        if (isNaN(rawTarget) || format === 'date') return;

        const startTime = performance.now();
        element.textContent = prefix + formatNumberOnly(0, format, decimals) + suffix;
        
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

    // --- INICIALIZAR FRONTEND (Observer) ---
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.acm-value').forEach(el => observer.observe(el));


    // --- LÓGICA DE VISTA PREVIA EN ADMIN ---
    
    const previewContainer = document.getElementById('acm-admin-preview');
    if (previewContainer) {
        
        // Función para actualizar la preview
        const updatePreview = () => {
            // Recoger valores de los inputs
            const val      = document.getElementById('acm_value').value;
            const label    = document.getElementById('acm_label').value;
            const color    = document.getElementById('acm_color').value;
            const format   = document.getElementById('acm_format').value;
            const decimals = document.getElementById('acm_decimals').value || 0;
            const prefix   = document.getElementById('acm_prefix').value;
            const suffix   = document.getElementById('acm_suffix').value;
            const duration = document.getElementById('acm_duration').value || 2.5;

            // Construir HTML simulado
            // Nota: Si no es numérico, solo mostramos el texto sin animación
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
                `;
            }

            const styleAttr = color ? `style="border-color: ${color}; color: ${color};"` : '';
            
            // HTML base
            let html = `
                <div class="acm-widget-card" style="margin:0;">
                    <div class="acm-value" ${styleAttr} ${dataAttrs}>
                        ${prefix}${val}${suffix} 
                    </div>
                    <div class="acm-label">${label}</div>
                </div>
            `;
            
            previewContainer.innerHTML = html;

            // Disparar animación si es numérico
            if (isNumeric) {
                const newMetric = previewContainer.querySelector('.acm-value');
                animateCounter(newMetric);
            }
        };

        // Escuchar eventos en todos los inputs relevantes
        const inputs = ['acm_value', 'acm_label', 'acm_color', 'acm_format', 'acm_decimals', 'acm_prefix', 'acm_suffix', 'acm_duration'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', updatePreview);
                el.addEventListener('change', updatePreview);
            }
        });

        // Ejecutar una vez al inicio
        updatePreview();
    }
});