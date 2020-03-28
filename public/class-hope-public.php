<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Hope
 * @subpackage Hope/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Hope
 * @subpackage Hope/public
 * @author     Rigal Patel <rigal9979@gmail.com>
 */
class Hope_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hope-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'bootstrapcss', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hope-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'bootstrapjs', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_params' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/**
	 * Front-end Product List Shortcode 
	 *
	 * @since 1.0.0
	 */
	public	function hope_productlist_shortcode ($atts){
	
		global $wpdb;
		$productlist = '';
		$is_ajax = (isset($_REQUEST['is_ajax'])) ? sanitize_text_field($_REQUEST['is_ajax']) : '';
		$limit = (isset($_REQUEST['limit'])) ? sanitize_text_field($_REQUEST['limit']) : 4;

		// extract(shortcode_atts(array(
		// 	"limit" => '2',
		// ), $atts));

		// pagination setting 
		$per_page = $limit;
		if (isset($_REQUEST['page_no'])) {
			$page = sanitize_text_field($_REQUEST['page_no']);
		} else {
			$page = 1;
		}
		$start = ($page - 1) * $per_page;
	
		if (isset($_REQUEST['per_page'])) {
			$page_val =  (isset($_REQUEST['per_page'])) ?  sanitize_text_field($_REQUEST['per_page']) : 1;
			$start = ($page_val - 1) * $limit;
		} else {
			$start = 0;
			$page_val = 1;
		}

		// Set the table where we will be querying data
		$table_name = $wpdb->prefix . "Product";
		
		// Query the necessary posts
        $all_products = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM " . $table_name . " ORDER BY product_name DESC LIMIT %d, %d", $start, $per_page ) );
       
        // count the number of guest posts
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(ID) FROM " . $table_name, array() ) );

			$productlist .= '<section class="details-card"><div class="container"><div class="row">';
		
			foreach($all_products as $key => $product):
           
				// Set the desired output into a variable
				$img_url = esc_url($product->product_image); 
				$img_url = !empty($img_url) ? $img_url :  'https://dummyimage.com/200x200/ccc/fff';
				$productlist .= '
				<div class="col-md-4 mb-5">
                <div class="card-content">
                    <div class="card-img">
                        <img src="'.$img_url.'" alt="">
                        <span><h4>'.$product->product_name.'</h4></span>
                    </div>
                    <div class="card-desc">
                        <h3>'.$product->product_name.'</h3>
                        <p>'.substr($product->Product_description, 0,80).'...</p>
                    </div>
                </div>
            </div>';
			endforeach;	
			
			// pagination link 
			$pages = ceil($count / $per_page);

			$pagination_links = '';
			$productlist .= '<div class="col-md-12 col-sm-12 mt-5"><nav aria-label="Page navigation example">
			<ul class="pagination">';
			if(isset($page_val) && $page_val  > 1)
				$productlist .= '<li class="page-item ' . $class . '" > <a class="page-link" href = "javascript:void(0)" data-type="master" data-limit =' . $limit . ' data-id="1">First</a></li>';
			for ($i = 1; $i <= $pages; $i++) {
				if ($page_val == $i || ($page_val == 0 && $i == 1)) {
					$class = "active";
				} else {
					$class = "";
				}
				$productlist .= '<li class="page-item ' . $class . '" > <a class="page-link" href = "javascript:void(0)" data-type="master" data-limit =' . $limit . ' data-id=' . $i . ' > ' . $i . '</a></li>';
				
			}
			if($page_val < $pages)
				$productlist .= '<li class="page-item ' . $class . '" > <a class="page-link" href = "javascript:void(0)" data-type="master" data-limit =' . $limit . ' data-id='.$pages.'>Last</a></li>';
			$productlist .= '</ul></nav></div>'; 
			$productlist .= '</div></div></section>';
			if ($is_ajax == "yes") {
				$result = str_replace(['<section class="details-card">', '</section>'], ' ', $productlist);
				echo $result; die;
			}
		    else{
				return $productlist;
			} 
	 }

}
