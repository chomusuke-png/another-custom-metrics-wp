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

    // --- DEBOUNCE FUNCTION ---
    // Para no saturar el servidor con cada tecla pulsada
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
        // 1. Recoger datos del formulario
        // Un truco rápido es usar FormData con un form ficticio o seleccionar inputs
        // Dado que los inputs están dispersos, vamos a seleccionarlos manualmente o envolver el metabox en un form (pero ya estamos dentro de un form #post).
        // Lo mejor es construir un FormData manual con los IDs que ya conoces.
        
        const formData = new FormData();
        formData.append('action', 'acm_render_preview');
        
        const inputs = [
            'acm_value', 'acm_label', 'acm_format', 'acm_decimals', 
            'acm_prefix', 'acm_suffix', 'acm_duration', 'acm_anim',
            'acm_color', 'acm_bg_color', 'acm_border_color', 'acm_image_id'
        ];

        inputs.forEach(id => {
            const el = document.getElementById(id);
            if(el) formData.append(id, el.value);
        });

        // Indicador de carga
        previewContainer.style.opacity = '0.5';

        // 2. Fetch
        fetch(ajaxurl, { // 'ajaxurl' es global en admin de WP
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                previewContainer.innerHTML = data.data;
                // Reiniciar animación
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

    const debouncedUpdate = debounce(updatePreview, 500); // Espera 500ms tras dejar de escribir

    // Listeners
    const inputsIds = [
        'acm_value', 'acm_label', 'acm_format', 'acm_decimals', 
        'acm_prefix', 'acm_suffix', 'acm_duration', 'acm_anim',
        'acm_color', 'acm_bg_color', 'acm_border_color'
    ];
    
    inputsIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', debouncedUpdate);
            el.addEventListener('change', updatePreview); // Change directo para selects/color
        }
    });

    // Para la imagen, trigger manual cuando se selecciona
    // (Añade updatePreview() dentro del callback del mediaFrame.on('select') que tienes arriba)

    // Inicializar
    updatePreview();
});