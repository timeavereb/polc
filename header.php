<?php

/**
 * Class Polc_Header
 */
class Polc_Header
{
    public static $curr_user;
    public static $curr_user_meta;

    /**
     * Polc_Header constructor.
     */
    public function __construct()
    {
        if (is_user_logged_in()) {
            self::$curr_user = wp_get_current_user();
        }

        $this->init_head();
        $this->register_handler();
        $this->login_handler();
        $this->lost_password_handler();
        $this->side_menu();
        $this->alert_popup();
    }

    /**
     * Return logged user infos.
     * @return mixed
     */
    public static function current_user()
    {
        if(empty(self::$curr_user_meta) && is_user_logged_in()){
            self::$curr_user->data->favorite_author_list = Polc_Favorite_Helper_Module::get_favorite_users(self::$curr_user->ID, "authors");
            self::$curr_user->data->favorite_content_list = Polc_Favorite_Helper_Module::get_favorite_stories(self::$curr_user->ID);
            self::$curr_user->data->user_birth_date = get_user_meta(self::$curr_user->ID, 'user_birth_date', true);
        }

        return self::$curr_user;
    }

    /**
     * Head section init
     */
    private function init_head()
    {
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
            <title><?php bloginfo('name'); ?></title>
            <?php wp_head(); ?>
        </head>

        <body>
        <script>var polc_header_handler = new polc_header_handler();</script>
        <header class="plc_header">
            <a href="http://polc.eu/"><span class="site_id"></span></a>
            <form id="plc_searchform" action="<?= home_url(); ?>" method="POST">
            <div class="plcHeaderSearch"><input type="text" name="s" placeholder="Search..." onkeypress="if(event.keyCode == 13){ jQuery('#plc_searchform').submit();) }"></div>
            </form>
        </header>
        <?php
    }

    /**
     * Registration popup.
     */
    private function register_handler()
    {
        ?>
        <div id="plc_reg_popup" class="popup">
            <span class="quit"></span>

            <div class="reg_box_top">
                <form id="plc_reg_form">
                    <h1><?= __('Registration', 'polc'); ?></h1>

                    <div class="regbox_row">
                        <input type="text" id="username" name="username" placeholder="<?= __('Username', 'polc'); ?>">
                        <span class="plcErrorText" id="username-error-msg"></span>
                    </div>
                    <div class="regbox_row">
                        <input type="text" id="email" name="email" placeholder="<?= __('Email', 'polc'); ?>">
                        <span class="plcErrorText" id="email-error-msg"></span>
                    </div>
                    <div class="regbox_row">
                        <input type="text" id="email-conf" name="email-conf"
                               placeholder="<?= __('Email confirmation', 'polc'); ?>">
                        <span class="plcErrorText" id=""></span>
                    </div>
                    <div class="regbox_row">
                        <input type="password" id="password" name="password"
                               placeholder="<?= __('Password', 'polc'); ?>">
                        <span class="plcErrorText" id="password-error-msg"></span>
                    </div>
                    <div class="regbox_row">
                        <input type="password" id="password-conf" name="password-conf"
                               placeholder="<?= __('Password confirmation', 'polc'); ?>">
                        <span class="plcErrorText" id=""></span>
                    </div>
                    <div class="regbox_row termsouse">
                        <input type="checkbox" id="terms" name="terms">
                        <label><?= __('I accept the', 'polc'); ?> <a
                                href=""><?= __('tems of use', 'polc'); ?></a></label>
                        <span class="plcErrorText" id="terms-error-msg"></span>
                    </div>
                    <div class="regbox_row submit">
                        <button id="plc_register_btn"><?= __('Registration', 'polc'); ?></button>
                    </div>
                </form>
            </div>
            <div class="reg_box_bottom">
                <p><?= __('Already registered?', 'polc'); ?></p><span
                    class="login"><?= __('Sign in!', 'polc'); ?></span>
            </div>
            <div class="reg_box_social">
            </div>
            <div class="reg_box_login">
            </div>
        </div>
        <?php
    }

