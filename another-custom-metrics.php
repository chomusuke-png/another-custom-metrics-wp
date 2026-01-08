<?php
/**
 * Plugin Name: Another Custom Metrics
 * Description: Sistema de gestión de widgets de estadísticas mediante CPT y Shortcodes.
 * Version: 1.0.1
 * Author: Zumito
 * Text Domain: another-custom-metrics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes de rutas
define( 'ACM_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACM_URL', plugin_dir_url( __FILE__ ) );

// Requiere los archivos de las clases
require_once ACM_PATH . 'includes/class-cpt.php';
require_once ACM_PATH . 'includes/class-metabox.php';
require_once ACM_PATH . 'includes/class-shortcode.php';

/**
 * Class ACM_Init
 * * Clase principal para inicializar los componentes del plugin.
 */
class ACM_Init {

    /**
     * Constructor.
     * * Inicia los hooks y las instancias de las clases.
     */
    public function __construct() {
        // Inicializar componentes
        new ACM_CPT();
        new ACM_Metabox();
        new ACM_Shortcode();

        // Cargar assets
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Encola los estilos CSS y Scripts JS necesarios para el frontend.
     * * @return void
     */
    public function enqueue_assets() {
        wp_enqueue_style( 
            'acm-styles', 
            ACM_URL . 'assets/css/style.css', 
            [], 
            '1.0.0' 
        );
    }
}

// Arrancar el plugin
new ACM_Init();