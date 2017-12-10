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
        $this->ppp = 10;

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

        ?>
        <div class="plcNewsListWrapper">
            <div class="listInnerWrapper">
                <?php
                foreach ($posts as $post):

                    $img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
                    $img = isset($img[0]) && $img[0] != "" ? $img[0] : "/wp-content/themes/polc/img/social/share_default.png";
                    ?>
                    <a href="<?= get_permalink($post->ID); ?>">
                        <article>
                            <div class="articleImageWrapper">
                                <div class="articleImage" style="background-image:url('<?= $img; ?>');"></div>
                            </div>
                            <div class="articleDatas">
                                <h1><?= $post->post_title; ?></h1>

                                <p class="lead"><?= $post->post_excerpt; ?></p>

                                <p class="newsDate"><?= get_the_date('', $post); ?></p>
                            </div>
                        </article>
                    </a>
                    <?php
                endforeach;
                ?>
            </div>
        </div>
        <form id="plcSearchForm" method="POST">
            <input type="hidden" id="page" name="page" value="<?= $this->paged; ?>">
        </form>
        <?php
        if ($this->total_pages > 1) {
            Polc_Helper_Module::pagination($this->total_pages, $this->paged);
        }
    }
}