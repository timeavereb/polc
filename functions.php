<?php

define('PLC_THEME_SLUG', 'polc');
define('PLC_THEME_PATH', get_template_directory_uri());

load_theme_textdomain('polc', get_template_directory() . '/languages');

require_once "includes/types/story.php";
require_once "includes/taxonomies/genre.php";
require_once "layout-handlers/load-layouts.php";
require_once "includes/modules/modules.php";
require_once "includes/admin/pages/pages.php";

remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'start_post_rel_link', 10);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'rel_next');
remove_action('wp_head', 'parent_post_rel_link', 10);
remove_action('wp_head', 'locale_stylesheet');
remove_action('wp_head', 'noindex');
remove_action('wp_head', 'wp_print_styles');
remove_action('wp_head', 'wp_print_head_scripts');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');


//nav menus
register_nav_menus([
    'header-menu' => 'Header menu',
    'footer-menu' => 'Footer menu'
]);

//enqueue style and scripts
add_action("wp_enqueue_scripts", function () {

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script('jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js');
    wp_enqueue_style('polc-style', PLC_THEME_PATH . '/css/style.css');
    wp_enqueue_style('fonts-style', PLC_THEME_PATH . '/css/fonts.css');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('polc-scripts', PLC_THEME_PATH . '/js/scripts.js');
    wp_enqueue_script('polc-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=recaptcha_loaded&render=explicit', [], false, true);
});

add_action("admin_enqueue_scripts", function(){

    wp_enqueue_style('polc-admin-style', PLC_THEME_PATH . '/css/admin_style.css');
    wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('polc-admin-scripts', PLC_THEME_PATH . '/js/admin-handler.js');
    wp_enqueue_script('jquery-ui-dialog');
});

//jquery migare fix
add_action('wp_default_scripts', function ($scripts) {
    if (!empty($scripts->registered['jquery'])):
        $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
    endif;
});

function polc_set_content_type()
{
    return "text/html";
}

add_theme_support('post-thumbnails');

//author slug
add_action('init', function () {
    global $wp_rewrite;
    $author_slug = __('author-slug', 'polc');
    $wp_rewrite->author_base = $author_slug;

    $comment_page_id = Polc_Settings_Manager::pages()["comment_page"];
    if (isset($comment_page_id) && is_numeric($comment_page_id)):

        $comment_page = get_post($comment_page_id);
        add_rewrite_rule('^' . $comment_page->post_name . '/([^/]*)/?$', 'index.php?page_id=' . $comment_page_id . '&comment_post_slug=$matches[1]', 'top');
    endif;
});

add_filter('query_vars', function ($vars) {
    $vars[] = "comment_post_slug";
    return $vars;
});

add_filter('pre_get_posts', 'polc_tag_query');
show_admin_bar(false);

/*
function polc_tag_query($query)
{
    if ($query->is_main_query() && ($query->is_paged() && is_tag())):
        $query->set('post_type', 'story');
        $query->set('posts_per_page', 20);
        $query->set('post_parent', 0);
        $query->set('post_status', 'publish');
    endif;

    return $query;
}*/

add_action("wp_head", "polc_setup_head");

/**
 * Sets the head section.
 */
function polc_setup_head()
{
    if (isset(Polc_Settings_Manager::common()["ga_id"])):
        ?>
        <!-- Google Analytics -->
        <script type="text/javascript">
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', '<?= Polc_Settings_Manager::common()["ga_id"]; ?>', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- End Google Analytics -->
        <?php
    endif;

    global $post;

    if (isset($post->post_type) && ($post->post_type == "story" || $post->post_type == "post") && !is_search()):

        if ($post->post_parent == 0):
            $content = wp_trim_words(strip_tags($post->post_excerpt), 40, "...");
        else:
            $content = wp_trim_words(strip_tags($post->post_content), 40, "...");
        endif;

        $featured_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "medium");
        $image = !$featured_img ? home_url() . "/wp-content/themes/polc/img/social/share_default.png" : $featured_img[0];

        if (!$featured_img):
            $image_info = getimagesize($image);
            $width = $image_info[0];
            $height = $image_info[1];
        else:
            $width = $featured_img[1];
            $height = $featured_img[2];
        endif;

        ?>
        <!--g+ sdk-->
        <script src="https://apis.google.com/js/platform.js" async defer></script>

        <!-- fb sdk-->
        <div id="fb-root"></div>
        <script>(function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/hu_HU/sdk.js#xfbml=1&version=v2.10";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>


        <script>window.twttr = (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0],
                    t = window.twttr || {};
                if (d.getElementById(id)) return t;
                js = d.createElement(s);
                js.id = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);

                t._e = [];
                t.ready = function (f) {
                    t._e.push(f);
                };

                return t;
            }(document, "script", "twitter-wjs"));</script>

        <!--setting up meta tags-->
        <meta property="og:title" content="<?= $post->post_title; ?>"/>
        <meta property="og:image" content="<?= $image; ?>">
        <meta property="og:site_name" content="Polc"/>
        <meta property="og:description" content="<?= $content; ?>"/>
        <meta property="og:image:width" content="<?= $width; ?>"/>
        <meta property="og:image:height" content="<?= $height; ?>"/>

        <?php
    endif;
}

