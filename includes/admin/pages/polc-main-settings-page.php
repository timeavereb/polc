<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 11.
 * Time: 16:44
 */
class Polc_Main_Settings_Page
{

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
        switch ($_REQUEST["page"]) {
            case POLC_SETTINGS_PAGE:
                self::render();
                break;
            case POLC_EMAIL_SETTINGS:
                Polc_Email_Settings_Page::render();
                break;
            case POLC_LAYOUT_SETTINGS:
                Polc_Layout_Settings_Page::render();
                break;
        }
    }

    public static function render()
    {
        if (isset($_REQUEST["submit"])):
            update_option("polc-page-settings", $_REQUEST["pages"]);
            update_option("polc-category-settings", $_REQUEST["categories"]);
        endif;

        $pages = Polc_Settings_Manager::pages();
        $categories = Polc_Settings_Manager::categories();
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
            <?php submit_button(); ?>
        </form>
        <?php
    }
}

new Polc_Main_Settings_Page();