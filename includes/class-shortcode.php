<?php
//

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACM_Shortcode {

    public function __construct() {
        add_shortcode( 'acm_widget', [ $this, 'render_shortcode' ] );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'acm_widget' );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'acm_widget' ) return '';

        // Datos
        $raw_value = get_post_meta( $post_id, '_acm_value', true );
        $label     = get_post_meta( $post_id, '_acm_label', true );
        $color     = get_post_meta( $post_id, '_acm_color', true );
        $format    = get_post_meta( $post_id, '_acm_format', true );
        $prefix    = get_post_meta( $post_id, '_acm_prefix', true );
        $suffix    = get_post_meta( $post_id, '_acm_suffix', true );
        
        $decimals  = get_post_meta( $post_id, '_acm_decimals', true );
        if ( $decimals === '' ) $decimals = 0;
        $decimals = intval( $decimals );
        
        $duration  = get_post_meta( $post_id, '_acm_duration', true );
        if ( empty( $duration ) ) $duration = 2.5;

        // Tipo de animación (nuevo)
        $anim = get_post_meta( $post_id, '_acm_anim', true );
        if ( empty( $anim ) ) $anim = 'count';

        // Render PHP inicial
        $formatted_number = $this->format_metric( $raw_value, $format, $decimals );
        $final_output     = esc_html( $prefix ) . $formatted_number . esc_html( $suffix );
        $style_attr       = $color ? "style='border-color: {$color}; color: {$color};'" : '';

        // Data attributes
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
        ?>
        <div class="acm-widget-card">
            <div class="acm-value acm-anim-<?php echo esc_attr( $anim ); ?>" <?php echo $style_attr; ?> <?php echo $data_attr; ?>>
                <?php echo $final_output; ?>
            </div>
            <div class="acm-label">
                <?php echo esc_html( $label ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function format_metric( $value, $format, $decimals = 0 ) {
        if ( empty( $value ) && $value !== '0' ) return $value;

        switch ( $format ) {
            case 'number': return number_format_i18n( (float) $value, $decimals );
            case 'money': return '$ ' . number_format_i18n( (float) $value, $decimals );
            case 'percent': return number_format_i18n( (float) $value, $decimals ) . '%';
            case 'compact': return $this->format_compact_number( (float) $value, $decimals );
            case 'money_compact': return '$ ' . $this->format_compact_number( (float) $value, $decimals );
            case 'weight': return $this->format_weight( (float) $value, $decimals );
            case 'date': 
                $timestamp = strtotime( $value );
                return $timestamp ? date_i18n( get_option( 'date_format' ), $timestamp ) : $value;
            case 'raw':
            default: return $value;
        }
    }

    private function format_compact_number( $n, $decimals ) {
        if ( $n < 1000 ) return number_format_i18n( $n, $decimals );
        $suffix = [ '', 'k', 'M', 'B', 'T' ];
        $power  = floor( log( $n, 1000 ) );
        if ( $power >= count( $suffix ) ) $power = count( $suffix ) - 1;
        return number_format_i18n( round( $n / pow( 1000, $power ), $decimals ), $decimals ) . $suffix[ $power ];
    }

    private function format_weight( $g, $decimals ) {
        $suffixes = [ 'g', 'kg', 't' ];
        if ( $g <= 0 ) return '0 g';
        $power = floor( log( $g, 1000 ) );
        if ( $power >= count( $suffixes ) ) $power = count( $suffixes ) - 1;
        if ( $power < 0 ) $power = 0;
        $number = $g / pow( 1000, $power );
        return number_format_i18n( round( $number, $decimals ), $decimals ) . ' ' . $suffixes[ $power ];
    }
}