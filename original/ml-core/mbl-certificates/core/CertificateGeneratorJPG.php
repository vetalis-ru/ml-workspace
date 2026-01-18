<?php
class CertificateGeneratorJPG implements CertificateGenerator
{
    public ?CertificateTemplate $template = null;
    public $data = null;

    public function __construct(CertificateTemplate $template, $data = null)
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function render($filename = 'certificate.jpg', $type = CertificateGenerator::VIEW)
    {
        if ( ob_get_length() > 0 ) {
            ob_clean();
        }
        ob_start();

        $orientation = $this->template->orientation;
        $fontPaths = FontHandler::getFontsMap();

        $imagePath = $this->template->getImgPath();
        $image = new Imagick();
        try {
            $image->readImage($imagePath);
            $image->setImageCompressionQuality(100);

            if ($orientation === 'horizontal' && ($image->getImageWidth() !== 3508 || $image->getImageHeight() !== 2480)) {
                $image->resizeImage(3508, 2480, Imagick::FILTER_LANCZOS, 1);
            }

            if ($orientation === 'vertical' && ($image->getImageHeight() !== 3508 || $image->getImageWidth() !== 2480)) {
                $image->resizeImage(2480, 3508, Imagick::FILTER_LANCZOS, 1);
            }

            $fields = $this->template->getFieldsWithReplacement($this->data);

            $scale = 3.125;
            foreach ($fields as $field) {
                if ($field->hide) {
                    continue;
                }

                $draw = new ImagickDraw();
                $draw->setFont($fontPaths[$field->font_family][$field->font_weight === 'bold' ? 'B' : 'R']);
                $draw->setFontSize($field->font_size * $scale);
                $draw->setFillColor($field->color);

                $textX = $field->position->left * $scale;
                $textY = ($field->position->top + $field->font_size) * $scale;

                $textMetrics = $image->queryFontMetrics($draw, $field->example_text);
                $textWidth = $textMetrics['textWidth'];

                if ($field->text_align === 'center') {
                    $textX += ($field->size->width * $scale - $textWidth) / 2;
                } elseif ($field->text_align === 'right') {
                    $textX += $field->size->width * $scale - $textWidth;
                }

                $image->annotateImage($draw, $textX, $textY, 0, $field->example_text);
            }

            $data = $image->getImageBlob();
            status_header(200);
            if ($type === CertificateGenerator::VIEW) {
                header('Content-Length: '.strlen($data));
                header('Content-Type: image/jpeg');
                echo $data;
            } elseif ($type === CertificateGenerator::DOWNLOAD) {
                header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');
                header("Content-Length: ".strlen($data));
                header("Content-Type: image/jpg");
                echo $data;
            }
        } catch (Exception $exception) {
            echo $exception->getMessage();
        } finally {
            $image->clear();
            $image->destroy();
        }
        die;
    }
}
