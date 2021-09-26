<?php
add_filter('woocommerce_product_data_tabs', 'isceb_add_new_event_product_tab');

function isceb_add_new_event_product_tab($tabs)
{
    $product_type = 'isceb_event';

    $tabs['isceb_event_tab'] = array(
        'label' => __('Events info', 'woocommerce'),
        'target' => 'isceb_events_tab',
        'class'    => array('show_if_isceb_event'),
        'priority' => 10,
    );


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
    $product = wc_get_product($post_id);

    $custom_fields_event_start_date = isset($_POST['isceb-start-of-event']) ? $_POST['isceb-start-of-event'] : '';
    $product->update_meta_data('isceb-start-of-event', sanitize_text_field($custom_fields_event_start_date));

    $custom_fields_event_end_date = isset($_POST['isceb-end-of-event']) ? $_POST['isceb-end-of-event'] : '';
    $product->update_meta_data('isceb-end-of-event', sanitize_text_field($custom_fields_event_end_date));

    $custom_fields_isceb_location_of_event = isset($_POST['isceb-location-of-event']) ? $_POST['isceb-location-of-event'] : '';
    $product->update_meta_data('isceb-location-of-event', sanitize_text_field($custom_fields_isceb_location_of_event));

    $custom_fields_isceb_event_option = isset($_POST["_isceb_event"]) ? "yes" : "no";
    $product->update_meta_data('_isceb_event', sanitize_text_field( $custom_fields_isceb_event_option));

    $product->save();
}
add_action('woocommerce_process_product_meta', 'isceb_save_product_custom_fields');


add_filter("product_type_options", function ($product_type_options) {

    $product_type_options["isceb_event"] = [
        "id"            => "_isceb_event",
        "wrapper_class" => "",
        "label"         => "Event",
        "description"   => "Events give access to the event tab",
        "default"       => "yes",
    ];

    return $product_type_options;
});




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

            if ($('input#_isceb_event').is(':checked')) {
                    jQuery('.isceb_event_tab_tab').show();
                } else {
                    jQuery('.isceb_event_tab_tab').hide();

                    //GO to inventory tab when event tab is current
                    if ($('.isceb_event_tab_tab').hasClass('active')) {
                        $('.inventory_options.inventory_tab > a').trigger('click');
                    }
                }


            $('input#_isceb_event').change(function() {
                if (this.checked) {
                    jQuery('.isceb_event_tab_tab').show();
                } else {
                    jQuery('.isceb_event_tab_tab').hide();

                    //GO to inventory tab when event tab is current
                    if ($('.isceb_event_tab_tab').hasClass('active')) {
                        $('.inventory_options.inventory_tab > a').trigger('click');
                    }
                }
            });


        });
    </script><?php
            }
