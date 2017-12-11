<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 11.
 * Time: 16:44
 */

/**
 * Class Polc_Main_Settings_Page
 */
class Polc_Main_Settings_Page
{
    /**
     * Polc_Main_Settings_Page constructor.
     */
    public function __construct()
    {
        add_action("admin_menu", function () {
            add_menu_page(__('Polc', 'polc'), __('Polc', 'polc'), POLC_SETTNGS_CAP, POLC_SETTINGS_PAGE, 'Polc_Main_Settings_Page::get_main', POLC_ICON_URL);
        });
    }

    public static function get_main()
    {
        $admin_url = admin_url();

        ?>
        <div id="polc-settings-main-wrapper">
            <!--settings selection -->
            <div class="polc-setting-selection-wrapper">
                <div class="polc-setting-select-element">
                    <a href="<?= $admin_url . "admin.php?page=" . POLC_SETTINGS_PAGE; ?>">
                        <?= __('Main settings', 'polc'); ?>
                    </a>
                </div>
                <div class="polc-setting-select-element">
                    <a href="<?= $admin_url . "admin.php?page=" . POLC_EMAIL_SETTINGS; ?>">
                        <?= __('Email settings', 'polc'); ?>
                    </a>
                </div>
                <div class="polc-setting-select-element">
                    <a href="<?= $admin_url . "admin.php?page=" . POLC_LAYOUT_SETTINGS; ?>">
                        <?= __('Layout settings', 'polc'); ?>
                    </a>
                </div>
            </div>
            <!-- settings container -->
            <div class="polc-settings-wrapper">
                <?php self::settings(); ?>
            </div>
        </div>
        <?php
    }

    public static function settings()
    {
        switch ($_REQUEST["page"]):
            case POLC_SETTINGS_PAGE:
                self::render();
                break;
            case POLC_EMAIL_SETTINGS:
                Polc_Email_Settings_Page::render();
                break;
            case POLC_LAYOUT_SETTINGS:
                Polc_Layout_Settings_Page::render();
                break;
        endswitch;
    }

    public static function render()
    {
        if (isset($_REQUEST["submit"])):
            self::save();
        endif;

        $pages = Polc_Settings_Manager::pages();
        $categories = Polc_Settings_Manager::categories();
        $common = Polc_Settings_Manager::common();
        ?>

        <h1><?= __('Main settings', 'polc'); ?></h1>
        <form action="<?= admin_url() . "admin.php?page=" . POLC_SETTINGS_PAGE; ?>" method="POST">
            <p>
                <label><?= __('Story content main category', 'polc'); ?></label>
                <?php
                wp_dropdown_categories([
                    "id" => "story-main-category",
                    "name" => "categories[story-main]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "hierarchical" => true,
                    "hide_empty" => false,
                    "selected" => isset($categories["story-main"]) ? $categories["story-main"] : 0
                ]);
                ?>
            </p>

            <p>
                <label><?= __('Story content sub-category', 'polc'); ?></label>
                <?php
                wp_dropdown_categories([
                    "id" => "story-main-category",
                    "name" => "categories[story-sub]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "hierarchical" => true,
                    "hide_empty" => false,
                    "selected" => isset($categories["story-sub"]) ? $categories["story-sub"] : 0
                ]);
                ?>
            </p>

            <p>
                <label><?= __('Help category', 'polc'); ?></label>
                <?php
                wp_dropdown_categories([
                    "id" => "help-category",
                    "name" => "categories[help-category]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "hierarchical" => true,
                    "hide_empty" => false,
                    "selected" => isset($categories["help-category"]) ? $categories["help-category"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="new-story-page"><?= __('New story content page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "new-story-page",
                    "name" => "pages[new-story-page]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["new-story-page"]) ? $pages["new-story-page"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="comment_page"><?= __('Comment list page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "comment_page",
                    "name" => "pages[comment_page]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["comment_page"]) ? $pages["comment_page"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="new-story-page"><?= __('Content editor page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "content-editor-page",
                    "name" => "pages[content-editor-page]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["content-editor-page"]) ? $pages["content-editor-page"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="reg-activation"><?= __('Activation page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "reg-activation",
                    "name" => "pages[reg-activation]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["reg-activation"]) ? $pages["reg-activation"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="reg-activation"><?= __('Password-reset page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "password-reset",
                    "name" => "pages[password-reset]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["password-reset"]) ? $pages["password-reset"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="news-list"><?= __('News list page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "news-list",
                    "name" => "pages[news-list]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["news-list"]) ? $pages["news-list"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="recommendation-list"><?= __('Recommendation list page', 'polc'); ?></label>
                <?php
                wp_dropdown_pages([
                    "id" => "recommendation-list",
                    "name" => "pages[recommendation-list]",
                    "show_option_none" => __('None selected', 'polc'),
                    "option_none_value" => 0,
                    "selected" => isset($pages["recommendation-list"]) ? $pages["recommendation-list"] : 0
                ]);
                ?>
            </p>

            <p>
                <label for="plc_ga_id"><?= __( 'Google analytics tracking ID', 'polc' );?></label>
                <input type="text" id="plc_ga_id" name="common[ga_id]" size="50" value="<?= isset($common["ga_id"]) ? $common["ga_id"] : ""; ?>">
            </p>

            <p>
                <label for="plc_recaptcha_key"><?= __( 'reCaptcha site key', 'polc' );?></label>
                <input type="text" id="plc_recaptcha_key" name="common[recaptcha_key]" size="50" value="<?= isset($common["recaptcha_key"]) ? $common["recaptcha_key"] : ""; ?>">
            </p>

            <p>
                <label for="plc_recaptcha_secret"><?= __( 'reCaptcha site secret', 'polc' );?></label>
                <input type="password" id="plc_recaptcha_secret" name="common[recaptcha_secret]" size="50" value="<?= isset($common["recaptcha_secret"]) ? $common["recaptcha_secret"] : ""; ?>">
            </p>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    private static function save()
    {
        update_option("polc-page-settings", $_REQUEST["pages"], true);
        update_option("polc-category-settings", $_REQUEST["categories"], true);
        update_option("polc-common-settings", $_REQUEST["common"], true);
    }
}

new Polc_Main_Settings_Page();