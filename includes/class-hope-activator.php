<?php

/**
 * Fired during plugin activation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Hope
 * @subpackage Hope/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Hope
 * @subpackage Hope/includes
 * @author     Rigal Patel <rigal9979@gmail.com>
 */
class Hope_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		/**
		 * Create Product table
		 *
		 * @since    1.0.0
		 */
		// create the lat & long table
		global $wpdb;
		$hope_product_tbl = $wpdb->prefix . 'Product'; 

		if($wpdb->get_var("show tables like '$hope_product_tbl'") != $hope_product_tbl) 
		{
				$sql = "CREATE TABLE " . $hope_product_tbl . " (
				`id` int(10) NOT NULL AUTO_INCREMENT,
				`product_name` varchar(255) NOT NULL,
				`product_image` varchar(255) NULL,
				`Product_description` text,
				 PRIMARY KEY (id)
				);";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
				
		}

	}

}
