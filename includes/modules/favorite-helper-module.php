<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 09. 29.
 * Time: 15:14
 */

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
        $list = array();

        if (!is_numeric($user_id)) {
            return $list;
        }

        global $wpdb;
        $table = $wpdb->prefix . "polc_favorite_authors";

        if ($mode == "authors") {
            $select = "AuthorId";
            $where = "UserId";
        } else {
            $select = "UserId";
            $where = "AuthorId";
        }

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT {$select} FROM {$table} WHERE {$where} = %d",
                $user_id
            )
        );

        if (count($result) == 0) {
            return $list;
        }

        foreach ($result as $value) {
            $list[$value->$select] = $value->$select;
        }

        return $list;
    }

    public static function get_favorite_stories($user_id){

        $list = array();

        if (!is_numeric($user_id)) {
            return $list;
        }

        global $wpdb;
        $table = $wpdb->prefix . "polc_favorite_stories";

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT PostId FROM {$table} WHERE UserId = %d",
                $user_id
            )
        );

        foreach ($results as $value) {
            $list[$value->PostId] = $value->PostId;
        }

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

        if($mode == "author"){
            $cache_key = "top_favorite_authors";
            $table = $wpdb->prefix . "polc_favorite_authors";
            $select = "AuthorId";
        }else{
            $cache_key = "top_favorite_contents";
            $table = $wpdb->prefix . "polc_favorite_stories";
            $select = "PostId";
        }

        if ($cache):
            $result = get_transient($cache_key);
            if ($result):
                return $result;
            endif;
        endif;

        $results =  $wpdb->get_results(
            "SELECT {$select} , COUNT(UserId) as FavoriteCnt
            FROM {$table}
            GROUP BY {$select}
            ORDER BY FavoriteCnt DESC
            LIMIT {$limit}"
        );

        $list = array();

        if($mode == "author"){
            foreach($results as $result){
                $user = get_user_by('ID', $result->AuthorId);
                $author_url = get_author_posts_url($result->AuthorId);
                $list[] = array("cnt" => $result->FavoriteCnt, "url" => $author_url, "name" => $user->data->display_name);
            }
        }else{
            foreach($results as $result){
                $user = get_post($result->PostId);
                $post_url = get_permalink($result->PostId);
                $list[] = array("cnt" => $result->FavoriteCnt, "url" => $post_url, "name" => $user->post_title);
            }
        }

        if ($cache):
            set_transient($cache_key, $list, Polc_Settings_Manager::top_lists()["cache_time"]);
        endif;

        return $list;
    }
}