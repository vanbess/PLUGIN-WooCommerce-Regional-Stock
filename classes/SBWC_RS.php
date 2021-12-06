<?php

// include traits
include RS_PATH . 'classes/traits/RS_AJAX.php';
include RS_PATH . 'classes/traits/RS_CSS.php';
include RS_PATH . 'classes/traits/RS_JS.php';

/**
 * Builds product edit screen tabs for regional stock status
 */

class SBWC_RS
{

    /**
     * Traits
     */
    use RS_JS,
        RS_CSS,
        RS_AJAX;

    /**
     * Class init
     */
    public static function init()
    {

        // scripts
        add_action('admin_footer', [__CLASS__, 'rs_scripts']);

        // rs product data tab
        add_filter('woocommerce_product_data_tabs', [__CLASS__, 'rs_product_tab']);

        // rs product dat tab html -> displays only for variable products
        add_action('woocommerce_product_data_panels', [__CLASS__, 'rs_product_tab_html']);

        // register ajax function to save countries to product meta
        add_action('wp_ajax_rs_save_countries', [__CLASS__, 'rs_save_countries']);
        add_action('wp_ajax_nopriv_rs_save_countries', [__CLASS__, 'rs_save_countries']);

        // filter stock status strings based on custom defined RS stock status strings
        add_filter('woocommerce_get_availability', [__CLASS__, 'rs_display_custom_stock_status'], 1, 2);

        // action to set correct class for stock status text and add to cart button
        add_action('woocommerce_before_variations_form', [__CLASS__, 'rs_disable_atc_bn']);
    }

    /**
     * Scripts and CSS
     *
     * @return void
     */
    public static function rs_scripts()
    {
        wp_register_style('rs-css', self::rs_css(), [], false);
        wp_register_script('rs-js', self::rs_js(), ['jquery'], false);
    }

    /**
     * Register regional stock product tab
     *
     * @return void
     */
    public static function rs_product_tab($tabs)
    {
        $tabs['rs_tab'] = [
            'label'    => __('Regional Stock Status', 'woocommerce'),
            'priority' => 30,
            'target'   => 'rs_regional_stock',
            'class'    => ['show_if_variable']
        ];

        return $tabs;
    }

    /**
     * Product tab html
     *
     * @return void
     */
    public static function rs_product_tab_html()
    {
        // retrieve product variation ids and names
        global $post;

        // product id
        $prod_id = $post->ID;

        // retrieve product object
        $prod_data = wc_get_product($prod_id);

        // retrieve children
        $children = $prod_data->get_children();

        // setup variation array
        $var_array  = [];

        // retrieve product titles
        foreach ($children as $cid) :
            $var_array[$cid] = get_the_title($cid);
        endforeach;

?>

        <!-- COUNTRY CODES -->
        <div id="rs_regional_stock" class="panel woocommerce_options_panel hidden">
            <div class="options_group">

                <!-- form container -->
                <p class="rs_form_container">
                    <!-- label -->
                    <label for="rs_countries rs_prod_ids">
                        <?php _e('Specify all country codes and associated variations for which you would <u><b>like to show a stock status of in stock:</b></u>', 'woocommerce'); ?>
                    </label>
                </p>

                <?php foreach ($children as $cid) : ?>
                    <?php if (get_post_meta($cid, 'rs_countries', true)) : ?>

                        <div class="rs_input_cont">

                            <!-- product id selector -->
                            <div class="rs_prod_ids_cont">
                                <select name="rs_prod_ids" class="rs_prod_ids" data-active="<?php echo $cid; ?>">
                                    <option value=""><?php _e('select variation', 'woocommerce'); ?></option>
                                    <?php foreach ($var_array as $id => $name) : ?>
                                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- countries text box -->
                            <div class="rs_countries_cont">
                                <input type="text" name="rs_countries" class="rs_countries" placeholder="<?php _e('comma separated list of country codes', 'woocommerce'); ?>" value="<?php echo get_post_meta($cid, 'rs_countries', true); ?>">
                            </div>

                        </div>
                    <?php else : ?>

                        <div class="rs_input_cont">

                            <!-- product id selector -->
                            <div class="rs_prod_ids_cont">
                                <select name="rs_prod_ids" class="rs_prod_ids">
                                    <option value=""><?php _e('select variation', 'woocommerce'); ?></option>
                                    <?php foreach ($var_array as $id => $name) : ?>
                                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- countries text box -->
                            <div class="rs_countries_cont">
                                <input type="text" name="rs_countries" class="rs_countries" placeholder="<?php _e('comma separated list of country codes', 'woocommerce'); ?>">
                            </div>

                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- submit -->
                <p class="rs_button_container">

                    <!-- save countries -->
                    <button id="rs_save_countries" class="button button-primary button-large" data-nonce="<?php echo wp_create_nonce('rs save countries'); ?>">
                        <?php _e('Save Regional Stock Avialability', 'woocommerce'); ?>
                    </button>

                    <!-- remove all stock restrictions -->
                    <button id="rs_remove_restrictions" class="button button-secondary button-large" data-children="<?php echo implode(',', $children); ?>" data-nonce="<?php echo wp_create_nonce('rs save countries'); ?>">
                        <?php _e('Reset Regional Stock Availability', 'woocommerce'); ?>
                    </button>

                </p>
            </div>

            <!-- CUSTOM OUT OF STOCK TEXT -->
            <div class="options_group">

                <!-- form container -->
                <p class="rs_form_container">

                    <!-- label -->
                    <label for="rs_custom_text">
                        <?php _e('If you would like to display custom out of stock text, define said text below and save:', 'woocommerce'); ?>
                    </label>
                </p>

                <?php foreach ($children as $cid) : ?>
                    <?php if (get_post_meta($cid, 'rs_text', true)) : ?>

                        <div class="rs_input_cont">

                            <!-- product id selector -->
                            <div class="rs_prod_ids_cont">
                                <select name="rs_prod_text_ids" class="rs_prod_text_ids" data-active="<?php echo $cid; ?>">
                                    <option value=""><?php _e('select variation', 'woocommerce'); ?></option>
                                    <?php foreach ($var_array as $id => $name) : ?>
                                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- out of stock text box -->
                            <div class="rs_text_cont">
                                <input type="text" name="rs_os_text" class="rs_os_text" placeholder="<?php _e('enter you custom out of stock text here', 'woocommerce'); ?>" value="<?php echo get_post_meta($cid, 'rs_text', true); ?>">
                            </div>

                        </div>
                    <?php else : ?>

                        <div class="rs_input_cont">

                            <!-- product id selector -->
                            <div class="rs_prod_ids_cont">
                                <select name="rs_prod_text_ids" class="rs_prod_text_ids">
                                    <option value=""><?php _e('select variation', 'woocommerce'); ?></option>
                                    <?php foreach ($var_array as $id => $name) : ?>
                                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- out of stock text box -->
                            <div class="rs_text_cont">
                                <input type="text" name="rs_os_text" class="rs_os_text" placeholder="<?php _e('enter you custom out of stock text here', 'woocommerce'); ?>">
                            </div>

                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- submit -->
                <p class="rs_button_container">

                    <!-- save countries -->
                    <button id="rs_save_text" class="button button-primary button-large" data-nonce="<?php echo wp_create_nonce('rs save countries'); ?>">
                        <?php _e('Save Custom Out Of Stock Text', 'woocommerce'); ?>
                    </button>

                    <!-- remove custom text -->
                    <button id="rs_remove_text" class="button button-secondary button-large" data-children="<?php echo implode(',', $children); ?>" data-nonce="<?php echo wp_create_nonce('rs save countries'); ?>">
                        <?php _e('Remove All Custom Out Of Stock Text', 'woocommerce'); ?>
                    </button>

                </p>

            </div>

        </div>

    <?php
        // css and js
        wp_enqueue_style('rs-css');
        wp_enqueue_style('rs-js');
    }


