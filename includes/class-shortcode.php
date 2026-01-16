<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ACM_Shortcode {

    public function __construct() {
        // Shortcode individual original
        add_shortcode( 'acm_widget', [ $this, 'render_single_shortcode' ] );
        // Nuevo shortcode de grupo
        add_shortcode( 'acm_group', [ $this, 'render_group_shortcode' ] );
    }

    /**
     * Shortcode: [acm_group ids="10,12,15" cols="3" color="#f00"]
     * Permite agrupar métricas y sobrescribir sus estilos globalmente.
     */
    public function render_group_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'ids'          => '',       // IDs separados por coma: "10,15,20"
            'cols'         => '3',      // Columnas en escritorio (1 a 4)
            'gap'          => '20px',   // Espacio entre métricas
            // -- Overrides Globales --
            'color'        => '',       // Sobrescribe Acento
            'bg_color'     => '',       // Sobrescribe Fondo
            'border_color' => '',       // Sobrescribe Borde
            'value_size'   => '',       // Sobrescribe tamaño valor
            'label_size'   => '',       // Sobrescribe tamaño etiqueta
            'icon_color'   => '',       // Sobrescribe color icono
        ], $atts, 'acm_group' );

        if ( empty( $atts['ids'] ) ) return '';

        $ids_array = explode( ',', $atts['ids'] );
        $ids_array = array_map( 'intval', $ids_array );
        
        // Preparamos los overrides para pasarlos a las métricas hijas
        $overrides = [];
        if ( ! empty( $atts['color'] ) )        $overrides['color']        = $atts['color'];
        if ( ! empty( $atts['bg_color'] ) )     $overrides['bg_color']     = $atts['bg_color'];
        if ( ! empty( $atts['border_color'] ) ) $overrides['border_color'] = $atts['border_color'];
        if ( ! empty( $atts['value_size'] ) )   $overrides['value_size']   = $atts['value_size'];
        if ( ! empty( $atts['label_size'] ) )   $overrides['label_size']   = $atts['label_size'];
        if ( ! empty( $atts['icon_color'] ) )   $overrides['icon_color']   = $atts['icon_color'];

        // Grid Container
        $grid_style = "display: grid; grid-template-columns: repeat(" . intval($atts['cols']) . ", 1fr); gap: " . esc_attr($atts['gap']) . ";";
        
        // Responsive simple: en móviles (pantallas pequeñas) forzamos 1 columna via CSS o inline media query (limitado en style).
        // Para simplificar, usamos una clase contenedora y un estilo base, el responsive idealmente va en CSS.
        
        $output = '<div class="acm-group-container acm-cols-' . intval($atts['cols']) . '" style="' . $grid_style . '">';
        
        foreach ( $ids_array as $post_id ) {
            if ( $post_id && get_post_type( $post_id ) === 'acm_widget' ) {
                // Renderizamos cada métrica pasando los overrides
                $output .= $this->get_metric_html( $post_id, $overrides );
            }
        }
        
        $output .= '</div>';

        return $output;
    }

    /**
     * Shortcode: [acm_widget id="123"]
     */
    public function render_single_shortcode( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'acm_widget' );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'acm_widget' ) return '';

        return $this->get_metric_html( $post_id );
    }

    /**
     * Lógica central de renderizado.
     * @param int $post_id ID del post.
     * @param array $overrides Array opcional con estilos que tienen prioridad sobre la DB.
     */
    private function get_metric_html( $post_id, $overrides = [] ) {
        
        // Helper para decidir valor: Override > Meta > Default
        $get_val = function( $key, $meta_key, $default = '' ) use ( $overrides, $post_id ) {
            if ( ! empty( $overrides[ $key ] ) ) {
                return $overrides[ $key ];
            }
            $val = get_post_meta( $post_id, $meta_key, true );
            return ( $val === '' || $val === false ) ? $default : $val;
        };

        // --- Extracción de Datos ---
        
        // Datos de contenido (No se pueden sobrescribir por grupo, son únicos)
        $raw_value = get_post_meta( $post_id, '_acm_value', true );
        $label     = get_post_meta( $post_id, '_acm_label', true );
        $url       = get_post_meta( $post_id, '_acm_url', true );
        $layout    = get_post_meta( $post_id, '_acm_layout', true );
        $format    = get_post_meta( $post_id, '_acm_format', true );
        $prefix    = get_post_meta( $post_id, '_acm_prefix', true );
        $suffix    = get_post_meta( $post_id, '_acm_suffix', true );
        $decimals  = get_post_meta( $post_id, '_acm_decimals', true );
        $duration  = get_post_meta( $post_id, '_acm_duration', true );
        $anim      = get_post_meta( $post_id, '_acm_anim', true );
        $image_id  = get_post_meta( $post_id, '_acm_image_id', true );
        $img_width = get_post_meta( $post_id, '_acm_img_width', true );
        
        // Defaults técnicos
        if ( empty( $layout ) ) $layout = 'top';
        if ( $decimals === '' ) $decimals = 0;
        if ( empty( $duration ) ) $duration = 2.5;
        if ( empty( $anim ) ) $anim = 'count';
        if ( empty( $img_width ) ) $img_width = 80;

        // Datos Estéticos (SÍ se pueden sobrescribir)
        $color        = $get_val( 'color', '_acm_color' );
        $bg_color     = $get_val( 'bg_color', '_acm_bg_color' );
        $border_color = $get_val( 'border_color', '_acm_border_color' );
        $value_size   = $get_val( 'value_size', '_acm_value_size', '3' );
        $label_size   = $get_val( 'label_size', '_acm_label_size', '1' );
        $icon_color   = $get_val( 'icon_color', '_acm_icon_color' );

        // --- Renderizado ---

        // 1. Imagen / Icono
        $image_html = '';
        // Nota: Asumimos que tienes el método ACM_Utils::render_icon_html de la respuesta anterior
        if ( class_exists('ACM_Utils') && method_exists('ACM_Utils', 'render_icon_html') ) {
            $image_html = ACM_Utils::render_icon_html( $image_id, $img_width, $icon_color );
        }

        // 2. Formato Número
        $formatted_number = ACM_Utils::format_metric( $raw_value, $format, (int)$decimals );
        $final_output     = esc_html( $prefix ) . $formatted_number . esc_html( $suffix );

        // 3. Estilos CSS Inline
        
        // Valor
        $value_style_arr = [];
        if ( $color ) { $value_style_arr[] = "color: {$color}; border-color: {$color};"; }
        $value_style_arr[] = "font-size: " . floatval($value_size) . "rem;";
        $value_style = 'style="' . implode( ' ', $value_style_arr ) . '"';

        // Etiqueta
        $label_style = 'style="font-size: ' . floatval($label_size) . 'rem;"';

        // Tarjeta
        $card_style_arr = [];
        if ( $bg_color ) { $card_style_arr[] = "background-color: {$bg_color};"; }
        if ( $border_color ) { $card_style_arr[] = "border-color: {$border_color};"; }
        $card_style = ! empty( $card_style_arr ) ? 'style="' . implode( ' ', $card_style_arr ) . '"' : '';

        $layout_class = 'acm-layout-' . esc_attr( $layout );

        // Atributos JS
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