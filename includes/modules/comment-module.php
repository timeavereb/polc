<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 08. 26.
 * Time: 13:28
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

if (isset($_REQUEST["action"])):
    new Polc_Comment_Module();
else:
    Polc_Helper_Module::error(__("Invalid action!", "polc"));
endif;

/**
 * Class Polc_Comment_Module
 */
class Polc_Comment_Module
{
    private $user;
    private $author_id;
    private $logged;
    public static $authors;
    private $total_comments;

    /**
     * Polc_Comment_Module constructor.
     */
    public function __construct()
    {
        switch ($_REQUEST["action"]) {
            case "get_comments":
                $this->get_comments($_REQUEST["params"]);
                break;
            case "add_comment":
                $this->add_comment($_REQUEST["params"]);
                break;
            default:
                Polc_Helper_Module::error(__("Invalid action!", "polc"));
        }
    }

    /**
     * @param $params
     */
    private function get_comments($params)
    {
        $this->author_id = $params["author"];
        $this->total_comments = wp_count_comments($params["post_id"]);
        $this->logged = is_user_logged_in();
        self::$authors = [];

        $more = false;
        $more_link = "";

        $default = [
            "hierarchical" => true,
            "callback" => [$this, 'comment_view']
        ];

        $args = wp_parse_args($params, $default);

        if ($args["number"] != 0 && $args["number"] < $this->total_comments->approved):
            $more = true;
            $more_link = get_permalink(Polc_Settings_Manager::pages()["comment_page"]) . get_post($args["post_id"])->post_name . "/";
        endif;

        $pre_args = [
            "number" => isset($params["number"]) ? $params["number"] : 0,
            "post_id" => $params["post_id"]
        ];

        $comments = get_comments($pre_args);
        wp_list_comments($args,$comments);
        $this->draw_comment_footer($more, $more_link);
    }

    /**
     * @param $params
     */
    private function add_comment($params)
    {
        Polc_Helper_Module::is_logged();
        $this->user = wp_get_current_user();

        $default = [
            "user_id" => $this->user->ID
        ];

        $args = wp_parse_args($params, $default);

        if (trim($args["comment_content"]) == ""):
            Polc_Helper_Module::error(__("Empty comment!", "polc"));
        endif;

        $args["comment_content"] = htmlentities($args["comment_content"]);

        wp_insert_comment($args);
    }

    /**
     * @param $comment
     * @param $args
     * @param $depth
     */
    public function comment_view($comment, $args, $depth)
    {
        $depth--;
        if ($depth == 0) {
            $custom_depth = "";
        } else {
            $custom_depth = "lvl-" . min($depth,2);
        }

        if (!array_key_exists($comment->user_id, self::$authors)):
            $recent_author = get_user_by('ID', $comment->user_id);
            self::$authors[$comment->user_id] = $recent_author->user_login;
        endif;

        ?>
        <div
            class="plcCommentWrapper<?= $this->author_id == $comment->user_id ? " author_comment " : ""; ?> <?= $custom_depth; ?>">
            <span class="plcCommentContent"><?= $comment->comment_content; ?></span>
            <a href="<?= get_author_posts_url($comment->user_id) ?>"><?= self::$authors[$comment->user_id]; ?></a>
            <span><?= __('wrote at', 'polc') . ' ' . mysql2date('Y F j', $comment->comment_date); ?></span>
            <?php
            if ($this->logged):
                ?>
                <div class="plcCommentReplyWrapper">
                    <span class="plcCommentReplyBtn"><?= __("Reply", "polc"); ?></span>

                    <div class="plcCommentTextWrapper" style="display: none;">
                        <textarea placeholder="<?= __("Write your reply..", "polc"); ?>" class="plcReplyText"
                                  data-id="<?= $comment->comment_ID; ?>"></textarea>
                    </div>
                </div>
                <?php
            endif;
            ?>
        </div>
        <?php
    }

    /**
     * @param $more
     * @param $more_link
     */
    private function draw_comment_footer($more, $more_link)
    {
        if ($more && $this->total_comments->approved > 0):
            ?>
            <a href="<?= $more_link; ?>"><?= __("All comments", "polc"); ?></a>
            <?php
        endif;
    }
}