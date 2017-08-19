<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 05.
 * Time: 18:05
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

/**
 * Class Polc_Login
 */
class Polc_Login_Module
{
    private $error;

    /**
     * Polc_Login constructor.
     */
    public function __construct()
    {
        $this->error = array();
        $this->init_login();
    }

    public function validate()
    {
        if (!is_email($_REQUEST["login"]) && !validate_username($_REQUEST["login"])) {
            $this->error["login"]["invalid"] = __('Login Invalid', 'polc');
        }
    }

    /**
     * Init login.
     */
    public function init_login()
    {
        $this->validate();

        if (!empty($this->error)) {
            wp_send_json(array("error" => $this->error));
        }

        $login = wp_authenticate($_REQUEST["login"], $_REQUEST["password"]);

        if (is_wp_error($login)) {
            $this->error =  __("Invalid login credentials", 'polc');
        }

        if (!empty($this->error)) {
            wp_send_json(array("error" => $this->error));
        }

        wp_logout();

        if(get_user_meta($login->ID, 'has_to_be_activated')){
            wp_send_json(array("error" => __('Your account is not active!')));
        }

        wp_signon(array("user_login" => $_REQUEST["login"], "user_password" => $_REQUEST["password"]));
        wp_send_json($login);
    }
}

new Polc_Login_Module();