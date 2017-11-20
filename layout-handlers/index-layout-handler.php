<?php

/**
 * Created by PhpStorm.
 * User: Pali
 * Date: 2017. 06. 17.
 * Time: 8:42
 */

if (!defined("ABSPATH")):
    exit();
endif;

/**
 * Class Polc_Index_Layout_Handler
 */
class Polc_Index_Layout_Handler extends Polc_Layout_Handler_Base
{
    private $layout_settings;

    CONST POLC_LAYOUT = "polc-index";
    CONST POLC_LAYOUT_NAME = "Főoldal";

    public function render()
    {
        $this->layout_settings = Polc_Settings_Manager::layout();
        ?>
		<div class="primePartWrapper">
            <div class="innerContainer">
				<div class="textPart">
					 <h1>Üdvözlünk a Polcon!</h1>
                    <h2>Csatlakozz olvasást kedvelő közösségünkhöz! Légy tagja egy olyan csapatnak, ahol akár íróként is kipróbálhatod magad.
                        Regisztrálj és tedd fel a polcra te is saját történeted, vagy csak böngészd másokét, akár tableteden vagy mobilodon is.
						Olvasó felületünket személyre szabhatod, hogy minden helyzetben kényelmesen ugorhass kedvenc történetedbe.</h2>
				</div>
				<div class="imagePart">
					<div class="innerImage">
					</div>
				</div>

            </div>
        </div>
		
        <div class="plcNewStoriesWrapper">

            <div class="plcLatestStoriesWrapper">
                <?php

                $args = [
                    "posts_per_page" => isset($this->layout_settings["stories"]["count"]) ? $this->layout_settings["stories"]["count"] : 10
                ];

                Polc_Get_Module::get_latest_stories($args, true);
                ?>
            </div>

            <div class="plcLatestNewsWrapper">
                <h1 class="plcTitle"><?= __('News', 'polc'); ?></h1>
                <?php

                $args = [
                    "posts_per_page" => isset($this->layout_settings["news"]["count"]) ? $this->layout_settings["news"]["count"] : 3,
                    "category" => $this->layout_settings["news"]["term_id"]
                ];

                Polc_Get_Module::get_latest_posts($args,false);
                ?>
                <h1 class="plcTitle"><?= __('Recommendation', 'polc'); ?></h1>
                <?php

                $args = [
                    "posts_per_page" => isset($this->layout_settings["recommend"]["count"]) ? $this->layout_settings["recommend"]["count"] : 3,
                    "category" => $this->layout_settings["recommend"]["term_id"]

                ];
                Polc_Get_Module::get_latest_posts($args,false);
                ?>
            </div>
        </div>
        <?php

    }
}