<?php

/**
 * Regional stock status CSS
 */

trait RS_CSS
{
    /**
     * CSS
     *
     * @return void
     */
    public static function rs_css()
    { ?>
        <style>
            p.rs_form_container>label {
                margin: 0 0 10px 0 !important;
                width: 100% !important;
                font-size: 15px !important;
                font-weight: 500 !important;
                display: block !important;
                padding-left: 5px;
            }

            .rs_countries,
            .rs_os_text {
                width: 100% !important;
            }

            .rs_input_cont {
                padding-left: 10px;
                margin-bottom: 5px;
                overflow: auto;
            }

            p.rs_form_container {
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .rs_prod_ids_cont {
                width: 40%;
                overflow: auto;
                display: inline-block;
            }

            p.rs_button_container {
                margin-top: 0;
            }

            .rs_prod_ids,
            .rs_prod_text_ids {
                width: 100%;
                font-size: 13px;
            }

            .rs_countries_cont,
            .rs_text_cont {
                width: 40%;
                display: inline-block;
            }

            button#rs_save_countries,
            button#rs_save_text {
                width: 39%;
                font-size: 14px;
                font-weight: 500;
            }

            button#rs_remove_restrictions,
            button#rs_remove_text {
                width: 39%;
                position: relative;
                left: 30px;
                background: #cf0101;
                border-color: #cf0101;
                color: white;
                font-size: 14px;
                font-weight: 500;
            }
        </style>
<?php }
}

?>