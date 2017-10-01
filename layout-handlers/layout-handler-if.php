<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 7:48
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Interface Polc_Layout_Handler_IF
 */
interface Polc_Layout_Handler_IF{

    public function render();

}