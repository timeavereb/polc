<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 08. 27.
 * Time: 16:00
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Comment_List_Layout_Handler
 */
class Polc_Comment_List_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-comment-list";
    CONST POLC_LAYOUT_NAME = "Komment lista";

    private $post;

    public function render()
    {
        $this->init();
        ?>
        <script>
            var polc_comment_handler,
                polc_content_handler = new polc_content_handler();
            jQuery(document).ready(function () {
                polc_comment_handler = new polc_comment_handler(<?= json_encode($this->options()); ?>);

                polc_comment_handler.load_comments();
            });
        </script>

        <div class="polcCommentWrapper">
            <?php if (is_user_logged_in()): ?>
                <div class="polcCommentInnerWrapper">
                    <div class="Name">
                            <textarea id="plcCommentContent"
                                      placeholder="<?= __("Share your thoughts...", "polc"); ?>"></textarea>
                    </div>
                    <button id="plcSendComment"><?= __("Send comment", "polc"); ?></button>
                </div>
                <?php
            endif;
            ?>
            <div class="plcCommentListWrapper">
            </div>
        </div>
        <?php
    }

    private function init()
    {
        $slug = get_query_var("comment_post_slug");
        if ($slug == ""):
            return false;
        endif;

        $post = get_posts(['name' => $slug,
            'post_type' => 'story',
            'post_status' => 'publish']);

        if (empty($post)):
            return false;
        endif;

        $this->post = $post[0];

        wp_enqueue_script("polc-comment-module", PLC_THEME_PATH . '/js/comment-handler.js');
        return true;
    }

    private function options()
    {
        $options = new stdClass();
        $options->id = $this->post->ID;
        $options->author = $this->post->post_author;

        return $options;
    }
}