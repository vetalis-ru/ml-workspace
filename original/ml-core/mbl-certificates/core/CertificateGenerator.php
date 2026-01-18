<?php

interface CertificateGenerator
{
    const VIEW = 'view';
    const DOWNLOAD = 'download';
    public function render($filename, $type);
}
