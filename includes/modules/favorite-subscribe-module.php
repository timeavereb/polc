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

            global $wpdb;
            $table = $wpdb->prefix . "polc_favorite_authors";

            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT Id FROM {$table} WHERE UserId = %d AND AuthorId = %d",
                    (int)self::$user->ID,
                    (int)$data["obj_id"]
                )
            );
            //If we found the author ID among the list, then it is "unlike", so we delete it from the list.
            if(count($result) > 0){
                $wpdb->delete($table, array("Id" => $result[0]->Id));
                $msg = __('Add author to favorites', 'polc');
            }
            //Otherwise we add the author id to the list
            else{
                $wpdb->insert(
                    $table,
                    array('UserId' => (int)self::$user->ID, 'AuthorId' => (int)$data["obj_id"]),
                    array('%d', '%d')
                );

                $msg = __('Remove author from favorites', 'polc');
            }

            wp_send_json(array("success" => $msg));
        }
    }
}