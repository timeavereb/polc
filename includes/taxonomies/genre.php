<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 07. 01.
 * Time: 8:03
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Registers taxonomy "genre".
 */
function genre()
{
    $labels = [
        'name' => _x('Genres', 'Taxonomy General Name', 'polc'),
        'singular_name' => _x('Genre', 'Taxonomy Singular Name', 'polc'),
        'menu_name' => __('Genre', 'polc'),
        'all_items' => __('All Genres', 'polc'),
        'parent_item' => __('Parent Genre', 'polc'),
        'parent_item_colon' => __('Parent Genre:', 'polc'),
        'new_item_name' => __('New Genre Name', 'polc'),
        'add_new_item' => __('Add New Genre', 'polc'),
        'edit_item' => __('Edit Genre', 'polc'),
        'update_item' => __('Update Genre', 'polc'),
        'view_item' => __('View Genre', 'polc'),
        'separate_items_with_commas' => __('Separate genres with commas', 'polc'),
        'add_or_remove_items' => __('Add or remove genres', 'polc'),
        'choose_from_most_used' => __('Choose from the most used', 'polc'),
        'popular_items' => __('Popular Genres', 'polc'),
        'search_items' => __('Search Genres', 'polc'),
        'not_found' => __('Not Found', 'polc'),
        'no_terms' => __('No genres', 'polc'),
        'items_list' => __('Genres list', 'polc'),
        'items_list_navigation' => __('Genres list navigation', 'polc'),
    ];
    $args = [
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => false,
    ];
    register_taxonomy('genre', ['story'], $args);
}

add_action('init', 'genre', 0);