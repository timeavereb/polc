<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 29.
 * Time: 20:36
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

switch($_REQUEST["action"]){
    case "retrieve":
        Polc_Lost_Password_Module::retrieve();
        break;
    case "reset":
        Polc_Lost_Password_Module::reset();
        break;
}

/**
 * Class Polc_Lost_Password_Module
 */
class Polc_Lost_Password_Module
{

    /**
     * Lost password init
     */
    public static function retrieve()
    {
        //If the credential is empty send error message to js.
        if ($_REQUEST["login"] == "") {
            wp_send_json(array("error" => __('Invalid credentials', 'polc')));
        }

        $login = $_REQUEST["login"];

        $user_id = email_exists($login);

        //If the credential is not email address lets check it as username
        if (!$user_id) {
            $user_id = username_exists($login);
            if (!$user_id) {
                wp_send_json(array("error" => __('The e-mail address or username does not exists.', 'polc')));
            }
        }

        //Check if the account is active
        $confirm = get_user_meta($user_id, 'has_to_be_activated');
        if ($confirm) {
            wp_send_json(array("error" => __("The account you're trying to retrieve is not yet active.", "polc")));
        }

        $user_data = get_userdata($user_id);
        //Let's load the lost password e-mail scheme
        $lost_pass_email = Polc_Settings_Manager::lost_password_email();

        //Generating reset code
        $code = sha1($user_id . time());
        $activation_link = add_query_arg(array('key' => $code, 'user' => $user_id), get_permalink(Polc_Settings_Manager::password_reset_page()));

        //Store the code and the valid time to the usermeta
        delete_user_meta($user_id, 'polc-pw-reset-code');
        delete_user_meta($user_id, 'plc-pw-reset-valid-till');

        add_user_meta($user_id, 'polc-pw-reset-code', $code, true);
        add_user_meta($user_id, 'plc-pw-reset-valid-till', date("c", strtotime("+ 1 day")), true);

        //Replace the e-mail scheme with real data
        $headers = array('From: ' . $lost_pass_email['sender_name'] . ' <' . $lost_pass_email['sender_email'] . '>');
        $body = $lost_pass_email["body"];
        $body = preg_replace("~#EMAIL#~", $user_data->user_email, $body);
        $body = preg_replace("~#USERDISPLAYNAME#~", $user_data->display_name, $body);
        $body = preg_replace("~#RESETURL#~", $activation_link, $body);

        //send mail and send response back to the js
        if (wp_mail($user_data->user_email, $lost_pass_email["subject"], $body, $headers)) {
            wp_send_json(array("success" => __('The information to reset your password has been sent to your e-mail address.', 'polc')));
        }
    }


    public static function reset(){

        if(!isset($_REQUEST["key"]) || !isset($_REQUEST["user_id"])){
            wp_send_json(array("error" => __('Invalid data', 'polc')));
        }

        if($_REQUEST["password"] != $_REQUEST["password_conf"]){
            wp_send_json(array("error" => __('The two must password has to be the same')));
        }

        $key = get_user_meta($_REQUEST["user_id"],"polc-pw-reset-code", true);
        if($_REQUEST["key"] != $key){
            wp_send_json(array("error" => __('The activation code is invalid.', 'polc')));
        }

        $valid_time = get_user_meta($_REQUEST["user_id"],"plc-pw-reset-valid-till",true);
        if(strtotime($valid_time) < strtotime (date("c"))){
            wp_send_json(array("error" => __('The activation code is has been expired.', 'polc')));
        }

        wp_set_password($_REQUEST["password"], $_REQUEST["user_id"]);

        delete_user_meta($_REQUEST["user_id"], "polc-pw-reset-code");
        delete_user_meta($_REQUEST["user_id"], "plc-pw-reset-valid-till");

        wp_send_json(array("success" => __( 'Your password has been changed!','polc')));
    }
}