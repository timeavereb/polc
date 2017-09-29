<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 06. 09.
 * Time: 20:25
 */
class Polc_Helper_Module
{

    public static function simple_list($posts, $animate = false)
    {
        ?>
        <div class="plc_latest">
            <?php

            $pages = Polc_Settings_Manager::pages();
            $new_story_page = get_permalink($pages["new-story-page"]);
            $editor_page = get_permalink($pages["content-editor-page"]);

            foreach ($posts as $post) {

                $tags = get_the_tags($post->ID);
                $user = Polc_Header::current_user();
                $logged = $user != null ? $user->ID : $user;

                ?>
                <div class="latest_item <?= ($animate) ? "animate" : ""; ?>">
                    <article class="plc_latest_item">
                        <div class="plc_article_datas top">
                            <?php if ($post->post_author != $logged) {
                                ?>
                                <a href="<?= get_author_posts_url($post->post_author); ?>"><span
                                        class="author"><?= get_the_author_meta("display_name", $post->post_author); ?></span></a>
                                <?php
                            } else {
                                ?>
                                <a href="#"><span
                                        class="comments"><?= wp_count_comments($post->ID)->total_comments . ' ' . __('Comment', 'polc'); ?>
                                </span></a>
                                <?php
                            }
                            ?>
                            <span class="date"><?= mysql2date("F j", $post->post_date); ?></span>
                        </div>
                        <a href="<?= get_permalink($post->ID); ?>">
                            <h2><?= $post->post_title; ?></h2>

                            <p><?= $post->post_excerpt; ?></p>
                        </a>

                        <div class="plc_article_datas bottom">
                            <?php
                            if ($post->post_author != $logged) {
                                ?>
                                <a href="#">
                                    <span
                                        class="comments"><?= wp_count_comments($post->ID)->total_comments . " " . __('Comment', 'polc'); ?></span>
                                </a>
                                <?php
                            } else {
                                if (count(get_children($args = array(
                                        'post_parent' => $post->ID,
                                        'post_type' => 'story',
                                        'numberposts' => 1,
                                        'post_status' => 'publish'
                                    ))) > 0
                                ):
                                    ?>
                                    <form action="<?= $new_story_page; ?>" method="POST">
                                        <input type="hidden" name="volume-id" value="<?= $post->ID; ?>">
                                        <?= wp_nonce_field('add-chapter', 'volume-id-nonce'); ?>
                                        <input type="submit" class="addChapter"
                                               value="<?= __('Add chapter', 'polc'); ?>">
                                    </form>
                                <?php endif; ?>

                                <a class="editStory">
                                    <form action="<?= $editor_page; ?>" method="POST">
                                        <input type="hidden" name="volume-id" value="<?= $post->ID; ?>">
                                        <input type="submit" class="editContent"
                                               value="<?= __('Edit story', 'polc'); ?>">
                                    </form>
                                </a>
                                <?php
                            }
                            if ($tags):
                                ?>
                                <div class="plcTagsWrapper">
                                    <?php
                                    for ($i = 0; $i < min(4, count($tags)); $i++):
                                        ?>
                                        <a href="<?= get_tag_link($tags[$i]->term_id); ?>">
                                            <span class="category"><?= $tags[$i]->name; ?></span>
                                        </a>
                                        <?php
                                    endfor;
                                    ?>
                                </div>
                                <?php
                            endif;
                            ?>
                        </div>
                    </article>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public static function search_list($posts, $animate)
    {
        ?>
        <div class="plc_latest">
            <?php
            foreach ($posts as $post) {

                $parent_post = array();

                if ($post->post_parent != 0) {
                    $parent_post = get_post($post->post_parent);
                    global $wpdb;
                    $select = "SELECT ID FROM wp_posts WHERE post_parent = " . $post->post_parent . " ORDER BY post_date ASC";
                    $result = $wpdb->get_results($select);
                    $cnt = 1;
                    foreach ($result as $res) {
                        if ($res->ID == $post->ID) {
                            $chapter = $cnt;
                            break;
                        }
                        $cnt++;
                    }
                }

                $tags = get_the_tags($post->ID);

                ?>
                <div class="latest_item <?= ($animate) ? "animate" : ""; ?>">
                    <article class="plc_latest_item">
                        <div class="plc_article_datas top">
                            <a href="<?= get_author_posts_url($post->post_author); ?>"><span
                                    class="author"><?= get_the_author_meta("display_name", $post->post_author); ?></span></a>
                            <span class="date"><?= mysql2date("F j", $post->post_date) ?></span>
                        </div>
                        <a href="<?= get_permalink($post->ID) ?>">
                            <h2>
                                <?php
                                if ($post->post_parent != 0) {
                                    echo $parent_post->post_title . " - " . __('Chapter', 'polc') . " " . $chapter . ": " . $post->post_title;
                                } else {
                                    echo $post->post_title;
                                }
                                ?>
                            </h2>

                            <p><?= $post->post_excerpt; ?></p>
                        </a>

                        <div class="plc_article_datas bottom">
                            <a href="#"><span
                                    class="comments"><?= wp_count_comments($post->ID)->total_comments . " " . __('Comment', 'polc'); ?>
                        </span></a>

                            <div class="plcTagsWrapper">
                                <?php
                                if ($tags):
                                    for ($i = 0; $i < min(4, count($tags)); $i++):
                                        ?>
                                        <a href="<?= get_tag_link($tags[$i]->term_id); ?>"><span
                                                class="category"><?= $tags[$i]->name; ?></span></a>
                                        <?php
                                    endfor;
                                endif;
                                ?>
                            </div>
                        </div>
                    </article>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public static function post_list($posts, $animate = false)
    {
        foreach ($posts as $post) {

            $img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
            $img = isset($img[0]) && $img[0] != "" ? $img[0] : "http:/defaultimg.jpg";
            ?>
            <a href="<?= get_permalink($post->ID); ?>">
                <article>
                    <div class="plcNewsImageWrapper">
                        <div class="plcNewsImage" style="background-image:url('<?= $img; ?>');"></div>
                    </div>
                    <div class="plcNewsText">
                        <h1><?= $post->post_title; ?></h1>

                        <p class="date"><?= get_the_date(); ?></p>
                    </div>
                </article>
            </a>
            <?php

        }
    }

    public static function pagination($pages = '', $range = 4)
    {
        $showitems = ($range * 2) + 1;

        global $paged;
        if (empty($paged)) $paged = 1;

        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }
        if (1 != $pages) {
            if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) echo "<a href='" . get_pagenum_link(1) . "'>&laquo; First</a>";
            if ($paged > 1 && $showitems < $pages) echo "<a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo; Previous</a>";

            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                    echo ($paged == $i) ? "<span class=\"current\">" . $i . "</span>" : "<a href='" . get_pagenum_link($i) . "' class=\"inactive\">" . $i . "</a>";
                }
            }

            if ($paged < $pages && $showitems < $pages) echo "<a href=\"" . get_pagenum_link($paged + 1) . "\">Next &rsaquo;</a>";
            if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) echo "<a href='" . get_pagenum_link($pages) . "'>Last &raquo;</a>";
            echo "</div>\n";
        }
    }

    /**
     * Displays the content blurb section
     * @param string $value
     */
    public static function content_blurb($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <textarea name="blurb" placeholder="<?= __('Blurb', 'polc'); ?>*" id="blurb"><?= $value; ?></textarea>

            <p class="form_info"><?= __('The bulrb character limit is 1200 characters.', 'polc'); ?></p>
        </div>
        <?php
    }

    /**
     * Displays the content warning section.
     * @param array $params
     */
    public static function content_warnings($params = array())
    {
        $default = array("obscene" => false, "violent" => false, "erotic" => false);
        $warnings = wp_parse_args($params, $default);
        ?>

        <div class="newStoryData_row contentWarning">
            <h4><?= __('Warnings', 'polc'); ?></h4>

            <div class="warning obscenecontent">
                <div class="plcCheckbox">
                    <input type="checkbox" id="obscene-content"
                           name="obscene-content" <?= (!$warnings["obscene"] ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Obscene content', 'polc'); ?></p>
            </div>
            <div class="warning violentcontent">
                <div class="plcCheckbox">
                    <input type="checkbox" id="violent-content"
                           name="violent-content" <?= (!$warnings["violent"] ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Violent content', 'polc'); ?></p>
            </div>
            <div class="warning eroticcontent">
                <div class="plcCheckbox">
                    <input type="checkbox" id="erotic-content"
                           name="erotic-content" <?= (!$warnings["erotic"] ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Erotic content', 'polc'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Displays the content age limit section.
     * @param int $selected
     */
    public static function content_age_limit($selected = 0)
    {
        ?>

        <div class="newStoryData_row agelimit">
            <h4><?= __('Age limit', 'polc'); ?></h4>

            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="18"
                           class="agelimit18" <?= ($selected === 18 ? "checked" : ""); ?>>
                    <span>18</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="16"
                           class="agelimit16" <?= ($selected === 16 ? "checked" : ""); ?>>
                    <span>16</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="14"
                           class="agelimit14" <?= ($selected === 14 ? "checked" : ""); ?>>
                    <span>14</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="12"
                           class="agelimit12" <?= ($selected === 12 ? "checked" : ""); ?>>
                    <span>12</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="0"
                           class="noagelimit" <?= ($selected === 0 ? "checked" : ""); ?>>
                    <span><?= __('No age limit', 'polc'); ?></span>
                </label>
            </div>
        </div>
        <?php
    }

    /**
     * @param bool|false $checked
     */
    public static function content_restriction($checked = false)
    {
        ?>

        <div class="newStoryData_row regWarning">
            <h4><?= __('Restrictions', 'polc'); ?></h4>

            <div class="warning registeredOnly">
                <div class="plcCheckbox">
                    <input type="checkbox" id="only-registered"
                           name="only-registered" <?= (!$checked ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Content only for registered users', 'polc'); ?></p>
            </div>
            <div class="newStoryData_row">
                <p class="form_info"><?= __('Warning! Content under 18+ available only for registered users!', 'polc'); ?></p>
            </div>
        </div>

        <?php
    }

    /**
     * Displays the author comment.
     * @param string $value
     */
    public static function content_author_comment($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" name="author-comment" placeholder="<?= __('Author\'s comment', 'polc'); ?>"
                   value="<?= $value; ?>">
        </div>
        <?php
    }

    /**
     * Displays the content editor.
     * @param string $value
     */
    public static function content_editor($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <?php
            wp_editor($value, 'story_content', array("media_buttons" => false, "teeny" => true, "quicktags" => false));
            ?>
            <label id="story_content-error" for="story_content"
                   style="display: none;"><?= __('Content is empty!', 'polc'); ?></label>

            <p class="form_info"><?= __('The content maximum character limit is 28 000 characters.', 'polc'); ?></p>
        </div>
        <?php
    }

    /**
     * Displays the content tags.
     * @param array $tags
     */
    public static function content_tags($tags = array())
    {
        ?>
        <div class="newStoryData_row tag">
            <label for="polc_tag_handler"><?= __('Add tags', 'polc'); ?></label>
            <input type="text" id="polc_tag_handler">

            <div class="plcTagContainer">
                <?php
                foreach ($tags as $tag):
                    ?>
                    <div class="plcTagElement">
                        <span class="plcTagELementText"><?= $tag; ?></span>
                        <input type="hidden" name="post_tag[]" value="<?= $tag; ?>">
                        <span class="plcTagElementDelete" onclick="polc_editor.remove_tag_element(this);"></span>
                    </div>
                    <?php
                endforeach;
                ?>
            </div>
            <div class="plcTagWarningWrapper">
                <p class="form_info"><?= __('The maximum number of tags is 8.', 'polc'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Displays the chapter title.
     * @param string $value
     */
    public static function content_chapter_title($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" id="chapter_title" name="chapter_title"
                   placeholder="<?= __('Chapter\'s title', 'polc'); ?>*" value="<?= $value; ?>">
        </div>
        <?php
    }

    /**
     * Displays the volume title.
     * @param string $value
     */
    public static function content_volume_title($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" name="volume_title" placeholder="<?= __('Volume title', 'polc'); ?>*"
                   value="<?= $value; ?>">
        </div>
        <?php
    }

    /**
     * Displays the volume sub title.
     * @param $value
     */
    public static function content_volume_sub_title($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" name="volume-sub-title" placeholder="<?= __('Volume sub-title', 'polc'); ?>"
                   value="<?= $value; ?>">
        </div>
        <?php
    }
}