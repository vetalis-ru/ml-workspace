<div class="tab-pane" id="mbl_hw_stats_pane" style="overflow-x: auto; padding-right: 5px; padding-bottom: 5px">
    <div class="mpt-color-content mask-tab" style="padding: 15px">
	    <?php wpm_render_partial('masks/mask-mpt', 'admin'); ?>
        <div class="page-content-wrap">
            <div class="mbla-stats-filter-block">
                <form id="mbla-stats-filter" lang="ru">
                    <input name="action" type="hidden" value="mbla_get_homework_stats_charts">
                    <div class="mbla-stats-filter-button" data-mobile-only="">
                        <i class="fa fa-sliders mbla-filters-icon" aria-hidden="true"></i>
                        Фильтр                    <i class="fa fa-chevron-down mbla-filters-closed" aria-hidden="true"></i>
                        <i class="fa fa-chevron-up mbla-filters-opened" aria-hidden="true"></i>
                    </div>
                    <div class="flex-row mbla-stats-filter-row">
                        <div class="flex-col-filters-stats pr-10">
                            <div class="mbla-select-wrap">
                                <i class="fa fa-flask" aria-hidden="true"></i>
                                <select name="type" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" data-select2-id="1" data-mbla-select-2="">
                                    <option value="" selected="" data-select2-id="3">Все типы заданий</option>
                                    <option value="question">Вопросы</option>
                                    <option value="test">Тесты</option>
                                </select><span class="select2 select2-container select2-container--default" style="width: auto;" dir="ltr" data-select2-id="2"><span class="selection"><span tabindex="0" class="select2-selection select2-selection--single" role="combobox" aria-disabled="false" aria-expanded="false" aria-haspopup="true" aria-labelledby="select2-type-oj-container"><span title="Все типы заданий" class="select2-selection__rendered" id="select2-type-oj-container" role="textbox" aria-readonly="true">Все типы заданий</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                            <div class="mbla-select-wrap">
                                <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                <select name="wpm-category" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" data-select2-id="4" data-mbla-select-2="">
                                    <option value="" selected="" data-select2-id="6">Все рубрики</option>
                                    <option value="simple-course">Simple course</option>
                                    <option value="%d0%b0%d0%b2%d1%82%d0%be%d1%82%d1%80%d0%b5%d0%bd%d0%b8%d0%bd%d0%b3">Автотренинг</option>
                                    <option value="%d1%81%d1%83%d0%bf%d0%b5%d1%80-%d0%ba%d1%83%d1%80%d1%81">Супер курс</option>
                                </select><span class="select2 select2-container select2-container--default" style="width: auto;" dir="ltr" data-select2-id="5"><span class="selection"><span tabindex="0" class="select2-selection select2-selection--single" role="combobox" aria-disabled="false" aria-expanded="false" aria-haspopup="true" aria-labelledby="select2-wpm-category-x4-container"><span title="Все рубрики" class="select2-selection__rendered" id="select2-wpm-category-x4-container" role="textbox" aria-readonly="true">Все рубрики</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                            <div class="mbla-select-wrap">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                <select name="material" tabindex="-1" class="select2-hidden-accessible" id="mbla_stats_material_select" aria-hidden="true" data-placeholder="Все материалы" data-select2-id="mbla_stats_material_select"></select><span class="select2 select2-container select2-container--default" style="width: auto;" dir="ltr" data-select2-id="10"><span class="selection"><span tabindex="0" class="select2-selection select2-selection--single" role="combobox" aria-disabled="false" aria-expanded="false" aria-haspopup="true" aria-labelledby="select2-mbla_stats_material_select-container"><span class="select2-selection__rendered" id="select2-mbla_stats_material_select-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Все материалы</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                            <div class="mbla-select-wrap">
                                <i class="fa fa-sitemap" aria-hidden="true"></i>
                                <select name="wpm-levels" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" data-select2-id="7" data-mbla-select-2="">
                                    <option value="" selected="" data-select2-id="9">Все уровни доступа</option>
                                    <option value="medics">Medics</option>
                                    <option value="simple">Simple</option>
                                    <option value="student">Student</option>
                                    <option value="test">test</option>
                                </select><span class="select2 select2-container select2-container--default" style="width: auto;" dir="ltr" data-select2-id="8"><span class="selection"><span tabindex="0" class="select2-selection select2-selection--single" role="combobox" aria-disabled="false" aria-expanded="false" aria-haspopup="true" aria-labelledby="select2-wpm-levels-cb-container"><span title="Все уровни доступа" class="select2-selection__rendered" id="select2-wpm-levels-cb-container" role="textbox" aria-readonly="true">Все уровни доступа</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                            <div class="mbla-select-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input name="date_from" id="mbla_stats_date_from_input" type="hidden" value="26.06.2020">
                                <input name="date_to" id="mbla_stats_date_to_input" type="hidden" value="03.07.2020">
                                <div class="select2 select2-container select2-container--default select2-container--above mbla-dates-select" dir="ltr">
                                <span class="selection">
                                <span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true">
                                <span class="select2-selection__rendered" id="mbla-dates-placeholder" role="textbox" aria-readonly="true">26.06.2020 - 03.07.2020</span>
                                <span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>
                                </span>
                                </span>
                                    <div class="mbla-dates-popup panel panel-default">
                                        <div class="date-title">Дата</div>
                                        <div class="flex-row flex-wrap">
                                            <div class="flex-col-6 pr-10">
                                                <div class="mbla-date-label">Дата начала</div>
                                                <div class="mbla-date hasDatepicker" id="mbla_stats_date_from" data-start-date="26.06.2020"><div class="ui-datepicker-inline ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="display: block;"><div class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all"><a title="Prev" class="ui-datepicker-prev ui-corner-all" data-event="click" data-handler="prev"><span class="ui-icon ui-icon-circle-triangle-w">Prev</span></a><a title="Next" class="ui-datepicker-next ui-corner-all" data-event="click" data-handler="next"><span class="ui-icon ui-icon-circle-triangle-e">Next</span></a><div class="ui-datepicker-title"><span class="ui-datepicker-month">June</span>&nbsp;<span class="ui-datepicker-year">2020</span></div></div><table class="ui-datepicker-calendar"><thead><tr><th class="ui-datepicker-week-end" scope="col"><span title="Sunday">Su</span></th><th scope="col"><span title="Monday">Mo</span></th><th scope="col"><span title="Tuesday">Tu</span></th><th scope="col"><span title="Wednesday">We</span></th><th scope="col"><span title="Thursday">Th</span></th><th scope="col"><span title="Friday">Fr</span></th><th class="ui-datepicker-week-end" scope="col"><span title="Saturday">Sa</span></th></tr></thead><tbody><tr><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">1</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">2</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">3</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">4</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">5</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">6</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">7</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">8</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">9</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">10</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">11</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">12</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">13</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">14</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">15</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">16</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">17</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">18</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">19</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">20</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">21</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">22</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">23</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">24</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">25</a></td><td class=" ui-datepicker-days-cell-over  ui-datepicker-current-day" data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default ui-state-active ui-state-hover" href="#">26</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">27</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">28</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">29</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="5"><a class="ui-state-default" href="#">30</a></td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td></tr></tbody></table></div></div>
                                            </div>
                                            <div class="flex-col-6 pr-10">
                                                <div class="mbla-date-label">Дата окончания</div>
                                                <div class="mbla-date hasDatepicker" id="mbla_stats_date_to"><div class="ui-datepicker-inline ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="display: block;"><div class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all"><a title="Prev" class="ui-datepicker-prev ui-corner-all" data-event="click" data-handler="prev"><span class="ui-icon ui-icon-circle-triangle-w">Prev</span></a><a title="Next" class="ui-datepicker-next ui-corner-all" data-event="click" data-handler="next"><span class="ui-icon ui-icon-circle-triangle-e">Next</span></a><div class="ui-datepicker-title"><span class="ui-datepicker-month">July</span>&nbsp;<span class="ui-datepicker-year">2020</span></div></div><table class="ui-datepicker-calendar"><thead><tr><th class="ui-datepicker-week-end" scope="col"><span title="Sunday">Su</span></th><th scope="col"><span title="Monday">Mo</span></th><th scope="col"><span title="Tuesday">Tu</span></th><th scope="col"><span title="Wednesday">We</span></th><th scope="col"><span title="Thursday">Th</span></th><th scope="col"><span title="Friday">Fr</span></th><th class="ui-datepicker-week-end" scope="col"><span title="Saturday">Sa</span></th></tr></thead><tbody><tr><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">1</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">2</a></td><td class=" ui-datepicker-days-cell-over  ui-datepicker-current-day ui-datepicker-today" data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default ui-state-highlight ui-state-active ui-state-hover" href="#">3</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">4</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">5</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">6</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">7</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">8</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">9</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">10</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">11</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">12</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">13</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">14</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">15</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">16</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">17</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">18</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">19</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">20</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">21</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">22</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">23</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">24</a></td><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">25</a></td></tr><tr><td class=" ui-datepicker-week-end " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">26</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">27</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">28</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">29</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">30</a></td><td class=" " data-event="click" data-handler="selectDay" data-year="2020" data-month="6"><a class="ui-state-default" href="#">31</a></td><td class=" ui-datepicker-week-end ui-datepicker-other-month ui-datepicker-unselectable ui-state-disabled">&nbsp;</td></tr></tbody></table></div></div>
                                            </div>
                                        </div>
                                        <div class="mbla-dates-buttons">
                                            <a class="close mbla-date-close" href="#">Отмена</a>
                                            <a class="mbla-date-submit button button-primary" href="#">Применить</a>
                                        </div>
                                    </div>
                                </div>                        </div>
                        </div>
                        <div class="flex-col-4 pr-10">
                            <div class="wpma-stats-users">
                                <div class="wpma-user-row pr-15">
                                    <label>
                                        <input name="users[]" type="checkbox" value="1">
                                        <i class="fa fa-user-circle-o"></i>
                                        admin                                        </label>
                                </div>
                                <div class="wpma-user-row pr-15">
                                    <label>
                                        <input name="users[]" type="checkbox" value="2">
                                        <i class="fa fa-user-circle-o"></i>
                                        user                                        </label>
                                </div>
                                
                            </div>
                            <div class="wpma-actions">
                                <button class="mbla-form-submit button button-primary" type="button">Применить</button>
                                <button class="mbla-form-clear button button-secondary" type="button">Сбросить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="mbla-hw-stats-charts">
                <section class="wpma-chart-section empty-section">
                    <div class="wpma-stats-chart-holder">
                        <div class="chartjs-size-monitor">
                            <div></div>
                        </div>
                    </div>
                    <div class="wpma-stats-total">
