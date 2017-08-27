<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 8:51
 */

/**
 * Class Polc_New_Story_Layout_Handler
 */
class Polc_New_Story_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-new-story";
    CONST POLC_LAYOUT_NAME = "Történet feltöltés";

    private $content_category;
    private $content_subcategory;
    private $content_genre;
    private $user;
    private $insert_id;
    private $mode;

    /**
     * @return bool
     */
    public function render()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $this->content_category = isset($_REQUEST["content-category"]) ? $_REQUEST["content-category"] : array();
        $this->content_subcategory = isset($_REQUEST["content-subcategory"]) ? $_REQUEST["content-subcategory"] : array();
        $this->content_genre = isset($_REQUEST["content-genre"]) ? $_REQUEST["content-genre"] : array();

        $this->user = Polc_Header::current_user();

        wp_enqueue_script("polc-story-handler", PLC_THEME_PATH . "/js/content-upload.js");
        ?>
        <script>
            var polc_editor;
            jQuery(document).ready(function () {
                polc_editor = new polc_content_editor_handler();
            });
        </script>
        <?php

        //Validate referer.
        if (isset($_REQUEST["volume-id-nonce"]) && wp_verify_nonce($_REQUEST["volume-id-nonce"], 'add-chapter') && is_numeric($_REQUEST["volume-id"])) {

            $volume = get_post($_REQUEST["volume-id"]);
            //Validate that the current user is the author of the volume and the volume is not a child post.
            if ($this->user->ID != $volume->post_author || $volume->post_parent != 0) {
                return false;
            }

            //Get the volume categories and genre
            foreach (wp_get_post_terms($_REQUEST["volume-id"], 'category') as $term) {
                $this->content_category[] = $term->term_id;
            }

            foreach (wp_get_post_terms($_REQUEST["volume-id"], 'genre') as $term) {
                $this->content_genre[] = $term->term_id;
            }

            $this->content_category = implode(",", $this->content_category);
            $this->content_genre = implode(",", $this->content_genre);

            $this->mode = "new-chapter";

        } else if (isset($_REQUEST["content-category"]) && wp_verify_nonce($_REQUEST["new-volume-nonce"], "volume-box")) {

            //Prevent the user from creating new genres or categories
            if (!term_exists((int)$this->content_genre, "genre") || !term_exists((int)$this->content_category, 'category')) {
                return false;
            }

            //Prevent the user from creating a new sub-category
            if ((int)$this->content_subcategory != 0 && !term_exists((int)$this->content_subcategory, 'category')) {
                return false;
            }

            //If we validated everything we set the mode to new volume.
            $this->mode = "new-volume";
        } else {
            $this->mode = "";
        }

        $_REQUEST["content-type"] = isset($_REQUEST["content-type"]) ? $_REQUEST["content-type"] : "";

        if (isset($_POST["submit"]) && wp_verify_nonce($_REQUEST["submit-id"], 'submit-story')) {
            $this->init_upload();
        }

        //If mode is empty the user is trying to get to this page with some trick, so he won't see anything displayed here.
        if ($this->mode != "") {

            ?>
            <form method="POST" id="polc-story-form" onkeypress="return event.keyCode != 13;">

                <div class="newStoryWrapper">
                    <div class="newStoryWrapperRight">
                        <?php
                        if ($this->mode == "new-volume") {
                            $this->volume();
                        }
                        $this->chapter();
                        Polc_Helper_Module::content_tags();
                        ?>
                        <div class="newStoryData_row">
                            <p class="form_info"><?= __('Fields marked with * are required.', 'polc'); ?></p>
                        </div>
                        <div class="newStoryData_row addStorySubmit">
                            <?php echo wp_nonce_field('submit-story', 'submit-id'); ?>
                            <input type="submit" id="submit" name="submit" value="<?= __('Upload', 'polc'); ?>">
                        </div>
                    </div>
                </div>
            </form>
            <?php
        }
    }

    /**
     * Draw volume inputs
     */
    public function volume()
    {
        ?>
        <input type="hidden" id="mode" name="mode" value="new-volume">
        <input type="hidden" name="category" value="<?= $this->content_category; ?>">
        <input type="hidden" name="subcategory" value="<?= $this->content_subcategory; ?>">
        <input type="hidden" name="genre" value="<?= $this->content_genre; ?>">
        <input type="hidden" id="content_type" name="content_type" value="<?= $_REQUEST["content-type"]; ?>">

        <h1><?= __('Story details', 'polc'); ?></h1>
        <?php
        Polc_Helper_Module::content_volume_title();
        Polc_Helper_Module::content_volume_sub_title();
        Polc_Helper_Module::content_blurb();
        Polc_Helper_Module::content_warnings();
        Polc_Helper_Module::content_age_limit();
        Polc_Helper_Module::content_restriction();
    }

    /**
     * Draw chapter inputs
     */
    public function chapter()
    {
        if ($this->mode == "new-chapter"):
            ?>
            <input type="hidden" name="volume-id" value="<?= $_REQUEST["volume-id"]; ?>">
            <input type="hidden" id="mode" name="mode" value="new-chapter">
            <input type="hidden" name="category" value="<?= $this->content_category; ?>">
            <input type="hidden" name="genre" value="<?= $this->content_genre; ?>">
        <?php endif; ?>
        <div class="plcStoryFirstChapter">
            <?php if ($_REQUEST["content-type"] == "sequel" || $this->mode === "new-chapter"): ?>
                <h1><?= __('Chapter details', 'polc'); ?></h1>
            <?php endif; ?>

            <?php if ($_REQUEST["content-type"] == "sequel" || $this->mode == "new-chapter"): ?>
                <?php
                Polc_Helper_Module::content_chapter_title();
            endif; ?>
            <?php
            Polc_Helper_Module::content_author_comment();
            Polc_Helper_Module::content_editor();
            ?>
        </div>
        <?php
    }

    /**
     * Init upload
     */
    public function init_upload()
    {
        if (count($_REQUEST["post_tag"]) > 8) {
            echo __('The maximum number of tags is 8.', 'polc');;
            return false;
        }

        //If it' a new story.
        if (isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "new-volume") {
            if (!$volume = $this->upload_volume()) {
                echo __('Error while uploading!', "polc");
                exit();
            }
        }

        $_REQUEST["content_type"] = isset($_REQUEST["content_type"]) ? $_REQUEST["content_type"] : "";

        //If it's a new chapter or a sequel story.
        if ($_REQUEST["content_type"] == "sequel" || $_REQUEST["mode"] == "new-chapter") {
            $this->upload_chapter();
        }

        ?>
        <script>
            location.href = "<?php echo get_author_posts_url($this->user->ID); ?>";
        </script>
        <?php
    }

    /**
     * Upload volume
     * @return bool
     */
    public function upload_volume()
    {
        $keys = array("blurb", "volume_title");

        foreach ($keys as $key) {
            if (!isset($_REQUEST[$key])) {
                return false;
            }
        }

        $args = array(
            'post_author' => $this->user->ID,
            'post_content' => "",
            'post_title' => $_REQUEST["volume_title"],
            'post_excerpt' => $_REQUEST["blurb"],
            'post_status' => 'pending',
            'post_type' => 'story',
            'post_parent' => 0,
            'post_category' => array($_REQUEST["category"], $_REQUEST["subcategory"])
        );

        //If it's a single story let's set the post content
        if ($_REQUEST["content_type"] == "single") {
            $args["post_content"] = $_REQUEST["story_content"];
        }

        $this->insert_id = wp_insert_post($args);

        wp_set_object_terms($this->insert_id, (int)$_REQUEST["genre"], 'genre');

        if (isset($_REQUEST["post_tag"]) && count($_REQUEST["post_tag"]) > 0) {
            wp_set_post_tags($this->insert_id, $_REQUEST["post_tag"]);
        }

        $keys = array("volume-sub-title", "obscene-content", "violent-content", "erotic-content", "agelimit", "only-registered", "author-comment");

        foreach ($keys as $key) {
            if (isset($_REQUEST[$key])) {
                update_post_meta($this->insert_id, $key, $_REQUEST[$key]);
            }
        }

        return true;
    }

    /**
     * Upload chapter
     */
    public function upload_chapter()
    {
        $new_chapter = false;
        if (isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "new-chapter") {
            $new_chapter = true;
            $parent_id = $_REQUEST["volume-id"];
        } else {
            $parent_id = $this->insert_id;
        }

        $args = array(
            'post_author' => $this->user->ID,
            'post_title' => $_REQUEST["chapter_title"],
            'post_content' => $_REQUEST["story_content"],
            'post_parent' => $parent_id,
            'post_type' => "story",
            'post_status' => "pending"
        );

        $insert_id = wp_insert_post($args);

        //Let's set the chapter categories and genre by the parent.
        wp_set_post_terms($insert_id, $_REQUEST["category"], "category");
        wp_set_post_terms($insert_id, $_REQUEST["genre"], "genre");
        if (isset($_REQUEST["post_tag"]) && count($_REQUEST["post_tag"]) > 0) {
            wp_set_post_tags($insert_id, $_REQUEST["post_tag"]);
        }

        //If it's a new chapter, we set the author-comment meta
        if ($new_chapter) {
            update_post_meta($insert_id, "author-comment", $_REQUEST["author-comment"]);
        }
    }
}