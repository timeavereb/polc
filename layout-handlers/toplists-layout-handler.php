<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 25.
 * Time: 20:17
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Class Polc_Toplists_Layout_Handler
 */
class Polc_Toplists_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-toplists";
    CONST POLC_LAYOUT_NAME = "Sikerlisták";

    private $top_favorited_authors;
    private $top_favorited_contents;
    private $top_commenters;

    private $top_views;

    private $cache;
    private $cache_time;

    public function render()
    {

        $this->cache = !isset(Polc_Settings_Manager::top_lists()["cache"]) || Polc_Settings_Manager::top_lists()["cache"] != "" ? true : false;
        $this->cache_time = is_numeric(Polc_Settings_Manager::top_lists()["cache_time"]) ? Polc_Settings_Manager::top_lists()["cache_time"] : 120;

        //most favorited authors
        $this->top_favorited_authors = Polc_Favorite_Helper_Module::get_top_favorites(Polc_Settings_Manager::top_lists()["authors_cnt"], "author");

        //most active commenters
        $this->top_commenters = $this->top_comment_authors(Polc_Settings_Manager::top_lists()["commenters_cnt"]);

        //top favorited contents
        $this->top_favorited_contents = Polc_Favorite_Helper_Module::get_top_favorites(Polc_Settings_Manager::top_lists()["stories_cnt"], "story");

        if (class_exists("Post_Views_Counter")):
            //most viewed contents ( with chapters )
            $this->top_views = $this->top_views(Polc_Settings_Manager::top_lists()["top_views_cnt"]);
        endif;
        ?>

        <div class="plcToplistsWrapper">
            <!-- Favorite authors-->
            <div class="toplistsItem favouriteWriters">
                <div class="innerItem">
                    <h1><?= __('Most favorited authors', 'polc'); ?></h1>

                    <div class="list">
                        <?php
                        $cnt = 1;
                        foreach ($this->top_favorited_authors as $author):
                            ?>
                            <div class="toplistListItem">
                                <a href="<?= $author["url"]; ?>"><span><?= $cnt; ?>.</span>

                                    <h2><?= $author["name"]; ?></h2>
                                </a>
                                <a href="#"><?= $author["cnt"] . ' ' .
                                    ($author["cnt"] == 1 ? __('person\'s favorite author', 'polc') : __('people\'s favorite author', 'polc')); ?></a>
                            </div>
                            <?php
                            $cnt++;
                        endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Top commenters -->
            <div class="toplistsItem commentators">
                <div class="innerItem">
                    <h1><?= __('Most active comment writers', 'polc'); ?></h1>

                    <div class="list">
                        <?php
                        if (is_array($this->top_commenters)):
                            $cnt = 1;
                            foreach ($this->top_commenters as $value):
                                ?>
                                <div class="toplistListItem">
                                    <a href="<?= $value["url"]; ?>">
                                        <span><?= $cnt; ?>.</span>

                                        <h2><?= $value["name"]; ?></h2>
                                        <a href="#"
                                           class="plcCommentCnt"><?= $value["cnt"] . " " . __('comments', 'polc'); ?></a>
                                    </a>
                                </div>
                                <?php
                                $cnt++;
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <!-- Favorite contents -->
            <div class="toplistsItem  favouriteContent">
                <div class="innerItem">
                    <h1><?= __('Most favorite stories', 'polc'); ?></h1>

                    <div class="list">
                        <?php
                        $cnt = 1;
                        foreach ($this->top_favorited_contents as $content): ?>
                            <div class="contentItem">
                                <a href="<?= $content["url"]; ?>"><span><?= $cnt; ?>.</span>

                                    <h2><?= $content["name"]; ?></h2></a>
                                <a href="#">
                                    <?= $content["cnt"] . ' ' .
                                    ($content["cnt"] == 1 ? __('person\'s favorite content', 'polc') : __('people\'s favorite content', 'polc')); ?></a>
                            </div>
                            <?php
                            $cnt++;
                        endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if (class_exists("Post_Views_Counter")): ?>
                <?php foreach ($this->top_views as $key => $value): ?>
                    <div class="toplistsItem  view">
                        <div class="innerItem">
                            <h1>
                                <?= $key == "with_chapter" ? __('The most read stories (with sequels)', 'polc') : __('The most read stories (without sequels)', 'polc'); ?>
                            </h1>

                            <div class="list">
                                <?php
                                $cnt = 1;
                                foreach ($value as $v): ?>
                                    <div class="contentItem">
                                        <a href="<?= $v["url"]; ?>"><span><?= $cnt; ?>.</span>

                                            <h2><?= $v["name"]; ?></h2></a>

                                        <p><?= $v["cnt"] . " " . __('views', 'polc'); ?></p>
                                    </div>
                                    <?php
                                    $cnt++;
                                endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>

        </div>

        <?php
    }

    /**
     * @param int $limit
     * @return bool
     */
    private function top_comment_authors($limit = 10)
    {
        if ($this->cache):
            $result = get_transient("top_comment_authors");
            if ($result):
                return $result;
            endif;
        endif;

        if (!is_numeric($limit)):
            return false;
        endif;

        global $wpdb;

        $results = $wpdb->get_results("
        SELECT COUNT(comment_ID) as CommentCnt, user_id as UserId
        FROM {$wpdb->comments}
        WHERE comment_approved = 1
        GROUP BY user_id
        ORDER BY CommentCnt DESC
        LIMIT {$limit}
        ");

        $list = [];

        foreach ($results as $value):
            $user = get_user_by('ID', $value->UserId);
            $url = get_author_posts_url($value->UserId);
            $list[] = ["url" => $url, "name" => $user->data->display_name, "cnt" => $value->CommentCnt];
        endforeach;

        if ($this->cache):
            set_transient("top_comment_authors", $list, $this->cache_time);
        endif;

        return $list;
    }

    private function top_views($limit = 10)
    {
        $list = [];
        if ($this->cache):
            $result = get_transient("top_views");
            if ($result):
                return $result;
            endif;
        endif;

        global $wpdb;
        $count_table = $wpdb->prefix . "post_views";
        $post_table = $wpdb->prefix . "posts";
        $post_meta_table = $wpdb->prefix . "postmeta";

        //Get top x volumes with it's sum of children views.
        $with_chapters = $wpdb->get_results("
                SELECT SUM(pw.count) as VolumeTotal , p.post_parent
                FROM {$count_table} pw
                JOIN {$post_table} p ON pw.id = p.ID
                WHERE pw.type = 4
                AND p.post_status = 'publish' AND p.post_type = 'story'
                AND p.post_parent != 0
                GROUP BY p.post_parent
                ORDER BY VolumeTotal DESC
                LIMIT {$limit}
        ");

        $no_chapters = $wpdb->get_results("
            SELECT pw.count, pw.id, p.post_title
            FROM {$count_table} pw
            JOIN {$post_table} p ON p.ID = pw.id
            JOIN {$post_meta_table} pm ON p.ID = pm.post_id
            WHERE pw.type = 4
            AND p.post_type = 'story'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'single'
            ORDER BY pw.count DESC
            LIMIT {$limit}
        ");

        foreach ($with_chapters as $story):
            $post = get_post($story->post_parent);
            $list["with_chapter"][] = ["cnt" => $story->VolumeTotal, "name" => $post->post_title, "url" => get_permalink($story->post_parent)];
        endforeach;

        foreach ($no_chapters as $story):
            $post = get_post($story->id);
            $list["no_chapter"][] = ["cnt" => $story->count, "name" => $post->post_title, "url" => get_permalink($story->id)];
        endforeach;

        if ($this->cache):
            set_transient("top_views", $list, $this->cache_time);
        endif;

        return $list;
    }
}