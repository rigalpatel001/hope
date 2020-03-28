<?php
/**
 * Main Modules class
 */

class Modules {

    /**
     * @var Module_Base[]
     */

   

    private  static $instance = null;

    public static function get_instance() {
        if ( ! self::$instance )
            self::$instance = new self;
        return self::$instance;
    }

    public function init(){
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );
    }
    

    public function widgets_registered() {
        // We check if the Elementor plugin has been installed / activated.
        if( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ){
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/class-hope-elementor-widget.php';

        }
    }
}
Modules::get_instance()->init();