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
        if (isset($_REQUEST["submit"])) {
            self::save();
        }

        $layout_settings = Polc_Settings_Manager::layout();

        echo '<h1>' . __('Layout settings', 'polc') . '</h1>';
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
                </tr>
            </table>

            <div id="sections_wrapper">

                <div id="section_1" class="section">

                    <?php

                    echo '<p class="polc-settings-element">' . PHP_EOL;
                    echo '<label>'. __('Number of stroies', 'polc') .'</label>';
                    echo '<input type="number" name="stories[count]" value="' . (!isset($layout_settings["stories"]["count"]) ? 10 : $layout_settings["stories"]["count"]) . '">' . PHP_EOL;
                    echo '</p>';

                    $args = array(
                        "show_option_none" => __("No category selected", 'polc'),
                        "hide_empty" => false,
                        "hierarchical" => true,
                        "class" => "polc-category-selector",
                        "option_none_value" => 0
                    );

                    $args["selected"] = !isset($layout_settings["news"]["term_id"]) ? 3 : $layout_settings["news"]["term_id"];
                    $args["name"] = "news[term_id]";

                    echo '<p class="polc-settings-element">' . PHP_EOL;
                    echo '<label>'. __('Number of news', 'polc') .'</label>';
                    echo '<input type="number" name="news[count]" value="' . (!isset($layout_settings["news"]["count"]) ? 0 : $layout_settings["news"]["count"]) . '">' . PHP_EOL;

                    echo '<label>'. __('News category', 'polc') .'</label>';
                    wp_dropdown_categories($args);
                    echo '</p>' . PHP_EOL;


                    echo '<p class="polc-settings-element">' . PHP_EOL;
                    echo '<label>'. __('Number of recommendation', 'polc') .'</label>';
                    echo '<input type="number" name="recommend[count]" value="' . (!isset($layout_settings["recommend"]["count"]) ? 0 : $layout_settings["recommend"]["count"]) . '">' . PHP_EOL;

                    $args["selected"] = !isset($layout_settings["recommend"]["term_id"]) ? 3 : $layout_settings["recommend"]["term_id"];
                    $args["name"] = "recommend[term_id]";

                    echo '<label>'. __('Recommendation category', 'polc') .'</label>';
                    wp_dropdown_categories($args);
                    echo '</p>' . PHP_EOL;

                    ?>
                </div>

                <div id="section_2" class="section" style="display: none;">
                    author layout settings
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    public static function save()
    {
        $update = array();
        $allowed = array("stories", "news", "recommend");

        foreach ($_REQUEST as $key => $value) {
            if (in_array($key, $allowed)) {
                $update[$key] = $value;
            }
        }

        update_option("polc-layout-settings", $update);
    }
}

new Polc_Layout_Settings_Page();