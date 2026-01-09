<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ACM_CPT
 * * Maneja el registro y configuración del Custom Post Type.
 */
class ACM_CPT {

    /**
     * Constructor.
     * * Registra el hook de init y las columnas personalizadas.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        
        // Hooks para columnas en el admin
        add_filter( 'manage_acm_widget_posts_columns', [ $this, 'add_custom_columns' ] );
        add_action( 'manage_acm_widget_posts_custom_column', [ $this, 'render_custom_columns' ], 10, 2 );
    }

    /**
     * Registra el tipo de post 'acm_widget'.
     * * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'               => 'Métricas / Widgets',
            'singular_name'      => 'Métrica',
            'menu_name'          => 'Custom Metrics',
            'add_new'            => 'Añadir Nueva',
            'add_new_item'       => 'Añadir Nueva Métrica',
            'edit_item'          => 'Editar Métrica',
            'new_item'           => 'Nueva Métrica',
            'view_item'          => 'Ver Métrica',
            'search_items'       => 'Buscar Métricas',
            'not_found'          => 'No se encontraron métricas',
            'not_found_in_trash' => 'No hay métricas en la papelera',
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false, // No accesible directamente por URL
            'show_ui'             => true,  // Mostrar en admin
            'show_in_menu'        => true,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-chart-bar',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => [ 'title' ], // Solo título, el resto via metabox
            'has_archive'         => false,
            'rewrite'             => false,
        ];

        register_post_type( 'acm_widget', $args );
    }

    /**
     * Agrega columnas a la tabla de listado de posts.
     * * @param array $columns Columnas existentes.
     * @return array Columnas modificadas.
     */
    public function add_custom_columns( $columns ) {
        $new_columns = [];
        // Reordenar para que las columnas nuevas salgan justo después del título
        foreach ( $columns as $key => $title ) {
            $new_columns[ $key ] = $title;
            if ( 'title' === $key ) {
                $new_columns['acm_shortcode'] = 'Shortcode';
                $new_columns['acm_value']     = 'Valor Actual'; 
            }
        }
        return $new_columns;
    }

    /**
     * Renderiza el contenido de las columnas personalizadas.
     * * @param string $column Nombre de la columna.
     * @param int $post_id ID del post.
     * @return void
     */
    public function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'acm_shortcode':
                // Input de solo lectura o code block fácil de copiar
                echo '<code style="background:#e0e0e0; padding:3px 5px; border-radius:3px; user-select: all;">[acm_widget id="' . $post_id . '"]</code>';
                break;

            case 'acm_value':
                // Mostramos una vista rápida del valor configurado
                $val = get_post_meta( $post_id, '_acm_value', true );
                $prefix = get_post_meta( $post_id, '_acm_prefix', true );
                $suffix = get_post_meta( $post_id, '_acm_suffix', true );
                
                if ( ! empty( $val ) ) {
                    echo '<strong>' . esc_html( $prefix . $val . $suffix ) . '</strong>';
                } else {
                    echo '<span style="color:#aaa;">—</span>';
                }
                break;
        }
    }
}