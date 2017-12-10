<?php

/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 12. 08.
 * Time: 21:25
 */
if (!defined("ABSPATH")) {
    exit();
}

class Polc_Authors_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-authors";
    CONST POLC_LAYOUT_NAME = "Felhasználó lista";

    private $authors;
    private $paged;
    private $max;
    private $ppp;

    public function render()
    {
        $this->authors;
        $this->max = 999999999;
        $this->paged = (get_query_var('page')) ? get_query_var('page') : 1;
        $this->ppp = 18;

        $args = [
            'number' => $this->ppp,
            'role' => 'polc_frontend_user',
            'paged' => $this->paged
        ];

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        $total_users = $user_query->get_total();
        $total_pages = round($total_users / $this->ppp);

        ?>
        <div class="membersContainer">
            <div class="innerWrapper">
                <h1>Felhasználók</h1>
                <?php
                foreach ($users as $user):

                    $avatar = Polc_Helper_Module::get_user_avatar($user->data->ID);
                    ?>
                    <a href="<?= get_author_posts_url($user->data->ID); ?>">
                        <div class="memberItem">
                            <div class="innerItem">
                                <div class="memberImage" style="background-image:url('<?= $avatar; ?>');"></div>
                                <h2><?= $user->data->user_login; ?></h2>
                                <?php if (count_user_posts($user->data->ID, 'story') > 0): ?>
                                    <span class="hasContent"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <?php
                endforeach;
                ?>
            </div>
            <div class="plcUserListPaginationWrapper">
                <?php
                if ($total_pages > 1):
                    Polc_Helper_Module::pagination($total_pages, $this->paged);
                endif;
                ?>
            </div>
        </div>
        <form id="plcSearchForm" method="POST">
            <input type="hidden" id="page" name="page" value="<?= $this->paged; ?>">
        </form>
        <?php
    }
}