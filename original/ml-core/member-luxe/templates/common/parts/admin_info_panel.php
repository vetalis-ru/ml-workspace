<div class="wrap wpm-admin-info-panel">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php _e('Информационная панель', 'mbl_admin') ?></h2>
    <div class="panel with-nav-tabs panel-default">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#notifications"><?php _e('Уведомления', 'mbl_admin') ?></a></li>
                    <?php if (!empty($offer)) : ?>
                        <li><a href="#offer"><?php _e('Специальное предложение', 'mbl_admin') ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        <div class="panel-body">
            <div class="tab-content clearfix">
                <div class="tab-pane active" id="notifications">
                    <div class="page-content-wrap">
                        <?php if (empty($notifications)) : ?>
                            <div class="empty-results">
                                <?php _e('Нет уведомлений', 'mbl_admin') ?>
                            </div>
                        <?php else : ?>
                            <?php foreach ($notifications as $notification) : ?>
                                <div class="wpm-notification <?php echo wpm_notification_is_read($notification['id']) ? 'read' : 'unread'; ?>">
                                    <?php echo wpautop($notification['content']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($offer)) : ?>
                    <?php $gmt_offset = get_option('gmt_offset'); ?>
                    <?php $now = time() + ($gmt_offset * HOUR_IN_SECONDS); ?>
                    <div class="tab-pane" id="offer">
                        <div class="page-content-wrap">
                            <?php if (isset($offer['end_date']) && strtotime($offer['end_date']) > $now) : ?>
                                <div id="countdown-layout-wrap" class="countdown-wrap c-skin-1 c-color-8 c-size-small">
                                    <div class="countdown-inner">
                                        <img class="timer-background-image" src="/wp-content/plugins/member-luxe/css/images/bg.png">
                                        <div class="wpm-timer countdown" id="wpm-offer-main-timer">
                                            <div class="digits-wrap">
                                              <span class="digit image-{d10}"></span>
                                              <span class="digit image-{d1}"></span>
                                              <span class="image-sep"></span>
                                              <span class="digit image-{h10}"></span>
                                              <span class="digit image-{h1}"></span>
                                              <span class="image-sep"></span>
                                              <span class="digit image-{m10}"></span>
                                              <span class="digit image-{m1}"></span>
                                              <span class="image-sep"></span>
                                              <span class="digit image-{s10}"></span>
                                              <span class="digit image-{s1}"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    jQuery(function ($) {
                                        $("#wpm-offer-main-timer").countdown({
                                            until: $.countdown.UTCDate('<?php echo $gmt_offset; ?>', new Date(<?php echo date("Y, m-1, d, H, i, s", strtotime($offer['end_date'])); ?>)),
                                            compact: true,
                                            layout: $("#wpm-offer-main-timer").html()
                                        });
                                    });
                                </script>
                            <?php endif; ?>
                            <div class="offer-content">
                                <?php echo wpautop($offer['content']); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	jQuery(function ($) {
        $(document).on('click', '.nav-tabs li', function() {
            switchTab($(this).find('>a'));

            return false;
        });

        function switchTab($link) {
            if ($link.length) {
                var $li = $link.closest('li'),
                    $tabs = $li.closest('.nav-tabs'),
                    $selector = $link.attr('href'),
                    $pane = $($selector),
                    $tabContent = $pane.closest('.tab-content');

                $tabs.find('li').removeClass('active');
                $tabContent.find('.tab-pane').removeClass('active');
                window.location.hash = $link.attr('href');

                $li.addClass('active');
                $pane.addClass('active');
            }

        }

        if (window.location.hash !== '') {
            switchTab($('a[href="' + window.location.hash + '"]'));
        }

        if ("onhashchange" in window) {
            window.onhashchange = function () {
                switchTab($('a[href="' + window.location.hash + '"]'));
            }
        }
    });
</script>