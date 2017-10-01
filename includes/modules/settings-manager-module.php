<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 18.
 * Time: 8:40
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Settings_Manager
 */
class Polc_Settings_Manager
{
    public static $email;
    public static $pages;
    public static $layout;
    public static $genres;
    public static $categories;

    /**
     * Layout settings.
     * @return mixed|void
     */
    public static function layout()
    {
        if (empty(self::$layout)):
            self::$layout = get_option("polc-layout-settings");
        endif;

        return self::$layout;
    }

    /**
     * Email settings;
     * @return mixed|void
     */
    public static function email()
    {
        if (empty(self::$email)):
            self::$email = json_decode(get_option("polc-email-settings"), true);
        endif;

        return self::$email;
    }

    /**
     * Returns register template settings.
     * @return mixed
     */
    public static function register_email()
    {
        return self::email()["register"];
    }

    /**
     * Returns lost password template settings.
     * @return mixed
     */
    public static function lost_password_email()
    {
        return self::email()["lost_password"];
    }

    /**
     * Returns pages settings.
     * @return mixed|void
     */
    public static function pages()
    {
        if (empty(self::$pages)):
            self::$pages = get_option("polc-page-settings");
        endif;

        return self::$pages;
    }

    /**
     * New story page
     * @return mixed
     */
    public static function new_story_page()
    {
        return self::pages()["new-story-page"];
    }

    /**
     * Registration activation page
     * @return mixed
     */
    public static function activation_page()
    {
        return self::pages()["reg-activation"];
    }

    /**
     * Return password reset page id.
     * @return mixed
     */
    public static function password_reset_page()
    {
        return self::pages()["password-reset"];
    }

    /**
     * Returns category settings.
     * @return mixed|void
     */
    public static function categories()
    {
        if (empty(self::$categories)):
            self::$categories = get_option("polc-category-settings");
        endif;

        return self::$categories;
    }

    /**
     * Returns toplists settings.
     * @return mixed
     */
    public static function top_lists()
    {
        return self::layout()["toplists"];
    }
}