/**
 * Managing custom capabilites
 */
function add_theme_caps()
{
    $admin = get_role("administrator");
    $recommendation_writer = get_role("polc_recommendation_writer");
    $content_censor = get_role("polc_content_censor");
    $help_writer = get_role("polc_help_writer");

    $admin->add_cap("edit_story");
    $admin->add_cap("edit_stories");
    $admin->add_cap("edit_other_stories");
    $admin->add_cap("edit_published_stories");
    $admin->add_cap("publish_stories");
    $admin->add_cap("read_story");
    $admin->add_cap("read_private_stories");
    $admin->add_cap("delete_stories");
    $admin->add_cap("delete_others_stories");
    $admin->add_cap("delete_published_stories");

    $content_censor->add_cap("read");
    $content_censor->add_cap("edit_story");
    $content_censor->add_cap("edit_stories");
    $content_censor->add_cap("edit_published_stories");
    $content_censor->add_cap("edit_other_stories");
    $content_censor->add_cap("publish_stories");
    $content_censor->add_cap("read_story");
    $content_censor->add_cap("delete_stories");
    $content_censor->add_cap("delete_others_stories");

    $recommendation_writer->add_cap("read");
    $recommendation_writer->add_cap("edit_post");
    $recommendation_writer->add_cap("edit_posts");
    $recommendation_writer->add_cap("upload_files");

    $help_writer->add_cap("read");
    $help_writer->add_cap("edit_post");
    $help_writer->add_cap("edit_posts");
    $help_writer->add_cap("upload_files");
}

add_filter('list_terms_exclusions', 'polc_category_exclusion', 10, 2);

/**
 * For recommendation writers only allows the selected category.
 * @param $exclusions
 * @param $args
 * @return string
 */
function polc_category_exclusion($exclusions, $args)
{
    if (is_admin() && current_user_can("polc_recommendation_writer")):
        $recommend_cat = Polc_Settings_Manager::layout()["recommend"]["term_id"];
        if (isset($recommend_cat) && is_numeric($recommend_cat) && $recommend_cat > 0):
            $exclusions = " {$exclusions} AND t.term_id IN (" . $recommend_cat . ")";
        endif;
    endif;

    if (is_admin() && current_user_can("polc_help_writer")):
        $help_cat = Polc_Settings_Manager::categories()["help-category"];
        if (isset($help_cat) && is_numeric($help_cat) && $help_cat > 0):
            $exclusions = " {$exclusions} AND t.term_id IN (" . $help_cat . ")";
        endif;
    endif;

    return $exclusions;
}

add_action('after_switch_theme', 'polc_theme_setup');

function polc_theme_setup()
{
    if (!get_option("polc_version")):
        require_once "install/custom-table.php";
        $install = new Polc_Install_Tables();
        $install->init();
    endif;

    //Creating custom roles if they don't exist.
    add_role('polc_recommendation_writer', __('Recommendation Writer', 'polc'));
    add_role('polc_help_writer', __('Help Writer', 'polc'));
    add_role('polc_content_censor', __('Content Censor', 'polc'));
    add_role('polc_frontend_user', __('Frontend User', 'polc'), ["read" => false]);

    add_theme_caps();
}
