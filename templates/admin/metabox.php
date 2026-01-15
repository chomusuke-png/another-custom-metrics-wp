<?php 
// Variables disponibles: $metric_value, $metric_label, etc... (Pasadas desde el Controller)
wp_nonce_field( 'acm_save_metabox_data', 'acm_metabox_nonce' );
?>
<div class="acm-metabox-wrapper" style="display: grid; gap: 15px;">
    
    <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
        <p style="margin-top:0;"><strong>Imagen / Icono Superior:</strong></p>
        
        <div id="acm_image_wrapper" style="margin-bottom: 10px; text-align: center; min-height: 50px; display: <?php echo $image_url ? 'block' : 'none'; ?>;">
            <img id="acm_image_preview_tag" src="<?php echo esc_url( $image_url ); ?>" style="max-width: 100px; max-height: 100px; display: block; margin: 0 auto;">
        </div>

        <input type="hidden" id="acm_image_id" name="acm_image_id" value="<?php echo esc_attr( $metric_image_id ); ?>">
        
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="button" id="acm_upload_image_btn"><?php echo $image_url ? 'Cambiar Imagen' : 'Subir Imagen'; ?></button>
            <button type="button" class="button button-link-delete" id="acm_remove_image_btn" style="<?php echo $image_url ? '' : 'display:none;'; ?>">Quitar</button>
        </div>
    </div>

    <p>
        <label for="acm_value"><strong>Valor de la Métrica:</strong></label><br>
        <input type="text" id="acm_value" name="acm_value" value="<?php echo esc_attr( $metric_value ); ?>" style="width: 100%;" placeholder="Ej: 1500">
    </p>

    <div style="background: #f0f0f1; padding: 10px; border-left: 4px solid #0073aa; margin-top: 20px;">
        <strong>Shortcode:</strong> <code>[acm_widget id="<?php echo $post->ID; ?>"]</code>
    </div>
</div>