<span class="wpma-user-name">
    Всего                            </span>
                        <span class="wpma-stats-value">
<i class="fa fa-book"></i> 0
</span>
                    </div>
                    <div class="wpma-stats-details flex-row flex-wrap">
                        <div title="" class="wpma-stats-value opened" data-original-title="Ожидающие" data-mbl-tooltip="">
                            <i class="fa fa-clock-o"></i> 0 (0%)
                        </div>
                        <div title="" class="wpma-stats-value approved" data-original-title="Одобренные вручную" data-mbl-tooltip="">
                            <i class="fa fa-hand-pointer-o"></i> 0 (0%)
                        </div>
                        <div title="" class="wpma-stats-value accepted" data-original-title="Одобренные автоматически" data-mbl-tooltip="">
                            <i class="fa fa-cogs"></i> 0 (0%)
                        </div>
                        <div title="" class="wpma-stats-value rejected" data-original-title="Неправильные" data-mbl-tooltip="">
                            <i class="fa fa-times-circle-o"></i> 0 (0%)
                        </div>
                    </div>

                </section>

                <script>
                    // jQuery(function ($) {
                    //     var $chart = $('#wpma-chart-all');
                    //
                    //     new Chart($chart, {
                    //         type: 'pie',
                    //         position : 'left',
                    //         data: {
                    //             datasets: [{
                    //                 data: [1],
                    //                 backgroundColor: ['#ccc']
                    //             }]
                    //         },
                    //         options: {
                    //             legend: false,
                    //             tooltips: {
                    //                 enabled: false
                    //             }
                    //         }
                    //     })
                    //     ;
                    // });
                </script>
            </div>
            <div class="mbla-note">
                Сбор данных статистики домашних заданий начинается с момента установки MEMBERLUX версии 2.5.0 или выше.            <br>
                Если за один и тот же автотренинг отвечают два или более тренеров, то задания, ожидающие проверку, будут учитываться в статистике каждого из них.        </div>
        </div>
    </div>
