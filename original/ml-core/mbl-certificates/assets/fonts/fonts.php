<?php foreach (FontHandler::getFontFamilies() as $fontFamily): ?>
    <?php if (FontHandler::hasWeight($fontFamily, 'bold')): ?>
        @font-face {
            font-family: '<?= $fontFamily ?>';
            src: url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-bold.eot');
            src: url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-bold.eot?#iefix') format('embedded-opentype'),
            url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-bold.woff') format('woff'),
            url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-bold.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
    <?php endif; ?>
    <?php if (FontHandler::hasWeight($fontFamily, 'regular')): ?>
        @font-face {
            font-family: '<?= $fontFamily ?>';
            src: url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-regular.eot');
            src: url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-regular.eot?#iefix') format('embedded-opentype'),
            url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-regular.woff') format('woff'),
            url('<?= mblc_plugin_assets_uri() ?>/fonts/<?= $fontFamily ?>/<?= $fontFamily ?>-regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
    <?php endif; ?>
<?php endforeach; ?>

