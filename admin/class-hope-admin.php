<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Hope
 * @subpackage Hope/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hope
 * @subpackage Hope/admin
 * @author     Rigal Patel <rigal9979@gmail.com>
 */
class Hope_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hope_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hope_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hope-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hope_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hope_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script('media-upload');
		wp_enqueue_media();
		wp_enqueue_script('thickbox');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hope-admin.js', array( 'jquery','media-upload','thickbox' ), $this->version, false );

	}

	/**
	 * Add Product Menu admin side
	 *
	 * @since  1.0.0
	 */
	public function add_menu_page() {
	
		/**
		 * Create Product menu 
		 * 
		 * @since  1.0.0
		 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Products', 'hope' ),
			__( 'Products', 'hope' ),
			'manage_options',
			'products',
			array( $this, 'list_all_products' ),
			"dashicons-products",
			10                        
		);

		/** 
		 * Create admin side List all Products
		 * 
		 * @since  1.0.0
		 */                
			$this->plugin_screen_hook_suffix = add_submenu_page(
			'products',
			__( 'Add Product', 'hope' ),
			__( 'Add Product', 'hope' ),
			'manage_options',
			'product-new',
			array( $this, 'display_product_page' )
		);
			
			 

	}	

	/**
	 * New Product add page 
	 *
	 * @since  1.0.0
	 */
        
	public function display_product_page() {
		include_once 'partials/hope_admin_product_page.php';
	}

	/**
	 * List all product 
	 *
	 * @since  1.0.0
	 */
        
	public function list_all_products() {
		include_once 'partials/hope_list_products_page.php';
	}

}
