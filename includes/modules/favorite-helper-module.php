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

    /**
     * Retirvies the top x favorited authors, where x can be passed as paramter.
     * @param int $limit
     * @return array|bool|null|object
     */
    public static function get_top_favorite_authors($limit = 15)
    {
        if(!is_numeric($limit)){
            return false;
        }

        global $wpdb;

        $table = $wpdb->prefix . "polc_favorite_authors";

        return $wpdb->get_results(
            "SELECT  AuthorId , COUNT(UserId) as FavoriteCnt
            FROM {$table}
            GROUP BY AuthorID
            ORDER BY FavoriteCnt DESC
            LIMIT {$limit}"
        );
    }
}