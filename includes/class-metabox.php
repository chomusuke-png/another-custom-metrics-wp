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
        $metric_url          = get_post_meta( $post->ID, '_acm_url', true );
        $metric_layout       = get_post_meta( $post->ID, '_acm_layout', true );
        
        $metric_format       = get_post_meta( $post->ID, '_acm_format', true );
        $metric_decimals     = get_post_meta( $post->ID, '_acm_decimals', true );
        $metric_prefix       = get_post_meta( $post->ID, '_acm_prefix', true );
        $metric_suffix       = get_post_meta( $post->ID, '_acm_suffix', true );
        $metric_duration     = get_post_meta( $post->ID, '_acm_duration', true );
        $metric_anim         = get_post_meta( $post->ID, '_acm_anim', true );
        $metric_color        = get_post_meta( $post->ID, '_acm_color', true );
        $metric_bg_color     = get_post_meta( $post->ID, '_acm_bg_color', true );
        $metric_border_color = get_post_meta( $post->ID, '_acm_border_color', true );
        
        // Imagen e Imagen Width
        $metric_image_id     = get_post_meta( $post->ID, '_acm_image_id', true );
        $metric_img_width    = get_post_meta( $post->ID, '_acm_img_width', true ); // NUEVO
        $image_url           = $metric_image_id ? wp_get_attachment_image_url( $metric_image_id, 'medium' ) : '';

        // Defaults
        if ( empty( $metric_format ) ) { $metric_format = 'raw'; }
        if ( $metric_decimals === '' ) { $metric_decimals = '0'; }
        if ( empty( $metric_duration ) ) { $metric_duration = '2.5'; }
        if ( empty( $metric_anim ) ) { $metric_anim = 'count'; }
        if ( empty( $metric_layout ) ) { $metric_layout = 'top'; }
        if ( empty( $metric_img_width ) ) { $metric_img_width = '80'; } // Default 80px

        include ACM_PATH . 'templates/admin/metabox.php';
    }

    public function render_preview_metabox( $post ) {
        ?>
        <div style="display: flex; align-items: center; justify-content: center; background: #fafafa;">
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
            '_acm_url'          => 'esc_url_raw',
            '_acm_layout'       => 'sanitize_key',
            '_acm_color'        => 'sanitize_hex_color',
            '_acm_format'       => 'sanitize_key',
            '_acm_decimals'     => 'intval',
            '_acm_prefix'       => 'sanitize_text_field',
            '_acm_suffix'       => 'sanitize_text_field',
            '_acm_duration'     => 'sanitize_text_field',
            '_acm_anim'         => 'sanitize_key',
            '_acm_bg_color'     => 'sanitize_hex_color',
            '_acm_border_color' => 'sanitize_hex_color',
            '_acm_image_id'     => 'intval',
            '_acm_img_width'    => 'intval', // NUEVO
        ];

        foreach ( $fields as $key => $sanitizer ) {
            $input_name = substr( $key, 1 ); 
            if ( isset( $_POST[ $input_name ] ) ) {
                update_post_meta( $post_id, $key, call_user_func( $sanitizer, $_POST[ $input_name ] ) );
            } else {
                if ( in_array($input_name, ['acm_image_id', 'acm_url', 'acm_img_width']) && empty($_POST[$input_name]) ) {
                    delete_post_meta( $post_id, $key );
                }
            }
        }
    }
}