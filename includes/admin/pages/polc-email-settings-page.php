<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 9:52
 */
class Polc_Email_Settings_Page
{

    public function __construct()
    {
        add_action("admin_menu", function () {
            add_submenu_page("", __('Polc', 'polc'), __('Polc', 'polc'), POLC_SETTNGS_CAP, POLC_EMAIL_SETTINGS, 'Polc_Email_Settings_Page::get_main');
        });
    }

    public static function get_main()
    {
        Polc_Main_Settings_Page::get_main();
    }

    public static function render()
    {
        if (isset($_REQUEST["submit"])) {
            self::save();
        }

        $reg_email = Polc_Settings_Manager::register_email();
        $lost_pass_email = Polc_Settings_Manager::lost_password_email();

        echo '<h1>' . __('Email settings', 'polc') . '</h1>';
        ?>

        <script type="text/javascript">

            jQuery(document).ready(function () {
                jQuery(".section_btn").click(function (e) {
                    e.preventDefault();
                })
            });

            function change_setion(section, sender) {

                jQuery(".section").hide();
                jQuery("#section_" + section).show();
                jQuery(".section_btn").removeClass("active");
                jQuery(sender).addClass("active");
            }

        </script>

        <form action="<?= admin_url() . "admin.php?page=" . POLC_EMAIL_SETTINGS; ?>" method="POST">

            <table>
                <tr>
                    <td>
                        <button onclick="change_setion(1,this);"
                                class="section_btn active"><?= __('Register mail', 'polc'); ?></button>
                    </td>
                    <td>
                        <button onclick="change_setion(2,this);"
                                class="section_btn"><?= __('LostPass mail', 'polc'); ?></button>
                    </td>
                </tr>
            </table>

            <div id="sections_wrapper">

                <div id="section_1" class="section">

                    <div class="polc-email-setting-element">

                        <div class="polc-placeholder-wrapper">

                            <p><?= __("The following placeholder can be used in the message body", 'polc'); ?></p>

                            <div class="polc-placeholder">
                                <span>#EMAIL#</span>
                                <span class="polc-placeholder-desc"><?= __("User's e-mail address", 'polc') ?></span>
                            </div>
                            <div class="polc-placeholder">
                                <span>#USERNAME#</span>
                                <span class="polc-placeholder-desc"><?= __("User's username", 'polc') ?></span>
                            </div>
                            <div class="polc-placeholder">
                                <span>#REGISTERDATE#</span>
                                <span class="polc-placeholder-desc"><?= __("User's registration date", 'polc') ?></span>
                            </div>
                            <div class="polc-placeholder">
                                <span>#ACTIVATIONURL#</span>
                                <span class="polc-placeholder-desc"><?= __("Activation url", 'polc') ?></span>
                            </div>
                        </div>

                        <div class="polc-email-element">
                            <label for="polc-reg-sender-name"><?= __("Sender's name", 'polc'); ?></label>
                            <input type="text" id="polc-reg-sender-name" name="polc_emails[register][sender_name]"
                                   value="<?= (isset($reg_email["sender_name"]) ? $reg_email["sender_name"] : ""); ?>"
                                   size="70">
                        </div>

                        <div class="polc-email-element">
                            <label for="polc-reg-sender-email"><?= __("Sender's email address", 'polc'); ?></label>
                            <input type="text" id="polc-reg-sender-email" name="polc_emails[register][sender_email]"
                                   value="<?= (isset($reg_email["sender_email"]) ? $reg_email["sender_email"] : ""); ?>"
                                   size="70">
                        </div>

                        <div class="polc-email-element">
                            <label for="polc-reg-subject"><?= __("Subject", 'polc'); ?></label>
                            <input type="text" id="polc-reg-subject" name="polc_emails[register][subject]"
                                   value="<?= (isset($reg_email["subject"]) ? $reg_email["subject"] : ""); ?>"
                                   size="70">
                        </div>

                        <div class="polc-email-element">
                            <label for="polc-reg-body"><?= __("Content", 'polc'); ?></label>
                            <?php
                            $body = isset($reg_email["body"]) ? $reg_email["body"] : "";
                            wp_editor($body, "polc-reg-body", array(
                                "media_buttons" => false,
                                "textarea_name" => "polc_emails[register][body]",
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div id="section_2" class="section" style="display: none;">
                    <div class="polc-email-setting-element">

                        <div class="polc-placeholder-wrapper">

                            <p><?= __("The following placeholder can be used in the message body", 'polc'); ?></p>

                            <div class="polc-placeholder">
                                <span>#EMAIL#</span>
                                <span class="polc-placeholder-desc"><?= __("User's e-mail address", 'polc') ?></span>
                            </div>
                            <div class="polc-placeholder">
                                <span>#USERDISPLAYNAME#</span>
                                <span class="polc-placeholder-desc"><?= __("User's display name", 'polc') ?></span>
                            </div>
                            <div class="polc-placeholder">
                                <span>#RESETURL#</span>
                                <span class="polc-placeholder-desc"><?= __("Password-reset url", 'polc') ?></span>
                            </div>
                        </div>

                        <?php
                        $reg_email = Polc_Settings_Manager::register_email();
                        ?>

                        <div class="polc-email-element">
                            <label for="polc-lost-password-sender-name"><?= __("Sender's name", 'polc'); ?></label>
                            <input type="text" id="polc-lost-pass-sender-name"
                                   name="polc_emails[lost_password][sender_name]"
                                   value="<?= (isset($lost_pass_email["sender_name"]) ? $lost_pass_email["sender_name"] : ""); ?>"
                                   size="70">
                        </div>

                        <div class="polc-email-element">
                            <label
                                for="polc-lost-password-sender-email"><?= __("Sender's email address", 'polc'); ?></label>
                            <input type="text" id="polc-lost-password-sender-email"
                                   name="polc_emails[lost_password][sender_email]"
                                   value="<?= (isset($lost_pass_email["sender_email"]) ? $lost_pass_email["sender_email"] : ""); ?>"
                                   size="70">
                        </div>

                        <div class="polc-email-element">
                            <label for="polc-lost-password-subject"><?= __("Subject", 'polc'); ?></label>
                            <input type="text" id="polc-lost-password-subject"
                                   name="polc_emails[lost_password][subject]"
                                   value="<?= (isset($lost_pass_email["subject"]) ? $lost_pass_email["subject"] : ""); ?>"
                                   size="70">
                        </div>

                        <div class="polc-email-element">
                            <label for="polc-lost-password-body"><?= __("Content", 'polc'); ?></label>
                            <?php
                            $body = isset($lost_pass_email["body"]) ? $lost_pass_email["body"] : "";
                            wp_editor($body, "polc-lost-password-body", array(
                                "media_buttons" => false,
                                "textarea_name" => "polc_emails[lost_password][body]",
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    public static function save()
    {
        $_REQUEST["polc_emails"]["register"]["body"] = apply_filters('the_content', $_REQUEST["polc_emails"]["register"]["body"]);
        $_REQUEST["polc_emails"]["lost_password"]["body"] = apply_filters('the_content', $_REQUEST["polc_emails"]["lost_password"]["body"]);
        update_option("polc-email-settings", json_encode($_REQUEST["polc_emails"]));
    }
}

new Polc_Email_Settings_Page();