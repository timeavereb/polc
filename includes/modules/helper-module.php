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
        echo '<div class="plc_latest">';
        foreach ($posts as $post) {

            $tags = get_the_tags($post->ID);

            $user = Polc_Header::current_user();
            $logged = $user != null ? $user->ID : $user;

            $pages = get_option("polc_page_settngs");
            $new_story_page = get_permalink($pages["new-story-page"]);

            ?>
            <div class="latest_item <?= ($animate) ? "animate" : ""; ?>">
                <article class="plc_latest_item">
                    <div class="plc_article_datas top">
                        <?php if ($post->post_author != $logged) {
                            echo '<a href="' . get_author_posts_url($post->post_author) . '"><span class="author">' . get_the_author_meta("display_name", $post->post_author) . '</span></a>';
                        } else {
                            echo '<a href="#"><span class="comments">' . wp_count_comments($post->ID)->total_comments;
                            echo ' ' . __('Comment', 'polc') . '</span></a>';
                        }
                        echo '<span class="date">' . mysql2date("F j", $post->post_date) . '</span>';
                        echo '</div>';
                        echo '<a href="' . get_permalink($post->ID) . '">';
                        echo '<h2>' . $post->post_title . '</h2>';
                        echo '<p>' . $post->post_excerpt . '</p>';
                        echo '</a>';

                        echo '<div class="plc_article_datas bottom">';
                        if ($post->post_author != $logged) {
                            echo '<a href="#"><span class="comments">' . wp_count_comments($post->ID)->total_comments;
                            echo ' ' . __('Comment', 'polc') . '</span></a>';
                        } else {
                            echo '<form action="' . $new_story_page . '" method="POST">';
                            echo '<input type="hidden" name="volume-id" value="' . $post->ID . '">';
                            echo wp_nonce_field('add-chapter', 'volume-id-nonce');
                            echo '<input type="submit" class="addChapter" value="' . __('Add chapter', 'polc') . '">';
                            echo '</form>';

                            echo '<a class="editStory">';
                            echo __('Edit story', 'polc');
                            echo '</a>';
                        }
                        if ($tags):
                            for ($i = 0; $i < min(4, count($tags)); $i++):
                                echo '<a href="' . get_tag_link($tags[$i]->term_id) . '"><span
                                        class="category">' . $tags[$i]->name . '</span></a>';
                            endfor;
                        endif;
                        ?>
                    </div>
                </article>
            </div>
            <?php
        }

        echo '</div>';
    }

    public static function search_list($posts, $animate)
    {
        echo '<div class="plc_latest">';
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

                        <?php
                        echo '<a href="' . get_author_posts_url($post->post_author) . '"><span class="author">' . get_the_author_meta("display_name", $post->post_author) . '</span></a>';
                        echo '<span class="date">' . mysql2date("F j", $post->post_date) . '</span>';
                        echo '</div>';
                        echo '<a href="' . get_permalink($post->ID) . '">';
                        echo '<h2>';
                        if ($post->post_parent != 0) {
                            echo $parent_post->post_title . " - " . __('Chapter', 'polc') . " " . $chapter . ": " . $post->post_title;
                        }else{
                            echo $post->post_title;
                        }
                        echo '</h2>';
                        echo '<p>' . $post->post_excerpt . '</p>';
                        echo '</a>';

                        echo '<div class="plc_article_datas bottom">';
                        echo '<a href="#"><span class="comments">' . wp_count_comments($post->ID)->total_comments;
                        echo ' ' . __('Comment', 'polc') . '</span></a>';

                        if ($tags):
                            for ($i = 0; $i < min(4, count($tags)); $i++):
                                echo '<a href="' . get_tag_link($tags[$i]->term_id) . '"><span
                                        class="category">' . $tags[$i]->name . '</span></a>';
                            endfor;
                        endif;
                        ?>
                    </div>
                </article>
            </div>
            <?php
        }

        echo '</div>';
    }

    public static function post_list($posts, $animate = false)
    {

        foreach ($posts as $post) {

            $img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
            $img = isset($img[0]) && $img[0] != "" ? $img[0] : "http:/defaultimg.jpg";

            ?>
            <article>
                <div class="plcNewsImageWrapper">
                    <div class="plcNewsImage" style="background-image:url('<?= $img; ?>');"></div>
                </div>
                <div class="plcNewsText">
                    <h1><?= $post->post_title; ?></h1>

                    <p class="date"><?php echo get_the_date(); ?></p>
                </div>
            </article>
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
}