<?php

/**
 * Regional stock status JS
 */

trait RS_JS
{

    /**
     * JS
     *
     * @return void
     */
    public static function rs_js()
    { ?>
        <script>
            jQuery(document).ready(function($) {

                // set selected product id
                $('.rs_prod_ids').each(function(index, element) {
                    if ($(this).data('active')) {
                        $(this).val($(this).data('active'));
                    }
                });

                // *****************************************
                // save list of countries and prod id pairs
                // *****************************************
                $('#rs_save_countries').on('click', function(e) {

                    e.preventDefault();

                    let prod_ids = [],
                        countries = [],
                        nonce = $(this).data('nonce');

                    $('.rs_prod_ids').each(function(index, element) {
                        if ($(this).val() !== '') {
                            prod_ids.push($(this).val());
                        }
                    });

                    $('.rs_countries').each(function(index, element) {
                        if ($(this).val() !== '') {
                            countries.push($(this).val());
                        }
                    });
                    if (prod_ids.length > 0 && countries.length > 0) {

                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'rs_save_countries',
                            'countries': countries,
                            'prod_ids': prod_ids
                        }

                        $.post(ajaxurl, data, function(response) {
                            // console.log(response)
                            alert(response);
                            location.reload();
                        });
                    } else {
                        alert('<?php _e('Please supply all relevant data before attempting to save', 'woocommerce'); ?>');
                    }

                });

                // ********************************
                // remove all country restrictions
                // ********************************
                $('#rs_remove_restrictions').on('click', function(e) {
                    e.preventDefault();

                    let nonce = $(this).data('nonce'),
                        children = $(this).data('children');

                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'rs_save_countries',
                        'children': children
                    }

                    $.post(ajaxurl, data, function(response) {
                        // console.log(response);
                        alert(response);
                        location.reload();
                    });
                });

                // *********************************************
                // set product ids for custom text on page load
                // *********************************************
                $('select.rs_prod_text_ids').each(function(index, element) {
                    $(this).val($(this).data('active'));
                });

                // ******************************
                // save custom out of stock text
                // ******************************
                $('button#rs_save_text').on('click', function(e) {
                    e.preventDefault();

                    // vars
                    let prod_ids = [],
                        texts = [],
                        nonce = $(this).data('nonce');

                    // retrieve prod ids
                    $('.rs_prod_text_ids').each(function(index, element) {
                        if ($(this).val() !== '') {
                            prod_ids.push($(this).val());
                        }
                    });

                    // retrieve custom text
                    $('.rs_os_text').each(function(index, element) {
                        if ($(this).val() !== '') {
                            texts.push($(this).val());
                        }
                    });

                    // if no prod ids on submit
                    if (prod_ids.length === 0) {
                        alert('<?php _e('Please select at least one product', 'woocommerce') ?>');
                        return;
                    }

                    // if no custom text on submit
                    if (texts.length === 0) {
                        alert('<?php _e('Please enter custom out of stock text', 'woocommerce') ?>');
                        return;
                    }

                    // json object
                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'rs_save_countries',
                        'texts': texts,
                        'prod_ids': prod_ids
                    }

                    // send
                    $.post(ajaxurl, data, function(response) {
                        // console.log(response)
                        alert(response);
                        location.reload();
                    });

                });

                // ********************************
                // remove custom out of stock text
                // ********************************
                $('button#rs_remove_text').on('click', function(e) {
                    e.preventDefault();

                    let nonce = $(this).data('nonce'),
                        children = $(this).data('children');

                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'rs_save_countries',
                        'rem_text': children
                    }

                    $.post(ajaxurl, data, function(response) {
                        alert(response);
                        location.reload();
                    });

                });
            });
        </script>
<?php }
}

?>