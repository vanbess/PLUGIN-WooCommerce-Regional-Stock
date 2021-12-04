<?php

/**
 * Plugin Name: SBWC Regional Stock
 * Description: Adds the ability to display an out stock message for WooCommerce products based on a set of predefined countries/locations
 * Version: 1.0.0
 * Author: WC Bessinger
 */

if (!defined('ABSPATH')) :
    exit();
endif;

// init
add_action('plugins_loaded', 'sbwc_rs_init');

function sbwc_rs_init()
{

    // constants
    define('RS_PATH', plugin_dir_path(__FILE__));
    define('RS_URL', plugin_dir_url(__FILE__));

    // classes and traits
    include RS_PATH . 'classes/SBWC_RS.php';
}
