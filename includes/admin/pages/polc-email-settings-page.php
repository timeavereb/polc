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
        if (isset($_REQUEST["submit"])):
            self::save();
        endif;

        $email = Polc_Settings_Manager::email();

        $templates = [
            "register" => [
                "section" => 1,
                "button_text" => __('Register mail', 'polc'),
                "placeholders" => [
                    ["id" => '#EMAIL#', "desc" => __("User's e-mail address", 'polc')],
                    ["id" => '#USERNAME#', "desc" => __("User's username", 'polc')],
                    ["id" => '#REGISTERDATE#', "desc" => __("User's registration date", 'polc')],
                    ["id" => '#ACTIVATIONURL#', "desc" => __("Activation url", 'polc')],
                ]
            ],
            "lost_password" => [
                "section" => 2,
                "button_text" => __('LostPass mail', 'polc'),
                "placeholders" => [
                    ["id" => '#EMAIL#', "desc" => __("User's e-mail address", 'polc')],
                    ["id" => '#USERDISPLAYNAME#', "desc" => __("User's display name", 'polc')],
                    ["id" => '#RESETURL#', "desc" => __("Password-reset url", 'polc')]
                ]
            ],
            "acceptance" => [
                "section" => 3,
                "button_text" => __('Content acceptance mail', 'polc'),
                "placeholders" => [
                    ["id" => '#TITLE#', "desc" => __('Content\'s title')],
                    ["id" => '#USERDISPLAYNAME#', "desc" => __("User's display name", 'polc')]
                ]
            ],
            "rejection" => [
                "section" => 4,
                "button_text" => __('Content rejection mail', 'polc'),
                "placeholders" => [
                    ["id" => '#TITLE#', "desc" => __('Content\'s title')],
                    ["id" => '#USERDISPLAYNAME#', "desc" => __("User's display name", 'polc')]
                ]
            ]
        ];

        ?>

        <h1><?= __('Email settings', 'polc'); ?></h1>
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
                    <?php
                    foreach ($templates as $template):
                        ?>
                        <td>
                            <button onclick="change_setion(<?= $template["section"]; ?>,this);"
                                    class="section_btn <?= $template["section"] == 1 ? "active" : ""; ?>"><?= $template["button_text"]; ?></button>
                        </td>
                        <?php
                    endforeach;
                    ?>
                </tr>
            </table>

            <div id="sections_wrapper">
                <?php foreach ($templates as $key => $template):
                    ?>
                    <div id="section_<?= $template["section"]; ?>" class="section" style="<?= $template["section"] != 1 ? "display:none;": ""; ?>">

                        <!-- register template -->
                        <div class="polc-email-setting-element">

                            <div class="polc-placeholder-wrapper">

                                <p><?= __("The following placeholder can be used in the message body", 'polc'); ?></p>
                                <?php
                                foreach ($template["placeholders"] as $placeholder):
                                    ?>
                                    <div class="polc-placeholder">
                                        <span><?= $placeholder["id"]; ?></span>
                                        <span class="polc-placeholder-desc"><?= $placeholder["desc"]; ?></span>
                                    </div>
                                    <?php
                                endforeach;
                                ?>
                            </div>

                            <div class="polc-email-element">
                                <label for="polc_<?= $key; ?>_sender-name"><?= __("Sender's name", 'polc'); ?></label>
                                <input type="text" id="polc_<?= $key; ?>_sender-name"
                                       name="polc_emails[<?= $key; ?>][sender_name]"
                                       value="<?= isset($email[$key]["sender_name"]) ? $email[$key]["sender_name"] : ""; ?>"
                                       size="70">
                            </div>

                            <div class="polc-email-element">
                                <label
                                    for="polc_<?= $key; ?>_sender-email"><?= __("Sender's email address", 'polc'); ?></label>
                                <input type="text" id="polc-reg-sender-email"
                                       name="polc_emails[<?= $key; ?>][sender_email]"
                                       value="<?= isset($email[$key]["sender_email"]) ? $email[$key]["sender_email"] : ""; ?>"
                                       size="70">
                            </div>

                            <div class="polc-email-element">
                                <label for="polc_<?= $key; ?>_subject"><?= __("Subject", 'polc'); ?></label>
                                <input type="text" id="polc_<?= $key; ?>_subject"
                                       name="polc_emails[<?= $key; ?>][subject]"
                                       value="<?= isset($email[$key]["subject"]) ? $email[$key]["subject"] : ""; ?>"
                                       size="70">
                            </div>

                            <div class="polc-email-element">
                                <label for="polc-reg-body"><?= __("Content", 'polc'); ?></label>
                                <?php
                                $body = isset($email[$key]["body"]) ? $email[$key]["body"] : "";
                                wp_editor($body, "polc_" . $key . "_body", [
                                    "media_buttons" => true,
                                    "textarea_name" => "polc_emails[" . $key . "][body]",
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php
            submit_button();
            ?>
        </form>
        <?php
    }

    public static function save()
    {
        $_REQUEST["polc_emails"]["register"]["body"] = apply_filters('the_content', stripslashes($_REQUEST["polc_emails"]["register"]["body"]));
        $_REQUEST["polc_emails"]["lost_password"]["body"] = apply_filters('the_content', stripslashes($_REQUEST["polc_emails"]["lost_password"]["body"]));
        $_REQUEST["polc_emails"]["acceptance"]["body"] = apply_filters('the_content', stripslashes($_REQUEST["polc_emails"]["acceptance"]["body"]));
        $_REQUEST["polc_emails"]["rejection"]["body"] = apply_filters('the_content', stripslashes($_REQUEST["polc_emails"]["rejection"]["body"]));

        update_option("polc-email-settings", json_encode($_REQUEST["polc_emails"]));
    }
}

new Polc_Email_Settings_Page();