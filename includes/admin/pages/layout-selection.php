<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 8:28
 */

class Polc_Layout_Selection{

    public function __construct()
    {
        add_action("admin_init", function(){
            add_meta_box("polc-layout-selection", __("Layout selection", "polc"), array($this, "render_meta_box"), "page", "side", "high" );
        });

        add_action("save_post", array($this, "save"));
    }

    public function render_meta_box($post){

        $selected_layout = get_post_meta($post->ID, "polc_layout");

        $selected_layout = count($selected_layout) > 0 ? $selected_layout[0] : "";

        $classes = get_declared_classes();
        $implementsIModule = array();

        foreach ($classes as $klass) {
            $reflect = new ReflectionClass($klass);
            if ($reflect->implementsInterface('Polc_Layout_Handler_IF') && !$reflect->isAbstract())
                $implementsIModule[] = $klass;
        }

        echo '<select name="polc_layout" id="polc_layout">';
        echo '<option value="" '. ($selected_layout == "" ? "selected" : "") .'>' . __('Default', 'polc') . '</option>';
        foreach ($implementsIModule as $class) {
            $current = new $class();
            echo '<option value="'. $current::POLC_LAYOUT .'" '. ($selected_layout == $current::POLC_LAYOUT  ? "selected" : "") .'>'. $current::POLC_LAYOUT_NAME .'</option>';
        }

        echo '</select>';
    }

    public function save($id){

        if(isset($_REQUEST["polc_layout"])){
            update_post_meta($id,"polc_layout", $_REQUEST["polc_layout"]);
        }
    }
}

new Polc_Layout_Selection();