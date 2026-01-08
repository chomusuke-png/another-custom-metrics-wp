<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ACM_Metabox
 * * Gestiona los campos personalizados (Metaboxes) para la configuración del widget.
 */
class ACM_Metabox {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_meta_box' ] );
    }

    /**
     * Añade el contenedor de metabox al editor del post.
     * * @return void
     */
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

    /**
     * Renderiza el formulario HTML dentro del metabox.
     * * @param WP_Post $post Objeto del post actual.
     * @return void
     */
    public function render_meta_box( $post ) {
        // Recuperar valores existentes
        $metric_value  = get_post_meta( $post->ID, '_acm_value', true );
        $metric_label  = get_post_meta( $post->ID, '_acm_label', true );
        $metric_color  = get_post_meta( $post->ID, '_acm_color', true );
        $metric_format = get_post_meta( $post->ID, '_acm_format', true );

        // Default
        if ( empty( $metric_format ) ) { 
            $metric_format = 'raw'; 
        }

        wp_nonce_field( 'acm_save_metabox_data', 'acm_metabox_nonce' );
        ?>
        <div class="acm-metabox-wrapper" style="display: grid; gap: 15px;">
            <p>
                <label for="acm_value"><strong>Valor de la Métrica:</strong></label><br>
                <input type="text" id="acm_value" name="acm_value" value="<?php echo esc_attr( $metric_value ); ?>" style="width: 100%;" placeholder="Ej: 1500000, 2024-12-31">
                <span class="description">Ingrese el valor crudo (sin símbolos ni formato).</span>
            </p>

            <p>
                <label for="acm_format"><strong>Formato de Visualización:</strong></label><br>
                <select id="acm_format" name="acm_format" style="width: 100%;">
                    <option value="raw" <?php selected( $metric_format, 'raw' ); ?>>Texto Plano (Sin formato)</option>
                    <option value="number" <?php selected( $metric_format, 'number' ); ?>>Número (1,234)</option>
                    <option value="compact" <?php selected( $metric_format, 'compact' ); ?>>Número Compacto (1.2k, 1M)</option>
                    <option value="money" <?php selected( $metric_format, 'money' ); ?>>Dinero ($ 1,234.00)</option>
                    <option value="date" <?php selected( $metric_format, 'date' ); ?>>Fecha (Según ajustes de WP)</option>
                </select>
            </p>

            <p>
                <label for="acm_label"><strong>Etiqueta / Descripción:</strong></label><br>
                <input type="text" id="acm_label" name="acm_label" value="<?php echo esc_attr( $metric_label ); ?>" style="width: 100%;" placeholder="Ej: Ingresos Totales">
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

    /**
     * Guarda los datos del metabox.
     * * @param int $post_id ID del post.
     * @return void
     */
    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['acm_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['acm_metabox_nonce'], 'acm_save_metabox_data' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = [
            '_acm_value'  => 'sanitize_text_field',
            '_acm_label'  => 'sanitize_text_field',
            '_acm_color'  => 'sanitize_hex_color',
            '_acm_format' => 'sanitize_key', // 'sanitize_key' es seguro para nuestros valores de select
        ];

        foreach ( $fields as $key => $sanitizer ) {
            $input_key = str_replace( '_', '', $key ); // _acm_value -> acmvalue (ajustar según name del input)
            // Nota: En el HTML usamos names como 'acm_value', así que mapeamos:
            $input_name = substr( $key, 1 ); // _acm_value -> acm_value

            if ( isset( $_POST[ $input_name ] ) ) {
                update_post_meta( $post_id, $key, call_user_func( $sanitizer, $_POST[ $input_name ] ) );
            }
        }
    }
}