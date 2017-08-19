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
class Polc_Content_Editor_Layout_Handler extends Polc_Layout_Handler_Base
{
    CONST POLC_LAYOUT = "polc-content-editor";
    CONST POLC_LAYOUT_NAME = "Tartalom szerkesztő";


    public function render()
    {
        ?>
        <div class="plcContentEditorWrapper">
            <div class="editorHead">
                <h1>Szerkesztendő tartalom főcím</h1>
                <h2>Szerkesztendő tartalom alcím</h2>
            </div>
            <div class="editorContainer">
                <div class="editorDataRow">
                    <textarea placeholder="Fülszöveg"></textarea>
                    <p class="form_info">A fülszöveg maximális hosszúsága 1200 karakter</p>
                </div>
                <div class="editorContainerRow contentWarning">
                    <h4>Figyelmzetetések</h4>
                    <div class="warning obscenecontent">
                        <div class="plcCheckbox">
                            <input type="checkbox" id="obscene-content" name="obscene-content">
                            <label></label>
                        </div>
                        <p>Obszcén tartalom</p>
                    </div>
                    <div class="warning violentcontent">
                        <div class="plcCheckbox">
                            <input type="checkbox" id="violent-content" name="violent-content">
                            <label></label>
                        </div>
                        <p>Erőszakos tartalom</p>
                    </div>
                    <div class="warning eroticcontent">
                        <div class="plcCheckbox">
                            <input type="checkbox" id="erotic-content" name="erotic-content">
                            <label></label>
                        </div>
                        <p>Erotikus tartalom</p>
                    </div>
                </div>
                <div class="editorContainerRow agelimit">
                    <h4>Korhatár</h4>
                    <div class="plcRadiobutton">
                        <label>
                            <input type="radio" name="agelimit" value="18" class="agelimit18">
                            <span>18</span>
                        </label>
                    </div>
                    <div class="plcRadiobutton">
                        <label>
                            <input type="radio" name="agelimit" value="16" class="agelimit16">
                            <span>16</span>
                        </label>
                    </div>
                    <div class="plcRadiobutton">
                        <label>
                            <input type="radio" name="agelimit" value="14" class="agelimit14">
                            <span>14</span>
                        </label>
                    </div>
                    <div class="plcRadiobutton">
                        <label>
                            <input type="radio" name="agelimit" value="12" class="agelimit12">
                            <span>12</span>
                        </label>
                    </div>
                    <div class="plcRadiobutton">
                        <label>
                            <input type="radio" name="agelimit" value="0" checked="" class="noagelimit">
                            <span>Nincs korhatár</span>
                        </label>
                    </div>

                </div>
                <div class="editorContainerRow regWarning">
                    <h4>Korlátozások</h4>
                    <div class="warning registeredOnly">
                        <div class="plcCheckbox">
                            <input type="checkbox" id="only-registered" name="only-registered">
                            <label></label>
                        </div>
                        <p>Csak regisztráltaknak elérhető tartalom</p>
                        <p class="form_info">Figyelem! A 18-as korhatár besorolásba eső történetek, automatikusan csak bejelentkezett felhasználóknak elérhető.</p>
                    </div>

                </div>
                <div class="editorContainerRow bttons">
                    <button>Mentés</button>
                </div>
            </div>
            <div class="contentList">
                <div class="listElement">
                    <h2>Első fejezet címe</h2>
                    <p>Fejezet szerkesztése</p>
                    <span></span>
                </div>
                <div class="listElement">
                    <h2>Második fejezet címe, ami rendkívül hosszú tesztelés szempontjából fontos</h2>
                    <p>Fejezet szerkesztése</p>
                    <span></span>
                </div>
                <div class="listElement">
                    <h2>Harmadik fejezet címe</h2>
                    <p>Fejezet szerkesztése</p>
                    <span></span>
                </div>
                <div class="listElement">
                    <h2>Negyedik fejezet címe</h2>
                    <p>Fejezet szerkesztése</p>
                    <span></span>
                </div>
                <div class="listElement">
                    <h2>Ötödik fejezet címe</h2>
                    <p>Fejezet szerkesztése</p>
                    <span></span>
                </div>
            </div>

        </div>

        <?php
    }
}