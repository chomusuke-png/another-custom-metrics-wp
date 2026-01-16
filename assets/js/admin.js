document.addEventListener('DOMContentLoaded', () => {

    const previewContainer = document.getElementById('acm-admin-preview');
    if (!previewContainer) return;

    // --- 1. GESTIÓN DE IMAGEN ---
    const uploadBtn = document.getElementById('acm_upload_image_btn');
    const removeBtn = document.getElementById('acm_remove_image_btn');
    const hiddenInput = document.getElementById('acm_image_id');
    const previewImg = document.getElementById('acm_image_preview_tag');
    const wrapper = document.getElementById('acm_image_wrapper');
    const widthInput = document.getElementById('acm_img_width');

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

    if (widthInput && previewImg) {
        widthInput.addEventListener('input', (e) => {
            const w = e.target.value;
            if (w) { previewImg.style.width = w + 'px'; }
        });
    }

    // --- 2. RESET DE COLORES ---
    const resetBtn = document.getElementById('acm_reset_colors');
    if (resetBtn) {
        resetBtn.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('acm_color').value = '#0073aa';
            document.getElementById('acm_label_color').value = '#666666';
            document.getElementById('acm_bg_color').value = '#ffffff';
            document.getElementById('acm_border_color').value = '#e5e5e5';
            document.getElementById('acm_icon_color').value = '#000000';
            updatePreview();
        });
    }

    // --- DEBOUNCE ---
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // --- VISTA PREVIA VIA AJAX ---
    const updatePreview = () => {
        const formData = new FormData();
        formData.append('action', 'acm_render_preview');
        
        // !!! AÑADIDO: 'acm_label_color' !!!
        const inputs = [
            'acm_value', 'acm_label', 'acm_url', 'acm_layout', 
            'acm_format', 'acm_decimals', 'acm_prefix', 'acm_suffix', 
            'acm_duration', 'acm_anim', 'acm_color', 'acm_label_color',
            'acm_bg_color', 'acm_border_color', 'acm_image_id', 'acm_img_width',
            'acm_value_size', 'acm_label_size', 'acm_icon_color'
        ];

        inputs.forEach(id => {
            const el = document.getElementById(id);
            if(el) formData.append(id, el.value);
        });

        previewContainer.style.opacity = '0.5';

        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                previewContainer.innerHTML = data.data;
                const newVal = previewContainer.querySelector('.acm-value');
                if(newVal && window.ACM) {
                    window.ACM.startAnimation(newVal);
                }
            }
        })
        .finally(() => {
            previewContainer.style.opacity = '1';
        });
    };

    const debouncedUpdate = debounce(updatePreview, 500);

    // Listeners
    // !!! AÑADIDO: 'acm_label_color' !!!
    const inputsIds = [
        'acm_value', 'acm_label', 'acm_url', 'acm_layout',
        'acm_format', 'acm_decimals', 'acm_prefix', 'acm_suffix', 
        'acm_duration', 'acm_anim', 'acm_color', 'acm_label_color',
        'acm_bg_color', 'acm_border_color', 'acm_img_width', 'acm_value_size', 
        'acm_label_size', 'acm_icon_color'
    ];
    
    inputsIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', debouncedUpdate);
            el.addEventListener('change', updatePreview);
        }
    });

    updatePreview();
});