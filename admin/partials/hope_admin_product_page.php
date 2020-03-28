<?php

/**
 * Provide a admin area Product Manage  for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Hope
 * @subpackage Hope/admin/partials
 */


    global $wpdb;
    $table_name = $wpdb->prefix . 'Product'; // do not forget about tables prefix
    $message = '';
    $notice = '';
    // this is default $item which will be used for new records
    $default = array(
    'id' => 0,
    'product_name' => NULL,
    'product_image' => NULL,
    'Product_description' => NULL,
    
    );

    /**
     *  Save product data
     * 
     * @since  1.0.0
     */
    
    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);  

        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
          $item_valid = product_validate_form($item);
      if ($item_valid === true) {
            if ($item['id'] == 0) {
                
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Product was successfully saved', 'hope');
                    $url = get_admin_url(get_current_blog_id(), 'admin.php?page=products');
                    //wp_redirect($url); exit;
                } else {
                    $notice = __('There was an error while saving item', 'hope');
                }
            } else {
            
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Product was successfully updated', 'hope');
                } else {
                    $notice = __('There was an error while updating item', 'hope');
                }
            }
        } else {
        // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
           }
        } else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
         
           // echo "SELECT count(*) as total FROM  $table_lead_name WHERE cacID IN ($id)";
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'hope');
            }
        }
    }
    

    // here we adding our custom meta box
    add_meta_box('product_form_meta_box', 'Products', 'product_form_meta_box_handler', 'productmanage', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Products', 'hope') ?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=products'); ?>"><?php _e('Back to Product List', 'hope') ?></a>
    </h2>

    <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif; ?>
    <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif; ?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('productmanage', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'aquascape') ?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php


/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function product_form_meta_box_handler($item)
{
    ?>
<div id="productmanage" class="rml_page_post_setting">
  
  <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
   <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Productname"><?php _e('Product Name', 'hope')?></label>
        </th>
        <td>
             <input id="product_name" name="product_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['product_name']);?>"
                   size="50" class="code" placeholder="<?php _e('Product Name', 'hope')?>">
        </td>
    </tr>

    <tr class="form-field">
            <th valign="top" scope="row">
                <label for="Productimage"><?php _e('Product Image', 'hope')?></label>
            </th>
            <td>

            <input type="text" name="product_image" value="<?php echo esc_attr($item['product_image']); ?>" class="featured_image_upload">
            <input type="button" name="image_upload" value="<?php esc_html_e( 'Upload Image', 'mytheme' ); ?>" class="upload_image_button button ">
            <!-- <input type="button" name="remove_image_upload" value="<?php esc_html_e( 'Remove Image', 'mytheme' ); ?>" class="remove_image_button button "> -->
    </td>
    </tr>

    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="Product_description"><?php _e('Product Description', 'hope')?></label>
        </th>
        <td>
        <?php wp_editor($item['Product_description'], 'Product_description', array(
            'wpautop'       =>      true,
            'media_buttons' =>      false,
            'textarea_name' =>      'Product_description',
            'textarea_rows' =>      10,
            'teeny'         =>      true
            )); ?>
        </td>
    </tr>
    </tbody>
</table>
</div>  
<?php
}

/**
 * Validatiion Fuction
 * @since   1.0.0
 */

function product_validate_form($item)
{
    $messages = array();

    if (empty($item['product_name'])) $messages[] = __('Product Name is required', 'hope');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}