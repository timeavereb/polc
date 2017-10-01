<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 08. 25.
 * Time: 14:48
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

if (!is_user_logged_in()):
    wp_send_json(["error" => __('You\'ve not signed in!', 'polc')]);
endif;

if (!isset($_REQUEST["story-id"]) || !is_numeric($_REQUEST["story-id"])):
    wp_send_json(["error" => __('Invalid content identifier!', 'polc')]);
endif;

new Polc_Story_Delete_Module();

/**
 * Class Polc_Story_Delete_Module
 */
class Polc_Story_Delete_Module
{
    private $user;
    private $post;
    private $mode;

    /**
     * Polc_Story_Delete_Module constructor.
     */
    public function __construct()
    {
        $this->user = wp_get_current_user();
        $this->post = get_post($_REQUEST["story-id"]);

        if ($this->post && $this->post->post_type == "story" && $this->post->post_author == $this->user->ID):
            $this->mode = $this->post->post_parent == 0 ? "volume" : "chapter";
            $this->init();
        else:
            wp_send_json(["error" => __('Invalid content identifier!', 'polc')]);
        endif;
    }

    /**
     * Inits content deletion.
     */
    private function init()
    {
        if ($this->mode == "chapter"):
            wp_trash_post($this->post->ID);
            wp_send_json(["success" => __("The chapter has been deleted!", "polc")]);
        else:
            $this->delete_chapters();
            wp_trash_post($this->post->ID);
            wp_send_json(["success" => __("The volume with all it's chapters has been deleted!", "polc"), "chapter_delete" => true]);
        endif;
    }

    /**
     * Delete all chapters based upon parent post id.
     */
    private function delete_chapters()
    {
        $chapters = get_children(["post_parent" => $this->post->ID, "post_type" => "story"]);

        foreach ($chapters as $chapter):
            wp_trash_post($chapter->ID);
        endforeach;
    }
}