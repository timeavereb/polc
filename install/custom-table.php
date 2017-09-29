<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 09. 29.
 * Time: 14:27
 */

if(!defined("ABSPATH")){
    exit();
}

/**
 * Class Polc_Install_Tables
 */
class Polc_Install_Tables
{
    public function init()
    {
        global $wpdb;
        $table = $wpdb->prefix . "polc_favorite_authors";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE  $table (
        Id INT(11) NOT NULL AUTO_INCREMENT,
        UserId INT(11) NOT NULL COMMENT 'WPUserId',
        AuthorId INT(11) NOT NULL COMMENT 'WPUserId',
        PRIMARY KEY  (Id),
        CONSTRAINT FK_polc_wpuser_author_user_id FOREIGN KEY (UserId) REFERENCES wp_users(ID),
        CONSTRAINT FK_polc_wpuser_author_author_id FOREIGN KEY (AuthorId) REFERENCES wp_users(ID)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $table = $wpdb->prefix . "polc_favorite_stories";
        $post_table = $wpdb->prefix . "posts";

        $sql = "CREATE TABLE  $table (
        Id INT(11) NOT NULL AUTO_INCREMENT,
        UserId INT(11) NOT NULL COMMENT 'WPUserId',
        PostId INT(11) NOT NULL COMMENT 'WPPostId',
        PRIMARY KEY  (Id),
        CONSTRAINT FK_polc_wpuser_story_user_id FOREIGN KEY (UserId) REFERENCES wp_users(ID),
        CONSTRAINT FK_polc_wpuser_story_post_id FOREIGN KEY (PostId) REFERENCES $post_table(ID)
        ) $charset_collate;";

        dbDelta($sql);

        update_option("polc_version", '1.0');
    }
}