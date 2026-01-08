<?php
//

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACM_Metabox {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_meta_box' ] );
    }

    public function add_meta_box() {
        add_meta_box(
            'acm_widget_config',
            'Configuración de la Métrica',
            [ $this, 'render_meta_box' ],
            'acm_widget',
            'normal',
            'high'
        );
    }

    public function render_meta_box( $post ) {
        $metric_value    = get_post_meta( $post->ID, '_acm_value', true );
        $metric_label    = get_post_meta( $post->ID, '_acm_label', true );
        $metric_color    = get_post_meta( $post->ID, '_acm_color', true );
        $metric_format   = get_post_meta( $post->ID, '_acm_format', true );
        $metric_decimals = get_post_meta( $post->ID, '_acm_decimals', true );
        $metric_prefix   = get_post_meta( $post->ID, '_acm_prefix', true );
        $metric_suffix   = get_post_meta( $post->ID, '_acm_suffix', true );

        // Defaults
        if ( empty( $metric_format ) ) { $metric_format = 'raw'; }
        if ( $metric_decimals === '' ) { $metric_decimals = '0'; }

        wp_nonce_field( 'acm_save_metabox_data', 'acm_metabox_nonce' );
        ?>
        <div class="acm-metabox-wrapper" style="display: grid; gap: 15px;">
            
            <p>
                <label for="acm_value"><strong>Valor de la Métrica:</strong></label><br>
                <input type="text" id="acm_value" name="acm_value" value="<?php echo esc_attr( $metric_value ); ?>" style="width: 100%;" placeholder="Ej: 1500">
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <p style="margin: 0;">
                    <label for="acm_prefix"><strong>Prefijo (Antes):</strong></label><br>
                    <input type="text" id="acm_prefix" name="acm_prefix" value="<?php echo esc_attr( $metric_prefix ); ?>" style="width: 100%;" placeholder="Ej: +, >, USD ">
                </p>
                <p style="margin: 0;">
                    <label for="acm_suffix"><strong>Sufijo (Después):</strong></label><br>
                    <input type="text" id="acm_suffix" name="acm_suffix" value="<?php echo esc_attr( $metric_suffix ); ?>" style="width: 100%;" placeholder="Ej:  clientes,  pax">
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

            <p>
                <label for="acm_label"><strong>Etiqueta / Descripción:</strong></label><br>
                <input type="text" id="acm_label" name="acm_label" value="<?php echo esc_attr( $metric_label ); ?>" style="width: 100%;" placeholder="Ej: Clientes Satisfechos">
            </p>

            <p>
                <label for="acm_color"><strong>Color de Acento:</strong></label><br>
                <input type="color" id="acm_color" name="acm_color" value="<?php echo esc_attr( $metric_color ? $metric_color : '#0073aa' ); ?>">
            </p>

            <div style="background: #f0f0f1; padding: 10px; border-left: 4px solid #0073aa;">
                <strong>Shortcode:</strong> <code>[acm_widget id="<?php echo $post->ID; ?>"]</code>
            </div>
        </div>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['acm_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['acm_metabox_nonce'], 'acm_save_metabox_data' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $fields = [
            '_acm_value'    => 'sanitize_text_field',
            '_acm_label'    => 'sanitize_text_field',
            '_acm_color'    => 'sanitize_hex_color',
            '_acm_format'   => 'sanitize_key',
            '_acm_decimals' => 'intval',
            '_acm_prefix'   => 'sanitize_text_field', // Nuevo
            '_acm_suffix'   => 'sanitize_text_field', // Nuevo
        ];

        foreach ( $fields as $key => $sanitizer ) {
            $input_name = substr( $key, 1 );
            if ( isset( $_POST[ $input_name ] ) ) {
                update_post_meta( $post_id, $key, call_user_func( $sanitizer, $_POST[ $input_name ] ) );
            }
        }
    }
}