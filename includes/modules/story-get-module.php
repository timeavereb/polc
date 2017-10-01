<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 06. 09.
 * Time: 19:36
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Get_Module
 */
class Polc_Get_Module
{
    /**
     * @param $args
     * @param bool|false $animate
     */
    public static function get_latest_posts($args, $animate = false)
    {
        $default = [
            "post_type" => "post",
            "posts_per_page" => 3,
            "post_status" => "publish",
            "order" => "desc",
        ];

        $args = wp_parse_args($args, $default);
        Polc_Helper_Module::post_list(get_posts($args), $animate);
    }

    /**
     * @param $args
     * @param bool|false $animate
     */
    public static function get_latest_stories($args, $animate = false)
    {
        $default = [
            "post_type" => "story",
            "posts_per_page" => 10,
            "post_status" => "publish",
            "orderby" => "modified",
            "order" => "desc",
            "post_parent" => "0"
        ];

        $args = wp_parse_args($args, $default);

        Polc_Helper_Module::simple_list(get_posts($args), $animate);
    }

    public static function search_by_tag($animate = false)
    {
        $tag = get_queried_object();

        $paged = (get_query_var('page')) ? get_query_var('page') : 1;

        $args = [
            "post_type" => "story",
            "paged" => $paged,
            "posts_per_page" => 20,
            "post_status" => "publish",
            'tax_query' => [
                [
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $tag->slug
                ]
            ]
        ];

        $posts = get_posts($args);
        Polc_Helper_Module::search_list($posts, $animate);
        echo paginate_links();
    }
}