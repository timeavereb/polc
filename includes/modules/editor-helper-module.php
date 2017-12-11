<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 08. 26.
 * Time: 13:29
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Editor_Helper_Module
 */
class Polc_Editor_Helper_Module
{
    /**
     * Displays the content blurb section
     * @param string $value
     */
    public static function content_blurb($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <textarea name="blurb" placeholder="<?= __('Blurb', 'polc'); ?>*" id="blurb"><?= $value; ?></textarea>

            <p class="form_info"><?= __('The bulrb character limit is 1200 characters.', 'polc'); ?></p>
        </div>
        <?php
    }

    /**
     * Displays the content warning section.
     * @param array $params
     */
    public static function content_warnings($params = [])
    {
        $default = ["obscene" => false, "violent" => false, "erotic" => false];
        $warnings = wp_parse_args($params, $default);
        ?>

        <div class="newStoryData_row contentWarning">
            <h4><?= __('Warnings', 'polc'); ?></h4>

            <div class="warning obscenecontent">
                <div class="plcCheckbox">
                    <input type="checkbox" id="obscene-content"
                           name="obscene-content" <?= (!$warnings["obscene"] ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Obscene content', 'polc'); ?></p>
            </div>
            <div class="warning violentcontent">
                <div class="plcCheckbox">
                    <input type="checkbox" id="violent-content"
                           name="violent-content" <?= (!$warnings["violent"] ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Violent content', 'polc'); ?></p>
            </div>
            <div class="warning eroticcontent">
                <div class="plcCheckbox">
                    <input type="checkbox" id="erotic-content"
                           name="erotic-content" <?= (!$warnings["erotic"] ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Erotic content', 'polc'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Displays the content age limit section.
     * @param int $selected
     */
    public static function content_age_limit($selected = 0)
    {
        ?>

        <div class="newStoryData_row agelimit">
            <h4><?= __('Age limit', 'polc'); ?></h4>

            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="18"
                           class="agelimit18" <?= ($selected === 18 ? "checked" : ""); ?>>
                    <span>18</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="16"
                           class="agelimit16" <?= ($selected === 16 ? "checked" : ""); ?>>
                    <span>16</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="14"
                           class="agelimit14" <?= ($selected === 14 ? "checked" : ""); ?>>
                    <span>14</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="12"
                           class="agelimit12" <?= ($selected === 12 ? "checked" : ""); ?>>
                    <span>12</span>
                </label>
            </div>
            <div class="plcRadiobutton">
                <label>
                    <input type="radio" name="agelimit" value="0"
                           class="noagelimit" <?= ($selected === 0 ? "checked" : ""); ?>>
                    <span><?= __('No age limit', 'polc'); ?></span>
                </label>
            </div>
        </div>
        <?php
    }

    /**
     * @param bool|false $checked
     */
    public static function content_restriction($checked = false)
    {
        ?>

        <div class="newStoryData_row regWarning">
            <h4><?= __('Restrictions', 'polc'); ?></h4>

            <div class="warning registeredOnly">
                <div class="plcCheckbox">
                    <input type="checkbox" id="only-registered"
                           name="only-registered" <?= (!$checked ? "" : "checked"); ?>>
                    <label></label>
                </div>
                <p><?= __('Content only for registered users', 'polc'); ?></p>
            </div>
            <div class="newStoryData_row">
                <p class="form_info"><?= __('Warning! Content under 18+ available only for registered users!', 'polc'); ?></p>
            </div>
        </div>

        <?php
    }

    /**
     * Displays the author comment.
     * @param string $value
     */
    public static function content_author_comment($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" name="author-comment" placeholder="<?= __('Author\'s comment', 'polc'); ?>"
                   value="<?= $value; ?>">
        </div>
        <?php
    }

    /**
     * Displays the content editor.
     * @param string $value
     */
    public static function content_editor($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <?php
            wp_editor($value, 'story_content', ["media_buttons" => false, "teeny" => true, "quicktags" => false]);
            ?>
            <label id="story_content-error" for="story_content"
                   style="display: none;"><?= __('Content is empty!', 'polc'); ?></label>

            <p class="form_info"><?= __('The content maximum character limit is 28 000 characters.', 'polc'); ?></p>
        </div>
        <?php
    }

    /**
     * Displays the content tags.
     * @param array $tags
     */
    public static function content_tags($tags = [])
    {
        ?>
        <div class="newStoryData_row tag">
            <label for="polc_tag_handler"><?= __('Add tags', 'polc'); ?></label>
            <div class="plc_tag_handler_wrapper">
                <input type="text" id="polc_tag_handler">
                <span style="display: none;" id="plc_tag_error"><?= __('You must provide 1 tag at least!', 'polc'); ?></span>
            </div>
            <button id="addTag"><?= __('Add tag', 'polc'); ?></button>

            <div class="plcTagContainer">
                <?php
                foreach ($tags as $tag):
                    ?>
                    <div class="plcTagElement">
                        <span class="plcTagELementText"><?= $tag; ?></span>
                        <input type="hidden" name="post_tag[]" value="<?= $tag; ?>">
                        <span class="plcTagElementDelete" onclick="polc_editor.remove_tag_element(this);"></span>
                    </div>
                    <?php
                endforeach;
                ?>
            </div>
            <div class="plcTagWarningWrapper">
                <p class="form_info"><?= __('The maximum number of tags is 8.', 'polc'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Displays the chapter title.
     * @param string $value
     */
    public static function content_chapter_title($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" id="chapter_title" name="chapter_title"
                   placeholder="<?= __('Chapter\'s title', 'polc'); ?>*" value="<?= $value; ?>">
        </div>
        <?php
    }

    /**
     * Displays the volume title.
     * @param string $value
     */
    public static function content_volume_title($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" name="volume_title" placeholder="<?= __('Volume title', 'polc'); ?>*"
                   value="<?= $value; ?>">
        </div>
        <?php
    }

    /**
     * Displays the volume sub title.
     * @param $value
     */
    public static function content_volume_sub_title($value = "")
    {
        ?>
        <div class="newStoryData_row">
            <input type="text" name="volume-sub-title" placeholder="<?= __('Volume sub-title', 'polc'); ?>"
                   value="<?= $value; ?>">
        </div>
        <?php
    }
}