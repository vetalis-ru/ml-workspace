<?php
/**
 * @global array $data
 * @global array $fields
 * @global int $certificate_template_id
 */

?>

<div id="mbc-root" class="<?= $data['orientation'] === 'vertical' ? 'mbc-vertical' : 'mbc-horizontal'?> container-fluid m-0 mt-3">
    <div class="mbc-layout">
        <div class="mbc-panel">
            <h2>Редактирование шаблона</h2>
            <div style="height: 100%;">
                <div class="mbc-sticky">
                    <div class="alert alert-danger" role="alert" style="display: none">
                    </div>
                    <form id="templateOptions" class="mbc-form needs-validation" novalidate>
                        <div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="certificateName">Название шаблона:</label>
                                        <input id="certificateName" class="form-control" type="text" name="name"
                                               value="<?= $data['title']; ?>" required minlength="3">
                                        <div class="invalid-feedback">
                                            Имя должно содержать не менее 3 символов и быть уникальным.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary" data-target="media-open" type="button">
                                    Сменить картинку
                                </button>
                                <p class="media-open">
                                    Рекомендуемый размер:
                                    2480 x 3508 пикселей
                                </p>
                            </div>
                            <div class="mt-3">
                                <div class="h5">Ориентация</div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           <?php checked($data['orientation'], 'vertical') ?>
                                           value="vertical"
                                           name="orientation" id="verticalOrientation">
                                    <label class="form-check-label" for="verticalOrientation">
                                        Вертикальная
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                        <?php checked($data['orientation'], 'horizontal') ?>
                                           value="horizontal"
                                           name="orientation" id="horizontalOrientation">
                                    <label class="form-check-label" for="horizontalOrientation">
                                        Горизонтальная
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <?php foreach ($fields as $key => $field) {
                                getFieldControl($field);
                            } ?>
                            <input type="hidden" name="action" value="ml_update_certificate_template">
                            <input type="hidden" name="attachment_id" value="<?= $data['attachment_id']; ?>">
                            <input type="hidden" name="certificate_id" value="<?= $certificate_template_id; ?>">
                            <div class="text-right">
                                <input class="mt-3 btn btn-primary" type="submit" value="Сохранить">
                            </div>
                        </div>
                    </form>
                    <div>
                        <a class="link" target="_blank" href="<?= $data['download'] ?>">
                            Скачать пример в pdf</a>
                    </div>
                    <?php if ( mblc_jpg_enabled() ): ?>
                        <div class="mt-3">
                            <a class="link" target="_blank" href="<?= $data['download'] ?>&type=jpg">
                                Скачать пример в jpg</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="mbc-canvas d-flex justify-content-center">
            <div class="parent-wrap">
                <div class="parent a">
                    <img src="<?= $data['image']; ?>" alt="" id="certificateImage"
                         class="mbc-image"
                    >
                    <?php foreach ($fields as $key => $field) {
                        getFieldView($field);
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
