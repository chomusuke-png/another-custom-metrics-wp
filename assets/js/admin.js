/**
 * ACM Admin
 * Lógica para el panel de administración de WordPress.
 * Depende de: acm-core.js, wp.media
 */
document.addEventListener('DOMContentLoaded', () => {

    const previewContainer = document.getElementById('acm-admin-preview');
    
    // Si no existe el contenedor de preview, no estamos en la página de edición correcta
    if (!previewContainer) return;

    // --- 1. GESTIÓN DE IMAGEN (MEDIA UPLOADER) ---
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
                updatePreview();
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

    // --- 2. RESET DE COLORES ---
    const resetBtn = document.getElementById('acm_reset_colors');
    if (resetBtn) {
        resetBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('acm_color').value = '#0073aa';
            document.getElementById('acm_bg_color').value = '#ffffff';
            document.getElementById('acm_border_color').value = '#e5e5e5';
            updatePreview();
        });
    }

    // --- 3. VISTA PREVIA EN TIEMPO REAL ---
    const updatePreview = () => {
        // Recoger datos
        const val         = document.getElementById('acm_value').value;
        const label       = document.getElementById('acm_label').value;
        const format      = document.getElementById('acm_format').value;
        const decimals    = document.getElementById('acm_decimals').value || 0;
        const prefix      = document.getElementById('acm_prefix').value;
        const suffix      = document.getElementById('acm_suffix').value;
        const duration    = document.getElementById('acm_duration').value || 2.5;
        const anim        = document.getElementById('acm_anim').value;
        
        // Colores
        const accentColor = document.getElementById('acm_color').value;
        const bgColor     = document.getElementById('acm_bg_color').value;
        const borderColor = document.getElementById('acm_border_color').value;

        // Imagen
        const imgSrc = (previewImg && wrapper.style.display !== 'none') ? previewImg.src : '';
        const imgHtml = imgSrc ? `<img class="acm-icon" src="${imgSrc}" style="max-width:80px; margin-bottom:10px;">` : '';

        // Construir atributos data
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

        // Estilos
        const valueStyle = accentColor ? `style="color: ${accentColor}; border-color: ${accentColor};"` : '';
        let cardStyle = 'margin:0;';
        if(bgColor) cardStyle += `background-color: ${bgColor};`;
        if(borderColor) cardStyle += `border-color: ${borderColor};`;

        // Render HTML
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

        // Ejecutar animación usando el Core
        if (isNumeric && window.ACM) {
            window.ACM.startAnimation(previewContainer.querySelector('.acm-value'));
        }
    };

    // Listeners para actualización en vivo
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

    // Inicializar
    updatePreview();
});