<?php
/**
 * Created by PhpStorm.
 * User: Timi
 * Date: 2017. 11. 23.
 * Time: 19:15
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Class Polc_About_Layout_Handler
 */
class Polc_About_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-about";
    CONST POLC_LAYOUT_NAME = "Rólunk";


    public function render()
    {
        ?>
            <div class="plcAboutwrapper">
                <div class="plcAboutContainer">
                    <div class="sectionContainer iconLeft">
                        <div class="part">
                            <h1>Mi is ez az oldal?</h1>
                            <h2>A Polc egy mindenki számára elérhető, ingyenes oldal, melyre bárki feltölthet saját irodalmi termékét, vagy böngészheti másokét, legyen az egy saját szereplős regény, vers, vagy akár fanfiction.</h2>
                        </div>
                        <div class="part">
                            <div class="icon">

                            </div>
                        </div>
                    </div>
                    <div class="sectionContainer iconRight">
                        <div class="part">
                            <h1>Kik vagyunk?</h1>
                            <h2>Az oldalt jelenleg két ember fejleszti. Egy webfejlesztő és egy designer. Mindketten szabadidejükben foglalkoznak az oldallal, és igyekeznek a legjobb tudásuk szerint a felhasználók rendelkezésére állni. Bármilyen észrevétellel bátran lehet őket keresni az <span>info@polc.eu</span> e-mail címen.</h2>
                        </div>
                        <div class="part">
                            <div class="icon">

                            </div>
                        </div>
                    </div>
                    <div class="sectionContainer iconLeft">
                        <div class="part">
                            <h1>Mik a célkitűzéseink?</h1>
                            <h2>Szeretnénk egy olyan oldalt létrehozni, ahová jó szívvel tér vissza az ember, ahol író és olvasó közvetlen kapcsolatban lehetnek, és ahol nem számít semmi más, csak az, hogy jól érezd magad! </h2>
                        </div>
                        <div class="part">
                            <div class="icon">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}