<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 10.
 * Time: 9:07
 */

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

        $chapter_args = array(
            "post_type" => "story",
            "posts_per_page" => -1,
            "orderby" => "date",
            "order" => "asc"
        );

        if ($this->post->post_parent != 0) {
            $this->main_title = get_post($this->post->post_parent)->post_title;
            $volume_id = $this->post->post_parent;
            $chapter_args["post_parent"] = $this->post->post_parent;
        } else {
            $volume_id = $this->post->ID;
            $chapter_args["post_parent"] = $this->post->ID;
            $this->main_title = $this->post->post_title;
        }

        $this->restrictions->only_registered = get_post_meta($volume_id, "only-registered", true);
        $this->restrictions->agelimit = get_post_meta($volume_id, "agelimit", true);
        $this->restrictions->obscene = get_post_meta($volume_id, "obscene-content", true);
        $this->restrictions->violent = get_post_meta($volume_id, "violent-content", true);
        $this->restrictions->erotic = get_post_meta($volume_id, "erotic-content", true);


        if (!$logged && $this->restrictions->only_registered == "on") {
            echo '<div class="plcOnlyRegisteredWarning">';
            echo __('Sorry, this content is only available to registered users!Please register or sign in if you\'re already a member', 'polc');
            echo '</div>';
            return false;
        }

        if ($logged) {
            $this->can_comment = true;
        }

        $this->chapters = get_posts($chapter_args);

        $this->render();
    }

    private function render()
    {
        ?>
        <script>var polc_content_handler = new polc_content_handler();</script>
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
                    <h1><?= $this->main_title; ?></h1>

                    <address class="author"><a rel="author"
                                               href="<?= get_author_posts_url($this->post->post_author); ?>"><?php the_author(); ?></a>
                    </address>

                    <?php
                    $this->chapter_selection();


                    if ($this->post->post_parent != 0):
                        echo '<h3>' . $this->post->post_title . '</h3>';
                    endif; ?>
                    <time pubdate datetime="2017-04-19"
                          title="April 19th, 2017"><?= mysql2date("Y F j", strtotime($this->post->post_date)); ?></time>
                    <p>
                        <?php
                        if ($this->post->post_parent == 0) {
                            the_excerpt();
                        } else {
                            the_content();
                        }
                        ?>
                    </p>

                    <?php
                    $this->chapter_selection();
                    ?>
                </div>
            </article>
            <div class="polcSocialShareAndTags">

                <?php
                $share = new Pol_Social_Share_Module();
                $tags = get_the_tags($this->post->ID);
                if ($tags):
                    echo '<div class="polcTagsWrapper">';
                    for ($i = 0; $i < min(4, count($tags)); $i++) : ?>
                        <a href="<?= get_tag_link($tags[$i]->term_id); ?>"><span
                                class="category"><?= $tags[$i]->name; ?></span></a>
                    <?php endfor;
                endif;
                echo '</div>';
                ?>
            </div>
            <div class="polcCommentWrapper">
                <?php if ($this->can_comment): ?>
                    <div class="polcCommentInnerWrapper">
                        <div class="Name">Name</div>
                        <div class="Name"><textarea>Írj kritikát...</textarea></div>
                    </div>
                <?php endif; ?>
                <div class="plcCommentListWrapper">
                    <?php
                    $comments = get_comments(array(
                        'post_id' => $this->post->ID,
                        'number' => '2'));
                    foreach ($comments as $comment) {
                        echo '<div class="plcCommentWrapper">';
                        echo '<span> ' . $comment->comment_content . ' </span>';
                        echo '<a href="' . get_author_posts_url($comment->user_id) . '">' . $comment->comment_author . '</a>';
                        echo '<span> ' . __('wrote at', 'polc') . ' ' . mysql2date('Y F j', strtotime($comment->comment_date)) . ' </span>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function chapter_selection()
    {
        if (count($this->chapters) > 0):

            echo '<div class="plcChapterselector">';
            echo '<div class="prevwrapper">';
            echo '<span class="prev"></span>';
            echo '</div>';

            echo '<select class="plcChapterSelect">';
            if ($this->post->post_parent == 0) {
                $volume_link = get_permalink($this->post->ID);
            } else {
                $volume_link = get_permalink($this->post->post_parent);
            }
            echo '<option data-link="' . $volume_link . '">' . __('Volume', 'polc') . '</option>';

            $cnt = 1;
            foreach ($this->chapters as $chapter):
                echo '<option ' . ($this->post->ID == $chapter->ID ? "selected" : "") . ' data-link="' . get_permalink($chapter->ID) . '">Fejezet ' . $cnt . '</option>';
                $cnt++;
            endforeach;
            echo '</select>';

            echo '<div class="nextwrapper">';
            echo '<span class="next"></span>';
            echo '</div>';
            echo '</div>';
        endif;
    }
}