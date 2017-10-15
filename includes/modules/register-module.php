<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 05.
 * Time: 13:24
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

/**
 * Class Polc_Register
 */
class Polc_Register_Module
{
    private $user;
    private $data;
    private $error;

    /**
     * Polc_Register constructor.
     */
    public function __construct()
    {
        $this->data = $_REQUEST;
        $this->error = [];
        $this->init_register();
    }

    /**
     * Bevitt adatok validálása
     */
    public function validate()
    {
        if (!is_email($_REQUEST["email"])):
            $this->error["email"]["invalid"] = __('Email Invalid', 'polc');
        endif;

        if (!validate_username($_REQUEST["username"])):
            $this->error["username"]["invalid"] = __('Username Invalid', 'polc');
        endif;

        if (username_exists($_REQUEST["username"])):
            $this->error["username"]["taken"] = __('Username Taken', 'polc');
        endif;

        if (email_exists($_REQUEST["email"])):
            $this->error["email"]["taken"] = __('Email Taken', 'polc');
        endif;

        if ($_REQUEST["password"] == ""):
            $this->error["password"]["invalid"] = __('Invalid Password', 'polc');
        endif;

        if ($_REQUEST["password"] != $_REQUEST["password-conf"]):
            $this->error["password"]["invalid"] = __('Passwords Are Not The Same', 'polc');
        endif;

        if ($_REQUEST["email"] != $_REQUEST["email-conf"]):
            $this->error["email"]["invalid"] = __('Emails Are Not The Same', 'polc');
        endif;
    }

    /**
     * Regisztráció kezdeményezése
     * @return stdClass|void
     */
    public function init_register()
    {
        $this->validate();

        if (!empty($this->error)):
            wp_send_json(["error" => $this->error]);
        endif;

        $this->insert_user();

        if (!$this->user && is_wp_error($this->user)):
            $this->error["user"] = true;
        endif;

        if (!empty($this->error)):
            wp_send_json(["error" => $this->error]);
        endif;

        $this->activation();

        wp_send_json(["success" => __('Register Success Message', 'polc')]);
    }

    /**
     * A felhasználó beszúrását végző
     * @return int|WP_Error
     */
    public function insert_user()
    {
        $this->data = [
            "user_login" => $_REQUEST["username"],
            "user_email" => $_REQUEST["email"],
            "user_pass" => $_REQUEST["password"],
            "user_registered" => date("Y-m-d H:i:s"),
            "show_admin_bar_front" => false,
            "role" => "polc_frontend_user"
        ];

        try {
            $this->user = wp_insert_user($this->data);
        } catch (Exception $e) {
            $this->error["exception"][] = $e->getMessage();
        }
    }

    /**
     * Sends actvation e-mail.
     */
    public function activation()
    {
        $register = Polc_Settings_Manager::register_email();
        $code = sha1($this->user . time());
        $activation_link = add_query_arg(['key' => $code, 'user' => $this->user], get_permalink(Polc_Settings_Manager::activation_page()));
        add_user_meta($this->user, 'has_to_be_activated', $code, true);

        $headers = ['From: ' . $register['sender_name'] . ' <' . $register['sender_email'] . '>'];
        $body = $register["body"];
        $body = preg_replace("~#EMAIL#~", $_REQUEST["email"], $body);
        $body = preg_replace("~#USERNAME#~", $_REQUEST["username"], $body);
        $body = preg_replace("~#REGISTERDATE#~", date("Y.m.d."), $body);
        $body = preg_replace("~#ACTIVATIONURL#~", $activation_link, $body);

        wp_mail($this->data['user_email'], $register["subject"], $body, $headers);
    }
}

new Polc_Register_Module();