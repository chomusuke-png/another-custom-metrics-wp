<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ACM_Shortcode {

    public function __construct() {
        add_shortcode( 'acm_widget', [ $this, 'render_shortcode' ] );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'acm_widget' );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'acm_widget' ) return '';

        // Metas
        $raw_value = get_post_meta( $post_id, '_acm_value', true );
        $label     = get_post_meta( $post_id, '_acm_label', true );
        $url       = get_post_meta( $post_id, '_acm_url', true );
        $layout    = get_post_meta( $post_id, '_acm_layout', true );
        if ( empty( $layout ) ) $layout = 'top';

        // --- NUEVO: TAMAÑOS ---
        $value_size = get_post_meta( $post_id, '_acm_value_size', true );
        $label_size = get_post_meta( $post_id, '_acm_label_size', true );
        if ( empty( $value_size ) ) $value_size = 3;
        if ( empty( $label_size ) ) $label_size = 1;

        $format    = get_post_meta( $post_id, '_acm_format', true );
        $prefix    = get_post_meta( $post_id, '_acm_prefix', true );
        $suffix    = get_post_meta( $post_id, '_acm_suffix', true );
        $decimals  = get_post_meta( $post_id, '_acm_decimals', true );
        if ( $decimals === '' ) $decimals = 0;
        $decimals = intval( $decimals );
        $duration  = get_post_meta( $post_id, '_acm_duration', true );
        if ( empty( $duration ) ) $duration = 2.5;
        $anim = get_post_meta( $post_id, '_acm_anim', true );
        if ( empty( $anim ) ) $anim = 'count';

        $color        = get_post_meta( $post_id, '_acm_color', true ); 
        $bg_color     = get_post_meta( $post_id, '_acm_bg_color', true ); 
        $border_color = get_post_meta( $post_id, '_acm_border_color', true );

        $image_id     = get_post_meta( $post_id, '_acm_image_id', true );
        $img_width    = get_post_meta( $post_id, '_acm_img_width', true );
        if ( empty( $img_width ) ) $img_width = 80;

        $image_html   = '';
        if ( $image_id ) {
            $image_url = wp_get_attachment_image_url( $image_id, 'medium' );
            if ( $image_url ) {
                $style_img = 'width: ' . intval($img_width) . 'px; max-width: none;';
                $image_html = '<img class="acm-icon" src="' . esc_url( $image_url ) . '" alt="" style="' . $style_img . '" />';
            }
        }

        $formatted_number = ACM_Utils::format_metric( $raw_value, $format, $decimals );
        $final_output     = esc_html( $prefix ) . $formatted_number . esc_html( $suffix );

        // Estilos Valor: Color + Font Size
        $value_style_arr = [];
        if ( $color ) { $value_style_arr[] = "color: {$color}; border-color: {$color};"; }
        $value_style_arr[] = "font-size: " . floatval($value_size) . "rem;";
        $value_style = 'style="' . implode( ' ', $value_style_arr ) . '"';

        // Estilos Label: Font Size
        $label_style = 'style="font-size: ' . floatval($label_size) . 'rem;"';

        // Estilos Tarjeta
        $card_style_arr = [];
        if ( $bg_color ) { $card_style_arr[] = "background-color: {$bg_color};"; }
        if ( $border_color ) { $card_style_arr[] = "border-color: {$border_color};"; }
        $card_style = ! empty( $card_style_arr ) ? 'style="' . implode( ' ', $card_style_arr ) . '"' : '';

        $layout_class = 'acm-layout-' . esc_attr( $layout );

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
        return ob_get_clean();
    }
}