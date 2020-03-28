<?php

/**
 * The file that defines Products (WP List table )
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Hope
 * @subpackage Hope/includes
 */


class Custom_products_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular' => 'distributormanage',
            'plural' => 'distributormanage',
        ));
    }
    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
   
   
    /**
     * Render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_product_name($item)
   {    
        
        $actions = array(
            'edit' => sprintf('<a href="?page=product-new&id=%s">%s</a>', $item['id'], __('Edit', 'hope')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'hope')),
        );
        
       return sprintf('%s %s', $item['product_name'], $this->row_actions($actions));
    
    }
   
   
    /**
     * Add checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
   
    
    /**
     * Limit Description
     *
     * @param $item - row (key, value array)
     * @return HTML
     */

    function column_Product_description($item)
    {
        return substr($item['Product_description'], 0, 100);
    }
    
    /**
     * Display Image
     *
     * @param $item - row (key, value array)
     * @return HTML
     */

    function column_product_image($item)
    {
        return ($item['product_image'] == "") ? '' : '<img src='.$item['product_image']. ' width="50"/>';
    }
    

    /**
     * This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns() 
    {
       
            $columns = array(
                'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
                'product_name' => __('Product', 'hope'),
                'product_image' => __('Image', 'hope'),
                'Product_description' => __('Description', 'hope')
            ); 
      
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */


    function get_sortable_columns()
    {
        $sortable_columns = array(
            'product_name' => array('Product', true)
        );
        return $sortable_columns;
    }
   
    
    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
            $actions = array(
                'delete' => 'Delete'
            );
        return $actions;
    }
    /**
     * This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'Product'; // do not forget about tables prefix
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
    
     /**
     *  This method processes Search Box 
     * 
     */
    
    function search_box($text, $input_id) {
        if (empty($_REQUEST['s']) && !$this->has_items())
            return;
        $input_id = $input_id . '-search-input';
      ?>
        <p class="search-box">
        <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
        <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
        <?php submit_button($text, 'button', false, false, array('id' => 'search-submit')); ?>
        </p>
        <?php
    }
    
    /**
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'Product'; // do not forget about tables prefix
        $per_page = 4; 
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


		$paged = isset($_REQUEST['paged']) ? ($_REQUEST['paged'] - 1 ) * $per_page : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'product_name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
        
       
        if(isset($_REQUEST['s'])){
              $str =  $_REQUEST['s'];
              $srh = "where product_name LIKE '$str%' ";
              $total_items = $wpdb->get_var("SELECT COUNT(*)  FROM $table_name $srh");   
              $this->items = $wpdb->get_results("SELECT * FROM $table_name $srh ORDER BY $orderby $order LIMIT $paged ,$per_page", ARRAY_A);     
        } else {
           
              $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name  ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
            
            //var_dump($wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name  ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A));
            }
        
       // $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}