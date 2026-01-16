<?php
/**
 * Plugin Name: Another Custom Metrics
 * Description: Sistema de gestión de widgets de estadísticas mediante CPT y Shortcodes.
 * Version: 1.1.1
 * Author: Zumito
 * Text Domain: another-custom-metrics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ACM_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACM_URL', plugin_dir_url( __FILE__ ) );

require_once ACM_PATH . 'includes/class-utils.php';
require_once ACM_PATH . 'includes/class-cpt.php';
require_once ACM_PATH . 'includes/class-metabox.php';
require_once ACM_PATH . 'includes/class-shortcode.php';
require_once ACM_PATH . 'includes/class-ajax.php';

class ACM_Init {

    public function __construct() {
        new ACM_CPT();
        new ACM_Metabox();
        new ACM_Shortcode();
        new ACM_Ajax();

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Carga de scripts para el Frontend (Visitantes)
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style( 'acm-styles', ACM_URL . 'assets/css/style.css', [], '1.0.0' );
        wp_enqueue_script( 'acm-core', ACM_URL . 'assets/js/core.js', [], '1.0.7', true );
        wp_enqueue_script( 'acm-frontend', ACM_URL . 'assets/js/frontend.js', ['acm-core'], '1.0.7', true );
    }

    /**
     * Carga de scripts para el Admin (Panel)
     */
    public function enqueue_admin_assets() {
        if ( ! is_admin() ) return;
        
        $screen = get_current_screen();
        if ( ! $screen || $screen->post_type !== 'acm_widget' ) return;

        wp_enqueue_media();

        wp_enqueue_style( 'acm-styles', ACM_URL . 'assets/css/style.css', [], '1.0.0' );
        wp_enqueue_script( 'acm-core', ACM_URL . 'assets/js/core.js', [], '1.0.7', true );
        wp_enqueue_script( 'acm-admin', ACM_URL . 'assets/js/admin.js', ['acm-core'], '1.0.7', true );
    }
}

new ACM_Init();