</div>

<style>
    .chartjs-size-monitor {
        width: 150px;
        height: 150px;
        display: block;
        background-color: gainsboro;
        border-radius: 50%;
        margin: auto;
        position: relative;
    }
    .chartjs-size-monitor div {
        position: absolute;
        top: 0;
        left: 74px;
        right: 0;
        height: 75px;
        width: 2px;
        background-color: white;
    }
	#mbl_hw_stats_pane a {
		color: #0073aa;
		transition-property: border, background, color;
		transition-duration: 0.05s;
		transition-timing-function: ease-in-out;
	}
    #mbl_hw_stats_pane .tab-content > .tab-pane, .pill-content > .pill-pane {
		display: none;
	}
    #mbl_hw_stats_pane .tab-content > .active, .pill-content > .active {
		display: block;
	}

    #mbl_hw_stats_pane .mbl-homework-wrap .tab-content {
		overflow: visible;
	}

    #mbl_hw_stats_pane .panel {
		margin-bottom: 20px;
		background-color: #ffffff;
		border: 1px solid transparent;
		border-radius: 4px;
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
		box-shadow: 0px 1px 1px rgba(0,0,0,0.05);
	}
    #mbl_hw_stats_pane .panel-default {
		border-color: #dddddd;
	}

    #mbl_hw_stats_pane .locale-ru-ru #adminmenu, .locale-ru-ru #wpbody {
		margin-left: 0px;
	}

    #mbl_hw_stats_pane .mbla-stats-filter-block {
		padding-bottom: 5px;
	}
    #mbl_hw_stats_pane .mbla-note {
		padding: 15px;
		background: #f1f1f1;
		border-radius: 3px;
		border: 1px solid #dddddd;
	}
    #mbl_hw_stats_pane .wpma-chart-section {
		border-top: 1px solid #ccc;
		padding: 20px 0;
		min-height: 190px;
		box-sizing: border-box;
		position: relative;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-chart-holder {
		width: 300px;
		text-align: left;
		position: absolute;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-total {
		font-weight: bold;
		padding-left: 300px;
	}
    #mbl_hw_stats_pane .flex-row {
		display: flex;
	}
    #mbl_hw_stats_pane .flex-wrap {
		flex-wrap: wrap;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-details {
		margin-top: 15px;
		padding-left: 300px;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-value {
		font-size: 16px;
		margin-right: 15px;
	}
    #mbl_hw_stats_pane .fa {
		display: inline-block;
		font: normal normal normal 14px/1 FontAwesome;
		font-size: inherit;
		text-rendering: auto;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
    #mbl_hw_stats_pane .fa-times-circle-o::before {
		content: "\f05c";
	}
    #mbl_hw_stats_pane .fa-gears::before, #mbl_hw_stats_pane .fa-cogs::before {
		content: "\f085";
	}
    #mbl_hw_stats_pane .fa-hand-pointer-o::before {
		content: "\f25a";
	}
    #mbl_hw_stats_pane .fa-clock-o::before {
		content: "\f017";
	}
    #mbl_hw_stats_pane .fa-book::before {
		content: "\f02d";
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-value i {
		margin-right: 3px;
		font-size: 24px;
		position: relative;
		top: 3px;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-value.rejected i {
		color: #d32527;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-value.accepted i {
		color: #0c5bac;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-value.approved i {
		color: #47ad47;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-stats-value.opened i {
		color: #fa9d11;
	}
    #mbl_hw_stats_pane .wpma-chart-section .wpma-user-name {
		font-weight: bold;
		margin-right: 15px;
	}

    #mbl_hw_stats_pane .mbla-stats-filter-button {
		text-align: center;
		padding: 20px 0;
		font-size: 16px;
		cursor: pointer;
	}
	/* @media screen and (min-width:782.1px) */
	[data-mobile-only] {
		display: none;
	}
    #mbl_hw_stats_pane .flex-col-4 {
		min-width: 33.33%;
		box-sizing: border-box;
		max-width: 100%;
	}
    #mbl_hw_stats_pane .flex-col-6 {
		min-width: 50%;
		box-sizing: border-box;
		max-width: 100%;
	}
    #mbl_hw_stats_pane .pr-10 {
		padding-right: 10px;
	}
    #mbl_hw_stats_pane .mbla-stats-filter-row .flex-col-filters-stats {
		width: 100%;
		box-sizing: border-box;
		max-width: 515px;
	}
    #mbl_hw_stats_pane .wpma-stats-users {
		min-height: 153px;
		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		padding-bottom: 15px;
		box-sizing: border-box;
	}
    #mbl_hw_stats_pane .wp-core-ui .button, .wp-core-ui .button-primary, .wp-core-ui .button-secondary {
		display: inline-block;
		text-decoration: none;
		font-size: 13px;
		line-height: 2.1538;
		min-height: 30px;
		margin: 0;
		padding: 0 10px;
		cursor: pointer;
		border-width: 1px;
		border-style: solid;
		-webkit-appearance: none;
		border-radius: 3px;
		white-space: nowrap;
		box-sizing: border-box;
	}
    #mbl_hw_stats_pane .wp-core-ui .button, .wp-core-ui .button-secondary {
		color: #0071a1;
		border-color: #0071a1;
		background: #f3f5f6;
		vertical-align: top;
	}
    #mbl_hw_stats_pane .wp-core-ui .button-primary {
		background: #007cba;
		border-color: #007cba;
		color: #fff;
		text-decoration: none;
		text-shadow: none;
	}
    #mbl_hw_stats_pane .wp-core-ui .wpma-actions button.button {
		width: 120px;
		margin-right: 10px;
	}
    #mbl_hw_stats_pane .wpma-user-row {
		margin-bottom: 5px;
	}
    #mbl_hw_stats_pane .pr-15 {
		padding-right: 15px;
	}
    #mbl_hw_stats_pane label {
		cursor: pointer;
	}
    #mbl_hw_stats_pane #your-profile label + a, label {
		vertical-align: middle;
	}
    #mbl_hw_stats_pane input[type=checkbox], input[type=radio] {
		border: 1px solid #7e8993;
		border-radius: 4px;
		background: #fff;
		color: #555;
		clear: none;
		cursor: pointer;
		display: inline-block;
		line-height: 0;
		height: 1rem;
		margin: -.25rem .25rem 0 0;
		outline: 0;
		padding: 0 !important;
		text-align: center;
		vertical-align: middle;
		width: 1rem;
		min-width: 1rem;
		-webkit-appearance: none;
		box-shadow: inset 0px 1px 2px rgba(0,0,0,0.1);
		transition: .05s border-color ease-in-out;
	}
    #mbl_hw_stats_pane .fa-user-circle-o::before {
		content: "\f2be";
	}
    #mbl_hw_stats_pane .wpma-user-row i.fa {
		font-size: 18px;
		width: 24px;
		text-align: center;
	}
    #mbl_hw_stats_pane .mbla-select-wrap {
		clear: both;
		padding-bottom: 10px;
	}
    #mbl_hw_stats_pane .fa-calendar::before {
		content: "\f073";
	}
    #mbl_hw_stats_pane .fa-sitemap::before {
		content: "\f0e8";
	}
    #mbl_hw_stats_pane .fa-file-text-o::before {
		content: "\f0f6";
	}
    #mbl_hw_stats_pane .fa-folder-open-o::before {
		content: "\f115";
	}
    #mbl_hw_stats_pane .fa-flask::before {
		content: "\f0c3";
	}
    #mbl_hw_stats_pane .mbla-select-wrap i.fa {
		font-size: 28px;
		margin-right: 5px;
		float: left;
		width: 30px;
	}
    #mbl_hw_stats_pane .select2-container {
		box-sizing: border-box;
		display: inline-block;
		margin: 0;
		position: relative;
		vertical-align: middle;
	}
    #mbl_hw_stats_pane .mbla-select-wrap .select2 {
		min-width: calc(100% - 60px);
		width: calc(100% - 60px) !important;
	}
    #mbl_hw_stats_pane .mbl-homework-wrap .select2 {
		font-size: 12px;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup {
		display: none;
		max-width: 100%;
		width: 500px;
		background: #fff;
		position: absolute;
		z-index: 9;
		margin-top: -28px;
		padding: 0 5px 15px 15px;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .date-title {
		color: #444;
		line-height: 28px;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .mbla-dates-buttons {
		text-align: right;
		padding: 15px 10px 0;
		line-height: 30px;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .mbla-dates-buttons a {
		width: 100px;
		display: inline-block;
		text-align: center;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .mbla-date-label {
		font-size: 16px;
		text-align: center;
		margin-bottom: 5px;
		font-style: italic;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-date {
		display: block;
	}
    #mbl_hw_stats_pane .ui-datepicker, div.ui-timepicker {
		background: #ffffff;
		border: 1px solid #a6a6a6;
		box-shadow: 0px 5px 10px #e7e7e7;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .ui-datepicker {
		box-shadow: none;
	}
    #mbl_hw_stats_pane .ui-datepicker-header {
		border-bottom: 1px solid #dadada;
	}
    #mbl_hw_stats_pane .mbla-stats-filter-row table.ui-datepicker-calendar {
		width: 100%;
		text-align: center;
	}

    #mbl_hw_stats_pane .mbla-stats-filter-row table.ui-datepicker-calendar td, #mbl_hw_stats_pane .mbla-stats-filter-row table.ui-datepicker-calendar th {
		padding: 0;
		border: none;
		text-align: center;
	}
    #mbl_hw_stats_pane .ui-datepicker-today > a {
		background: #30a957;
		color: #ffffff !important;
	}
    #mbl_hw_stats_pane .ui-datepicker-calendar a {
		text-decoration: none;
	}
    #mbl_hw_stats_pane .ui-datepicker .ui-state-default, .ui-timepicker .ui-state-default {
		padding: 5px;
		color: #000000;
	}
    #mbl_hw_stats_pane .ui-datepicker .ui-state-default:hover, .ui-datepicker-current-day > a, .ui-timepicker .ui-state-default:hover, .ui-datepicker .ui-state-active, .ui-timepicker .ui-state-active {
		background: #30a957;
		color: #ffffff !important;
		cursor: pointer;
	}
    #mbl_hw_stats_pane .ui-state-active {
		cursor: default;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .ui-datepicker-today > a {
		background: #aaaaaa;
		color: #ffffff !important;
	}
    #mbl_hw_stats_pane .mbla-dates-select .mbla-dates-popup .ui-datepicker .ui-state-default:hover, .mbla-dates-select .mbla-dates-popup .ui-datepicker-current-day > a, .mbla-dates-select .mbla-dates-popup .ui-timepicker .ui-state-default:hover, .mbla-dates-select .mbla-dates-popup .ui-datepicker .ui-state-active, .mbla-dates-select .mbla-dates-popup .ui-timepicker .ui-state-active {
		background: #30a957;
		color: #ffffff !important;
		cursor: pointer;
	}
    #mbl_hw_stats_pane .mbla-stats-filter-row table.ui-datepicker-calendar th {
		font-weight: bold;
	}
    #mbl_hw_stats_pane .mbla-select-wrap select {
		width: calc(100% - 60px);
	}
    #mbl_hw_stats_pane .wp-admin select {
		max-width: 99%;
	}
    #mbl_hw_stats_pane .select2-container--default .select2-selection--single .select2-selection__placeholder {
		color: #999;
	}
    #mbl_hw_stats_pane .mbla-select-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder {
		color: inherit;
	}
    #mbl_hw_stats_pane .mbl-homework-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder {
		color: inherit;
	}
    #mbl_hw_stats_pane .fa-sliders::before {
		content: "\f1de";
	}
    #mbl_hw_stats_pane .mbla-stats-filter-button .mbla-filters-icon {
		font-size: 24px;
		border: 1px solid;
		border-radius: 3px;
		display: inline-block;
		padding: 2px 3px;
		margin-right: 10px;
		position: relative;
		top: 3px;
	}
    #mbl_hw_stats_pane .fa-chevron-down::before {
		content: "\f078";
	}
    #mbl_hw_stats_pane .mbla-stats-filter-button .mbla-filters-closed {
		font-size: 20px;
		margin-left: 10px;
	}
    #mbl_hw_stats_pane .fa-chevron-up::before {
		content: "\f077";
	}
    #mbl_hw_stats_pane .mbla-stats-filter-button .mbla-filters-opened {
		margin-left: 10px;
		font-size: 20px;
		display: none;
	}
</style>
