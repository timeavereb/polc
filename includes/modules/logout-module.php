<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 11.
 * Time: 13:39
 */


require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

if(is_user_logged_in()){
    wp_logout();
    wp_send_json(array("success" => "true"));
}