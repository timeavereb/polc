<?php

get_header();

global $post;
$layout_slug = get_post_meta($post->ID, "polc_layout", "");

$layout_slug = count($layout_slug) == 0 || $layout_slug[0] == "" ? "polc-index" : $layout_slug[0];

$classes = get_declared_classes();
$implementsIModule = [];
foreach ($classes as $klass):
    $reflect = new ReflectionClass($klass);
    if ($reflect->implementsInterface('Polc_Layout_Handler_IF') && !$reflect->isAbstract()):
        $implementsIModule[] = $klass;
    endif;
endforeach;

foreach ($implementsIModule as $class):
    $current = new $class();

    if ($current::POLC_LAYOUT == $layout_slug):
        $current->render();
        break;
    endif;
endforeach;

get_footer();