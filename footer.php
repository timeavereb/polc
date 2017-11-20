
<footer>
 <div class="socialBlock">
  <span class="email">Fordulj hozzánk bizalommal: <strong>info@polc.eu</strong></span>
 </div>
 <div class="footer_menu_container">
 <?php

 $menu = wp_get_nav_menu_items('polc-main-menu', [
     'order' => 'ASC',
     'orderby' => 'menu_order',
     'post_type' => 'nav_menu_item',
     'post_status' => 'publish',
     'output' => ARRAY_A,
     'output_key' => 'menu_order',
     'nopaging' => true
 ]);

 foreach ($menu as $menu_element):
  ?>
       <a href="<?= $menu_element->url; ?>"
          class="plc_navigation_footer_item_wrapper <?= sanitize_title($menu_element->title); ?>">
        <div class="plc_footer_navigation_item"><?= $menu_element->title; ?></div>
       </a>
  <?php
 endforeach;
 ?>
 </div>
 <div class="footerwrapper">
  <div class="cpright">© Polc. All rights reserved.</div>
 </div>

</footer>

<?php wp_footer();?>

</body>
</html>