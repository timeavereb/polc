<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 7:57
 */

/**
 * Class Polc_Activation_Layout_Handler
 */
class Polc_Activation_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-activation";
    CONST POLC_LAYOUT_NAME = "Aktiváció";


    public function render()
    {
        $user_id = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if ($user_id) {
            $code = get_user_meta($user_id, 'has_to_be_activated', true);
            if ($code == filter_input(INPUT_GET, 'key')) {
                delete_user_meta($user_id, 'has_to_be_activated');
                echo '<div class="plcSuccessNotification">';
                    echo __('Your account has been succesfuly confirmed!', 'polc');
                echo '</div>';
            }
        }
    }
}