<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACM_Metabox {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_meta_box' ] );
    }

    public function add_meta_box() {
        add_meta_box( 'acm_widget_config', 'Configuración de la Métrica', [ $this, 'render_config_metabox' ], 'acm_widget', 'normal', 'high' );
        add_meta_box( 'acm_widget_preview', 'Vista Previa en Vivo', [ $this, 'render_preview_metabox' ], 'acm_widget', 'side', 'high' );
    }

    public function render_config_metabox( $post ) {
        // Recuperar valores
        $metric_value        = get_post_meta( $post->ID, '_acm_value', true );
        $metric_label        = get_post_meta( $post->ID, '_acm_label', true );
        $metric_format       = get_post_meta( $post->ID, '_acm_format', true );
        $metric_decimals     = get_post_meta( $post->ID, '_acm_decimals', true );
        $metric_prefix       = get_post_meta( $post->ID, '_acm_prefix', true );
        $metric_suffix       = get_post_meta( $post->ID, '_acm_suffix', true );
        $metric_duration     = get_post_meta( $post->ID, '_acm_duration', true );
        $metric_anim         = get_post_meta( $post->ID, '_acm_anim', true );
        
        // Colores
        $metric_color        = get_post_meta( $post->ID, '_acm_color', true ); // Acento
        $metric_bg_color     = get_post_meta( $post->ID, '_acm_bg_color', true ); // Fondo
        $metric_border_color = get_post_meta( $post->ID, '_acm_border_color', true ); // Borde

        // Defaults
        if ( empty( $metric_format ) ) { $metric_format = 'raw'; }
        if ( $metric_decimals === '' ) { $metric_decimals = '0'; }
        if ( empty( $metric_duration ) ) { $metric_duration = '2.5'; }
        if ( empty( $metric_anim ) ) { $metric_anim = 'count'; }

        wp_nonce_field( 'acm_save_metabox_data', 'acm_metabox_nonce' );
        ?>
        <div class="acm-metabox-wrapper" style="display: grid; gap: 15px;">
            
            <p>
                <label for="acm_value"><strong>Valor de la Métrica:</strong></label><br>
                <input type="text" id="acm_value" name="acm_value" value="<?php echo esc_attr( $metric_value ); ?>" style="width: 100%;" placeholder="Ej: 1500">
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <p style="margin: 0;">
                    <label for="acm_prefix"><strong>Prefijo:</strong></label><br>
                    <input type="text" id="acm_prefix" name="acm_prefix" value="<?php echo esc_attr( $metric_prefix ); ?>" style="width: 100%;" placeholder="Ej: +">
                </p>
                <p style="margin: 0;">
                    <label for="acm_suffix"><strong>Sufijo:</strong></label><br>
                    <input type="text" id="acm_suffix" name="acm_suffix" value="<?php echo esc_attr( $metric_suffix ); ?>" style="width: 100%;" placeholder="Ej: ud.">
                </p>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <p style="margin: 0;">
                    <label for="acm_format"><strong>Tipo de Formato:</strong></label><br>
                    <select id="acm_format" name="acm_format" style="width: 100%;">
                        <option value="raw" <?php selected( $metric_format, 'raw' ); ?>>Texto General</option>
                        <option value="number" <?php selected( $metric_format, 'number' ); ?>>Número</option>
                        <option value="money" <?php selected( $metric_format, 'money' ); ?>>Moneda ($)</option>
                        <option value="percent" <?php selected( $metric_format, 'percent' ); ?>>Porcentaje (%)</option>
                        <option value="compact" <?php selected( $metric_format, 'compact' ); ?>>Compacto (1k, 1M)</option>
                        <option value="money_compact" <?php selected( $metric_format, 'money_compact' ); ?>>Moneda Compacta ($ 1M)</option>
                        <option value="weight" <?php selected( $metric_format, 'weight' ); ?>>Peso (g/kg/t)</option>
                        <option value="date" <?php selected( $metric_format, 'date' ); ?>>Fecha</option>
                    </select>
                </p>
                <p style="margin: 0;">
                    <label for="acm_decimals"><strong>Decimales:</strong></label><br>
                    <input type="number" id="acm_decimals" name="acm_decimals" value="<?php echo esc_attr( $metric_decimals ); ?>" min="0" max="10" style="width: 100%;">
                </p>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <p style="margin: 0;">
                    <label for="acm_anim"><strong>Tipo de Animación:</strong></label><br>
                    <select id="acm_anim" name="acm_anim" style="width: 100%;">
                        <option value="count" <?php selected( $metric_anim, 'count' ); ?>>Conteo (Normal)</option>
                        <option value="slot" <?php selected( $metric_anim, 'slot' ); ?>>Tragamonedas (Slot Machine)</option>
                        <option value="blur" <?php selected( $metric_anim, 'blur' ); ?>>Revelado Desenfoque</option>
                        <option value="bounce" <?php selected( $metric_anim, 'bounce' ); ?>>Rebote / Zoom</option>
                    </select>
                </p>
                <p style="margin: 0;">
                    <label for="acm_duration"><strong>Duración (s):</strong></label><br>
                    <input type="number" id="acm_duration" name="acm_duration" value="<?php echo esc_attr( $metric_duration ); ?>" step="0.1" min="0" style="width: 100%;">
                </p>
            </div>

            <p>
                <label for="acm_label"><strong>Etiqueta / Descripción:</strong></label><br>
                <input type="text" id="acm_label" name="acm_label" value="<?php echo esc_attr( $metric_label ); ?>" style="width: 100%;">
            </p>

            <hr style="border: 0; border-top: 1px solid #ddd; margin: 20px 0;">

            <p><strong>Apariencia de la Tarjeta:</strong></p>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <p style="margin: 0;">
                    <label for="acm_color">Acento (Texto/Valor):</label><br>
                    <input type="color" id="acm_color" name="acm_color" value="<?php echo esc_attr( $metric_color ? $metric_color : '#0073aa' ); ?>" style="width: 100%; height: 40px;">
                </p>
                <p style="margin: 0;">
                    <label for="acm_bg_color">Fondo (Card):</label><br>
                    <input type="color" id="acm_bg_color" name="acm_bg_color" value="<?php echo esc_attr( $metric_bg_color ? $metric_bg_color : '#ffffff' ); ?>" style="width: 100%; height: 40px;">
                </p>
                <p style="margin: 0;">
                    <label for="acm_border_color">Borde (Card):</label><br>
                    <input type="color" id="acm_border_color" name="acm_border_color" value="<?php echo esc_attr( $metric_border_color ? $metric_border_color : '#e5e5e5' ); ?>" style="width: 100%; height: 40px;">
                </p>
            </div>

            <div style="background: #f0f0f1; padding: 10px; border-left: 4px solid #0073aa; margin-top: 20px;">
                <strong>Shortcode:</strong> <code>[acm_widget id="<?php echo $post->ID; ?>"]</code>
            </div>
        </div>
        <?php
    }

    public function render_preview_metabox( $post ) {
        ?>
        <div style="display: flex; align-items: center; justify-content: center; min-height: 150px; background: #fafafa;">
            <div id="acm-admin-preview" style="width: 100%;">
                <p style="text-align:center; color:#999;">Cargando vista previa...</p>
            </div>
        </div>
        <p class="description" style="text-align: center;">La animación se reinicia al cambiar valores.</p>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['acm_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['acm_metabox_nonce'], 'acm_save_metabox_data' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $fields = [
            '_acm_value'        => 'sanitize_text_field',
            '_acm_label'        => 'sanitize_text_field',
            '_acm_color'        => 'sanitize_hex_color',
            '_acm_format'       => 'sanitize_key',
            '_acm_decimals'     => 'intval',
            '_acm_prefix'       => 'sanitize_text_field',
            '_acm_suffix'       => 'sanitize_text_field',
            '_acm_duration'     => 'sanitize_text_field',
            '_acm_anim'         => 'sanitize_key',
            '_acm_bg_color'     => 'sanitize_hex_color', // Nuevo
            '_acm_border_color' => 'sanitize_hex_color', // Nuevo
        ];

        foreach ( $fields as $key => $sanitizer ) {
            $input_name = substr( $key, 1 ); // _acm_value -> acm_value
            if ( isset( $_POST[ $input_name ] ) ) {
                update_post_meta( $post_id, $key, call_user_func( $sanitizer, $_POST[ $input_name ] ) );
            }
        }
    }
}