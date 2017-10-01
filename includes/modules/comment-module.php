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
        $more = false;
        $more_link = "";
        $default = ["number" => 0, "hierarchical" => true];
        $args = wp_parse_args($params, $default);

        if ($args["number"] != 0 && $args["number"] < wp_count_comments($args["post_id"])):
            $more = true;
            $more_link = get_permalink(Polc_Settings_Manager::pages()["comment_page"]) . get_post($args["post_id"])->post_name . "/";
        endif;

        $this->draw_comments(get_comments($args), $more, $more_link);
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

        if ($args["comment_content"] == ""):
            Polc_Helper_Module::error(__("Empty comment!", "polc"));
        endif;

        $args["comment_content"] = htmlentities($args["comment_content"]);

        wp_insert_comment($args);
    }

    /**
     * @param $comments
     * @param bool|false $more
     * @param string $more_link
     */
    private function draw_comments($comments, $more = false, $more_link = "")
    {
        $logged = is_user_logged_in();
        $authors = [];
        $comment_list = [];

        foreach ($comments as $comment) {
            $comment_list[$comment->comment_ID] = $comment;
        }

        foreach ($comments as $comment) {

            if (!array_key_exists($comment->user_id, $authors)):
                $recent_author = get_user_by('ID', $comment->user_id);
                $authors[$comment->user_id] = $recent_author->display_name;
            endif;

            if ($comment->comment_parent != 0 && $comment_list[$comment->comment_parent]->comment_parent != 0):
                $child = "lvl-2";
            elseif ($comment->comment_parent != 0 && $comment_list[$comment->comment_parent]->comment_parent == 0):
                $child = "lvl-1";
            else:
                $child = "";
            endif;

            ?>
            <div class="plcCommentWrapper <?= $child; ?>">
                <span class="plcCommentContent"><?= $comment->comment_content; ?></span>
                <a href="<?= get_author_posts_url($comment->user_id) ?>"><?= $authors[$comment->user_id]; ?></a>
                <span><?= __('wrote at', 'polc') . ' ' . mysql2date('Y F j', strtotime($comment->comment_date)); ?></span>
                <?php
                if ($logged):
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

        if ($more && count($comments) > 0):
            ?>
            <a href="<?= $more_link; ?>"><?= __("All comments", "polc"); ?></a>
            <?php
        endif;
    }
}