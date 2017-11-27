<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 09. 29.
 * Time: 15:14
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Favorite_Helper_Module
 */
class Polc_Favorite_Helper_Module
{
    /**
     * Retrieves the list of the author ids or user_ids based upon mode.
     * @param $user_id
     * @param string $mode
     * @return bool
     */
    public static function get_favorite_users($user_id, $mode = "authors")
    {
        if (!is_numeric($user_id)):
            return false;
        endif;

        global $wpdb;
        $table = $wpdb->prefix . "polc_favorite_authors";

        if ($mode == "authors"):
            $select = "AuthorId";
            $where = "UserId";
        elseif ($mode == "users"):
            $select = "UserId";
            $where = "AuthorId";
        else:
            return false;
        endif;

        $list = [];

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT {$select} FROM {$table} WHERE {$where} = %d",
                $user_id
            )
        );

        if (count($result) == 0):
            return $list;
        endif;

        foreach ($result as $value):
            $list[$value->$select] = $value->$select;
        endforeach;

        return $list;
    }

    /**
     * Retrieves the list of favorited post ids by user id.
     * @param $user_id
     * @return array
     */
    public static function get_favorite_stories($user_id)
    {
        $list = [];

        if (!is_numeric($user_id)):
            return $list;
        endif;

        global $wpdb;
        $table = $wpdb->prefix . "polc_favorite_stories";

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT PostId FROM {$table} WHERE UserId = %d",
                $user_id
            )
        );

        foreach ($results as $value):
            $list[$value->PostId] = $value->PostId;
        endforeach;

        return $list;
    }

    /**
     * Retirvies the top x favorited elements, where x can be passed as paramter.
     * @param int $limit
     * @param string $mode ( author, story )
     * @return array|mixed|null|object
     */
    public static function get_top_favorites($limit = 15, $mode = "author")
    {
        $cache = !isset(Polc_Settings_Manager::top_lists()["cache"]) || Polc_Settings_Manager::top_lists()["cache"] != "" ? true : false;
        global $wpdb;

        if ($mode == "author"):
            $cache_key = "top_favorite_authors";
            $table = $wpdb->prefix . "polc_favorite_authors";
            $select = "AuthorId";
        elseif ($mode == "story"):
            $cache_key = "top_favorite_contents";
            $table = $wpdb->prefix . "polc_favorite_stories";
            $select = "PostId";
        else:
            return false;
        endif;

        if ($cache):
            $result = get_transient($cache_key);
            if ($result):
                return $result;
            endif;
        endif;

        $results = $wpdb->get_results(
            "SELECT {$select} , COUNT(UserId) as FavoriteCnt
            FROM {$table}
            GROUP BY {$select}
            ORDER BY FavoriteCnt DESC
            LIMIT {$limit}"
        );

        $list = [];

        if ($mode == "author"):
            foreach ($results as $result):
                $user = get_user_by('ID', $result->AuthorId);
                $author_url = get_author_posts_url($result->AuthorId);
                $list[] = ["cnt" => $result->FavoriteCnt, "url" => $author_url, "name" => $user->data->user_login];
            endforeach;
        else:
            foreach ($results as $result):
                $user = get_post($result->PostId);
                $post_url = get_permalink($result->PostId);
                $list[] = ["cnt" => $result->FavoriteCnt, "url" => $post_url, "name" => $user->post_title];
            endforeach;
        endif;

        if ($cache):
            set_transient($cache_key, $list, Polc_Settings_Manager::top_lists()["cache_time"]);
        endif;

        return $list;
    }
}