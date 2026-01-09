<?php
/**
 * Plugin Name: Another Custom Metrics
 * Description: Sistema de gestión de widgets de estadísticas mediante CPT y Shortcodes.
 * Version: 1.0.5
 * Author: Zumito
 * Text Domain: another-custom-metrics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ACM_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACM_URL', plugin_dir_url( __FILE__ ) );

require_once ACM_PATH . 'includes/class-cpt.php';
require_once ACM_PATH . 'includes/class-metabox.php';
require_once ACM_PATH . 'includes/class-shortcode.php';

class ACM_Init {

    public function __construct() {
        new ACM_CPT();
        new ACM_Metabox();
        new ACM_Shortcode();

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        // NUEVO: Encolar también en el admin para la vista previa
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function enqueue_assets() {
        // Solo cargar en admin si estamos editando el post type correcto
        if ( is_admin() ) {
            $screen = get_current_screen();
            if ( ! $screen || $screen->post_type !== 'acm_widget' ) {
                return;
            }
        }

        wp_enqueue_style( 'acm-styles', ACM_URL . 'assets/css/style.css', [], '1.0.0' );
        wp_enqueue_script( 'acm-script', ACM_URL . 'assets/js/script.js', [], '1.0.0', true );
    }
}

new ACM_Init();