<?php

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Class Polc_Toplists_Layout_Handler
 */
class Polc_Help_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-help";
    CONST POLC_LAYOUT_NAME = "Segítség";

    public function render()
    {
        global $post;
        ?>
        <div class="helpWrapper">
            <div class="helpInnerWrapper">
                <div class="helpArticle">
                    <h1 class="articleTitle"><?= $post->post_title; ?></h1>

                    <h2 class="articleAuthor"><?= get_user_by("id", $post->post_author)->user_login; ?></h2>

                    <div class="helpArticleLead">
                        <?= $post->post_excerpt; ?>
                    </div>
                    <div class="aricleContent">
                        <?= apply_filters('the_content', $post->post_content); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}