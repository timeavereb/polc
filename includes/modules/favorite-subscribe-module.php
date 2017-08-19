<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 07. 01.
 * Time: 10:46
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

if (!is_user_logged_in()) {
    wp_send_json(array("error" => __('You\'ve not signed in!', 'polc')));
}

if (!isset($_REQUEST["action"]) || !isset($_REQUEST["mode"])) {
    wp_send_json(array("error" => __('Invalid action!', 'polc')));
}

$manager = new Polc_Favorite_Subscribe_Module();

switch ($_REQUEST["action"]) {
    case "favorite":
        $manager::favorite($_REQUEST);
        break;
}

/**
 * Favorite and subscribe handler class.
 * Class Polc_Favorite_Subscribe_Module
 */
class Polc_Favorite_Subscribe_Module
{
    public static $user;
    public static $list;

    public function __construct()
    {
        self::$user = wp_get_current_user();
        self::$list = new stdClass();
    }

    /**
     * @param $data
     */
    public static function favorite($data)
    {
        if ($data["mode"] == "author") {

            /*delete_user_meta(self::$user->ID, "favorite_author_list");
            delete_user_meta($_REQUEST["obj_id"], 'favorite_user_list');
            delete_user_meta($_REQUEST["obj_id"], 'user_favorite_cnt');

            return;*/
            //The meta container all the author ids
            self::$list->favorite_list = unserialize(get_user_meta(self::$user->ID, "favorite_author_list", true));
            //The meta that the author has containing all user id whom favorited her/him.
            self::$list->user_list = unserialize(get_user_meta($_REQUEST["obj_id"], 'favorite_user_list', true));
            //The meta that the author has containg favourite count
            $cnt = get_user_meta($_REQUEST["obj_id"], 'user_favorite_cnt', true);
            $add = true;

            //If we found the author ID among the list, then it is "unlike", so we delete it from the list.
            if (is_array(self::$list->favorite_list) && array_key_exists($data["obj_id"], self::$list->favorite_list)) {
                unset( self::$list->favorite_list[$data["obj_id"]]);
                unset(self::$list->user_list[self::$user->ID]);
                $add = false;
            }
            //Otherwise we add the author id to the list
            else {
                self::$list->favorite_list[$data["obj_id"]] = $data["obj_id"];
                self::$list->user_list[self::$user->ID] = self::$user->ID;
            }

            if ($add) {
                if (empty($cnt)) {
                    $cnt = 1;
                } else {
                    $cnt++;
                }

                $msg = __('Remove author from favorites', 'polc');

            } else {
                if (empty(self::$list->user_list)) {
                    $cnt = "";
                } else {
                    $cnt--;

                }
                $msg = __('Add author to favorites', 'polc');
            }

            update_user_meta($_REQUEST["obj_id"], 'user_favorite_cnt', $cnt);
            update_user_meta($_REQUEST["obj_id"], 'favorite_user_list', serialize(self::$list->user_list));
            update_user_meta(self::$user->ID, "favorite_author_list",  serialize(self::$list->favorite_list));
            wp_send_json(array("success" => $msg));
        }
    }
}