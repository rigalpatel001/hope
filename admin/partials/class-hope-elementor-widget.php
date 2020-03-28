<?php
namespace Elementor;

//if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Widget_Popular_Posts extends Widget_Base {

	public function get_name() {

		return 'popular-posts';
	}

	public function get_title() {
		return __( 'Product List', 'elementor-custom-widget' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	protected function _register_controls() {


		/*
		 * start control section and followup with adding control fields.
		 * end control after all control field and repeat if you need other control section respectively.
		*/

		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Basic', 'elementor-custom-widget' ),
			]
		);
		$this->add_control(
			'heading_text',
			[
				'label' => __( 'Heading Text', 'elementor-custom-widget' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Enter some text', 'elementor-custom-widget' ),
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Number of Products', 'elementor-custom-widget' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 5,
				'options' => [
					1  => __( 'One', 'elementor-custom-widget' ),
					2  => __( 'Two', 'elementor-custom-widget' ),
					5  => __( 'Five', 'elementor-custom-widget' ),
					10 => __( 'Ten', 'elementor-custom-widget' ),
					//-1 => __( 'All', 'elementor-custom-widget' ),
				]
			]
		);
		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
        // get our input from the widget settings.
        global $wpdb;
        $productlist = '';
        
		$settings = $this->get_settings_for_display();
		$custom_text = ! empty( $settings['heading_text'] ) ? $settings['heading_text'] : ' Products ';
		$limit = ! empty( $settings['posts_per_page'] ) ? (int)$settings['posts_per_page'] : 4;
        
		$is_ajax = (isset($_REQUEST['is_ajax'])) ? sanitize_text_field($_REQUEST['is_ajax']) : '';

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
            wp_reset_postdata();
			
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
				echo $productlist;
			} 
        ?>
		<?php

	}

	protected function content_template() {}

	public function render_plain_content( $instance = [] ) {}

}
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widget_Popular_Posts() );