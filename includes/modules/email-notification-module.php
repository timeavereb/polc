<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 12. 08.
 * Time: 21:37
 */

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

parse_str($_REQUEST["data"], $data);
if (!wp_verify_nonce($data["plc_notification_nonce"], "plc_notification")) {
    wp_send_json(["error" => __('Invalid identifier!', 'polc')]);
    die();
}

$headers = ['From: ' . $data['sender_name'] . ' <' . $data['sender_email'] . '>'];
$body = $data["body"];
add_filter('wp_mail_content_type', 'polc_set_content_type');
var_dump(wp_mail($data['recipient'], $data["subject"], $body, $headers));