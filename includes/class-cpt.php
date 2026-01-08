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
     * * Registra el hook de init.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
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
}