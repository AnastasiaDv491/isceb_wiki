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
    $product->update_meta_data('_isceb_event', sanitize_text_field($custom_fields_isceb_event_option));

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
    </script>
<?php
}

function isceb_get_price_html_zero_free($product)
{
    $price = '';
    // var_dump($product->get_type());
    if ($product->get_type() == 'variable') {
        $prices = $product->get_variation_prices(true);
        if (empty($prices['price'])) {
            $price = apply_filters('woocommerce_variable_empty_price_html', '', $product);
        } else {
            $min_price     = current($prices['price']);
            $max_price     = end($prices['price']);
            $min_reg_price = current($prices['regular_price']);
            $max_reg_price = end($prices['regular_price']);

            if ($min_price !== $max_price) {

                if (intval($min_price) === 0) {

                    $min_price = 'Free';
                }
                $price = wc_format_price_range($min_price, $max_price);
            } elseif ($product->is_on_sale() && $min_reg_price === $max_reg_price) {
                $price = wc_format_sale_price(wc_price($max_reg_price), wc_price($min_price));
            } else {
                $price = wc_price($min_price);
            }

            $price = apply_filters('woocommerce_variable_price_html', $price . $product->get_price_suffix(), $product);
        }
    } else {
        $price = $product->get_price_html();
    }


    $price = str_replace('woocommerce-Price-amount', 'isceb_event_price', $price);

    return apply_filters('woocommerce_get_price_html', $price, $product);
}

/* WooCommerce: The Code Below Removes Checkout Fields */
add_filter('woocommerce_checkout_fields', 'isceb_custom_override_checkout_fields');
function isceb_custom_override_checkout_fields($fields)
{
    // unset($fields['billing']['billing_first_name']);
    // unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    // unset($fields['billing']['billing_phone']);
    // unset($fields['order']['order_comments']);
    // unset($fields['billing']['billing_email']);
    // unset($fields['account']['account_username']);
    // unset($fields['account']['account_password']);
    // unset($fields['account']['account_password-2']);

    return $fields;
}

function isceb_get_programs()
{
    return ['Business Administration', 'Business Engineering', 'Master of International Business Economics and Management', 'Other'];
}

function isceb_get_phases()
{
    return ['1st Bachelor', '2nd Bachelor', '3rd Bachelor', 'Bridging', '1st Master', '2nd Master', 'Prepratory', 'Other'];
}


add_action('woocommerce_after_checkout_billing_form', 'isceb_display_extra_fields_after_billing_address', 10, 1);
function isceb_display_extra_fields_after_billing_address()
{
    woocommerce_form_field('isceb_program', array(
        'type'      => 'select',
        'options'   => isceb_get_programs(),
        'label'     => __('Program', 'woocommerce'),
        'placeholder'   => _x('Program', 'placeholder', 'woocommerce'),
        'required'  => true,
        'class'     => array('form-row-wide'),
        'clear'     => true
    ));

    woocommerce_form_field('isceb_phase', array(
        'type'      => 'select',
        'options'   =>  isceb_get_phases(),
        'label'     => __('Phase', 'woocommerce'),
        'placeholder'   => _x('Phase', 'placeholder', 'woocommerce'),
        'required'  => true,
        'class'     => array('form-row-wide'),
        'clear'     => true
    ));

    woocommerce_form_field('isceb_newsletter_consent', array(
        'type'      => 'checkbox',
        'label'     => __('Phase', 'woocommerce'),
        'placeholder'   => _x('Phase', 'placeholder', 'woocommerce'),
        'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
        'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
        'required'      => false, // Mandatory or Optional
        'label'         => 'ISCEB can keep me up to date about what is going on', // Label and Link
    ));
}

/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process()
{
    // Check if set, if its not set add an error.
    if (!$_POST['isceb_program'])
        wc_add_notice(__('Please select a program.'), 'error');
    if (!$_POST['isceb_phase'])
        wc_add_notice(__('Please select a phase.'), 'error');
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1);

function my_custom_checkout_field_display_admin_order_meta($order)
{
    $order_metadata = get_post_meta($order->get_id());

    if (array_key_exists('isceb_program', $order_metadata) && is_numeric($order_metadata['isceb_program'][0])) {
        echo '<p><strong>' . __('Program') . ':</strong> ' . esc_html(isceb_get_programs()[$order_metadata['isceb_program'][0]]) . '</p>';
    }

    if (array_key_exists('isceb_phase', $order_metadata) && is_numeric($order_metadata['isceb_phase'][0])) {
        echo '<p><strong>' . __('Phase') . ':</strong> ' .  esc_html(isceb_get_phases()[$order_metadata['isceb_phase'][0]]) . '</p>';
    }

    if (array_key_exists('isceb_newsletter_consent', $order_metadata) && is_numeric($order_metadata['isceb_newsletter_consent'][0])) {
        echo '<p><strong>' . __('Newsletter') . ':</strong> ' .  esc_html(($order_metadata['isceb_newsletter_consent'][0] === '1'?'Yes':'No')) . '</p>';
    }
}

add_action('woocommerce_checkout_update_order_meta', 'isceb_custom_checkout_field_update_order_meta');
function isceb_custom_checkout_field_update_order_meta($order_id)
{
    if (!empty($_POST['isceb_program'])) {
        update_post_meta($order_id, 'isceb_program', sanitize_text_field($_POST['isceb_program']));
    }
    if (!empty($_POST['isceb_phase'])) {
        update_post_meta($order_id, 'isceb_phase', sanitize_text_field($_POST['isceb_phase']));
    }
    if (!empty($_POST['isceb_newsletter_consent'])) {
        update_post_meta($order_id, 'isceb_newsletter_consent', sanitize_text_field($_POST['isceb_newsletter_consent']));
    }
}
