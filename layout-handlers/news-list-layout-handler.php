<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 10. 01.
 * Time: 13:39
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Class Polc_Toplists_Layout_Handler
 */
class Polc_News_List_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-news";
    CONST POLC_LAYOUT_NAME = "Hírek/Ajánlók";

    private $paged;
    private $max;
    private $ppp;
    private $total_pages;
    private $total_items;

    public function render()
    {
        global $post;
        if ($post->ID == Polc_Settings_Manager::pages()["news-list"]):
            $category = Polc_Settings_Manager::layout()["news"]["term_id"];
        else:
            $category = Polc_Settings_Manager::layout()["recommend"]["term_id"];
        endif;

        $this->max = 999999999;
        $this->paged = (get_query_var('page')) ? get_query_var('page') : 1;
        $this->ppp = 1;

        $args = [
            "post_type" => "post",
            "post_status" => "publish",
            "category__in" => [$category],
            "posts_per_page" => $this->ppp,
            "paged" => $this->paged
        ];

        $query = new WP_Query($args);
        $posts = $query->get_posts();

        $this->total_items = $query->found_posts;
        $this->total_pages = round($this->total_items / $this->ppp);

        foreach ($posts as $post):
            echo $post->post_title . "<br>";
        endforeach;

        ?>

        <form id="plcSearchForm" method="POST">
            <input type="hidden" id="page" name="page" value="<?= $this->paged; ?>">
        </form>
        <?php
        Polc_Helper_Module::pagination($this->total_pages, $this->paged);
    }
}