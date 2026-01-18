<?php add_filter('the_content', 'wpautop'); //добавил фильтр потому что пропадали теги p ?>
<?php $footer = apply_filters('the_content', do_shortcode(wpm_get_option('footer.content'))) ?>
<?php if (wpm_option_is('footer.visible', 'on')) : ?>
    <footer class="footer-row">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <div class="footer-content wpm-content-text">
                        <?php echo $footer; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        // Собираем все активные соглашения для footer
        $footer_agreements = array();
        
        if (wpm_option_is('user_agreement.enabled_footer', 'on')) {
            $footer_agreements[] = '<a href="#wpm_user_agreement_text" data-toggle="modal" data-target="#wpm_user_agreement_text">' . 
                                  wpm_get_option('user_agreement.footer_link_title', __('Пользовательское соглашение', 'mbl')) . '</a>';
        }
        
        if (wpm_option_is('user_agreement_2.enabled_footer', 'on')) {
            $footer_agreements[] = '<a href="#wpm_user_agreement_2_text" data-toggle="modal" data-target="#wpm_user_agreement_2_text">' . 
                                  wpm_get_option('user_agreement_2.footer_link_title', __('Соглашение №2', 'mbl')) . '</a>';
        }
        
        if (wpm_option_is('user_agreement_3.enabled_footer', 'on')) {
            $footer_agreements[] = '<a href="#wpm_user_agreement_3_text" data-toggle="modal" data-target="#wpm_user_agreement_3_text">' . 
                                  wpm_get_option('user_agreement_3.footer_link_title', __('Соглашение №3', 'mbl')) . '</a>';
        }
        
        if (wpm_option_is('user_agreement_4.enabled_footer', 'on')) {
            $footer_agreements[] = '<a href="#wpm_user_agreement_4_text" data-toggle="modal" data-target="#wpm_user_agreement_4_text">' . 
                                  wpm_get_option('user_agreement_4.footer_link_title', __('Соглашение №4', 'mbl')) . '</a>';
        }
        
        // Выводим все соглашения через разделитель
        if (!empty($footer_agreements)) : ?>
            <div class="footer-user-agreement">
                <?php echo implode(' | ', $footer_agreements); ?>
            </div>
        <?php endif; ?>
    </footer>
<?php endif; ?>
