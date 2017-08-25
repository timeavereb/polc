<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 25.
 * Time: 20:17
 */

/**
 * Class Polc_Toplists_Layout_Handler
 */
class Polc_Content_Editor_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-content-editor";
    CONST POLC_LAYOUT_NAME = "Tartalom szerkesztő";

    private $id;
    private $mode;
    private $user;
    private $update;
    private $post;
    private $post_meta = array();
    private $child_contents;
    private $tags;
    private $has_child;
    private $warnings;
    private $pages;
    private $content_editor;

    public function render()
    {
        if (!$this->validate()) {
            return false;
        }

        //If the save button has been triggered.
        if (isset($_REQUEST["update"])) {
            if (!$this->update()) {
                echo __("Error at updating story!", "polc");
                return false;
            }

            $this->update = true;
        }

        wp_enqueue_script("polc-story-handler", PLC_THEME_PATH . "/js/content-upload.js");
        ?>
        <script>
            var polc_editor;
            jQuery(document).ready(function () {
                polc_editor = new polc_content_editor_handler();
            });
        </script>
        <?php

        //If the content was updated we have to get it again.
        if ($this->update) {
            $this->post = get_post($this->id);
        }

        $keys = array("author-comment");

        //If it's a volume, we extend the meta key collection.
        if ($this->mode == "volume") {
            $volume_metas = array("volume-sub-title", "obscene-content", "violent-content", "erotic-content", "agelimit", "only-registered");
            $keys = array_merge($keys, $volume_metas);
        }

        $this->child_contents = get_children(array("post_parent" => $this->post->ID, "post_type" => "story"));
        $this->has_child = count($this->child_contents) > 0 ? true : false;
        $this->tags = wp_get_post_tags($this->post->ID, array("fields" => "names"));

        if ($this->has_child) {
            $this->content_editor = get_permalink(Polc_Settings_Manager::pages()["content-editor-page"]);
        }

        foreach ($keys as $meta) {
            $this->post_meta[$meta] = get_post_meta($this->post->ID, $meta, true);
        }

        if ($this->mode == "volume") {

            $this->warnings = array(
                "obscene" => $this->post_meta["obscene-content"],
                "erotic" => $this->post_meta["erotic-content"],
                "violent" => $this->post_meta["violent-content"]
            );

            $this->volume_editor();
            $this->delete_confirm();
        } else {
            $this->chapter_editor();
        }
    }

    /**
     * Draws the volume editor section.
     */
    private function volume_editor()
    {
        ?>
        <div class="plcContentEditorWrapper">
            <div class="editorContainer">
                <form method="POST" id="polc-story-form">
                    <div class="editorHead">
                        <?php
                        Polc_Helper_Module::content_volume_title($this->post->post_title);
                        Polc_Helper_Module::content_volume_sub_title($this->post_meta["volume-sub-title"]);
                        ?>
                    </div>
                    <?= wp_nonce_field("polc-editor-validate", "polc-edit"); ?>
                    <input type="hidden" name="volume-id" value="<?= $_REQUEST["volume-id"]; ?>">
                    <input type="hidden" id="mode" name="mode" value="new-volume">
                    <?php
                    Polc_Helper_Module::content_blurb((string)$this->post->post_excerpt);
                    Polc_Helper_Module::content_warnings((array)$this->warnings);
                    Polc_Helper_Module::content_age_limit((int)$this->post_meta["agelimit"]);
                    Polc_Helper_Module::content_restriction((bool)$this->post_meta["only-registered"]);

                    if (!$this->has_child) {
                        Polc_Helper_Module::content_author_comment((string)$this->post_meta["author-comment"]);
                        Polc_Helper_Module::content_editor($this->post->post_content);
                    }

                    Polc_Helper_Module::content_tags($this->tags);
                    ?>
                    <div class="editorContainerRow bttons">
                        <input type="submit" id="submit" name="update" value="<?= __("Save", "polc"); ?>">
                    </div>
                </form>
            </div>
            <?php
            if ($this->has_child):
                ?>
                <div class="contentList">
                    <?php
                    foreach ($this->child_contents as $story):
                        ?>
                        <div class="listElement">
                            <h2><?= $story->post_title; ?></h2>

                            <form action="<?= $this->content_editor; ?>" method="POST">
                                <input type="hidden" name="chapter-id" value="<?= $story->ID; ?>">
                                <input type="submit" value="<?= __("Edit chapter", "polc"); ?>">
                            </form>
                            <span class="delete-chapter" data-id="<?= $story->ID; ?>"
                                  data-name="<?= $story->post_title; ?>"
                                  title="<?= __("Delete chapter", "polc"); ?>"></span>
                        </div>
                        <?php
                    endforeach;
                    ?>
                </div>
                <?php
            endif; ?>
        </div>
        <?php
    }

    /**
     * Draws the chapter editor section.
     */
    private function chapter_editor()
    {
        ?>
        <div class="plcContentEditorWrapper">
            <div class="editorContainer">
                <form method="POST" id="polc-story-form">
                    <?= wp_nonce_field("polc-editor-validate", "polc-edit"); ?>
                    <input type="hidden" name="chapter-id" value="<?= $this->id; ?>">
                    <input type="hidden" id="mode" name="mode" value="new-chapter">
                    <?php
                    Polc_Helper_Module::content_chapter_title($this->post->post_title);
                    Polc_Helper_Module::content_author_comment($this->post_meta["author-comment"]);
                    Polc_Helper_Module::content_editor($this->post->post_content);
                    Polc_Helper_Module::content_tags($this->tags);
                    ?>
                    <input type="submit" id="submit" name="update" value="<?= __("Save", "polc"); ?>">
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Chapter delete confirmation.
     */
    private function delete_confirm()
    {
        ?>
        <script>
            var delete_confirm;
            jQuery(document).ready(function () {
                delete_confirm = new polc_chapter_delete_handler();
            });
        </script>

        <div id="polc-chapter-delete" style="display: none;">
            <h1><?= __("Confirmation", "polc"); ?></h1>

            <div id="plcChapterDeleteInner">
                <input type="hidden" id="chapter-id">
                <span class="plcConfirmMsg"><?= __("Are you sure you want to delete this chapter?", "polc"); ?></span>
                <span id="plcChapterName"></span>

                <div class="plcPopupButtonBar">
                    <button id="chapterDeleteCancel"><?= __("Cancel", "polc"); ?></button>
                    <button id="chapterDeleteSubmit"><?= __("Delete", "polc"); ?></button>
                </div>
            </div>
        </div>
        <?php
    }

    private function validate()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        if ((!isset($_REQUEST["volume-id"]) || !is_numeric($_REQUEST["volume-id"])) &&
            (!isset($_REQUEST["chapter-id"]) || !is_numeric($_REQUEST["chapter-id"]))
        ) {
            echo __("Invalid content identifier!", "polc");
            return false;
        }

        $this->id = isset($_REQUEST["volume-id"]) ? $_REQUEST["volume-id"] : $_REQUEST["chapter-id"];
        $this->mode = isset($_REQUEST["volume-id"]) ? "volume" : "chapter";

        $this->user = Polc_Header::current_user();
        $this->post = get_post($this->id);

        //If someone would try to send a volume id with a chapter id paramter we throw error.
        if ($this->post->post_parent == 0 && $this->mode == "chapter") {
            $this->auth_error();
            return false;
        }

        //check, that the content belongs to the current user
        if ($this->post->post_author != $this->user->ID) {
            $this->auth_error();
            return false;
        }

        //validate the content
        if (!$this->post || $this->post->post_type != "story") {
            echo __("Invalid content identifier!", "polc");
            return false;
        }

        return true;
    }

    private function auth_error()
    {
        echo __("You're not authorized to access this page!", "polc");
    }

    /**
     * Updates the story.
     */
    private function update()
    {
        if (!wp_verify_nonce($_REQUEST["polc-edit"], "polc-editor-validate")) {
            return false;
        }

        $meta_keys = array("author-comment");

        if ($_REQUEST["mode"] == "new-volume") {
            $id = $_REQUEST["volume-id"];
            $args["post_excerpt"] = $_REQUEST["blurb"];
            $args["post_title"] = $_REQUEST["volume_title"];
            $volume_meta = array("volume-sub-title", "obscene-content", "violent-content", "erotic-content", "agelimit", "only-registered", "");
            $meta_keys = array_merge($meta_keys, $volume_meta);
        } else {
            $id = $_REQUEST["chapter-id"];
            $args["post_title"] = $_REQUEST["chapter_title"];
        }

        $args["ID"] = $id;

        if (isset($_REQUEST["story_content"])) {
            $args["post_content"] = $_REQUEST["story_content"];
        }

        wp_update_post($args);

        foreach ($meta_keys as $key) {
            $value = isset($_REQUEST[$key]) ? $_REQUEST[$key] : "";
            update_post_meta($id, $key, $value);
        }

        $this->update_tags($id);

        return true;
    }

    private function update_tags($id)
    {
        if (isset($_REQUEST["post_tag"]) && count($_REQUEST["post_tag"]) > 0) {
            wp_set_post_tags($id, $_REQUEST["post_tag"]);
        }
    }
}