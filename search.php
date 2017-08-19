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

        echo '<div id="plcSearchMainWrapper">';
        $this->form();
        $this->pagination();
        echo '</div>';
    }


    private function form()
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery(".plcPagerBtn").click(function () {
                    jQuery("#page").val(jQuery(this).attr("data-page"));
                    jQuery("#plcSearchForm").submit();
                });
            });
        </script>

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

                            $args = array(
                                "taxonomy" => "category",
                                'orderby' => 'name',
                                'hide_empty' => 0,
                                'order' => 'ASC'
                            );

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
        </div>
        <?php
    }

    private function search()
    {
        $args = array(
            "post_type" => "story",
            "s" => $this->phrase,
            "posts_per_page" => $this->ppp,
            "paged" => $this->paged,
            "orderby" => "date",
            "order" => "desc"
        );

        $tax_query = array();

        if (isset($_REQUEST["category"])) {
            $cat = array(
                "taxonomy" => "category",
                "field" => "term_id",
                "terms" => $_REQUEST["category"],
                "operator" => "IN"
            );
            $tax_query[] = $cat;
        }

        if (isset($_REQUEST["sub-category"])) {
            $sub_cat = array(
                "taxonomy" => "category",
                "field" => "term_id",
                "terms" => $_REQUEST["sub-category"],
                "operator" => "IN"
            );
            $tax_query[] = $sub_cat;
        }

        if (isset($_REQUEST["genre"])) {
            $genre = array(
                "taxonomy" => "genre",
                "field" => "term_id",
                "terms" => $_REQUEST["genre"],
                "operator" => "IN"
            );
            $tax_query[] = $genre;
        }

        if (count($tax_query) > 0) {
            $tax_query["relation"] = "AND";
            $args["tax_query"] = $tax_query;
        }

 /*       global $wpdb;
        $stmt = "SELECT pp.ID, pp.post_name, pp.post_author";

        $stmt .= " FROM ".$wpdb->prefix . "posts pp";

        $stmt .= " JOIN ".$wpdb->prefix . "posts p ON pp.Id = p.post_parent";

        if(isset($_REQUEST["category"])){
            $stmt .= " JOIN " . $wpdb->prefix . "term_relationships ctrs ON ctrs.object_id = pp.ID";
            $stmt .= " JOIN " . $wpdb->prefix . "term_taxonomy ctt ON ctt.term_id = ctrs.term_taxonomy_id";
        }

        $stmt .= " WHERE p.post_parent != 0 AND p.post_type = 'story' AND p.post_status = 'publish' AND pp.post_status = 'publish'";

        $stmt .= " AND ( p.post_content LIKE '%". $this->phrase . "%' OR p.post_title LIKE '%" . $this->phrase ."%' )";
        if(isset($_REQUEST["category"])){
            $stmt .= " AND ctt.taxonomy = 'category' AND ctt.term_id IN (". implode(",", $_REQUEST["category"]) .")";
        }

        $stmt .= "GROUP BY p.post_parent";

        $result = $wpdb->get_results($stmt);

        print_r($_REQUEST);
        echo "<br><br>";

        echo $stmt . "<br><br>";

        print_r($result);
*/
        $this->items = new WP_Query($args);
    }

    private function render_checkboxes($terms, $name)
    {
        foreach ($terms as $term) {

            $selected = isset($_REQUEST[$name]) && is_array($_REQUEST[$name]) && in_array($term->term_id, $_REQUEST[$name]) ? "checked" : "";
            echo '<div class="plcCheckBoxWrapper">';
            echo '<div class="plcCheckbox">';
            echo '<input type="checkbox" name="' . $name . '[]" value="' . $term->term_id . '" ' . $selected . '>';
            echo '<label></label>';
            echo '</div>';
            echo '<p>' . $term->name . '</p>';
            echo '</div>';
        }
    }

    private function render()
    {
        Polc_Helper_Module::search_list($this->items->posts, true);
    }

    private function pagination()
    {
        if ($this->total_pages == 1) return;

        echo '<div class="plcPagerWrapper">';
            echo '<div class="plcPagerInnerWrapper">';


            if ($this->paged > 4) {
                echo '<button class="plcPagerBtn" data-page="1">' . __('First page', 'polc') . '</button>';
            }

            //display backwards
            if ($this->paged > 1) {
                for ($i = max(1, $this->paged - 3); $i < $this->paged; $i++) {
                    echo '<button class="plcPagerBtn" data-page="' . $i . '">' . $i . '</button>';
                }
            }

            //display current page
            echo '<button class="current_page">' . $this->paged . '</button>';

            for ($i = $this->paged + 1; $i <= min($this->paged + 3, $this->total_pages); $i++) {
                echo '<button class="plcPagerBtn" data-page="' . $i . '">' . $i . '</button>';
            }

            if ($this->paged < $this->total_pages - 3) {
                echo '<button class="plcPagerBtn" data-page="' . $this->total_pages . '">' . __('Last page', 'polc') . '</button>';
            }

            echo '</div>';
        echo '</div>';
    }
}

new Polc_Search();

get_footer();