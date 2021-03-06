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
        add_action('publish_story', [$this, 'publish'], 1, 2);
    }

    /**
     * Init metaboxes.
     */
    public function meta_box_init()
    {
        add_meta_box('story-parent', __('Volume', 'polc'), [$this, 'story_parent_meta_box'], 'story', 'side', 'high');
        add_meta_box('story-details', __('Volume details', 'polc'), [$this, 'story_details_meta_box'], 'story', 'normal', 'high');
        add_meta_box('email-notification', __('E-mail notification', 'polc'), [$this, 'story_email_notification'], 'story', 'normal', 'high');
    }

    /**
     * @param $post_id
     * @param $post
     * @return bool|void
     */
    public function save($post_id, $post)
    {
        if ($post->post_type != "story"):
            return;
        endif;

        if (!is_admin() || (isset($post->post_status) && 'auto-draft' == $post->post_status)):
            return;
        endif;

        $keys = $this->get_metas();

        if (isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != ""):
            $id = $_REQUEST["parent_id"];
        else:
            $id = isset($_REQUEST["post_ID"]) && is_numeric($_REQUEST["post_ID"]) ? $_REQUEST["post_ID"] : false;
        endif;

        if (!$id):
            return false;
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
    }

    /**
     * Modifies parent's story modified date if any child is being published.
     * @param $post_id
     * @param $post
     */
    public function publish($post_id, $post)
    {
        //FRONTEND CONTENT EDIT
        if (isset($_REQUEST["polc-edit"])) {
            return;
        }

        //Refreshing parent's post modofied if child was published
        if ($post->post_type == "story" && $post->post_status == "publish" && is_numeric($post->post_parent) && $post->post_parent != 0):
            //Removing save post action avoiding infinite loop before we update parent post
            remove_action('save_post', 'wpse51363_save_post');
            $args = ["ID" => $post->post_parent, "post_modified" => $post->post_modified, "post_modified_gmt" => $post->post_modified_gmt];
            wp_update_post($args);
            add_action('save_post', [$this, 'save'], 1, 2);
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
            if (current_user_can("manage_options")):
                ?>
                <tr>
                    <td>
                        <?= __('Content was last modified by :'); ?>
                    </td>
                    <td>
                        <?php the_modified_author(); ?>
                    </td>
                </tr>
                <?php
            endif;
            ?>
        </table>
        <?php
    }

    /**
     * @param $post
     */
    public function story_email_notification($post)
    {
        $user_data = get_user_by('id', $post->post_author);
        $user_name = $user_data->user_login;

        $editor_settings = [
            "media_buttons" => false,
            "quicktags" => false,
            "teeny" => true,
            "textarea_name" => "body"
        ];

        $email_templates = Polc_Settings_Manager::email();

        $templates = [
            "acceptance" => $email_templates["acceptance"],
            "rejection" => $email_templates["rejection"]
        ];

        foreach ($templates as $key => &$value):
            $value["body"] = preg_replace('~#USERDISPLAYNAME#~', $user_name, $value["body"]);
            $value["body"] = preg_replace('~#TITLE#~', $post->post_title, $value["body"]);
        endforeach;

        ?>
        <div class="plcNotificationTabWrapper">
            <div class="plcNotificationTab"
                 onclick="jQuery('#plcAcceptanceWrapper').show(); jQuery('#plcRejectionWrapper').hide();"><?= __('Acceptance', 'polc'); ?></div>
            <div class="plcNotificationTab"
                 onclick="jQuery('#plcAcceptanceWrapper').hide(); jQuery('#plcRejectionWrapper').show();"><?= __('Rejection', 'polc'); ?></div>
        </div>

        <div class="plcEmailMessagesWrapper">
            <!-- acceptance-->
            <div id="plcAcceptanceWrapper">
                <div id="plcAcceptanceForm">
                    <div
                        class="plcNotificationBodyWrapper"><?php wp_editor($templates["acceptance"]["body"], 'plc_acceptance', $editor_settings); ?></div>
                    <input type="hidden" name="subject" value="<?= $templates["acceptance"]["subject"]; ?>">
                    <input type="hidden" name="sender_email" value="<?= $templates["acceptance"]["sender_email"]; ?>">
                    <input type="hidden" name="sender_name" value="<?= $templates["acceptance"]["sender_name"]; ?>">
                    <input type="hidden" name="recipient" value="<?= $user_data->user_email; ?>">
                    <div
                        id="plcNotificationNonce"><?php wp_nonce_field('plc_notification', 'plc_notification_nonce'); ?></div>
                </div>
                <div class="plcNotificationController">
                    <button class="plcSendEmailNotification"><?= __("Send e-mail", 'polc'); ?></button>
                </div>
            </div>

            <!-- rejection -->
            <div id="plcRejectionWrapper" style="display: none;">
                <div id="plcRejectionForm">

                    <div class="plcNotificationBodyWrapper">
                        <?php wp_editor($templates["rejection"]["body"], 'plc_rejection', $editor_settings); ?>
                    </div>
                    <input type="hidden" name="subject" value="<?= $templates["rejection"]["subject"]; ?>">
                    <input type="hidden" name="sender_email" value="<?= $templates["rejection"]["sender_email"]; ?>">
                    <input type="hidden" name="sender_name" value="<?= $templates["rejection"]["sender_name"]; ?>">
                    <input type="hidden" name="recipient" value="<?= $user_data->user_email; ?>">

                    <div id="plcNotificationNonce">
                        <?php wp_nonce_field('plc_notification', 'plc_notification_nonce'); ?>
                    </div>
                </div>
                <div class="plcNotificationController">
                    <button class="plcSendEmailNotification"><?= __("Send e-mail", 'polc'); ?></button>
                </div>
            </div>
        </div>

        <div id="confirmationDialog" style="display: none;" title="<?= __('Confirmation', 'polc'); ?>">
            <?= __('Are you sure you want to send this e-mail?', 'polc'); ?>
        </div>

        <script type="text/javascript">
            var notifiation_handler;
            jQuery(document).ready(function () {
                notifiation_handler = new polc_email_notification_handler();
            });
        </script>
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
            'capabilities' => [
                'edit_post' => 'edit_story',
                'edit_posts' => 'edit_stories',
                'edit_others_posts' => 'edit_other_stories',
                'edit_published_posts' => 'edit_published_stories',
                'publish_posts' => 'publish_stories',
                'read_post' => 'read_story',
                'read_private_posts' => 'read_private_stories',
                'delete_posts' => 'delete_stories',
                'delete_others_posts' => 'delete_others_stories',
                'delete_published_posts' => 'delete_published_stories',
            ],
            'map_meta_cap' => true,
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






