<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 10.
 * Time: 11:22
 */

get_header();

the_title();
the_post_thumbnail("full");
the_excerpt();
the_content();

new Polc_Social_Share_Module();

get_footer();