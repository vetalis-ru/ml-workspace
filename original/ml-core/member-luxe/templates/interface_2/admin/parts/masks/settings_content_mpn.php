<div id="tab-mbl-nav" class="tab mpn-color-content mask-tab">
	<?php wpm_render_partial('masks/mask-mpn', 'admin'); ?>
    <div class="wpm-tab-content">
        <div class="wpm-control-row">
            <label><input type="checkbox" checked="">
                &nbsp;<?php _e('Включить', 'mbl_admin') ?>
            </label><br>
        </div>

        <h3><?php _e('Фон', 'mbl_admin') ?></h3>

        <div class="wpm-control-row">
            <label><?php _e('Цвет фона', 'mbl_admin') ?><br>
                <input type="text" class="color" value="F7F8F9"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(247, 248, 249); color: rgb(0, 0, 0);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label><?php _e('Фоновое изображение', 'mbl_admin') ?><br>
                <input type="text" id="wpm_mbli3_background" value="" class="wide"></label>

            <div class="wpm-control-row upload-image-row">
                <p>
                    <button type="button" class="wpm-media-upload-button button" data-id="mbli3_background"><?php _e('Загрузить', 'mbl_admin') ?></button>
                    &nbsp;&nbsp; <a id="delete-wpm-mbli3_background"
                                    class="wpm-delete-media-button button submit-delete"
                                    data-id="mbli3_background"><?php _e('Удалить', 'mbl_admin') ?></a>
                </p>
            </div>
            <div class="wpm-background-preview-wrap">
                <div class="wpm-background-preview-box preview-box">
                    <img src="" title="" alt="" id="wpm-mbli3_background-preview">
                </div>
            </div>
        </div>
        <div class="wpm-control-row">
            <label><?php _e('Выравнивание изображения', 'mbl_admin') ?></label><br>
            <select>
                <option value="left top">сверху слева</option>
            </select></div>
        <div class="wpm-control-row">
            <label>Повторение изображения</label><br>
            <select>
                <option value="no-repeat">не повторять</option>
            </select></div>
        <br>

        <div class="wpm-control-row">
            <label><input type="checkbox">
                &nbsp;<?php _e('Зафиксировать фон', 'mbl_admin') ?></label><br>
        </div>

        <br>

        <hr>

        <h3><?php _e('Закрытие панели', 'mbl_admin') ?></h3>

        <div class="wpm-control-row">
            <label>Цвет крестика<br>
                <input type="text" class="color" value="868686"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(134, 134, 134); color: rgb(0, 0, 0);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label>Цвет крестика при наведении<br>
                <input type="text" class="color"
                       value="2E2E2E"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(46, 46, 46); color: rgb(255, 255, 255);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label>Цвет крестика при клике<br>
                <input type="text" class="color"
                       value="2E2E2E"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(46, 46, 46); color: rgb(255, 255, 255);">
            </label>
        </div>
        <br>

        <hr>

        <h3>Заголовок рубрики</h3>

        <div class="wpm-control-row">
            <label>Цвет<br>
                <input type="text" class="color" value="000000"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(0, 0, 0); color: rgb(255, 255, 255);">
            </label>
        </div>
        <div class="wpm-control-row">
            Размер заголовка &nbsp;
            <select>
                <option selected="" value="20">20px</option>
            </select>
        </div>

        <br>

        <hr>

        <h3>Пункт меню</h3>

        <div class="wpm-row">
            <label>
                Название<br>
                <input type="text" value="Меню">
            </label>
        </div>

        <br>

        <div class="wpm-control-row">
            <label>Цвет иконки<br>
                <input type="text" class="color" value="868686"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(134, 134, 134); color: rgb(0, 0, 0);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label>Цвет иконки при наведении<br>
                <input type="text" class="color" value="2E2E2E"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(46, 46, 46); color: rgb(255, 255, 255);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label>Цвет иконки при клике<br>
                <input type="text" class="color" value="2E2E2E"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(46, 46, 46); color: rgb(255, 255, 255);">
            </label>
        </div>
        <br>

        <div class="wpm-control-row">
            <label>Цвет текста<br>
                <input type="text" class="color" value="868686"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(134, 134, 134); color: rgb(0, 0, 0);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label>Цвет текста при наведении<br>
                <input type="text" class="color"
                       value="2E2E2E"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(46, 46, 46); color: rgb(255, 255, 255);">
            </label>
        </div>
        <div class="wpm-control-row">
            <label>Цвет текста при клике<br>
                <input type="text" class="color"
                       value="2E2E2E"
                       autocomplete="off"
                       style="background-image: none; background-color: rgb(46, 46, 46); color: rgb(255, 255, 255);">
            </label>
        </div>
        <div class="wpm-row">
            <label>
                <input type="hidden" value="off">
                <input type="checkbox" value="on">Скрыть ссылку на главную
                страницу<br>
            </label>
        </div>

        <div class="wpm-tab-footer">
            <button type="submit" class="button button-primary wpm-save-options">Сохранить</button>
            <span class="buttom-preloader"></span>
        </div>
    </div>
</div>