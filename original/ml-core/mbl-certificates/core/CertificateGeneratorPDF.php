<?php

use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Output\Destination;

require_once mblc_plugin_path('libs/vendor/autoload.php');

class CertificateGeneratorPDF implements CertificateGenerator
{
    public $template = null;
    public $data = null;

    public function __construct(CertificateTemplate $template, $data = null)
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function render($filename = 'certificate.pdf', $type = self::VIEW)
    {
        $fields = $this->template->getFieldsWithReplacement($this->data);
        $image_src = $this->template->getImgPath();
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        if(ob_get_length() > 0) {
            ob_clean();
        }

        try {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
//              'format' => $this->template->orientation === 'vertical' ? [297, 210] : [210, 297],
                'fontDir' => array_merge($fontDirs, FontHandler::getDirs()),
                'dpi' => 96,
                'fontdata' => $fontData + FontHandler::getFonts(),
                'default_font' => FontHandler::getDefault(),
                'orientation' => $this->template->orientation === 'vertical' ? 'P' : 'L'
            ]);
            $mpdf->img_dpi = 300;

            if(getenv( 'MBL_DEV_MODE' ) == 1) {
                $mpdf->showImageErrors = true;
            }

            $stylesheet = file_get_contents(mblc_plugin_path('assets/css/fonts.css'));
            $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
            $mpdf->BeginLayer(1);
            $mpdf->WriteHTML($this->getImage($image_src, $this->template->orientation), HTMLParserMode::HTML_BODY);
            $mpdf->EndLayer();
            $mpdf->BeginLayer(2);
            foreach ($fields as $field) {
                if ($field->hide){
                    continue;
                }
                $mpdf->WriteHTML($this->getField((array)$field));
            }

            status_header(200);
            if ($type === CertificateGenerator::VIEW) {
                $mpdf->Output($filename, Destination::INLINE);
            } elseif ($type === CertificateGenerator::DOWNLOAD) {
                $mpdf->Output($filename, Destination::DOWNLOAD);
            }
            die();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getImage(string $filename, $orientation): string
    {
        ob_start();
        if ($orientation === 'vertical') {
            $style = "width: 210mm; height: 297mm;";
        } else {
            $style = "width: 297mm; height: 210mm;";
        }
        ?>
        <div style="position: absolute; z-index: -1; left:0; right: 0; top: 0; bottom: 0;">
            <img src="<?= $filename; ?>" style="<?= $style ?> margin: 0;" alt=""/>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getField(array $props): string
    {
        $values = [];
        foreach ($props as $key => $value) {
            if ($key === 'position') {
                $values[] = "top:{$value->top}px;";
                $values[] = "left:{$value->left}px;";
                continue;
            } elseif ($key === 'size') {
                $values[] = "width:{$value->width}px;";
                continue;
            } elseif ($key === 'text' || $key === 'title' || $key === 'example_text') {
                continue;
            }
            $px = is_numeric($value) ? 'px' : '';
            $values[] = !empty($value)
                ? preg_replace("|_|", "-", $key) . ": {$value}$px;"
                : '';
        }

        ob_start();
        ?>
        <div style="
                display: inline;
                position: absolute;
                height: auto;
        <?php foreach ($values as $value): ?>
            <?= $value ?>
        <?php endforeach; ?>
                ">
            <?= $props['example_text'] ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
