<?php
/**
 * Plugin Name: ML Learning Monitor
 * Description: Dormant students monitoring for MemberLux
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ML_Learning_Monitor {

    public function __construct() {
        add_action( 'wpm-levels_edit_form_fields', [ $this, 'render_term_fields' ] );
        add_action( 'edited_wpm-levels', [ $this, 'save_term_fields' ] );

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function enqueue_assets() {
        wp_add_inline_style(
            'wp-admin',
            '
            .mlm-tabs { margin-top:20px; }
            .mlm-tabs .nav-tab { cursor:pointer; }
            .mlm-tab-content { display:none; margin-top:20px; }
            .mlm-tab-content.is-active { display:block; }
            .mlm-monitoring-block { margin-top:20px; padding:15px; border:1px solid #ccd0d4; background:#fff; }
            '
        );

        wp_add_inline_script(
            'jquery-core',
            '
            jQuery(function($){
                function toggleMonitoringBlock() {
                    if ($("#mlm_enable_monitoring").is(":checked")) {
                        $(".mlm-monitoring-block").show();
                    } else {
                        $(".mlm-monitoring-block").hide();
                    }
                }

                toggleMonitoringBlock();
                $("#mlm_enable_monitoring").on("change", toggleMonitoringBlock);

                $(".mlm-tabs .nav-tab").on("click", function(e){
                    e.preventDefault();
                    var target = $(this).data("target");

                    $(".mlm-tabs .nav-tab").removeClass("nav-tab-active");
                    $(this).addClass("nav-tab-active");

                    $(".mlm-tab-content").removeClass("is-active");
                    $("#" + target).addClass("is-active");
                });
            });
            '
        );
    }

    public function render_term_fields( $term ) {
        $options = get_option( "mlm_term_{$term->term_id}", [] );
        $enabled = ! empty( $options['enabled'] );
        ?>
        <tr class="form-field">
            <th scope="row">ML Learning Monitor</th>
            <td>
                <label>
                    <input type="checkbox"
                           id="mlm_enable_monitoring"
                           name="mlm[enabled]"
                           value="1" <?php checked( $enabled ); ?>>
                    Включить мониторинг для этого УД
                </label>

                <div class="mlm-monitoring-block">

                    <h3>Уведомления</h3>

                    <h2 class="nav-tab-wrapper mlm-tabs">
                        <a class="nav-tab nav-tab-active" data-target="mlm-tab-student-1">Письмо студенту #1</a>
                        <a class="nav-tab" data-target="mlm-tab-student-2">Письмо студенту #2</a>
                        <a class="nav-tab" data-target="mlm-tab-student-3">Письмо студенту #3</a>
                        <a class="nav-tab" data-target="mlm-tab-admin">Письмо администратору</a>
                    </h2>

                    <div id="mlm-tab-student-1" class="mlm-tab-content is-active">
                        <?php $this->render_student_email_fields( 1, $options ); ?>
                    </div>

                    <div id="mlm-tab-student-2" class="mlm-tab-content">
                        <?php $this->render_student_email_fields( 2, $options ); ?>
                    </div>

                    <div id="mlm-tab-student-3" class="mlm-tab-content">
                        <?php $this->render_student_email_fields( 3, $options ); ?>
                    </div>

                    <div id="mlm-tab-admin" class="mlm-tab-content">
                        <?php $this->render_admin_email_fields( $options ); ?>
                    </div>

                    <p><strong>Доступные шорткоды:</strong><br>
                        [user_email], [user_login], [course_name], [expired_date], [is_bundle_course], [bundle_name]
                    </p>

                </div>
            </td>
        </tr>
        <?php
    }

    private function render_student_email_fields( $index, $options ) {
        $data = $options["student_{$index}"] ?? [];
        ?>
        <p>
            <label>Через сколько дней после окончания УД?</label><br>
            <input type="number"
                   name="mlm[student_<?php echo $index; ?>][delay]"
                   value="<?php echo esc_attr( $data['delay'] ?? '' ); ?>">
        </p>

        <p>
            <label>Заголовок письма</label><br>
            <input type="text"
                   class="large-text"
                   name="mlm[student_<?php echo $index; ?>][subject]"
                   value="<?php echo esc_attr( $data['subject'] ?? '' ); ?>">
        </p>

        <p>
            <label>Тело письма</label><br>
            <textarea class="large-text" rows="6"
                      name="mlm[student_<?php echo $index; ?>][body]"><?php
                echo esc_textarea( $data['body'] ?? '' );
            ?></textarea>
        </p>
        <?php
    }

    private function render_admin_email_fields( $options ) {
        $data = $options['admin'] ?? [];
        ?>
        <p>
            <label>Через сколько дней после последнего письма студенту?</label><br>
            <input type="number"
                   name="mlm[admin][delay]"
                   value="<?php echo esc_attr( $data['delay'] ?? '' ); ?>">
        </p>

        <p>
            <label>Email администратора</label><br>
            <input type="email"
                   class="large-text"
                   name="mlm[admin][email]"
                   value="<?php echo esc_attr( $data['email'] ?? '' ); ?>">
        </p>

        <p>
            <label>Заголовок письма</label><br>
            <input type="text"
                   class="large-text"
                   name="mlm[admin][subject]"
                   value="<?php echo esc_attr( $data['subject'] ?? '' ); ?>">
        </p>

        <p>
            <label>Тело письма</label><br>
            <textarea class="large-text" rows="6"
                      name="mlm[admin][body]"><?php
                echo esc_textarea( $data['body'] ?? '' );
            ?></textarea>
        </p>
        <?php
    }

    public function save_term_fields( $term_id ) {
        if ( empty( $_POST['mlm'] ) || ! is_array( $_POST['mlm'] ) ) {
            delete_option( "mlm_term_{$term_id}" );
            return;
        }

        $data = wp_unslash( $_POST['mlm'] );
        update_option( "mlm_term_{$term_id}", $data );
    }
}

new ML_Learning_Monitor();
