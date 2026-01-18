<div id="mbl_login_backdrop"></div>
<header class="header-row">
    <section class="top-nav-row">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 not-relative top-nav-row--inner">
                    <button type="button" name="button" class="mobile-menu-button visible-md visible-xs visible-sm">
                        <span class="line line-1"></span>
                        <span class="line line-2"></span>
                        <span class="line line-3"></span>
                    </button>
                    <?php if (current_user_can('manage_options')) : ?>
                        <a class="nav-item hidden-md hidden-xs hidden-sm" href="<?php echo admin_url('edit.php?post_type=wpm-page'); ?>" title="<?php _e('Панель управления', 'mbl'); ?>"><span class="icon-sliders"></span></a>
                    <?php endif; ?>
                    <?php if (is_single() && $editPostUrl = get_edit_post_link()) : ?>
                        <a class="nav-item hidden-md hidden-xs hidden-sm" href="<?php echo esc_url($editPostUrl); ?>" title="<?php _e('Редактировать материал', 'mbl'); ?>"><span class="icon-pencil"></span></a>
                    <?php endif; ?>
                    <?php echo apply_filters('mbl-header-main-link', wpm_render_partial('header-main-link', 'base', array(), true)); ?>
                    <?php if (!wpm_option_is('hide_schedule', 'on') && wpm_get_option('schedule_id') && !(wpm_option_is('schedule_id', 'no'))) : ?>
                        <a class="nav-item hidden-md hidden-xs hidden-sm"
                           href="<?php echo get_permalink(wpm_get_option('schedule_id')); ?>">
                            <span class="iconmoon icon-calendar"></span>
                            <?php _e('Расписание', 'mbl'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!wpm_option_is('main.hide_ask', 'hide') && (is_user_logged_in() || !wpm_option_is('main.hide_ask_for_not_registered', 'on'))) : ?>
                        <div class="dropdown user-registration-button nav-item navbar-left hidden-md hidden-xs hidden-sm" data-dropdown-backdrop>
                            <a id="ask-dropdown" class="dropdown-button dropdown-toggle">
                                <span class="iconmoon icon-question-circle"></span><?php _e('Задать вопрос', 'mbl'); ?>
                            </a>
                            <div class="mbl-dropdown-menu dropdown-panel" aria-labelledby="ask-dropdown">
                                <?php wpm_render_partial('ask-form', 'base', array('full' => true)) ?>
                            </div>
                        </div>
                    <?php endif; ?>
	                <?php do_action('mbl-navbar-before-search') ?>
                    <div class="search-hint-form">
                        <?php if (wpm_option_is('main.search_visible', 'on', 'on')) : ?>
                            <a class="nav-item search-toggle-button" title="<?php _e('Найти', 'mbl'); ?>"><span class="iconmoon icon-search"></span></a>
                            <form class="form" action="<?php echo wpm_search_link(); ?>">
                                <?php if (get_option('permalink_structure') == '') : ?>
                                    <input type="hidden" name="wpm-page" value="search">
                                <?php endif; ?>
                                <input class="search-hint-input" id="search-input" type="text" name="s" autocomplete="off" placeholder="<?php _e('Поиск...', 'mbl'); ?>">
                                <button type="submit" class="search-input-icon icon-search"></button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <?php do_action('mbl_after_search_hint_form'); ?>
                    <script>
                      (function ($) {
                        let parent = $('.top-nav-row--inner');
                        let toggle = $('.shop-menu-dropdown');
                        let dropdown = $('.shop-menu-dropdown .dropdown-menu');
                        toggle.on('show.bs.dropdown', function (event) {
                          setPos()
                          $( window ).on( "resize", setPos );
                        });
                        toggle.on('hide.bs.dropdown', function (event) {
                          $( window ).off( "resize", setPos );
                        });
                        function setPos () {
                          let left = Math.floor(toggle.offset().left - parent.offset().left - 15);
                          let width = $( window ).width() < 767 ? parent.width() : '';
                          dropdown.css('width', `${width}px`)
                          dropdown.css('left', $( window ).width() < 767 ? `-${left}px` : '')
                        }
                      })(jQuery);
                    </script>
                    <?php if (!is_user_logged_in()) : ?>
                        <div class="dropdown user-login-button nav-item navbar-right hidden-md hidden-xs hidden-sm" data-dropdown-backdrop>
                            <a id="login-dropdown" class="dropdown-button dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
                                <span class="iconmoon icon-sign-in"></span><?php _e('Войти', 'mbl'); ?>
                            </a>
                            <div class="dropdown-menu dropdown-panel" aria-labelledby="login-dropdown" style="z-index: 999">
                                <?php wpm_render_partial('login-form', 'base', array('full' => true)); ?>
                            </div>
                        </div>
                        <?php if (!wpm_is_users_overflow()) : ?>
                            <div class="dropdown user-registration-button nav-item navbar-right hidden-md hidden-xs hidden-sm main-holder" data-dropdown-backdrop>
                                <a id="registration-dropdown" class="dropdown-button dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
                                    <span class="iconmoon icon-user"></span><?php _e('Регистрация', 'mbl'); ?>
                                </a>
                                <div class="dropdown-menu dropdown-panel" aria-labelledby="registration-dropdown" style="z-index: 999">
                                    <?php wpm_render_partial('register-form', 'base', array('full' => true)); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="dropdown user-profile-button nav-item navbar-right" data-dropdown-backdrop>
                            <a id="user-profile-dropdown" title="<?= esc_attr(wpm_get_current_user('display_name')); ?>" class="dropdown-button user-profile-dropdown-button dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
                                <span>
                                    <span class="user-profile-humbnail"><?php echo wpm_get_avatar_tag(get_current_user_id(), 32); ?></span><?php /* <span class="user-name hidden-xs hidden-sm hidden-md"><?php echo wpm_get_current_user('display_name'); ?></span>
                                 */ ?><span class="caret"></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="user-profile-dropdown">
                                <?php if(wpm_get_option( 'mbl_access.enable_profile_menu_link', 'off' ) === 'off'): ?>
                                    <li><a href="<?php echo admin_url('/user-edit.php?user_id=' . get_current_user_id()); ?>"><span class="iconmoon icon-cog"></span><?php _e('Профиль', 'mbl'); ?></a></li>
                                <?php endif; ?>
	                            <?php if(wpm_get_option( 'mbl_access.enable_activation_menu_link', 'off' ) === 'off'): ?>
                                    <li><a href="<?php echo wpm_activation_link(); ?>"><span class="iconmoon icon-key"></span><?php _e('Активация', 'mbl'); ?></a></li>
                                <?php endif; ?>
                                <?php /*
                                    <li><a href="./billing.php"><span class="iconmoon icon-dollar"></span><?php _e('Биллинг', 'mbl'); ?></a></li>
                                */ ?>
                                <?php do_action('mbl_user_profile_dropdown_before_logout'); ?>
                                <li><a href="<?php echo wp_logout_url(wpm_get_start_url()); ?>"><span class="iconmoon icon-sign-out"></span><?php _e('Выход', 'mbl'); ?></a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</header>
