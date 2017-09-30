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
$manager->favorite($_REQUEST);

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
    public function favorite($data)
    {
        global $wpdb;

        if ($data["mode"] == "author") {
            $table = $wpdb->prefix . "polc_favorite_authors";
            $key = "AuthorId";
            $messages = array(
                "add" => __('Add author to favorites', 'polc'),
                "remove" => __('Remove author from favorites', 'polc')
            );
        } else {
            $table = $wpdb->prefix . "polc_favorite_stories";
            $key = "PostId";
            $messages = array(
                "add" => __('Add to favorites', 'polc'),
                "remove" => __('Remove from favorites', 'polc')
            );
        }

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT Id FROM {$table} WHERE UserId = %d AND {$key} = %d",
                (int)self::$user->ID,
                (int)$data["obj_id"]
            )
        );

        //If we found the author ID among the list, then it is "unlike", so we delete it from the list.
        if (count($result) > 0) {
            $wpdb->delete($table, array("Id" => $result[0]->Id));
            $msg = $messages["add"];
        }
        //Otherwise we add the author id to the list
        else {
            $wpdb->insert(
                $table,
                array('UserId' => (int)self::$user->ID, $key => (int)$data["obj_id"]),
                array('%d', '%d')
            );
            $msg = $messages["remove"];
        }

        wp_send_json(array("success" => $msg));
    }
}