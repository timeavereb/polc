<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 06. 09.
 * Time: 19:37
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Story_Post_Type
 */
class Polc_Story_Post_Type
{
    /**
     * Polc_Story_Post_Type constructor.
     */
    public function __construct()
    {
        add_action('init', [$this, 'register_story_post_type']);
        add_action('add_meta_boxes', [$this, 'meta_box_init']);
        add_filter('post_type_link', [$this, 'polc_story_author_tag'], 10, 4);
        add_action('save_post', [$this, 'save'], 1, 2);
    }

    /**
     * Init metaboxes.
     */
    public function meta_box_init()
    {
        add_meta_box('story-parent', __('Volume', 'polc'), [$this, 'story_parent_meta_box'], 'story', 'side', 'high');
        add_meta_box('story-details', __('Volume details', 'polc'), [$this, 'story_details_meta_box'], 'story', 'normal', 'high');
    }

    /**
     * @param $post_id
     * @param $post
     */
    public function save($post_id, $post)
    {
        if (!is_admin() || (isset($post->post_status) && 'auto-draft' == $post->post_status)):
            return;
        endif;

        $keys = $this->get_metas();

        if (isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != ""):
            $id = $_REQUEST["parent_id"];
        else:
            $id = $_REQUEST["post_ID"];
        endif;

        foreach ($keys as $key => $value):
            $meta_value = isset($_REQUEST[$key]) ? $_REQUEST[$key] : "";
            //in case of authors comment we always update the current post ( it's individual in every story content )
            if ($key == "author-comment"):
                update_post_meta($_REQUEST["post_ID"], $key, $meta_value);
            else:
                update_post_meta($id, $key, $meta_value);
            endif;
        endforeach;

        if ($post->post_type == "story" && $_REQUEST["parent_id"] != ""):
            //TODO::szülő utolsó módosítás dátum frissítése.
            //wp_update_post(["ID" => $_REQUEST["parent_id"]]);
        endif;
    }

    public function story_parent_meta_box($post)
    {
        $post_type_object = get_post_type_object($post->post_type);
        if ($post_type_object->hierarchical):

            $pages = wp_dropdown_pages([
                'post_type' => 'story',
                'selected' => $post->post_parent,
                'name' => 'parent_id',
                'show_option_none' => __('(no parent)'),
                'sort_column' => 'menu_order, post_title',
                'echo' => 0,
                'depth' => 0,
                'child_of' => 0,
                'parent' => 0,
                'post_status' => ['publish', 'draft', 'pending']
            ]);

            if (!empty($pages)):
                echo $pages;
            endif;

        endif;
    }

    public function get_metas()
    {
        return [
            "volume-sub-title" => [
                "name" => __('Volume sub-title', 'polc'),
                "type" => "text"
            ],
            "obscene-content" => [
                "name" => __('Obscene content', 'polc'),
                "type" => "checkbox"
            ],
            "violent-content" => [
                "name" => __('Violent content', 'polc'),
                "type" => "checkbox"
            ],
            "erotic-content" => [
                "name" => __('Erotic content', 'polc'),
                "type" => "checkbox"
            ],
            "agelimit" => [
                "name" => __('Age limit', 'polc'),
                "type" => "text"
            ],
            "only-registered" => [
                "name" => __('Content only for registered users', 'polc'),
                "type" => "checkbox"
            ],
            "author-comment" => [
                "name" => __("Author's comment", "polc"),
                "type" => "text"
            ]
        ];
    }

    public function story_details_meta_box($post)
    {
        $id = ($post->post_parent == 0) ? $post->ID : $post->post_parent;
        $keys = $this->get_metas();
        ?>
        <table class="widefat">
            <?php
            foreach ($keys as $key => $value):

                //in case of authors comment we always display the current post ( it's individual in every story content )
                if ($key == "author-comment"):
                    $meta = get_post_meta($post->ID, $key);
                else:
                    $meta = get_post_meta($id, $key);
                endif;
                ?>
                <tr>
                    <td>
                        <?= $value["name"]; ?>
                    </td>
                    <td>
                        <?php
                        if ($value["type"] == "checkbox"):
                            ?>
                            <input name="<?= $key; ?>"
                                   type="checkbox" <?= isset($meta[0]) && $meta[0] == "on" ? "checked" : ""; ?>>
                            <?php
                        else:
                            ?>
                            <input name="<?= $key; ?>" type="text" value="<?= isset($meta[0]) ? $meta[0] : ""; ?>">
                            <?php
                        endif;
                        ?>
                    </td>
                </tr>
                <?php
            endforeach;
            ?>
        </table>
        <?php
    }

    public function register_story_post_type()
    {
        $labels = [
            'name' => __('Content', 'polc'),
            'singular_name' => __('Content', 'polc'),
            'menu_name' => __('Contents', 'polc'),
            'name_admin_bar' => __('Contents', 'polc'),
            'add_new' => __('Add New', 'polc'),
            'add_new_item' => __('Add New Item', 'polc'),
            'new_item' => __('New Item', 'polc'),
            'edit_item' => __('Content Edit', 'polc'),
            'view_item' => __('View Content', 'polc'),
            'all_items' => __('All Content', 'polc'),
            'search_items' => __('Search Content', 'polc'),
            'parent_item_colon' => "",
            'not_found' => __('Content Not Found', 'polc'),
            'not_found_in_trash' => __('Content Not Found In Trash', 'polc'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'menu_icon' => 'dashicons-book-alt',
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => __('content-slug', 'polc')],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => true,
            'menu_position' => 5,
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'],
            'taxonomies' => ['category', 'post_tag'],
        ];

        register_post_type('story', $args);
    }

    public function polc_story_author_tag($post_link, $post, $leavename, $sample)
    {
        if (get_post_type($post) != "story"):
            return $post_link;
        endif;

        $author = get_userdata($post->post_author)->user_nicename;;
        $post_link = str_replace('%author%', $author, $post_link);

        return $post_link;
    }
}

new Polc_Story_Post_Type();






