<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 25.
 * Time: 20:17
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Class Polc_Toplists_Layout_Handler
 */
class Polc_Toplists_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-toplists";
    CONST POLC_LAYOUT_NAME = "Sikerlisták";

    private $top_favorited_authors;
    private $top_commenters;

    public function render()
    {
        $this->top_favorited_authors = Polc_Favorite_Helper_Module::get_top_favorite_authors(10);
        $this->top_commenters = $this->top_comment_authors(10);
        ?>
        <div class="plcToplistsWrapper  ">
            <div class="toplistsItem favouriteWriters">
                <div class="innerItem">
                    <h1><?= __('Most favorited authors', 'polc'); ?></h1>

                    <div class="list">
                        <?php
                        $cnt = 1;
                        foreach ($this->top_favorited_authors as $author):
                            $user = get_user_by('ID', $author->AuthorId);
                            $author_url = get_author_posts_url($author->AuthorId); ?>
                            <div class="toplistListItem">
                                <a href="<?= $author_url; ?>"><span><?= $cnt; ?>.</span>

                                    <h2><?= $user->data->display_name; ?></h2>
                                </a>
                                <a href="#"><?= $author->FavoriteCnt . ' ' .
                                    ($author->FavoriteCnt == 1 ? __( 'person\'s favorite author', 'polc' ) : __( 'people\'s favorite author', 'polc' )); ?></a>
                            </div>
                            <?php
                            $cnt++;
                        endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="toplistsItem commentators">
                <div class="innerItem">
                    <h1><?= __('Most active comment writers', 'polc'); ?></h1>

                    <div class="list">
                        <?php
                        if (is_array($this->top_commenters)):
                            $cnt = 1;
                            foreach ($this->top_commenters as $value):
                                $user = get_user_by('ID', $value->UserId)
                                ?>
                                <a href="<?= get_author_posts_url($value->AuthorId); ?>">
                                    <span><?= $cnt; ?>.</span>
                                    <h2><?= $user->data->display_name; ?></h2>
                                </a>
                                <?php
                                $cnt++;
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <div class="toplistsItem  favouriteContent">
                <div class="innerItem">
                    <h1>Kedvenc tartalmak</h1>

                    <div class="list">
                        <div class="contentItem">
                            <a href=""><span>1.</span>

                                <h2>Aki kapja marja - Stephen King</h2></a>
                            <a href="">256 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>2.</span>

                                <h2>Valami, aminek nem egy soros a címe - Valaki, akinek hosszú a neve</h2></a>
                            <a href="">223 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>3.</span>

                                <h2>Vesztegzár - Joe Schreiber</h2></a>
                            <a href="">210 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>4.</span>

                                <h2>Naruto és az ezer arcú rókakölyök - Mosttaláltamki</h2></a>
                            <a href="">140 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>5.</span>

                                <h2>Harry Potter és a kitalált karakterek fanfictionja - Kitalált szerző</h2></a>
                            <a href="">129 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>6.</span>

                                <h2>Rám zuhant a háztető - Anonymus</h2></a>
                            <a href="">101 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>7.</span>

                                <h2>Darth Vader és a legjobb apukák csoportköre - Luke</h2></a>
                            <a href="">92 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>8.</span>

                                <h2>Nem hinném, hogy jó vagyok - Egy jó ember</h2></a>
                            <a href="">82 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>9.</span>

                                <h2>Altató és kloroform - Drogériás</h2></a>
                            <a href="">256 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>10.</span>

                                <h2>Hogy múlik az idő - Óra</h2></a>
                            <a href="">256 felhasználó kedvence</a>
                        </div>

                    </div>
                </div>
            </div>

            <div class="toplistsItem  view">
                <div class="innerItem">
                    <h1>Legolvasottabb tartalmak</h1>

                    <div class="list">
                        <div class="contentItem">
                            <a href=""><span>1.</span>

                                <h2>Aki kapja marja - Stephen King</h2></a>

                            <p>12 300 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>2.</span>

                                <h2>Valami, aminek nem egy soros a címe - Valaki, akinek hosszú a neve</h2></a>

                            <p>11 029 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>3.</span>

                                <h2>Vesztegzár - Joe Schreiber</h2></a>

                            <p>10 921 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>4.</span>

                                <h2>Naruto és az ezer arcú rókakölyök - Mosttaláltamki</h2></a>

                            <p>8320 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>5.</span>

                                <h2>Harry Potter és a kitalált karakterek fanfictionja - Kitalált szerző</h2></a>

                            <p>5123 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>6.</span>

                                <h2>Rám zuhant a háztető - Anonymus</h2></a>

                            <p>1231 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>7.</span>

                                <h2>Darth Vader és a legjobb apukák csoportköre - Luke</h2></a>

                            <p>902 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>8.</span>

                                <h2>Nem hinném, hogy jó vagyok - Egy jó ember</h2></a>

                            <p>123 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>9.</span>

                                <h2>Altató és kloroform - Drogériás</h2></a>

                            <p>122 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>10.</span>

                                <h2>Hogy múlik az idő - Óra</h2></a>

                            <p>99 megtekintés</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <?php
    }

    /**
     * @param int $limit
     * @return bool
     */
    private function top_comment_authors($limit = 10)
    {
        $list = array();

        if (!is_numeric($limit)) :
            return false;
        endif;

        global $wpdb;

        $results = $wpdb->get_results("
        SELECT COUNT(comment_ID) as CommentCnt, user_id as UserId
        FROM {$wpdb->comments}
        WHERE comment_approved = 1
        GROUP BY user_id
        ORDER BY CommentCnt DESC
        LIMIT {$limit}
        ");

        return $results;
    }
}