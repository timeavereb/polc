<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 25.
 * Time: 18:14
 */
class Polc_Layout_Settings_Page
{

    public function __construct()
    {
        add_action("admin_menu", function () {
            add_submenu_page("", __('Polc', 'polc'), __('Polc', 'polc'), POLC_SETTNGS_CAP, POLC_LAYOUT_SETTINGS, 'Polc_Layout_Settings_Page::get_main');
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

        $layout_settings = Polc_Settings_Manager::layout();
        ?>

        <h1><?= __('Layout settings', 'polc'); ?></h1>
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

        <form action="<?= admin_url() . "admin.php?page=" . POLC_LAYOUT_SETTINGS; ?>" method="POST">
            <table>
                <tr>
                    <td>
                        <button onclick="change_setion(1,this);"
                                class="section_btn active"><?= __('Main page', 'polc'); ?></button>
                    </td>
                    <td>
                        <button onclick="change_setion(2,this);"
                                class="section_btn"><?= __('Author page', 'polc'); ?></button>
                    </td>
                    <td>
                        <button onclick="change_setion(3,this);"
                                class="section_btn"><?= __('Top-lists page', 'polc'); ?></button>
                    </td>
                </tr>
            </table>

            <div id="sections_wrapper">
                <!-- main page settings-->
                <div id="section_1" class="section">
                    <!-- stories settings-->
                    <p class="polc-settings-element">
                        <label><?= __('Number of stroies', 'polc'); ?></label>
                        <input type="number" name="stories[count]"
                               value="<?= !isset($layout_settings["stories"]["count"]) ? 10 : $layout_settings["stories"]["count"]; ?>">
                    </p>
                    <?php
                    $args = [
                        "show_option_none" => __("No category selected", 'polc'),
                        "hide_empty" => false,
                        "hierarchical" => true,
                        "class" => "polc-category-selector",
                        "option_none_value" => 0
                    ];

                    $args["selected"] = !isset($layout_settings["news"]["term_id"]) ? 3 : $layout_settings["news"]["term_id"];
                    $args["name"] = "news[term_id]";
                    ?>
                    <!-- news settings -->
                    <p class="polc-settings-element">
                        <label><?= __('Number of news', 'polc'); ?></label>
                        <input type="number" name="news[count]"
                               value="<?= !isset($layout_settings["news"]["count"]) ? 0 : $layout_settings["news"]["count"]; ?>">

                        <label><?= __('News category', 'polc'); ?></label>
                        <?php
                        wp_dropdown_categories($args);
                        ?>
                    </p>

                    <!-- recommendations settings -->
                    <p class="polc-settings-element">
                        <label><?= __('Number of recommendation', 'polc'); ?></label>
                        <input type="number" name="recommend[count]"
                               value="<?= !isset($layout_settings["recommend"]["count"]) ? 0 : $layout_settings["recommend"]["count"]; ?>">
                        <?php
                        $args["selected"] = !isset($layout_settings["recommend"]["term_id"]) ? 3 : $layout_settings["recommend"]["term_id"];
                        $args["name"] = "recommend[term_id]";
                        ?>
                        <label><?= __('Recommendation category', 'polc'); ?></label>
                        <?php
                        wp_dropdown_categories($args);
                        ?>
                    </p>

                </div>
                <!-- author page settings-->
                <div id="section_2" class="section" style="display: none;">
                    author layout settings
                </div>

                <!-- top-lists page settings-->
                <div id="section_3" class="section" style="display: none;">

                    <!-- toplist limit settings-->
                    <p>
                        <label><?= __('Favorite authors limit', 'polc'); ?></label>
                        <input type="number" min="1" name="toplists[authors_cnt]"
                               value="<?= !isset($layout_settings["toplists"]["authors_cnt"]) ? 10 : $layout_settings["toplists"]["authors_cnt"]; ?>">

                        <label><?= __('Top commenters limit', 'polc'); ?></label>
                        <input type="number" min="1" name="toplists[commenters_cnt]"
                               value="<?= !isset($layout_settings["toplists"]["commenters_cnt"]) ? 10 : $layout_settings["toplists"]["commenters_cnt"]; ?>">

                        <label><?= __('Favorite stories limit', 'polc'); ?></label>
                        <input type="number" min="1" name="toplists[stories_cnt]"
                               value="<?= !isset($layout_settings["toplists"]["stories_cnt"]) ? 10 : $layout_settings["toplists"]["stories_cnt"]; ?>">

                        <label><?= __('Most viewed stories', 'polc'); ?></label>
                        <input type="number" min="1" name="toplists[top_views_cnt]"
                               value="<?= !isset($layout_settings["toplists"]["top_views_cnt"]) ? 10 : $layout_settings["toplists"]["top_views_cnt"]; ?>">
                    </p>

                    <!-- toplist cache settings-->
                    <p>
                        <label><?= __("Top-lists cache"); ?></label>
                        <input type="checkbox" name="toplists[cache]"
                            <?= !isset($layout_settings["toplists"]["cache"]) || $layout_settings["toplists"]["cache"] != "" ? "checked" : ""; ?>>

                        <label><?= __("Top-lists cache time in seconds"); ?></label>
                        <input type="number" name="toplists[cache_time]"
                               value="<?= !isset($layout_settings["toplists"]["cache_time"]) ? 120 : $layout_settings["toplists"]["cache_time"]; ?>">
                    </p>
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    public static function save()
    {
        $update = [];
        $allowed = ["stories", "news", "recommend", "toplists"];

        $_REQUEST["toplists"]["cache"] = !isset($_REQUEST["toplists"]["cache"]) ? "" : $_REQUEST["toplists"]["cache"];

        foreach ($_REQUEST as $key => $value):
            if (in_array($key, $allowed)):
                $update[$key] = $value;
            endif;
        endforeach;

        update_option("polc-layout-settings", $update);
    }
}

new Polc_Layout_Settings_Page();