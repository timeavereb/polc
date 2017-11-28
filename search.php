<?php

get_header();

/**
 * Class Polc_Search
 */
class Polc_Search
{
    private $paged;
    private $phrase;
    private $ppp;
    private $category;
    private $subcategory;
    private $genre;
    private $total_items;
    private $total_pages;
    private $max;
    private $items;

    /**
     * Polc_Search constructor.
     */
    public function __construct()
    {
        $this->max = 999999999;
        $this->paged = (get_query_var('page')) ? get_query_var('page') : 1;
        $this->phrase = (get_query_var('s')) ? get_query_var('s') : "";
        $this->category = isset($_REQUEST["category"]) ? $_REQUEST["category"] : "";
        $this->subcategory = isset($_REQUEST["sub-category"]) ? $_REQUEST["sub-category"] : "";
        $this->genre = isset($_REQUEST["genre"]) ? $_REQUEST["genre"] : "";
        $this->ppp = 10;

        $this->search();

        $this->total_items = $this->items->found_posts;
        $this->total_pages = round($this->total_items / $this->ppp);
        ?>
        <div id="plcSearchMainWrapper">
            <?php
            $this->form();
            Polc_Helper_Module::pagination($this->total_pages, $this->paged);
            ?>
        </div>
        <?php
    }

    private function form()
    {
        ?>
        <div class="plcSearchWrapper">
            <form id="plcSearchForm" method="POST">

                <div class="plcSearchFormTop">
                    <div class="plcPhraseAndSubmitWrapper">
                        <input type="hidden" id="page" name="page" value="<?= $this->paged; ?>">

                        <div class="searchPhraseWrapper">
                            <input type="text" placeholder="<?= __('Search phrase', 'polc'); ?>" id="plcSearchPhrase"
                                   name="s"
                                   value="<?= $this->phrase; ?>">
                            <input type="submit" value="<?= __('Filter', 'polc'); ?>">
                        </div>
                    </div>
                    <div class="plcTotalHitsWrapper">
                        <p id="total_hits_text">
                            <?= __('Total hits found', 'polc'); ?>
                            <span><?= $this->total_items; ?></span>
                        </p>
                    </div>
                </div>

                <div class="plcSearchFormMiddle">
                    <div class="plcFilterWrapper">
                        <p class="plcSearchFilter">
                            <?php

                            $args = [
                                "taxonomy" => "category",
                                'orderby' => 'name',
                                'hide_empty' => 0,
                                'order' => 'ASC'
                            ];

                            $args["child_of"] = Polc_Settings_Manager::categories()["story-main"];
                            echo '<span>' . __('Choose category', 'polc') . '</span>';
                            $this->render_checkboxes(get_categories($args), "category");

                            ?>
                        </p>

                        <p class="plcSearchFilter">
                            <?php
                            $args["child_of"] = Polc_Settings_Manager::categories()["story-sub"];
                            echo '<span>' . __('Choose sub-category', 'polc') . '</span>';
                            $this->render_checkboxes(get_categories($args), "sub-category");
                            ?>
                        </p>

                        <p class="plcSearchFilter">
                            <?php
                            $args["taxonomy"] = "genre";
                            $args["child_of"] = false;
                            echo '<span>' . __('Choose genre', 'polc') . '</span>';
                            $this->render_checkboxes(get_categories($args), "genre");
                            ?>
                        </p>
                    </div>
            </form>
            <div class="plcHitsWrapper">
                <?php $this->render(); ?>
            </div>
        </div>
        <?php
    }

    private function search()
    {
        $args = [
            "post_type" => "story",
            "s" => $this->phrase,
            "posts_per_page" => $this->ppp,
            "paged" => $this->paged,
            "orderby" => "date",
            "order" => "desc"
        ];

        $tax_query = [];

        if (isset($_REQUEST["category"])):
            $cat = [
                "taxonomy" => "category",
                "field" => "term_id",
                "terms" => $_REQUEST["category"],
                "operator" => "IN"
            ];
            $tax_query[] = $cat;
        endif;

        if (isset($_REQUEST["sub-category"])):
            $sub_cat = [
                "taxonomy" => "category",
                "field" => "term_id",
                "terms" => $_REQUEST["sub-category"],
                "operator" => "IN"
            ];
            $tax_query[] = $sub_cat;
        endif;

        if (isset($_REQUEST["genre"])):
            $genre = [
                "taxonomy" => "genre",
                "field" => "term_id",
                "terms" => $_REQUEST["genre"],
                "operator" => "IN"
            ];
            $tax_query[] = $genre;
        endif;

        if (count($tax_query) > 0):
            $tax_query["relation"] = "AND";
            $args["tax_query"] = $tax_query;
        endif;

        $this->items = new WP_Query($args);
    }

    private function render_checkboxes($terms, $name)
    {
        foreach ($terms as $term):
            $selected = isset($_REQUEST[$name]) && is_array($_REQUEST[$name]) && in_array($term->term_id, $_REQUEST[$name]) ? "checked" : "";
            ?>
            <div class="plcCheckBoxWrapper">
                <div class="plcCheckbox">
                    <input type="checkbox" name="<?= $name; ?>[]" value="<?= $term->term_id; ?>" <?= $selected; ?>>
                    <label></label>
                </div>
                <p><?= $term->name ?></p>
            </div>
            <?php
        endforeach;
    }

    private function render()
    {
        Polc_Helper_Module::search_list($this->items->posts, true);
    }
}

new Polc_Search();

get_footer();