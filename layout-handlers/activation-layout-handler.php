<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 7:57
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Activation_Layout_Handler
 */
class Polc_Activation_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-activation";
    CONST POLC_LAYOUT_NAME = "Aktiváció";

    /**
     * Inits user acivation.
     */
    public function render()
    {
        $user_id = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($user_id):
            $code = get_user_meta($user_id, 'has_to_be_activated', true);
            if ($code == filter_input(INPUT_GET, 'key')):
                delete_user_meta($user_id, 'has_to_be_activated');
                ?>
                <div class="plcSuccessNotification">
                    <?= __('Your account has been succesfuly confirmed!', 'polc'); ?>
                </div>
                <?php
            endif;
        endif;
    }
}