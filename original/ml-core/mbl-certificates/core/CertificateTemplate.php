<?php


class CertificateTemplate
{
    public $id = 0;
    public $tableName = '';
    public $name = '';
    public $content = '';
    public string $orientation;
    public $fields = [];
    public $img_src = '';
    const TABLE_NAME = 'memberlux_certificate_templates';

    public function __construct( int $id, string $name, stdClass $content, $orientation )
    {
        global $wpdb;
        $this->tableName = $wpdb->prefix . self::TABLE_NAME;
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->orientation = $orientation;
    }

    public static function getCertificateGeneratorByCertificate( Certificate $certificate, $type = 'pdf' ): CertificateGenerator
    {
        $template = CertificateTemplate::getTemplate( $certificate->certificate_template_id );
        $data = [
                'name' => getFIO( $certificate->user_id ),
                'date' => $certificate->date_issue,
                'series' => $certificate->series,
                'number' => $certificate->number,
                'course' => $certificate->course_name,
            ] + $certificate->getAdditionFields();

        if ( $type === 'jpg' ) {
            $generator = new CertificateGeneratorJPG( $template, $data );
        } else {
            $generator = new CertificateGeneratorPDF( $template, $data );
        }

        return $generator;
    }

    /**
     * @return stdClass|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        if ( !empty( $this->fields ) ) {
            return $this->fields;
        }

        $allFiledCodes = [];
        foreach ( (array)$this->getContent()->fields as $code => $field ) {
            $allFiledCodes[] = $code;
            $this->fields[] = new Field( $field->name, $code, (array)$field );
        }

        $allFields = mblc_certificate_fields();
        foreach ( $allFields as $field ) {
            if ( in_array( $field->code, $allFiledCodes ) ) {
                continue;
            }

            $this->fields[] = $field;
        }

        return $this->fields;
    }

    /**
     * @return string
     */
    public function getImgSrc(): string
    {
        if ( !empty( $this->img_src ) ) {
            return $this->img_src;
        }
        $this->img_src = wp_get_attachment_image_url( $this->getContent()->attachment_id, 'full' );
        if ( $this->img_src && is_ssl() ) {
            $this->img_src = str_replace( 'http://', 'https://', $this->img_src );
        }
        return $this->img_src ?: $this->defaultImg();
    }

    public static function getTemplate( int $id ): CertificateTemplate
    {
        global $wpdb;
        $tableName = $wpdb->prefix . self::TABLE_NAME;
        $data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `$tableName` WHERE certificate_template_id = %d", $id ) );

        return new CertificateTemplate( $id, $data->name, json_decode( $data->content ), $data->orientation );
    }


    public static function getCertificateTemplates(): array
    {
        global $wpdb;
        $ids = $wpdb->get_col( "SELECT certificate_template_id FROM {$wpdb->prefix}" . self::TABLE_NAME );
        return array_map( function ( $template_id ) {
            return CertificateTemplate::getTemplate( $template_id );
        }, $ids );
    }

    public static function getDownloadLink( int $certificateTemplateId ): string
    {
        return admin_url() . 'admin.php?page=mblc_certificate_templates&download=' . $certificateTemplateId;
    }

    public static function getEditLink( int $certificateTemplateId ): string
    {
        return admin_url() . 'admin.php?page=mblc_certificate_templates&certificate_id=' . $certificateTemplateId;
    }

    public static function createTemplate( string $name, int $attachment_id, array $fields, string $orientation ): int
    {
        global $wpdb;
        $sql = "INSERT INTO `$wpdb->prefix" . self::TABLE_NAME . "`
                (`name`, `content`, `orientation`)
                VALUES (%s, %s, %s)
            ";
        $content = json_encode( [
            'attachment_id' => $attachment_id,
            'fields' => $fields
        ] );
        $data = [ $name, $content, $orientation ];
        $wpdb->query( $wpdb->prepare( $sql, $data ) );
        return $wpdb->insert_id;
    }

    public static function updateTemplate( int $id, string $name, int $attachment_id, array $fields, string $orientation )
    {
        global $wpdb;
        $sql = "
            UPDATE `$wpdb->prefix" . self::TABLE_NAME . "`
            SET `name` = %s, `content` = %s, `orientation` = %s
            WHERE certificate_template_id = %d
        ";
        $content = json_encode( [
            'attachment_id' => $attachment_id,
            'fields' => $fields
        ] );
        $data = [ $name, $content, $orientation, $id ];
        $wpdb->query( $wpdb->prepare( $sql, $data ) );
    }

