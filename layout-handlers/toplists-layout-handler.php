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
class Polc_Toplists_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-toplists";
    CONST POLC_LAYOUT_NAME = "Sikerlisták";


    public function render()
    {
        ?>
        <div class="plcToplistsWrapper  ">
            <div class="toplistsItem favouriteWriters">
                <div class="innerItem">
                    <h1>Kedvenc szerzők</h1>
                    <div class="list">
                        <div class="toplistListItem">
                            <a href=""><span>1.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>2.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>3.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>4.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>5.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>6.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>7.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>8.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>9.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                        <div class="toplistListItem">
                            <a href=""><span>10.</span><h2>Stephen King</h2></a>
                            <a href="">245 ember kedvenc szerzője</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="toplistsItem commentators">
                <div class="innerItem">
                    <h1>Legaktívabb vélemény írók</h1>
                    <div class="list">
                        <a href=""><span>1.</span><h2>Stephen King</h2></a>
                        <a href=""><span>2.</span><h2>Lőrinc L. László</h2></a>
                        <a href=""><span>3.</span><h2>Joe Schreiber</h2></a>
                        <a href=""><span>4.</span><h2>Jane Austen</h2></a>
                        <a href=""><span>5.</span><h2>Meg Cabot</h2></a>
                        <a href=""><span>6.</span><h2>Peter Straub</h2></a>
                        <a href=""><span>7.</span><h2>Paula Hawkins</h2></a>
                        <a href=""><span>8.</span><h2>Csernus Imre</h2></a>
                        <a href=""><span>9.</span><h2>Nem jut eszembe senki</h2></a>
                        <a href=""><span>10.</span><h2>Rejtő Jenő</h2></a>
                    </div>
                </div>
            </div>

            <div class="toplistsItem  favouriteContent">
                <div class="innerItem">
                    <h1>Kedvenc tartalmak</h1>
                    <div class="list">
                        <div class="contentItem">
                            <a href=""><span>1.</span><h2>Aki kapja marja - Stephen King</h2></a>
                            <a href="">256 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>2.</span><h2>Valami, aminek nem egy soros a címe - Valaki, akinek hosszú a neve</h2></a>
                            <a href="">223 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>3.</span><h2>Vesztegzár - Joe Schreiber</h2></a>
                            <a href="">210 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>4.</span><h2>Naruto és az ezer arcú rókakölyök - Mosttaláltamki</h2></a>
                            <a href="">140 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>5.</span><h2>Harry Potter és a kitalált karakterek fanfictionja - Kitalált szerző</h2></a>
                            <a href="">129 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>6.</span><h2>Rám zuhant a háztető - Anonymus</h2></a>
                            <a href="">101 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>7.</span><h2>Darth Vader és a legjobb apukák csoportköre - Luke</h2></a>
                            <a href="">92 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>8.</span><h2>Nem hinném, hogy jó vagyok - Egy jó ember</h2></a>
                            <a href="">82 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>9.</span><h2>Altató és kloroform - Drogériás</h2></a>
                            <a href="">256 felhasználó kedvence</a>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>10.</span><h2>Hogy múlik az idő - Óra</h2></a>
                            <a href="">256 felhasználó kedvence</a>
                        </div>

                    </div>
                </div>
            </div>

            <div class="toplistsItem  view">
                <div class="innerItem">
                    <h1>Legolvasottabb tartalmak</h1>
                    <div class="list">
                        <div class="contentItem">
                            <a href=""><span>1.</span><h2>Aki kapja marja - Stephen King</h2></a>
                            <p>12 300 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>2.</span><h2>Valami, aminek nem egy soros a címe - Valaki, akinek hosszú a neve</h2></a>
                            <p>11 029 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>3.</span><h2>Vesztegzár - Joe Schreiber</h2></a>
                            <p>10 921 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>4.</span><h2>Naruto és az ezer arcú rókakölyök - Mosttaláltamki</h2></a>
                            <p>8320 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>5.</span><h2>Harry Potter és a kitalált karakterek fanfictionja - Kitalált szerző</h2></a>
                            <p>5123 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>6.</span><h2>Rám zuhant a háztető - Anonymus</h2></a>
                            <p>1231 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>7.</span><h2>Darth Vader és a legjobb apukák csoportköre - Luke</h2></a>
                            <p>902 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>8.</span><h2>Nem hinném, hogy jó vagyok - Egy jó ember</h2></a>
                            <p>123 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>9.</span><h2>Altató és kloroform - Drogériás</h2></a>
                            <p>122 megtekintés</p>
                        </div>
                        <div class="contentItem">
                            <a href=""><span>10.</span><h2>Hogy múlik az idő - Óra</h2></a>
                            <p>99 megtekintés</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <?php
    }
}