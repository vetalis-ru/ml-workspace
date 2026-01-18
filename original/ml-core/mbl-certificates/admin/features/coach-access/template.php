<?php /**
 * @global WP_User $profile_user
 * @global int[] $userAccess
 */
?>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <label>Выдача сертификатов</label>
        </th>
        <td>
            <div class="mbl-settings-color mblc-settings-color">
                <div class="wpm-row">
                    <label>
                        <input type="checkbox" id="mblc_coach_certificate_access-all"
                               <?= $userAccess === 'all' ? 'checked' : '' ?>
                               name="_mblc_coach_certificate_access" value="all"> Все уровни доступа
                    </label>
                </div>
                <?php foreach ((new MBLC_WpmLevels())->query(['how_to_issue' => 'employee']) as $wpm_level): ?>
                    <div class="wpm-row">
                        <label>
                            <input type="checkbox" name="_mblc_coach_certificate_access[]"
                                <?php if (is_array($userAccess)) {
                                    echo in_array($wpm_level->term_id, $userAccess) ? 'checked' : '';
                                } elseif ($userAccess === 'all') {
                                    echo 'checked disabled';
                                } ?>
                                   value="<?= esc_attr($wpm_level->term_id) ?>">
                            <?= $wpm_level->name ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<script>
    $(function () {
        let items = $('[name="_mblc_coach_certificate_access[]"]');
        $('#mblc_coach_certificate_access-all').on('change', function () {
            let checked = this.checked;
            items.each(function (i, checkbox) {
                $(checkbox).prop('disabled', checked);
            });
        });
    });
</script>