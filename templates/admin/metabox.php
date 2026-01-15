<?php
// Variables disponibles: $metric_value, ... $metric_layout
wp_nonce_field('acm_save_metabox_data', 'acm_metabox_nonce');
?>
<div class="acm-metabox-wrapper" style="display: grid; gap: 15px;">

    <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <p style="margin-top:0;"><strong>Imagen / Icono:</strong></p>
        <div id="acm_image_wrapper" style="margin-bottom: 10px; text-align: center; min-height: 50px; display: <?php echo $image_url ? 'block' : 'none'; ?>;">
            <img id="acm_image_preview_tag" src="<?php echo esc_url($image_url); ?>" style="max-width: 100px; max-height: 100px; display: block; margin: 0 auto;">
        </div>
        <input type="hidden" id="acm_image_id" name="acm_image_id" value="<?php echo esc_attr($metric_image_id); ?>">
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="button" id="acm_upload_image_btn"><?php echo $image_url ? 'Cambiar Imagen' : 'Subir Imagen'; ?></button>
            <button type="button" class="button button-link-delete" id="acm_remove_image_btn" style="<?php echo $image_url ? '' : 'display:none;'; ?>">Quitar</button>
        </div>
    </div>

    <p>
        <label for="acm_value"><strong>Valor de la Métrica:</strong></label><br>
        <input type="text" id="acm_value" name="acm_value" value="<?php echo esc_attr($metric_value); ?>" style="width: 100%;" placeholder="Ej: 1500">
    </p>

    <p>
        <label for="acm_layout"><strong>Disposición / Alineación:</strong></label><br>
        <select id="acm_layout" name="acm_layout" style="width: 100%;">
            <option value="top" <?php selected($metric_layout, 'top'); ?>>Icono Arriba (Centro)</option>
            <option value="left" <?php selected($metric_layout, 'left'); ?>>Icono Izquierda (Horizontal)</option>
            <option value="right" <?php selected($metric_layout, 'right'); ?>>Icono Derecha (Horizontal)</option>
            <option value="bottom" <?php selected($metric_layout, 'bottom'); ?>>Icono Abajo (Centro)</option>
        </select>
    </p>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <p style="margin: 0;">
            <label for="acm_prefix"><strong>Prefijo:</strong></label><br>
            <input type="text" id="acm_prefix" name="acm_prefix" value="<?php echo esc_attr($metric_prefix); ?>" style="width: 100%;" placeholder="Ej: +">
        </p>
        <p style="margin: 0;">
            <label for="acm_suffix"><strong>Sufijo:</strong></label><br>
            <input type="text" id="acm_suffix" name="acm_suffix" value="<?php echo esc_attr($metric_suffix); ?>" style="width: 100%;" placeholder="Ej: ud.">
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
        <p style="margin: 0;">
            <label for="acm_format"><strong>Tipo de Formato:</strong></label><br>
            <select id="acm_format" name="acm_format" style="width: 100%;">
                <option value="raw" <?php selected($metric_format, 'raw'); ?>>Texto General</option>
                <option value="number" <?php selected($metric_format, 'number'); ?>>Número</option>
                <option value="money" <?php selected($metric_format, 'money'); ?>>Moneda ($)</option>
                <option value="percent" <?php selected($metric_format, 'percent'); ?>>Porcentaje (%)</option>
                <option value="compact" <?php selected($metric_format, 'compact'); ?>>Compacto (1k, 1M)</option>
                <option value="money_compact" <?php selected($metric_format, 'money_compact'); ?>>Moneda Compacta ($ 1M)</option>
                <option value="weight" <?php selected($metric_format, 'weight'); ?>>Peso (g/kg/t)</option>
                <option value="date" <?php selected($metric_format, 'date'); ?>>Fecha</option>
            </select>
        </p>
        <p style="margin: 0;">
            <label for="acm_decimals"><strong>Decimales:</strong></label><br>
            <input type="number" id="acm_decimals" name="acm_decimals" value="<?php echo esc_attr($metric_decimals); ?>" min="0" max="10" style="width: 100%;">
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
        <p style="margin: 0;">
            <label for="acm_anim"><strong>Tipo de Animación:</strong></label><br>
            <select id="acm_anim" name="acm_anim" style="width: 100%;">
                <option value="count" <?php selected($metric_anim, 'count'); ?>>Conteo (Normal)</option>
                <option value="slot" <?php selected($metric_anim, 'slot'); ?>>Tragamonedas (Slot Machine)</option>
                <option value="blur" <?php selected($metric_anim, 'blur'); ?>>Revelado Desenfoque</option>
                <option value="bounce" <?php selected($metric_anim, 'bounce'); ?>>Rebote / Zoom</option>
            </select>
        </p>
        <p style="margin: 0;">
            <label for="acm_duration"><strong>Duración (s):</strong></label><br>
            <input type="number" id="acm_duration" name="acm_duration" value="<?php echo esc_attr($metric_duration); ?>" step="0.1" min="0" style="width: 100%;">
        </p>
    </div>

    <p>
        <label for="acm_label"><strong>Etiqueta / Descripción:</strong></label><br>
        <input type="text" id="acm_label" name="acm_label" value="<?php echo esc_attr($metric_label); ?>" style="width: 100%;">
    </p>

    <p>
        <label for="acm_url"><strong>URL de Destino (Opcional):</strong></label><br>
        <input type="url" id="acm_url" name="acm_url" value="<?php echo esc_attr($metric_url); ?>" style="width: 100%;" placeholder="https://...">
    </p>

    <hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <strong>Apariencia:</strong>
        <button type="button" id="acm_reset_colors" class="button button-small">Restablecer Colores</button>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
        <p style="margin: 0;">
            <label for="acm_color">Acento:</label><br>
            <input type="color" id="acm_color" name="acm_color" value="<?php echo esc_attr($metric_color ? $metric_color : '#0073aa'); ?>" style="width: 100%; height: 40px;">
        </p>
        <p style="margin: 0;">
            <label for="acm_bg_color">Fondo:</label><br>
            <input type="color" id="acm_bg_color" name="acm_bg_color" value="<?php echo esc_attr($metric_bg_color ? $metric_bg_color : '#ffffff'); ?>" style="width: 100%; height: 40px;">
        </p>
        <p style="margin: 0;">
            <label for="acm_border_color">Borde:</label><br>
            <input type="color" id="acm_border_color" name="acm_border_color" value="<?php echo esc_attr($metric_border_color ? $metric_border_color : '#e5e5e5'); ?>" style="width: 100%; height: 40px;">
        </p>
    </div>

    <div style="background: #f0f0f1; padding: 10px; border-left: 4px solid #0073aa; margin-top: 20px;">
        <strong>Shortcode:</strong> <code>[acm_widget id="<?php echo $post->ID; ?>"]</code>
    </div>
</div>