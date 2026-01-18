<div class="mbl-material-countdown-holder">
    <div class="mbl-material-countdown-title">
        <?php _e('Урок откроется через', 'mbl'); ?>:
    </div>
    <div class="mbl-material-countdown-content">
        <i class="fa fa-hourglass-half"></i>
        <div class="mbl-material-countdown" data-mbl-release-date="<?php echo $releaseDate; ?>">
            <div class="mbl-material-countdown-sector">
                <div class="mbl-material-countdown-digit">{d10}{d1}</div>
                <div class="mbl-material-countdown-legend"><?php _e('дн.', 'mbl'); ?></div>
            </div>
            <div class="mbl-material-countdown-separator">:</div>
            <div class="mbl-material-countdown-sector">
                <div class="mbl-material-countdown-digit">{h10}{h1}</div>
                <div class="mbl-material-countdown-legend"><?php _e('час.', 'mbl'); ?></div>
            </div>
            <div class="mbl-material-countdown-separator">:</div>
            <div class="mbl-material-countdown-sector">
                <div class="mbl-material-countdown-digit">{m10}{m1}</div>
                <div class="mbl-material-countdown-legend"><?php _e('мин.', 'mbl'); ?></div>
            </div>
            <div class="mbl-material-countdown-separator">:</div>
            <div class="mbl-material-countdown-sector">
                <div class="mbl-material-countdown-digit">{s10}{s1}</div>
                <div class="mbl-material-countdown-legend"><?php _e('сек.', 'mbl'); ?></div>
            </div>
        </div>
    </div>
</div>