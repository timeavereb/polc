<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 06. 09.
 * Time: 20:25
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Helper_Module
 */
class Polc_Helper_Module
{
    /**
     * Check if user logged in.
     */
    public static function is_logged()
    {
        if (!is_user_logged_in()):
            self::error(__('You\'ve not signed in!', 'polc'));
        endif;
    }

    /**
     * Send error message as json and stops the code.
     * @param $message
     */
    public static function error($message)
    {
        wp_send_json(["error" => $message]);
    }

    /**
     * @param $text
     * @param $limit
     * @return string
     */
    public static function limit_text($text, $limit) {

        if (strlen($text) > $limit) {
            $text = substr($text,0,$limit) . '...';
        }

        return $text;
    }

    /**
     * @param $posts
     * @param bool|false $animate
     */
    public static function simple_list($posts, $animate = false)
    {
        ?>
        <div class="plc_latest">
            <?php
            $pages = Polc_Settings_Manager::pages();
            $new_story_page = get_permalink($pages["new-story-page"]);
            $editor_page = get_permalink($pages["content-editor-page"]);

            foreach ($posts as $post):

                $tags = get_the_tags($post->ID);
                $user = Polc_Header::current_user();
                $logged = $user != null ? $user->ID : $user;

                if ($post->post_parent != 0):
                    $id = $post->post_parent;
                else:
                    $id = $post->ID;
                endif;

                $keys = [
                    "obscene-content" => __('Obscene content', 'polc'),
                    "violent-content" => __('Violent content', 'polc'),
                    "erotic-content" => __('Erotic content', 'polc')
                ];

                $restriction = "";
                foreach ($keys as $key => $value):
                    if (get_post_meta($id, $key, true) == "on"):
                        $restriction .= '<span class="warning ' . $key . '" title="' . $value . '"></span>';
                    endif;
                endforeach;

                if (strlen($restriction) > 0):
                    $restriction = '<div class="plcContentWarningWrapper">'. $restriction . '</div>';
                endif;

                ?>
                <div class="latest_item <?= ($animate) ? "animate" : ""; ?>">
                    <article class="plc_latest_item">

                        <div class="plc_article_datas top">
                            <?php if ($post->post_author != $logged):
                                ?>
                                <a href="<?= get_author_posts_url($post->post_author); ?>"><span
                                        class="author"><?= get_the_author_meta("display_name", $post->post_author); ?></span></a>
                                <?php
                            else:
                                ?>
                                <a href="#"><span
                                        class="comments"><?= wp_count_comments($post->ID)->total_comments . ' ' . __('Comment', 'polc'); ?>
                                </span></a>
                                <?php

                                echo $restriction;
                            endif;
                            ?>
                            <span class="date"><?= mysql2date("Y F j", $post->post_modified); ?></span>
                        </div>
                        <a href="<?= get_permalink($post->ID); ?>">
                            <h2><?= $post->post_title; ?></h2>
                            <p><?= apply_filters('the_content', $post->post_excerpt); ?></p>
                        </a>

                        <div class="plc_article_datas bottom">
                            <?php
                            if ($post->post_author != $logged):
                                ?>
                                <a href="#">
                                    <span
                                        class="comments"><?= wp_count_comments($post->ID)->total_comments . " " . __('Comment', 'polc'); ?></span>
                                </a>
                                <?php
                                echo $restriction;
                            else:
                                if (count(get_children($args = [
                                        'post_parent' => $post->ID,
                                        'post_type' => 'story',
                                        'numberposts' => 1,
                                        'post_status' => 'publish'
                                    ])) > 0
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
                            endif;

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
            endforeach;
            ?>
        </div>
        <?php
    }

    /**
     * @param $posts
     * @param $animate
     */
    public static function search_list($posts, $animate)
    {
        ?>
        <div class="plc_latest">
            <?php
            foreach ($posts as $post):

                if ($post->post_parent != 0):
                    $id = $post->post_parent;
                else:
                    $id = $post->ID;
                endif;

                $keys = [
                    "obscene-content" => __('Obscene content', 'polc'),
                    "violent-content" => __('Violent content', 'polc'),
                    "erotic-content" => __('Erotic content', 'polc')
                ];

                $restriction = "";
                foreach ($keys as $key => $value):
                    if (get_post_meta($id, $key, true) == "on"):
                        $restriction .= '<span class="warning ' . $key . '" title="' . $value . '"></span>';
                    endif;
                endforeach;

                if (strlen($restriction) > 0):
                    $restriction = '<div class="plcContentWarningWrapper">'. $restriction . '</div>';
                endif;

                $parent_post = [];

                if ($post->post_parent != 0):
                    $parent_post = get_post($post->post_parent);
                    global $wpdb;
                    $select = "SELECT ID FROM wp_posts WHERE post_parent = " . $post->post_parent . " AND post_status = 'publish' ORDER BY post_date ASC";
                    $result = $wpdb->get_results($select);
                    $cnt = 1;
                    foreach ($result as $res):
                        if ($res->ID == $post->ID):
                            $chapter = $cnt;
                            break;
                        endif;
                        $cnt++;
                    endforeach;
                endif;

                $tags = get_the_tags($post->ID);

                ?>
                <div class="latest_item <?= ($animate) ? "animate" : ""; ?>">
                    <article class="plc_latest_item">
                        <div class="plc_article_datas top">
                            <a href="<?= get_author_posts_url($post->post_author); ?>"><span
                                    class="author"><?= get_the_author_meta("display_name", $post->post_author); ?></span></a>
                            <span class="date"><?= mysql2date("Y F j", $post->post_modified); ?></span>
                        </div>
                        <a href="<?= get_permalink($post->ID) ?>">
                            <h2>
                                <?php
                                if ($post->post_parent != 0):
                                    echo $parent_post->post_title . " - " . __('Chapter', 'polc') . " " . $chapter . ": " . $post->post_title;
                                else:
                                    echo $post->post_title;
                                endif;
                                ?>
                            </h2>

                            <p><?= $post->post_parent != 0 ? Polc_Helper_Module::limit_text(strip_tags($post->post_content), 600) : strip_tags($post->post_excerpt); ?></p>
                        </a>

                        <div class="plc_article_datas bottom">
                            <a href="#"><span
                                    class="comments"><?= wp_count_comments($post->ID)->total_comments . " " . __('Comment', 'polc'); ?>
                        </span></a>
                            <?php echo $restriction; ?>
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
            endforeach;
            ?>
        </div>
        <?php
    }

    /**
     * @param $posts
     * @param bool|false $animate
     */
    public static function post_list($posts, $animate = false)
    {
        foreach ($posts as $post):

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

                        <p class="date"><?= get_the_date('',$post); ?></p>
                    </div>
                </article>
            </a>
            <?php
        endforeach;
    }

    /**
     * @param $total_pages
     * @param $paged
     */
    public static function pagination($total_pages, $paged)
    {
        if ($total_pages == 1):
            return;
        endif;
        ?>

        <script>
            jQuery(document).ready(function () {
                jQuery(".plcPagerBtn").click(function () {
                    jQuery("#page").val(jQuery(this).attr("data-page"));
                    jQuery("#plcSearchForm").submit();
                });
            });
        </script>

        <div class="plcPagerWrapper">
            <div class="plcPagerInnerWrapper">
                <?php if ($paged > 4): ?>
                    <button class="plcPagerBtn" data-page="1"><?= __('First page', 'polc'); ?></button>
                <?php endif;

                //display backwards
                if ($paged > 1):
                    for ($i = max(1, $paged - 3); $i < $paged; $i++):
                        ?>
                        <button class="plcPagerBtn" data-page="<?= $i ?>"><?= $i; ?></button>
                        <?php
                    endfor;
                endif;

                //display current page
                ?>
                <button class="current_page"><?= $paged; ?></button>
                <?php for ($i = $paged + 1; $i <= min($paged + 3, $total_pages); $i++): ?>
                    <button class="plcPagerBtn" data-page="<?= $i ?>"><?= $i; ?></button>
                    <?php
                endfor;

                if ($paged < $total_pages - 3): ?>
                    <button class="plcPagerBtn"
                            data-page="<?= $total_pages ?>"><?= __('Last page', 'polc'); ?></button>
                    <?php
                endif;
                ?>
            </div>
        </div>
        <?php
    }
}