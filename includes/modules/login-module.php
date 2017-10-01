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
        $this->error = [];
        $this->init_login();
    }

    public function validate()
    {
        if (!is_email($_REQUEST["login"]) && !validate_username($_REQUEST["login"])):
            $this->error["login"]["invalid"] = __('Login Invalid', 'polc');
        endif;
    }

    /**
     * Init login.
     */
    public function init_login()
    {
        $this->validate();

        if (!empty($this->error)):
            wp_send_json(["error" => $this->error]);
        endif;

        $login = wp_authenticate($_REQUEST["login"], $_REQUEST["password"]);

        if (is_wp_error($login)):
            $this->error = __("Invalid login credentials", 'polc');
        endif;

        if (!empty($this->error)):
            wp_send_json(["error" => $this->error]);
        endif;

        wp_logout();

        if (get_user_meta($login->ID, 'has_to_be_activated')):
            wp_send_json(["error" => __('Your account is not active!')]);
        endif;

        wp_signon(["user_login" => $_REQUEST["login"], "user_password" => $_REQUEST["password"]]);
        wp_send_json($login);
    }
}

new Polc_Login_Module();