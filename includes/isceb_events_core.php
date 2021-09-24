<?php
add_filter('woocommerce_product_data_tabs', 'isceb_add_new_event_product_tab');

function isceb_add_new_event_product_tab($tabs)
{
    $product_type = 'isceb_event';

    $tabs['isceb_event_tab'] = array(
        'label' => __('Events info', 'woocommerce'),
        'target' => 'isceb_events_tab',
        'class'    => array('hide_if_simple', 'hide_if_variable', 'hide_if_grouped', 'hide_if_external'),
        'priority' => 10,
    );

    $tabs['variations']['class'][] = 'show_if_' . $product_type;

    return $tabs;
}



/* Woocommerce testing */
/* First test add field in general*/
// add_action('woocommerce_product_options_general_product_data', 'isceb_create_start_date_fields');
add_action('woocommerce_product_data_panels', 'isceb_fill_new_events_tab');
function isceb_fill_new_events_tab()
{

    // global $post, $product_object;

    // Dont forget to change the id in the div with your target of your product tab
?>
    <div id='isceb_events_tab' class='panel woocommerce_options_panel'>
        <?php
        ?><div class='options_group'>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'isceb-start-of-event',
                    'label' => __('Start of event', 'woocommerce'),
                    'type'  => 'datetime-local'
                )

            );

            woocommerce_wp_text_input(
                array(
                    'id'                => 'isceb-end-of-event',
                    'label'             => __('End of event', 'woocommerce'),
                    'placeholder'       => '',
                    'type'              => 'datetime-local',
                )
            );

            woocommerce_wp_text_input(
                array(
                    'id' => 'isceb-location-of-event',
                    'label' => __('Location of event', 'woocommerce'),
                    'type'  => 'text'
                )

            );

            ?></div>
    </div>
<?php



}



// Save the start of the event date 
function isceb_save_product_custom_fields($post_id)
{
    // wp_set_object_terms( $post_id, 'isceb_event_product', 'product_type' );
    $product = wc_get_product($post_id);

    $custom_fields_event_start_date = isset($_POST['isceb-start-of-event']) ? $_POST['isceb-start-of-event'] : '';
    $product->update_meta_data('isceb-start-of-event', sanitize_text_field($custom_fields_event_start_date));

    $custom_fields_event_end_date = isset($_POST['isceb-end-of-event']) ? $_POST['isceb-end-of-event'] : '';
    $product->update_meta_data('isceb-end-of-event', sanitize_text_field($custom_fields_event_end_date));

    $custom_fields_isceb_location_of_event = isset($_POST['isceb-location-of-event']) ? $_POST['isceb-location-of-event'] : '';
    $product->update_meta_data('isceb-location-of-event', sanitize_text_field( $custom_fields_isceb_location_of_event));

    $product->save();
}
add_action('woocommerce_process_product_meta', 'isceb_save_product_custom_fields');



// add_action('save_post', 'wc_rrp_save_product');
// function wc_rrp_save_product($product_id)
// {
// 	// If this is a auto save do nothing, we only save when update button is clicked
// 	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
// 		return;
// 	if (isset($_POST['rrp_price'])) {
// 		if (is_numeric($_POST['rrp_price']))
// 			update_post_meta($product_id, 'rrp_price', $_POST['rrp_price']);

// 	} else delete_post_meta($product_id, 'rrp_price');

// 	var_dump($_POST['isceb-start-of-event']);


// 	if (isset($_POST['isceb-start-of-event'])) {
// 		if (is_numeric($_POST['isceb-start-of-event']))
// 			update_post_meta($product_id, 'isceb-start-of-event', $_POST['isceb-start-of-event']);

// 	} else delete_post_meta($product_id, 'isceb-start-of-event');
// }


/* Second test custom product type */
// add a product type
add_filter('product_type_selector', 'isceb_add_custom_product_type_event');
function isceb_add_custom_product_type_event($types)
{
    $types['isceb_event'] = __('Event');
    return $types;
}

// add_action('plugins_loaded', 'isceb_create_custom_product_type_event');
add_action('init', 'isceb_create_custom_product_type_event');
function isceb_create_custom_product_type_event()
{
    // declare the product class
    class WC_Product_isceb_event extends WC_Product
    {
        public function __construct($product)
        {
            $this->product_type = 'isceb_event';
            parent::__construct($product);
            // add additional functions here
        }
    }
}

function isceb_woocommerce_event_product_class($classname, $product_type)
{
    if ($product_type == 'WC_Product_Isceb_event') { // notice the checking here.
        $classname = 'WC_Product_isceb_event';
    }

    return $classname;
}

add_filter('woocommerce_product_class', 'isceb_woocommerce_event_product_class', 10, 2);


add_action('admin_footer', 'isceb_wc_show_tabs_on_custom_product');

/**
 * Show pricing fields for gift_coupon product.
 */
function isceb_wc_show_tabs_on_custom_product()
{
    // var_dump($product_object->get_type());
    if ('product' != get_post_type()) :
        return;
    endif;
?><script type='text/javascript'>
        jQuery(function($) {
            //For variations tab
            $('.show_if_variable:not(.hide_if_isceb_event)').addClass('show_if_isceb_event');

            //for Price tab
            jQuery('.product_data_tabs .general_tab').addClass('show_if_variable_bulk').show();
            jQuery('#general_product_data').addClass('show_if_variable_bulk').show();
            jQuery('.show_if_simple').addClass('show_if_variable_bulk').show();
            //for Inventory tab
            jQuery('.inventory_options').addClass('show_if_variable_bulk').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_variable_bulk').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_variable_bulk').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_variable_bulk').show();

            $('input#_downloadable, input#_virtual').on('change', function() {
                jQuery('.product_data_tabs .general_tab').addClass('show_if_variable_bulk').show();
                jQuery('#general_product_data').addClass('show_if_variable_bulk').show();
                jQuery('.show_if_simple').addClass('show_if_variable_bulk').show();
                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_variable_bulk').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_variable_bulk').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_variable_bulk').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_variable_bulk').show();
            });

            $('#product-type').on('change', function($) {

                jQuery('.product_data_tabs .general_tab').addClass('show_if_variable_bulk').show();
                jQuery('#general_product_data').addClass('show_if_variable_bulk').show();
                jQuery('.show_if_simple').addClass('show_if_variable_bulk').show();
                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_variable_bulk').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_variable_bulk').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_variable_bulk').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_variable_bulk').show();
            });

            // Show variable type options when new attribute is added.
            $(document.body).on('woocommerce_added_attribute', function(e) {

                $('#product_attributes .show_if_variable:not(.hide_if_isceb_event)').addClass('show_if_isceb_event');

                var $attributes = $('#product_attributes').find('.woocommerce_attribute');

                if ('isceb_event' == $('select#product-type').val()) {
                    $attributes.find('.enable_variation').show();
                }
            });

        });
    </script><?php
            }
