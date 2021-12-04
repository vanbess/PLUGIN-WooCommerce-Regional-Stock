<?php

/**
 * Regional stock status AJAX
 */

trait RS_AJAX
{

    /**
     * AJAX function to save countries to product meta
     *
     * @return void
     */
    public static function rs_save_countries()
    {

        check_ajax_referer('rs save countries');

        // ******************
        // save restrictions
        // ******************
        if (isset($_POST['countries'])) :

            $prod_ids  = $_POST['prod_ids'];
            $countries = $_POST['countries'];

            $combined = array_combine($prod_ids, $countries);

            $updated = [];

            foreach ($combined as $pid => $codes) :
                $updated[] = update_post_meta($pid, 'rs_countries', $codes);
            endforeach;

            if (!empty($updated)) :
                wp_send_json(__('Countries updated', 'woocommerce'));
            endif;
        endif;

        // ************************
        // remove all restrictions
        // ************************
        if (isset($_POST['children'])) :

            $cids = explode(',', $_POST['children']);

            foreach ($cids as $cid) :
                $meta_deleted = delete_post_meta($cid, 'rs_countries');
            endforeach;

            if ($meta_deleted) :
                wp_send_json(__('All stock restrictions have been removed', 'woocommerce'));
            endif;

        endif;

        // ******************************
        // save custom out of stock text
        // ******************************
        if (isset($_POST['texts'])) :

            $prod_ids = $_POST['prod_ids'];
            $texts    = $_POST['texts'];

            $combined = array_combine($prod_ids, $texts);

            foreach ($combined as $pid => $text) :
                $updated = update_post_meta($pid, 'rs_text', $text);
            endforeach;

            if ($updated) :
                wp_send_json(__('Custom out of stock text(s) updated', 'woocommerce'));
            endif;

        endif;

        // ********************************
        // remove custom out of stock text
        // ********************************
        if (isset($_POST['rem_text'])) :

            $prod_ids = explode(',', $_POST['rem_text']);

            foreach ($prod_ids as $pid) :
                $remmed = delete_post_meta($pid, 'rs_text');
            endforeach;

            if ($remmed) :
                wp_send_json(__('Custom out of stock text(s) removed', 'woocommerce'));
            endif;

        endif;

        wp_die();
    }
}
