<?php
/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 10.
 * Time: 11:00
 */

get_header();
?>
    <div class="plcTagSearchWrapper">
        <?php
        Polc_Get_Module::search_by_tag(true);
        ?>
    </div>
<?php
get_footer();