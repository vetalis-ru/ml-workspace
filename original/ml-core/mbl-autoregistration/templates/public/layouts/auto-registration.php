<?php wpm_render_partial('head', 'base') ?>
<div class="site-content">
    <?php wpm_render_partial('header-cover', 'base', array('alias' => 'login')); ?>

    <section class="main-row clearfix">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="login-tabs bordered-tabs tabs-count-2">
                        <!-- Nav tabs -->
                          <ul class="nav nav-tabs text-center" role="tablist">
                            <li role="presentation"
                                class="tab-1">
                                <a href="#wpm-login"
                                   aria-controls="login-tab"
                                   role="tab"
                                   data-toggle="tab">
                                    <span class="icon-sign-in iconmoon"></span>
                                    <?php _e('Вход', 'mbl'); ?>
                                </a>
                            </li>
                                <li role="presentation"
                                    class="tab-2 active">
                                    <a href="#wpm-register"
                                       aria-controls="registration-tab"
                                       role="tab"
                                       data-toggle="tab">
                                        <span class="iconmoon icon-user"></span>
                                        <?php _e('Регистрация', 'mbl'); ?>
                                    </a>
                                </li>
                          </ul>
                          <!-- Tab panes -->
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="wpm-login">
                                <div class="login-form">
                                    <?php wpm_render_partial('login-form', 'base', array('full' => true, 'standalone' => true)); ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="wpm-register">
                                <div class="registration-form">
                                    <?php do_action('mblr_auto_register_form'); ?>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
<?php wpm_render_partial('footer') ?>
<?php wpm_render_partial('footer-scripts') ?>