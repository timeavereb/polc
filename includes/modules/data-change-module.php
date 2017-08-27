<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 08. 19.
 * Time: 14:17
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

Polc_Helper_Module::is_logged();

if(!isset($_REQUEST["plc_user_data_change_nonce"]) || !wp_verify_nonce($_REQUEST["plc_user_data_change_nonce"],"plc_user_data_change")):
    wp_send_json(array("error" => "Invalid identification!"));
endif;

$data_change = new Polc_Data_Change_Module();

class Polc_Data_Change_Module
{
    public static $user;
    private $password_change = false;
    private $validate_error = false;

    public function __construct()
    {
        self::$user = wp_get_current_user();
        $this->init();
    }

    private function init()
    {
        $validate = $this->validate_params();

        //if there was any error we send message and quit.
        if ($this->validate_error) {
            wp_send_json(array("error" => $validate));
        }

        if ($this->password_change):
            $this->update_password();
        endif;

        $this->update_user_data();
        $this->update_user_meta();

        $response = array(
            "success" => "ok",
            "password_change" => $this->password_change
        );

        $msg = __("You've successfully changed your data!", "polc");

        if ($this->password_change):
            $msg .= "<br>" . __("Your password has been changed!", "polc");
        endif;

        $response["message"] = $msg;

        wp_send_json($response);
    }

    private function validate_params()
    {
        //if the user set old and new password
        if (isset($_REQUEST["plc_old_password"]) && $_REQUEST["plc_old_password"] != ""
            && isset($_REQUEST["plc_new_password"]) && $_REQUEST["plc_new_password"] != ""
        ) {

            //if the user isn't providing confirmation
            if (!isset($_REQUEST["plc_new_password_conf"]) || $_REQUEST["plc_new_password_conf"] == "") {
                $this->validate_error = true;
                return __("You have to confirm your new password!", "polc");
            }

            //validates that the new password is same with the confirm password
            if ($_REQUEST["plc_new_password"] != $_REQUEST["plc_new_password_conf"]) {
                $this->validate_error = true;
                return __("The two passwords are not equal!", "polc");
            }

            //validates old password
            if (!wp_check_password($_REQUEST["plc_old_password"], self::$user->user_pass, self::$user->ID)) {
                $this->validate_error = true;
                return __("Your old password is not valid!", "polc");
            }

            if (strlen($_REQUEST["plc_new_password"]) < 6) {
                $this->validate_error = true;
                return __("The password has to be at least 6 characters long!", "polc");
            }

            $this->password_change = true;
        }

        //user's age validation
        if (isset($_REQUEST["plc_user_birth_date"]) && $_REQUEST["plc_user_birth_date"] != "") {

            $birth_date = rtrim($_REQUEST["plc_user_birth_date"], ".");

            $date_arr = explode(".", $birth_date);

            if (!is_array($date_arr) || count($date_arr) != 3) {
                $this->validate_error = true;
                return __("Invalid birth date format!", "polc");
            }

            if (!checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                $this->validate_error = true;
                return __("Invalid birth date format!", "polc");
            }

            if (date("c") < date("c", strtotime(str_replace(".", "-", $birth_date)))) {
                $this->validate_error = true;
                return __( "Your birth date can't be farther than the current date!", "polc");
            }
        }

        return true;
    }

    private function update_user_data()
    {
        $data = array(
            "ID" => self::$user->ID,
            "display_name" => $_REQUEST["plc_user_display_name"],
            "user_url" => $_REQUEST["plc_user_url"]
        );

        wp_update_user($data);
    }

    private function update_user_meta()
    {
        update_user_meta(self::$user->ID, "user_birth_date", $_REQUEST["plc_user_birth_date"]);
    }

    /**
     * Set the new password for the user
     */
    private function update_password()
    {
        wp_set_password($_REQUEST["plc_new_password"], self::$user->ID);
    }
}