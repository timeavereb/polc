<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 06. 10.
 * Time: 9:46
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Pol_Social_Share_Module
 */
class Polc_Social_Share_Module
{
    /**
     * Polc_Social_Share_Module constructor.
     * @param array $custom_args
     */
    public function __construct($custom_args = [])
    {
        $default_args = [
            "fb_like",
            "tw_share",
            "g_share",
        ];

        $args = wp_parse_args($custom_args, $default_args);
        ?>
        <div class="polcSocialShare">
            <?php
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
            ?>
        </div>
        <?php
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
            <div class="fb-like" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>
        </div>
        <?php
    }

    public function twitter_share()
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        ?>
        <a href="https://twitter.com/share?url=<?= $actual_link; ?>" target="_blank" class="twitterShareAnchor twitter-share-button" data-size="default">
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