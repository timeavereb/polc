<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 10.
 * Time: 11:22
 */

get_header();

global $post;
setup_postdata($post);

$args = [
    "post_type" => ["post"],
    "posts_per_page" => 3,
    "post_status" => "publish",
    "post__not_in" => [$post->ID],
    "category__in" => [Polc_Settings_Manager::layout()["recommend"]["term_id"]]
];

$article_category = wp_get_post_categories($post->ID)[0];
$recommend_articles = get_posts($args);
$args["category__in"] = [Polc_Settings_Manager::layout()["news"]["term_id"]];
$news = get_posts($args);
?>


    <div class="plcArticleWrapper">
        <div class="plcArticleInnerWrapper">
            <header
                style="background-image:url('<?= wp_get_attachment_image_src(get_post_thumbnail_id($post->iD), 'full')[0]; ?>');">
                <hgroup>
                    <h4><?= get_term($article_category)->name; ?></h4>
                    <h1><?php the_title(); ?></h1>
                    <div class="articleDatas">
                        <a hre="#"><h2><?php the_author(); ?></h2></a>
                        <h3>2017.szeptember 07</h3>
                    </div>
                </hgroup>
            </header>
            <section class="left">
                <div class="socialElements">
                </div>
                <div class="plc_story_content_settings">
                    <div class="plc_text_settings">
                        <span class="plc_text_contrast day"></span>
                        <span class="text_alignment left"></span>

                        <div class="fontstyle_selector">
                            <span class="fontstyle"></span>
                            <ul class="fontstyle_list">
                                <li class="select_titillium" style="font-family: 'Titillium', sans-serif;">Titillium
                                </li>
                                <li class="select_ptserif" style="font-family: 'PT Serif', serif;">PT Serif</li>
                                <li class="select_ubuntu" style="font-family: 'Ubuntu', sans-serif;">Ubuntu</li>
                            </ul>
                        </div>
                        <div class="fontsizeselector">
                            <span class="fontsize"></span>
                            <ul class="fontsize_list">
                                <li class="fontsizeDefault">Alapértelmezett</li>
                                <li class="fontsizeBig">AAA</li>
                                <li class="fontsizeMedium">AAA</li>
                                <li class="fontsizeSmall">AAA</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <article>
                    <?php the_excerpt();
                    the_content(); ?>
                </article>
                <div>
                    <?php new Polc_Social_Share_Module(); ?>
                </div>
            </section>
            <div class="right">
                <div class="currentChategory">
                    <?php
                    if ($article_category == Polc_Settings_Manager::layout()["news"]["term_id"]):
                        $current = $news;
                        $other = $recommend_articles;
                    else:
                        $current = $recommend_articles;
                        $other = $news;
                    endif;

                    foreach ($current as $value):
                        ?>
                        <a href="<?= get_permalink($value->ID); ?>">
                            <article
                                style="background-image:url('<?= wp_get_attachment_image_src(get_post_thumbnail_id($value->ID), 'medium')[0]; ?>');">
                                <h1><?= $value->post_title; ?></h1>
                            </article>
                        </a>
                        <?php
                    endforeach;
                    ?>
                    <div class="plcButtonWrapper">
                        <button>More articles</button>
                    </div>
                </div>
                <div class="otherChategory">
                    <?php foreach ($other as $value): ?>
                        <a href="<?= get_permalink($value->ID); ?>">
                            <article>
                                <div class="plcArticleImage"
                                     style="background-image:url('<?= wp_get_attachment_image_src(get_post_thumbnail_id($value->ID), 'medium')[0]; ?>');"></div>
                                <h1><?= $value->post_title; ?></h1>
                            </article>
                        </a>
                    <?php endforeach; ?>

                    <div class="plcButtonWrapper">
                        <button>More articles</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();

