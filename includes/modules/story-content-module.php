<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 10.
 * Time: 9:07
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Content display module.
 * Class Polc_Story_Content
 */
class Polc_Story_Content_Module
{
    private $post;
    private $main_title;
    private $chapters;
    private $can_comment;
    private $restrictions;

    public function __construct()
    {
        global $post;

        setup_postdata($post);
        $this->post = $post;
        $logged = is_user_logged_in();

        $this->restrictions = new StdClass();

        $chapter_args = [
            "post_type" => "story",
            "posts_per_page" => -1,
            "orderby" => "ID",
            "order" => "ASC",
            "post_status" => "publish"
        ];

        if ($this->post->post_parent != 0):
            $this->main_title = get_post($this->post->post_parent)->post_title;
            $volume_id = $this->post->post_parent;
            $chapter_args["post_parent"] = $this->post->post_parent;
        else:
            $volume_id = $this->post->ID;
            $chapter_args["post_parent"] = $this->post->ID;
            $this->main_title = $this->post->post_title;
        endif;

        $this->restrictions->only_registered = get_post_meta($volume_id, "only-registered", true);
        $this->restrictions->agelimit = get_post_meta($volume_id, "agelimit", true);
        $this->restrictions->obscene = get_post_meta($volume_id, "obscene-content", true);
        $this->restrictions->violent = get_post_meta($volume_id, "violent-content", true);
        $this->restrictions->erotic = get_post_meta($volume_id, "erotic-content", true);


        if (!$logged && $this->restrictions->only_registered == "on"):
            ?>
            <div class="plcOnlyRegisteredWarning">
                <?= __('Sorry, this content is only available to registered users!Please register or sign in if you\'re already a member', 'polc'); ?>
            </div>
            <?php
            return false;
        endif;

        if ($logged):
            $this->can_comment = true;
        endif;

        $this->chapters = get_posts($chapter_args);

        $this->render();
    }

