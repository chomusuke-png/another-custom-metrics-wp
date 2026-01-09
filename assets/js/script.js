/**
 * ACM Script
 * Maneja animaciones, upload de imágenes y vista previa en admin.
 */
document.addEventListener('DOMContentLoaded', () => {

    // --- UTILS FORMATO (Sin cambios) ---
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

    // --- ANIMACIONES (Sin cambios) ---
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
                for(let i=0; i<=digit; i++) { content += `<span>${i}</span>`; }
                column.innerHTML = content;
                wrapper.appendChild(column);
                element.appendChild(wrapper);
                requestAnimationFrame(() => { column.style.transform = `translateY(-${digit * 1.2}em)`; });
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
            const ease = 1 - Math.pow(1 - progress, 4);
            const currentVal = rawTarget * ease;
            element.textContent = prefix + formatNumberOnly(currentVal, format, decimals) + suffix;
            if (progress < 1) { requestAnimationFrame(step); } 
            else { element.textContent = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix; }
        };
        requestAnimationFrame(step);
    };
    const animateCssEffect = (element, rawTarget, format, decimals, prefix, suffix, effectClass) => {
        element.textContent = prefix + formatNumberOnly(rawTarget, format, decimals) + suffix;
        element.classList.remove(effectClass);
        void element.offsetWidth;
        element.classList.add(effectClass);
    };
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
        element.classList.remove('acm-effect-blur', 'acm-effect-bounce');
        element.style.display = '';
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
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startAnimation(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.acm-value').forEach(el => observer.observe(el));


    // --- GESTIÓN DE METABOX EN ADMIN ---
    const previewContainer = document.getElementById('acm-admin-preview');
    if (previewContainer) {
        
        // 1. MANEJO DE UPLOAD DE IMAGEN (WordPress Media)
        const uploadBtn = document.getElementById('acm_upload_image_btn');
        const removeBtn = document.getElementById('acm_remove_image_btn');
        const hiddenInput = document.getElementById('acm_image_id');
        const previewImg = document.getElementById('acm_image_preview_tag');
        const wrapper = document.getElementById('acm_image_wrapper');
        let mediaFrame;

        if (uploadBtn) {
            uploadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (mediaFrame) { mediaFrame.open(); return; }
                
                mediaFrame = wp.media({
                    title: 'Seleccionar Icono o Imagen',
                    button: { text: 'Usar esta imagen' },
                    multiple: false
                });

                mediaFrame.on('select', () => {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    hiddenInput.value = attachment.id;
                    previewImg.src = attachment.url;
                    wrapper.style.display = 'block';
                    removeBtn.style.display = 'inline-block';
                    uploadBtn.textContent = 'Cambiar Imagen';
                    
                    updatePreview(); // Actualizar preview general
                });

                mediaFrame.open();
            });

            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                hiddenInput.value = '';
                previewImg.src = '';
                wrapper.style.display = 'none';
                removeBtn.style.display = 'none';
                uploadBtn.textContent = 'Subir Imagen';
                
                updatePreview();
            });
        }

        // 2. MANEJO DE RESET DE COLORES
        const resetBtn = document.getElementById('acm_reset_colors');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                // Valores por defecto
                const defAccent = '#0073aa';
                const defBg     = '#ffffff';
                const defBorder = '#e5e5e5';

                // Asignar
                document.getElementById('acm_color').value = defAccent;
                document.getElementById('acm_bg_color').value = defBg;
                document.getElementById('acm_border_color').value = defBorder;

                // Forzar evento input para actualizar la preview
                updatePreview();
            });
        }

        // 3. ACTUALIZAR VISTA PREVIA
        const updatePreview = () => {
            const val         = document.getElementById('acm_value').value;
            const label       = document.getElementById('acm_label').value;
            const format      = document.getElementById('acm_format').value;
            const decimals    = document.getElementById('acm_decimals').value || 0;
            const prefix      = document.getElementById('acm_prefix').value;
            const suffix      = document.getElementById('acm_suffix').value;
            const duration    = document.getElementById('acm_duration').value || 2.5;
            const anim        = document.getElementById('acm_anim').value;
            
            const accentColor = document.getElementById('acm_color').value;
            const bgColor     = document.getElementById('acm_bg_color').value;
            const borderColor = document.getElementById('acm_border_color').value;

            // Obtener imagen actual de la caja de configuración (si existe)
            const imgSrc = previewImg && wrapper.style.display !== 'none' ? previewImg.src : '';
            const imgHtml = imgSrc ? `<img class="acm-icon" src="${imgSrc}" style="max-width:80px; margin-bottom:10px;">` : '';

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

            const valueStyle = accentColor ? `style="color: ${accentColor}; border-color: ${accentColor};"` : '';
            let cardStyle = 'margin:0;';
            if(bgColor) cardStyle += `background-color: ${bgColor};`;
            if(borderColor) cardStyle += `border-color: ${borderColor};`;

            // HTML simulado
            let html = `
                <div class="acm-widget-card" style="${cardStyle}">
                    ${imgHtml}
                    <div class="acm-value" ${valueStyle} ${dataAttrs}>
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

        const inputs = [
            'acm_value', 'acm_label', 'acm_format', 'acm_decimals', 
            'acm_prefix', 'acm_suffix', 'acm_duration', 'acm_anim',
            'acm_color', 'acm_bg_color', 'acm_border_color'
        ];
        
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