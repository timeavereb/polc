<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 10.
 * Time: 10:50
 */

get_header();

/**
 * Class Polc_Author
 */
class Polc_Author
{
    private $author;
    private $user_stories;
    private $can_edit = false;
    private $avatar;
    private $user_favorite_cnt;
    private $favorited_by;
    private $user_subscribe_cnt;

    /**
     * Polc Author constructor
     */
    public function __construct()
    {
        $this->author = get_queried_object();
        if (is_user_logged_in()):
            $this->can_edit = Polc_Header::current_user()->ID == $this->author->ID ? true : false;
        endif;

        $avatar = get_user_meta($this->author->ID, "polc_current_avatar");
        $this->favorited_by = $this->favorited_by = (array)Polc_Favorite_Helper_Module::get_favorite_users($this->author->ID, "users");
        $this->user_favorite_cnt = count($this->favorited_by);

        $this->avatar = !empty($avatar) ? $avatar[0]["src"] : "";
        wp_enqueue_script("author-handler", PLC_THEME_PATH . '/js/author-handler.js');
        wp_enqueue_script('jquery-ui-datepicker');
        ?>

        <script type="text/javascript">
            var polc_author_handler;
            jQuery(document).ready(function () {
                polc_author_handler = new polc_author_handler();
            });
        </script>
        <?php

        if ($this->can_edit):
            ?>
            <div id="plc_image_selection_wrapper" style="display:none;">
                <form id="uploadimage" action="" method="post" enctype="multipart/form-data">
                    <input type="file" name="file" id="file" multiple="false">
                </form>
            </div>
            <?php
            $this->story_selection_popup();
        endif;

        $args = [
            "post_type" => "story",
            "author" => $this->author->ID,
            "posts_per_page" => -1,
            "post_parent" => 0,
            "post_status" => "publish",
            "order_by" => "modified",
            "order" => "desc",
        ];

        $this->user_stories = get_posts($args);
        ?>
        <div class="plc_profile_wrapper">
            <?php

            if ($this->can_edit):
                $this->profile_admin_bar();
            endif;

            $this->profile_left_stories();
            $this->profile_left_favorited_by();

            if ($this->can_edit):
                $this->profile_left_favorite_authors();
                $this->profile_left_favorite_contents();
                $this->profile_left_data_change();
            endif;

            $this->profile_right();
            ?>
        </div>
        <?php
    }