    /**
     * Returns custom RS out of stock string for associated products on the frontend, if set
     *
     * @param  array $availability - array of stock availability strings
     * @param  object $product - WC product object
     * @return array $availability - array containing custom availability string
     */
    public static function rs_display_custom_stock_status($availability, $product)
    {

        // retrieve product id
        $prod_id = $product->get_id();

        // retrieve custom stock text
        $rs_stock_status = get_post_meta($prod_id, 'rs_text', true);

        // retrieve included countries string
        $instock_countries = explode(',', get_post_meta($prod_id, 'rs_countries', true));

        // retrieve user location
        $location = WC_Geolocation::geolocate_ip();
        $country = $location['country'];

        // If a country is not present in $instock_countries array, mark items as out of stock.
        if (!in_array($country, $instock_countries)) :
            if ($rs_stock_status) :
                $availability['availability'] = __($rs_stock_status, 'woocommerce');
            else :
                $availability['availability'] = __('Out of stock', 'woocommerce');
            endif;
        endif;

        return $availability;
    }

    /**
     * Set stock text class and disable add to cart button as needed
     *
     * @return void
     */
    public static function rs_disable_atc_bn()
    {
        // retrieve product object
        global $post;
        $parent_id = $post->ID;
        $prod_obj = wc_get_product($parent_id);

        // if product does not have child, bail early
        if ($prod_obj->has_child() === false) :
            return;
        endif;

        // retrieve children
        $children = $prod_obj->get_children();

        // array to hold relevant (regionally available) product ids
        $rs_products = [];

        // retrieve user location
        $location = WC_Geolocation::geolocate_ip();
        $country = $location['country'];

        // if current user country present in list of countries for any child, push to $rs_products
        foreach ($children as $cid) :
            if (get_post_meta($cid, 'rs_countries', true)) :

                $instock_countries  = explode(',', get_post_meta($cid, 'rs_countries', true));

                if (in_array($country, $instock_countries)) :
                    $rs_products[] = $cid;
                endif;

            endif;
        endforeach;

        // if $rs_products is empty, bail once again
        if (empty($rs_products)) :
            return;
        endif;
    ?>
        <input type="hidden" id="rs_in_stock" value="<?php echo implode(',', $rs_products); ?>">
        <script>
            jQuery(document).ready(function($) {

                $('.swatchinput').on('click', function() {

                    setTimeout(function() {

                        let variation_id = $('.variation_id').val();
                        let rs_in_stock = $('#rs_in_stock').val();
                        let position = rs_in_stock.indexOf(variation_id);

                        if (position === -1) {
                            $('.single_add_to_cart_button').addClass('disabled wc-variation-is-unavailable');
                            $('.stock').removeClass('in-stock').addClass('out-of-stock');
                        } else if (position === 0) {
                            $('.single_add_to_cart_button').removeClass('disabled wc-variation-is-unavailable');
                            $('.stock').removeClass('out-of-stock').addClass('in-stock');
                        }

                    }, 100);

                });

            });
        </script>
<?php }
}

SBWC_RS::init();