    private function render()
    {
        wp_enqueue_script("polc-comment-module", PLC_THEME_PATH . '/js/comment-handler.js');
        ?>
        <script>
            var polc_comment_handler,
                polc_content_handler = new polc_content_handler();
            jQuery(document).ready(function () {
                polc_comment_handler = new polc_comment_handler(<?= json_encode($this->options()); ?>);

                polc_comment_handler.load_comments();
            });
        </script>
        <div class="plc_story_content_wrapper">
            <div class="plc_story_content_settings">
                <div class="plc_text_settings">
                    <span class="plc_text_contrast day"></span>
                    <span class="text_alignment left"></span>

                    <div class="fontstyle_selector">
                        <span class="fontstyle"></span>
                        <ul class="fontstyle_list">
                            <li class="select_titillium" style="font-family: 'Titillium', sans-serif;">Titillium
                            </li>
                            <li class="select_ptserif" style="font-family: 'PT Serif', serif;">PT Serif</li>
                            <li class="select_ubuntu" style="font-family: 'Ubuntu', sans-serif;">Ubuntu</li>
                        </ul>
                    </div>
                    <div class="fontsizeselector">
                        <span class="fontsize"></span>
                        <ul class="fontsize_list">
                            <li class="fontsizeDefault"><?= __('Default', 'polc'); ?></li>
                            <li class="fontsizeBig">AAA</li>
                            <li class="fontsizeMedium">AAA</li>
                            <li class="fontsizeSmall">AAA</li>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <article class="plc_story_content">
                <div class="plc_article_inner_wrapper">

                    <div class="storyBar">
                        <?php

                        if (class_exists("Post_Views_Counter")):
                            if ($this->post->post_parent != 0 || get_post_meta($this->post->ID, "single", true) == 1):
                                ?>
                                <div class="plcContentViews">
                                    <span><?= pvc_get_post_views($this->post->ID); ?></span>
                                </div>
                                <?php
                            endif;
                        endif;

                        if (is_user_logged_in()): ?>
                            <div class="favoriteBtnWrapper">
                                <?php
                                $class = "";
                                $favorite_list = Polc_Header::$curr_user->data->favorite_content_list;
                                if (is_array($favorite_list) && count($favorite_list) > 0 && array_key_exists($this->post->ID, $favorite_list)):
                                    $text = __('Remove from favorites', 'polc');
                                    $class = "favorited";
                                else:
                                    $text = __('Add to favorites', 'polc');
                                    $class = "";
                                endif;
                                ?>
                                <span id="plcFavoriteBtn" class="<?= $class; ?>"
                                      data-post-id="<?= $this->post->ID; ?>"><?= $text; ?></span>
                            </div>
                            <?php
                        else:
                            ?>
                            <div class="favoriteBtnWrapper">
                                <span id="plcFavoriteBtnLogout"
                                      data-post-id="<?= $this->post->ID; ?>"><?= __('Add to favorites', 'polc'); ?></span>
                            </div>
                            <?php
                        endif;
                        ?>
                    </div>

                    <h1><?= $this->main_title; ?></h1>
                    <address class="author"><a rel="author"
                                               href="<?= get_author_posts_url($this->post->post_author); ?>"><?php the_author(); ?></a>
                    </address>
                    <?php
                    $this->chapter_selection();


                    if ($this->post->post_parent != 0):
                        ?>
                        <h3><?= $this->post->post_title; ?></h3>
                        <?php
                    endif;

                    $date = mysql2date("Y F j", $this->post->post_date);
                    ?>
                    <time datetime="<?= date("Y-m-d", strtotime($this->post->post_date)); ?>" pubdate="pubdate"
                          title="<?= $date;?>"><?= $date; ?></time>
                    <p>
                        <?php
                        if ($this->post->post_parent == 0):
                            the_excerpt();
                        else:
                            the_content();
                        endif;
                        ?>
                    </p>
                    <?php
                    $this->chapter_selection();
                    ?>
                </div>
            </article>
            <div class="polcSocialShareAndTags">
                <?php
                new Polc_Social_Share_Module();
                $tags = get_the_tags($this->post->ID);
                if ($tags):
                    ?>
                    <div class="polcTagsWrapper">
                        <?php
                        for ($i = 0; $i < min(4, count($tags)); $i++) : ?>
                            <a href="<?= get_tag_link($tags[$i]->term_id); ?>"><span
                                    class="category"><?= $tags[$i]->name; ?></span></a>
                            <?php
                        endfor;
                        ?>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            <div class="polcCommentWrapper">
                <?php if ($this->can_comment): ?>
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
        </div>
        <?php
    }

    public function chapter_selection()
    {
        if (count($this->chapters) > 0):
            ?>
            <div class="plcChapterselector">
                <div class="prevwrapper">
                    <span class="prev"></span>
                </div>

                <select class="plcChapterSelect">
                    <?php
                    if ($this->post->post_parent == 0):
                        $volume_link = get_permalink($this->post->ID);
                    else:
                        $volume_link = get_permalink($this->post->post_parent);
                    endif;
                    ?>
                    <option data-link="<?= $volume_link; ?>"><?= __('Volume', 'polc') ?></option>
                    <?php
                    $cnt = 1;
                    foreach ($this->chapters as $chapter):
                        ?>
                        <option <?= ($this->post->ID == $chapter->ID ? "selected" : ""); ?>
                            data-link="<?= get_permalink($chapter->ID); ?>">
                            <?= __("Chapter", "polc") . " " . $cnt; ?>
                        </option>
                        <?php
                        $cnt++;
                    endforeach;
                    ?>
                </select>

                <div class="nextwrapper">
                    <span class="next"></span>
                </div>
            </div>
            <?php
        endif;
    }

    private function options()
    {
        global $post;
        $options = new stdClass();
        $options->id = $post->ID;
        $options->number = 5;
        $options->parent = 0;
        $options->author = $post->post_author;

        return $options;
    }
}