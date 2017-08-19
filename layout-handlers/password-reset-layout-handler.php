<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 29.
 * Time: 20:03
 */
class Polc_Password_Reset_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-reset-password";
    CONST POLC_LAYOUT_NAME = "Elfelejtett jelszó";

    public function render()
    {
        ?>
        <div id="plc_password_reset_container">
            <form id="plc_password_reset_form">
                <input type="hidden" id="success" value="">
                <input type="hidden" id="key" name="key" value="<?= $_REQUEST["key"]; ?>">
                <input type="hidden" id="user_id" name="user_id" value="<?= $_REQUEST["user"]; ?>">

                <div class="plc_password_reset_element">
                    <input type="password" id="password" name="password" placeholder="<?= __('Password', 'polc'); ?>">
                </div>
                <div class="plc_password_reset_element">
                    <input type="password" id="password-conf" name="password-conf"
                           placeholder="<?= __('Password confirmation', 'polc'); ?>">
                </div>
                <div class="plc_password_reset_element">
                    <button id="plc_password_reset_ok"><?= __('Ok', 'polc'); ?></button>
                </div>
            </form>
        </div>

        <script>
            jQuery(document).ready(function () {
                jQuery(document).on("plc_alert_closed", function () {
                    if(jQuery("#success").val() == 1){
                        document.location.href="/";
                    }
                });
            });
        </script>
        <?php
    }
}