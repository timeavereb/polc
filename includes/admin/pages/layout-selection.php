<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 8:28
 */

/**
 * Class Polc_Layout_Selection
 */
class Polc_Layout_Selection
{

    /**
     * Polc_Layout_Selection constructor.
     */
    public function __construct()
    {
        add_action("admin_init", function () {
            add_meta_box("polc-layout-selection-page", __("Layout selection", "polc"), [$this, "render_meta_box"], "page", "side", "high");
            add_meta_box("polc-layout-selection-post", __("Layout selection", "polc"), [$this, "render_meta_box"], "post", "side", "high");
        });

        add_action("save_post", [$this, "save"]);
    }

    /**
     * @param $post
     */
    public function render_meta_box($post)
    {
        $selected_layout = get_post_meta($post->ID, "polc_layout");
        $selected_layout = count($selected_layout) > 0 ? $selected_layout[0] : "";

        $classes = get_declared_classes();
        $implementsIModule = [];

        foreach ($classes as $klass):
            $reflect = new ReflectionClass($klass);
            if ($reflect->implementsInterface('Polc_Layout_Handler_IF') && !$reflect->isAbstract()):
                $implementsIModule[] = $klass;
            endif;
        endforeach;

        ?>
        <select name="polc_layout" id="polc_layout">
            <option value="" <?= ($selected_layout == "" ? "selected" : ""); ?>><?= __('Default', 'polc'); ?></option>
            <?php
            foreach ($implementsIModule as $class):
                $current = new $class();
                ?>
                <option
                    value="<?= $current::POLC_LAYOUT ?>" <?= ($selected_layout == $current::POLC_LAYOUT ? "selected" : ""); ?>><?= $current::POLC_LAYOUT_NAME; ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * @param $id
     */
    public function save($id)
    {
        if (isset($_REQUEST["polc_layout"])):
            update_post_meta($id, "polc_layout", $_REQUEST["polc_layout"]);
        endif;
    }
}

new Polc_Layout_Selection();