    public static function saveTemplates( string $action )
    {
        $certificate_id = isset( $_POST['certificate_id'] ) ? intval( $_POST['certificate_id'] ) : 0;
        $template_name = trim( $_POST['name'] );
        $attachment_id = intval( $_POST['attachment_id'] );
        $orientation = $_POST['orientation'];
        $fields = [];
        /*
        if (empty($attachment_id)) {
            echo json_encode(['error' => 'Не выбрана картинка']);
            die();
        }
        */
        if ( !self::isUniqueName( $template_name, $certificate_id ) ) {
            echo json_encode( [ 'error' => 'Шаблон с таким названием уже существует', 'error_input' => '#certificateName' ] );
            die();
        }
        foreach ( $_POST['fields'] as $name => $field ) {
            $fieldForSave = $field;
            if ( !isset( $field['hide'] ) ) {
                $fieldForSave['hide'] = 0;
            }
            $fields[$name] = $fieldForSave;
        }

        switch ( $action ) {
            case('create') :
                $certificate_id = CertificateTemplate::createTemplate(
                    $template_name,
                    $attachment_id,
                    $fields,
                    $orientation
                );
                echo json_encode( [
                    'redirectUrl' => CertificateTemplate::getTemplateLink( $certificate_id )
                ] );
                die();
            case('save'):
                if ( empty( $certificate_id ) ) {
                    echo json_encode( [ 'error' => 'Ошибка в передачи id' ] );
                    die();
                }
                CertificateTemplate::updateTemplate(
                    $certificate_id,
                    $template_name,
                    $attachment_id,
                    $fields,
                    $orientation
                );
                echo json_encode( [ 'success' => 'Настройки обновлены успешно!' ] );
                die();
            default:
                echo json_encode( [ 'error' => 'Не указанный параметр action. Обратитесь к разработчику плагина' ] );
                die();
        }
    }

    public static function isUniqueName( string $name, int $certificate_id ): bool
    {
        global $wpdb;
        $sql = "
            SELECT `certificate_template_id` FROM `{$wpdb->prefix}" . self::TABLE_NAME . "`
            WHERE `name` = %s AND `certificate_template_id` != %d
        ";
        $res = $wpdb->get_var( $wpdb->prepare( $sql, [ $name, $certificate_id ] ) );
        return empty( $res );
    }

    public static function getTemplateLink( int $certificate_template_id ): string
    {
        return admin_url() . 'admin.php?page=mblc_certificate_templates&certificate_id=' . $certificate_template_id;
    }

    public static function getNameById( int $id ): ?string
    {
        global $wpdb;
        return $wpdb->get_var( "SELECT name FROM {$wpdb->prefix}" . self::TABLE_NAME . " 
        WHERE certificate_template_id = $id" );
    }

    public static function delete( int $id )
    {
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->prefix}" . self::TABLE_NAME . " WHERE certificate_template_id = $id" );
    }

    public static function isAccessToDelete( int $id ): bool
    {
        $certificates = Certificate::query( [
            'filter' => [ 'certificate_template_id' => $id ]
        ] );
        $wpm_levels = (new MBLC_WpmLevels())->query( [ 'template_id' => $id ] );
        return empty( count( $wpm_levels ) ) && empty( $certificates['total'] );
    }

    public function defaultImg(): string
    {
        return $this->orientation === 'vertical'
            ? mblc_plugin_assets_uri( 'css/images/sert.jpeg' )
            : mblc_plugin_assets_uri( 'css/images/cert-horizontal.jpg' );
    }

    public function defaultImgPath(): string
    {
        return $this->orientation === 'vertical'
            ? mblc_plugin_path( 'assets/css/images/sert.jpeg' )
            : mblc_plugin_path( 'assets/css/images/cert-horizontal.jpg' );
    }

    public function getImgPath(): string
    {
        if ( !empty( $this->img_src ) ) {
            return $this->img_src;
        }
        $this->img_src = wp_get_original_image_path( $this->getContent()->attachment_id );

        return $this->img_src ?: $this->defaultImgPath();
    }

    public function getFieldsWithReplacement( $data = null ): array
    {
        $fields = $this->getFields();

        if ( !is_null( $data ) ) {
            foreach ( $fields as $field ) {
                switch ( $field->code ) {
                    case 'name':
                        $field->example_text = $data['name'];
                        break;
                    case 'date':
                        $field->example_text = date( 'd.m.Y', strtotime( $data['date'] ) );
                        break;
                    case 'series':
                        $field->example_text = $data['series'];
                        break;
                    case 'number':
                        $field->example_text = $data['number'];
                        break;
                    case 'course':
                        $field->example_text = $data['course'];
                        break;
                    default:
                        if ( preg_match( '/^field\d+$/', $field->code ) === 1 ) {
                            $field->example_text = $data[$field->code] ?? '';
                        }
                }
            }
        }

        return $fields;
    }
}