    /**
     * Admin tab menu bar
     */
    private function profile_admin_bar()
    {
        ?>
        <div class="plcAdminBar">
            <?php if ($this->can_edit): ?>

                <div class="plcProfileTabMenu">
                    <?php if (count($this->user_stories) > 0): ?>
                        <button id="section_1_btn" class="section_btn active my_content_btn"
                                onclick="polc_header_handler.change_section(1);"><?= __('My contents', 'polc'); ?></button>
                    <?php endif; ?>
                    <?php if ($this->user_favorite_cnt > 0): ?>
                        <button id="section_2_btn" class="section_btn favorited_by_btn"
                                onclick="polc_header_handler.change_section(2);"><?= __('Favorited by', 'polc'); ?></button>
                    <?php endif; ?>

                    <button id="section_3_btn" class="section_btn favorite_authors_btn"
                            onclick="polc_header_handler.change_section(3);"><?= __('Favorited Authors', 'polc'); ?></button>

                    <button id="section_4_btn" class="section_btn favorite_stories_btn"
                            onclick="polc_header_handler.change_section(4);"><?= __('Favorited Contents', 'polc'); ?></button>

                    <button id="section_5_btn" class="section_btn datachange_btn"
                            onclick="polc_header_handler.change_section(5);"><?= __('Data change', 'polc'); ?></button>
                    <?php if (count($this->user_stories) == 0): ?>
                        <button id="section_1_btn" class="section_btn active my_content_btn"
                                onclick="polc_header_handler.change_section(1);"><?= __('My contents', 'polc'); ?></button>
                    <?php endif; ?>
                </div>

                <div class="addStoryWrapper">
                    <span></span>

                    <p class="addStory"><?= __('Add new volume', 'polc'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Data change section
     */
    private function profile_left_data_change()
    {
        ?>
        <div class="plcProfileLeft section" id="section_5" style="display: none;">
            <div id="plcDataChangeWrapper">
                <form id="plcDataChangeForm">
                    <p class="plcDataChangeElement">
                        <label for="plc_user_email"><?= __("Email (can't be modified)", "polc"); ?></label>
                        <input type="text" id="plc_user_email" value="<?= Polc_Header::$curr_user->data->user_email; ?>"
                               disabled="disabled">
                    </p>

                    <p class="plcDataChangeElement">
                        <label for="plc_user_login"><?= __("Username (can't be modified)", "polc"); ?></label>
                        <input type="text" id="plc_user_login" value="<?= Polc_Header::$curr_user->data->user_login; ?>"
                               disabled="disabled">
                    </p>

                    <p class="plcDataChangeElement">
                        <label for="plc_user_display_name"><?= __("Name", "polc"); ?>*</label>
                        <input type="text" id="plc_user_display_name" name="plc_user_display_name"
                               value="<?= Polc_Header::$curr_user->data->display_name; ?>">
                    </p>

                    <p class="plcDataChangeElement">
                        <label for="plc_user_url"><?= __("Webpage", "polc"); ?></label>
                        <input type="text" id="plc_user_url" name="plc_user_url"
                               value="<?= Polc_Header::$curr_user->data->user_url; ?>">
                    </p>

                    <p class="plcDataChangeElement">
                        <label for="plc_user_birth_date"><?= __("Date of birth", "polc"); ?></label>
                        <input type="text" id="plc_user_birth_date" name="plc_user_birth_date"
                               value="<?= Polc_Header::$curr_user->data->user_birth_date; ?>">
                    </p>

                    <?php wp_nonce_field('plc_user_data_change', 'plc_user_data_change_nonce'); ?>
                </form>

                <form id="plcPasswordChangeForm">
                    <p class="plcDataChangeElement">
                        <label for="plc_old_password"><?= __("Old password", "polc"); ?></label>
                        <input type="password" id="plc_old_password" name="plc_old_password">
                    </p>

                    <p class="plcDataChangeElement">
                        <label for="plc_new_password"><?= __("New password", "polc"); ?></label>
                        <input type="password" id="plc_new_password" name="plc_new_password">
                    </p>

                    <p class="plcDataChangeElement">
                        <label for="plc_new_password_conf"><?= __("New password confirmation", "polc"); ?></label>
                        <input type="password" id="plc_new_password_conf" name="plc_new_password_conf">
                    </p>
                </form>

                <button id="plcUserDataSaveBtn"><?= __('Save', 'polc'); ?></button>
            </div>
        </div>
        <?php
    }

    /**
     * Stories section.
     */
    private function profile_left_stories()
    {
        ?>
        <div class="plcProfileLeft section" id="section_1">
            <div class="plcUserStories">
                <?php
                if (count($this->user_stories) > 0):
                    Polc_Helper_Module::simple_list($this->user_stories, true);
                else:
                    ?>
                    <span class="plcEmptyContent">
                        <?= __("You didn't upload any content yet!", "polc"); ?>
                    </span>
                    <?php
                endif;
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Users list who favorited the author section.
     */
    private function profile_left_favorited_by()
    {
        ?>
        <div class="plcProfileLeft section" id="section_2" style="display: none;">
            <?php
            if ($this->user_favorite_cnt > 0):
                foreach ($this->favorited_by as $user):
                    ?>
                    <div class="plcUserFavoriteElement">
                        <a href="<?= get_author_posts_url($user); ?>"><?= get_user_by('id', $user)->display_name; ?></a>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
        <?php
    }

    private function profile_left_favorite_authors()
    {
        $favorite_authors = (array)Polc_Favorite_Helper_Module::get_favorite_users(Polc_Header::current_user()->ID);
        ?>
        <div class="plcProfileLeft section" id="section_3" style="display: none;">
            <?php
            if (count($favorite_authors) > 0):
                foreach ($favorite_authors as $user): ?>
                    <div class="plcUserFavoriteElement">
                        <a href="<?= get_author_posts_url($user); ?>"><?= get_user_by('id', $user)->display_name ?></a>
                    </div>
                    <?php
                endforeach;
            else:
                ?>
                <span class="plcEmptyContent"><?= __('You didn\'t add any author as favorite.', 'polc'); ?></span>
                <?php
            endif;
            ?>
        </div>
        <?php
    }

    private function profile_left_favorite_contents()
    {
        $favorite_contents = (array)Polc_Favorite_Helper_Module::get_favorite_stories(Polc_Header::current_user()->ID);
        $post_ids = array_keys($favorite_contents);
        ?>
        <div class="plcProfileLeft section" id="section_4" style="display: none;">
            <?php
            if (count($favorite_contents) > 0):
                $posts = get_posts(["post__in" => $post_ids, "post_type" => "story", "posts_per_page" => -1]);
                Polc_Helper_Module::simple_list($posts, true);
            else:
                ?>
                <span class="plcEmptyContent"><?= __('You didn\'t add any content as favorite.', 'polc'); ?></span>
                <?php
            endif;
            ?>
        </div>
        <?php
    }

    /**
     * Profile right.
     */
    public function profile_right()
    {
        ?>
        <div class="plcProfileRight">
            <div class="plcUserInformation admin">
                <h1><?= $this->author->user_nicename; ?></h1>

                <div class="plcUserImage"
                     style="background-image:url('<?= $this->avatar; ?>');">
                    <?php if ($this->can_edit): ?>
                        <div class="changeImage"><p><?= __('Change profile picture', 'polc') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="plcUserDefault">
                    <p><?= $this->author->user_email; ?></p>
                    <a href="<?= $this->author->user_url; ?>"><?= $this->author->user_url; ?></a>
                </div>
                <div class="plcUserStat">
                    <div class="plcUserStatElement">
                        <?php if (is_numeric($this->user_favorite_cnt) && $this->user_favorite_cnt !== 0): ?>
                            <div><span
                                    onclick="polc_header_handler.change_section(2);"><?= $this->user_favorite_cnt; ?></span>

                                <p><?= __('people\'s  favourite author', 'polc'); ?></p></div>
                        <?php endif; ?>
                        <div><span
                                onclick="polc_header_handler.change_section(1);"><?= count($this->user_stories); ?></span>

                            <p> <?= __('stories', 'polc'); ?></p></div>
                        <?php if (is_numeric($this->user_subscribe_cnt) && $this->user_subscribe_cnt != 0): ?>
                            <div><span><?= $this->user_subscribe_cnt; ?></span>

                                <p><?= __('followers', 'polc'); ?></p></div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$this->can_edit && is_user_logged_in()): ?>
                        <div class="addFavouriteUser" data-author-id="<?= $this->author->ID; ?>">
                            <span></span>

                            <p id="addFavouriteText">
                                <?php
                                $author_list = Polc_Header::$curr_user->data->favorite_author_list;
                                if (is_array($author_list) && array_key_exists($this->author->ID, $author_list)):
                                    echo __('Remove author from favorites', 'polc');
                                else:
                                    echo __('Add author to favorites', 'polc');
                                endif;
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!--<div class="plcWriterLatestActivities">
                <div class="activitiesItem underControl">
                    <div class="activitiesDate">
                        <p>Május</p>
                        <span>17</span>
                    </div>
                    <div class="activitiesTitle">
                        <h2>Nullam imperdiet facilisis gravida - Nullam imperdiet facilisis gravida</h2>
                    </div>
                </div>
                <div class="activitiesItem">
                    <div class="activitiesDate">
                        <p>Május</p>
                        <span>16</span>
                    </div>
                    <div class="activitiesTitle">
                        <h2>Nullam imperdiet facilisis gravida</h2>
                    </div>
                </div>
                <div class="activitiesItem">
                    <div class="activitiesDate">
                        <p>Május</p>
                        <span>10</span>
                    </div>
                    <div class="activitiesTitle">
                        <h2>Nullam imperdiet facilisis gravida</h2>
                    </div>
                </div>
                <div class="activitiesItem">
                    <div class="activitiesDate">
                        <p>Május</p>
                        <span>5</span>
                    </div>
                    <div class="activitiesTitle">
                        <h2>Nullam gravida imperdiet facilisis gravida</h2>
                    </div>
                </div>
            </div>-->
            <div class="plcUserCommentary">
            </div>
        </div>
        <?php
    }

    /**
     * Story selection popup.
     */
    private function story_selection_popup()
    {
        $new_story_page = get_permalink(Polc_Settings_Manager::new_story_page());

        ?>
        <div id="addStoryPopup">
            <span class="quit"></span>

            <form id="addStoryForm" method="POST" action="<?= $new_story_page; ?>">
                <h1><?= __('Genre and category selections', 'polc'); ?></h1>

                <div class="addStoryPopup_wrapper">

                    <div class="newStoryData_row storyType">
                        <h4><?= __('Type', 'polc'); ?></h4>
                        <select name="content-type">
                            <option value="single"><?= __('Single', 'polc'); ?></option>
                            <option value="sequel"><?= __('Sequels', 'polc'); ?></option>
                        </select>
                    </div>

                    <div class="newStoryData_row storyCategory">
                        <h4><?= __('Category', 'polc'); ?></h4>
                        <?php

                        wp_dropdown_categories([
                            "id" => "content-category",
                            "name" => "content-category",
                            "taxonomy" => "category",
                            "show_option_none" => __('Choose category', 'polc'),
                            "child_of" => Polc_Settings_Manager::categories()["story-main"],
                            'orderby' => 'name',
                            'hide_empty' => 0,
                            'order' => 'ASC',
                            'option_none_value' => 0
                        ]);
                        ?>
                    </div>
                    <div class="newStoryData_row storySubCategory" style="display:none;">
                        <h4><?= __('Sub-category', 'polc'); ?></h4>
                        <?php

                        wp_dropdown_categories([
                            "id" => "content-subcategory",
                            "name" => "content-subcategory",
                            "taxonomy" => "category",
                            "show_option_none" => __('Choose subcategory', 'polc'),
                            "child_of" => Polc_Settings_Manager::categories()["story-sub"],
                            'orderby' => 'name',
                            'hide_empty' => 0,
                            'order' => 'ASC',
                            'option_none_value' => 0
                        ]); ?>
                    </div>
                    <div class="newStoryData_row storyGenre" style="display:none;">
                        <h4><?= __('Genre', 'polc'); ?></h4>
                        <?php
                        wp_dropdown_categories([
                            "id" => "content-genre",
                            "name" => "content-genre",
                            "taxonomy" => "genre",
                            "show_option_none" => __('Choose genre', 'polc'),
                            'orderby' => 'name',
                            'hide_empty' => 0,
                            'order' => 'ASC',
                            'option_none_value' => 0
                        ]); ?>
                    </div>
                    <div class="newStoryData_row submit" style="display:none;">
                        <?= wp_nonce_field('volume-box', 'new-volume-nonce'); ?>
                        <input type="submit" value="Tovább">
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

new Polc_Author();

get_footer();