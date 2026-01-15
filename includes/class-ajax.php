<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ACM_Ajax {

    public function __construct() {
        add_action( 'wp_ajax_acm_render_preview', [ $this, 'render_preview' ] );
    }

    public function render_preview() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'No permission' );
        }

        // Recoger datos del POST
        $raw_value = isset($_POST['acm_value']) ? sanitize_text_field($_POST['acm_value']) : '';
        $label     = isset($_POST['acm_label']) ? sanitize_text_field($_POST['acm_label']) : '';
        $url       = isset($_POST['acm_url']) ? esc_url_raw($_POST['acm_url']) : ''; // --- NUEVO ---
        $format    = isset($_POST['acm_format']) ? sanitize_key($_POST['acm_format']) : 'raw';
        $prefix    = isset($_POST['acm_prefix']) ? sanitize_text_field($_POST['acm_prefix']) : '';
        $suffix    = isset($_POST['acm_suffix']) ? sanitize_text_field($_POST['acm_suffix']) : '';
        $decimals  = isset($_POST['acm_decimals']) ? intval($_POST['acm_decimals']) : 0;
        $duration  = isset($_POST['acm_duration']) ? sanitize_text_field($_POST['acm_duration']) : '2.5';
        $anim      = isset($_POST['acm_anim']) ? sanitize_key($_POST['acm_anim']) : 'count';
        
        $color        = isset($_POST['acm_color']) ? sanitize_hex_color($_POST['acm_color']) : '';
        $bg_color     = isset($_POST['acm_bg_color']) ? sanitize_hex_color($_POST['acm_bg_color']) : '';
        $border_color = isset($_POST['acm_border_color']) ? sanitize_hex_color($_POST['acm_border_color']) : '';

        $image_id = isset($_POST['acm_image_id']) ? intval($_POST['acm_image_id']) : 0;
        $image_html = '';
        if ( $image_id ) {
            $image_url = wp_get_attachment_image_url( $image_id, 'medium' );
            if ( $image_url ) {
                $image_html = '<img class="acm-icon" src="' . esc_url( $image_url ) . '" alt="" />';
            }
        }

        $formatted_number = ACM_Utils::format_metric( $raw_value, $format, $decimals );
        $final_output     = esc_html( $prefix ) . $formatted_number . esc_html( $suffix );

        $value_style = $color ? "style='color: {$color}; border-color: {$color};'" : '';
        $card_style_arr = [];
        if ( $bg_color ) { $card_style_arr[] = "background-color: {$bg_color};"; }
        if ( $border_color ) { $card_style_arr[] = "border-color: {$border_color};"; }
        $card_style = ! empty( $card_style_arr ) ? 'style="' . implode( ' ', $card_style_arr ) . '"' : '';

        $data_attr = '';
        if ( $format !== 'date' && is_numeric( $raw_value ) ) {
            $data_attr  = 'data-acm-value="' . esc_attr( $raw_value ) . '" ';
            $data_attr .= 'data-acm-format="' . esc_attr( $format ) . '" ';
            $data_attr .= 'data-acm-decimals="' . esc_attr( $decimals ) . '" ';
            $data_attr .= 'data-acm-prefix="' . esc_attr( $prefix ) . '" ';
            $data_attr .= 'data-acm-suffix="' . esc_attr( $suffix ) . '" ';
            $data_attr .= 'data-acm-duration="' . esc_attr( $duration ) . '" ';
            $data_attr .= 'data-acm-anim="' . esc_attr( $anim ) . '"';
        }

        ob_start();
        include ACM_PATH . 'templates/public/metric.php';
        $html = ob_get_clean();

        wp_send_json_success( $html );
    }
}