    /**
     * Login popup.
     */
    private function login_handler()
    {
        ?>
        <div id="plc_login_popup" class="popup">

            <span class="quit"></span>

            <form id="plc_login_form">
                <h1><?= __('Sign in', 'polc'); ?></h1>

                <div class="reg_box_top">
                    <div class="regbox_row">
                        <input type="text" id="login" name="login"
                               placeholder="<?= __('Username or email', 'polc'); ?>">
                    </div>
                    <div class="regbox_row">
                        <input type="password" id="password" name="password"
                               placeholder="<?= __('Password', 'polc'); ?>">
                    </div>
                    <div class="polc_lostpass-wrapper">
                        <button id="plc_lost_password_btn"><?= __( 'Forgot your password?', 'polc' );?></button>
                    </div>
                    <div class="regbox_row submit">
                        <button id="plc_login_btn"><?= __('Sign In', 'polc'); ?></button>
                    </div>
                </div>
                <div class="reg_box_bottom">
                    <p><?= __('Still not part of the team?', 'polc'); ?></p><span
                        class="signup"><?= __('Sign up now!', 'polc'); ?></span>
                </div>
                <div class="login_box_social">
                    <h2><?= __('Sign in with your social account', 'polc'); ?></h2>

                    <div class="social_login_icon_wrapper">
                        <span class="plc_social_login facebook"></span>
                        <span class="plc_social_login google"></span>
                        <span class="plc_social_login twitter"></span>
                    </div>
                </div>
                <div class="reg_box_login">
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Side menu.
     */
    private function side_menu()
    {
        ?>
        <div class="plc_side_naviagtion">
            <span class="plc_navigation_icon"></span>

            <div class="plc_regnlogin">
                <?php
                if (!self::current_user()) {
                    ?>
                    <a href="#" class="signup" id="plc_registration"><?= __('Registration', 'polc'); ?></a>
                    <a href="#" class="login" id="plc_sign_up"><?= __('Login', 'polc'); ?></a>
                    <?php
                } else {
                    ?>
                    <a href="#" class="logout" id="plc_logout"><?= __('Logout', 'polc'); ?></a>
                    <?php
                    echo '<div class="plc_user_info_wrapper">';
                    echo '<div class="welcome-wrapper">';
                    echo '<span class="plc_welcome_text">' . __('Welcome', 'polc') . '</span>';
                    echo '<span class="name">' . self::$curr_user->user_nicename . '</span>';
                    echo '</div>';
                    echo '<ul class="plc_user_menu">';
                    echo '<li><a href="' . get_author_posts_url(self::$curr_user->ID) . '">' . __('My profile', 'polc') . '</a></li>';
                    echo '</ul>';
                    echo '</div>';
                } ?>
            </div>
            <?php

            foreach (wp_get_nav_menu_items('polc-main-menu',
                array(
                    'order' => 'ASC',
                    'orderby' => 'menu_order',
                    'post_type' => 'nav_menu_item',
                    'post_status' => 'publish',
                    'output' => ARRAY_A,
                    'output_key' => 'menu_order',
                    'nopaging' => true
                )) as $menu_element) {
                ?>
                <a href="<?= $menu_element->url; ?>"
                   class="plc_navigation_item_wrapper <?= sanitize_title($menu_element->title); ?>">
                    <div class="plc_navigation_item"><?= $menu_element->title; ?></div>
                </a>
                <?php
            }
            ?>
        </div>
        <?php
    }

    private function lost_password_handler(){
        ?>
        <div id="plc_lost_password_popup" class="popup" style="display:none;">
            <span class="plc_lost_password_msg">
                <?= __("Forgot your password? Enter the email address or username to retrieve your account", "polc");?>
            </span>
            <form id="plc_lost_password_form">
                <input id="lost_password_login" name="lost_password_login">
            </form>
        </div>
        <?php
    }

    private function alert_popup()
    {
        ?>
        <div id="plc_alert_popup" class="popup">
            <span class="quit"></span>
        </div>
        <?php
    }
}

new Polc_Header();
?>