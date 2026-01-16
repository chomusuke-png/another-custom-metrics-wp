<?php
// Variables disponibles: ... $metric_icon_color, $metric_value, etc.
wp_nonce_field('acm_save_metabox_data', 'acm_metabox_nonce');
?>

<div class="acm-metabox-wrapper">

    <div class="acm-section">
        <h3 class="acm-section-title">Contenido Principal</h3>
        
        <div class="acm-grid acm-grid-main">
            <div class="acm-field">
                <label for="acm_value">Valor de la Métrica</label>
                <input type="text" id="acm_value" name="acm_value" value="<?php echo esc_attr($metric_value); ?>" placeholder="Ej: 1500">
            </div>
            <div class="acm-field">
                <label for="acm_value_size">Tamaño (rem)</label>
                <input type="number" id="acm_value_size" name="acm_value_size" value="<?php echo esc_attr($metric_value_size); ?>" step="0.1" min="0.5">
            </div>
        </div>

        <div class="acm-grid acm-grid-main">
            <div class="acm-field">
                <label for="acm_label">Etiqueta / Descripción</label>
                <input type="text" id="acm_label" name="acm_label" value="<?php echo esc_attr($metric_label); ?>" placeholder="Ej: Usuarios Activos">
            </div>
            <div class="acm-field">
                <label for="acm_label_size">Tamaño (rem)</label>
                <input type="number" id="acm_label_size" name="acm_label_size" value="<?php echo esc_attr($metric_label_size); ?>" step="0.1" min="0.5">
            </div>
        </div>

        <div class="acm-grid acm-grid-3">
            <div class="acm-field">
                <label for="acm_prefix">Prefijo</label>
                <input type="text" id="acm_prefix" name="acm_prefix" value="<?php echo esc_attr($metric_prefix); ?>" placeholder="Ej: +">
            </div>
            <div class="acm-field">
                <label for="acm_suffix">Sufijo</label>
                <input type="text" id="acm_suffix" name="acm_suffix" value="<?php echo esc_attr($metric_suffix); ?>" placeholder="Ej: ud.">
            </div>
            <div class="acm-field">
                <label for="acm_url">URL de Destino</label>
                <input type="url" id="acm_url" name="acm_url" value="<?php echo esc_attr($metric_url); ?>" placeholder="https://...">
            </div>
        </div>
    </div>

    <div class="acm-section">
        <h3 class="acm-section-title">Iconografía & Diseño</h3>
        
        <div class="acm-image-preview-box" id="acm_image_wrapper" style="<?php echo $image_url ? 'display:flex' : 'display:none'; ?>;">
            <?php 
                $preview_width = $metric_img_width ? intval($metric_img_width) : 80; 
                // Style inline solo para el ancho dinámico de la preview
            ?>
            <img id="acm_image_preview_tag" src="<?php echo esc_url($image_url); ?>" style="width: <?php echo $preview_width; ?>px;">
        </div>
        <input type="hidden" id="acm_image_id" name="acm_image_id" value="<?php echo esc_attr($metric_image_id); ?>">

        <div class="acm-image-actions">
            <button type="button" class="button" id="acm_upload_image_btn">
                <?php echo $image_url ? 'Cambiar Imagen' : 'Seleccionar Imagen'; ?>
            </button>
            <button type="button" class="button button-link-delete" id="acm_remove_image_btn" style="<?php echo $image_url ? '' : 'display:none;'; ?>">
                Quitar
            </button>
        </div>

        <div class="acm-grid acm-grid-3">
            <div class="acm-field">
                <label for="acm_layout">Disposición</label>
                <select id="acm_layout" name="acm_layout">
                    <option value="top" <?php selected($metric_layout, 'top'); ?>>Icono Arriba</option>
                    <option value="left" <?php selected($metric_layout, 'left'); ?>>Icono Izquierda</option>
                    <option value="right" <?php selected($metric_layout, 'right'); ?>>Icono Derecha</option>
                    <option value="bottom" <?php selected($metric_layout, 'bottom'); ?>>Icono Abajo</option>
                </select>
            </div>
            <div class="acm-field">
                <label for="acm_img_width">Ancho Icono (px)</label>
                <input type="number" id="acm_img_width" name="acm_img_width" value="<?php echo esc_attr($metric_img_width); ?>" min="10" max="500">
            </div>
            <div class="acm-field">
                <label for="acm_icon_color">Colorear Icono</label>
                <input type="color" id="acm_icon_color" name="acm_icon_color" value="<?php echo esc_attr($metric_icon_color ? $metric_icon_color : '#000000'); ?>">
                <span class="acm-help">Negro (#000000) = Original</span>
            </div>
        </div>
    </div>

    <div class="acm-section">
        <h3 class="acm-section-title">Formato Numérico & Animación</h3>
        
        <div class="acm-grid acm-grid-2">
            <div class="acm-field">
                <label for="acm_format">Formato de Datos</label>
                <select id="acm_format" name="acm_format">
                    <option value="raw" <?php selected($metric_format, 'raw'); ?>>Texto General</option>
                    <option value="number" <?php selected($metric_format, 'number'); ?>>Número Simple</option>
                    <option value="money" <?php selected($metric_format, 'money'); ?>>Moneda ($)</option>
                    <option value="percent" <?php selected($metric_format, 'percent'); ?>>Porcentaje (%)</option>
                    <option value="compact" <?php selected($metric_format, 'compact'); ?>>Compacto (1k, 1M)</option>
                    <option value="money_compact" <?php selected($metric_format, 'money_compact'); ?>>Moneda Compacta</option>
                    <option value="weight" <?php selected($metric_format, 'weight'); ?>>Peso (g/kg/t)</option>
                    <option value="date" <?php selected($metric_format, 'date'); ?>>Fecha</option>
                </select>
            </div>
            <div class="acm-field">
                <label for="acm_decimals">Decimales</label>
                <input type="number" id="acm_decimals" name="acm_decimals" value="<?php echo esc_attr($metric_decimals); ?>" min="0" max="10">
            </div>
        </div>

        <div class="acm-grid acm-grid-2">
            <div class="acm-field">
                <label for="acm_anim">Tipo de Animación</label>
                <select id="acm_anim" name="acm_anim">
                    <option value="count" <?php selected($metric_anim, 'count'); ?>>Conteo (Numérico)</option>
                    <option value="slot" <?php selected($metric_anim, 'slot'); ?>>Tragamonedas (Slot)</option>
                    <option value="blur" <?php selected($metric_anim, 'blur'); ?>>Revelado Desenfoque</option>
                    <option value="bounce" <?php selected($metric_anim, 'bounce'); ?>>Rebote / Zoom</option>
                </select>
            </div>
            <div class="acm-field">
                <label for="acm_duration">Duración (segundos)</label>
                <input type="number" id="acm_duration" name="acm_duration" value="<?php echo esc_attr($metric_duration); ?>" step="0.1" min="0">
            </div>
        </div>
    </div>

    <div class="acm-section">
        <div class="acm-section-header">
            <h3 class="acm-section-title" style="margin-bottom:0; border:none;">Apariencia de la Tarjeta</h3>
            <button type="button" id="acm_reset_colors" class="button button-small">Restablecer Colores</button>
        </div>

        <div class="acm-grid acm-grid-3">
            <div class="acm-field">
                <label for="acm_color">Color Acento (Texto)</label>
                <input type="color" id="acm_color" name="acm_color" value="<?php echo esc_attr($metric_color ? $metric_color : '#0073aa'); ?>">
            </div>
            <div class="acm-field">
                <label for="acm_bg_color">Color Fondo</label>
                <input type="color" id="acm_bg_color" name="acm_bg_color" value="<?php echo esc_attr($metric_bg_color ? $metric_bg_color : '#ffffff'); ?>">
            </div>
            <div class="acm-field">
                <label for="acm_border_color">Color Borde</label>
                <input type="color" id="acm_border_color" name="acm_border_color" value="<?php echo esc_attr($metric_border_color ? $metric_border_color : '#e5e5e5'); ?>">
            </div>
        </div>
    </div>

    <div class="acm-shortcode-box">
        <strong>Shortcode:</strong> <code>[acm_widget id="<?php echo $post->ID; ?>"]</code>
    </div>

</div>