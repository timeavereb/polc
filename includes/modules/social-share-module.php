<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 06. 10.
 * Time: 9:46
 */

/**
 * Class Pol_Social_Share_Module
 */
class Polc_Social_Share_Module
{
    /**
     * @param $custom_args
     */
    public function __construct($custom_args = array())
    {
        $default_args = array(
            "fb_share",
            "fb_like",
            "tw_share",
            "g_share",
        );

        $args = wp_parse_args($custom_args, $default_args);

        echo '<div class="polcSocialShare">';

        foreach ($args as $value):
            switch ($value) {
                case "fb_share":
                    $this->facebook_share();
                    break;
                case "fb_like":
                    $this->facebook_like();
                    break;
                case "tw_share":
                    $this->twitter_share();
                    break;
                case "g_share":
                    $this->google_share();
                    break;
            }
        endforeach;

        echo '</div>';
    }

    public function facebook_share()
    {
        ?>
        <div class="fb_share_wrapper">
            <span></span>
            <div class="fb-share-button"></div>
        </div>
        <?php
    }

    public function facebook_like()
    {
        ?>
        <div class="fb_like_wrapper">
            <span></span>
            <div class="fb-like" data-layout="box_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
        </div>
        <?php
    }

    public function twitter_share()
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        ?>
        <a href="https://twitter.com/share?url=<?= $actual_link; ?>" target="_blank" class="twitterShareAnchor">
            <div class="twitter_share_wrapper"><span></span>
            </div>
        </a>
        <?php
    }

    public function google_share()
    {
        ?>
        <div class="gplus_share_wrapper">
            <span></span>
            <div class="g-plus-share-wrapper">
                <div class="g-plus" data-action="share"></div>
            </div>
        </div>
        <?php
    }
}