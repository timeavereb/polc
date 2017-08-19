<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 11.
 * Time: 18:26
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

$allowed = array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG");
$ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
if(!in_array($ext, $allowed)){
    wp_send_json(array("error" => __( 'The file your\'re trying to upload isn\'t a valid image type', 'polc')));
}

if (!is_user_logged_in()) {
    wp_die();
}

$user = wp_get_current_user();

$current = get_user_meta($user->ID, "polc_current_avatar")[0];

//if the user current has avatar we delete it , later we upload the new one
if ($current != "") {
    wp_delete_attachment($current["id"], true);
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

$attachment_id = media_handle_upload('file', 0);

$data = array("id" => $attachment_id, "src" => wp_get_attachment_image_src($attachment_id, "medium")[0]);

update_user_meta($user->ID, "polc_current_avatar", $data);

wp_send_json($data);
