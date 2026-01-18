<?php


function wpm_view_autotraining_page()
{
    if(!empty($_GET['cat_id'])) {
        if(wpm_is_interface_2_0()) {
            $map = new AutoTrainingAdmin(intval($_GET['cat_id']));
            $map->render();
        } else {
            wpm_autotraining_map($_GET['cat_id']);
        }
    } else {
        wpm_autotrainings_list();
    }
}

function wpm_autotraining_map ($cat_id)
{
    global $wpdb;

    wpm_enqueue_style('view_autotraining', plugins_url('../css/view-autotraining.css', __FILE__));

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";

    $autotraining = $wpdb->get_row("SELECT a.*, b.count, b.parent
                                    FROM " . $terms_table . " AS a
                                    JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                    WHERE b.taxonomy='wpm-category' AND a.term_id=" . $cat_id . ";", OBJECT);


    if (count($autotraining)) {
        $schedule     = wpm_autotraining_schedule_option($cat_id);
        $cnt_schedule = count($schedule);
    }
    ?>

    <div class="wrap nosubsub">
        <?php if(count($autotraining)):?>
            <h2><?php _e('Обзор автотренинга', 'mbl_admin'); ?> «<?php echo $autotraining->name;?>»</h2>

            <br class="clear">

            <?php if($cnt_schedule):?>
                <div id="col-container">
                    <div class="col-wrap">

                        <table class="wp-list-table widefat fixed tags">
                            <tbody id="the-list" class="ui-sortable">
                            <tr>
                                <td>
                                    <div class="view-autotraining-list">
                                        <?php $cnt = 1;?>
                                        <?php foreach($schedule as $post_id => $data):?>
                                            <?php $post = get_post($post_id);?>

                                            <div class="autotraining-material">
                                                <div class="inner-wrapper">
                                                    <div class="title"><?php echo $post->post_title;?></div>
                                                    <div class="total-shift">
                                                        <?php if($data['shift']>0):?>
                                                            <b><?php _e('Общее смещение:', 'mbl_admin'); ?> <?php echo wpm_get_time_text($data['shift']/3600);?></b>
                                                            <ul>
                                                                <?php $shift = ($data['shift'] - $data['transmitted_shift'])/3600;?>
                                                                <?php $shift = $shift > 0 ? '+' . wpm_get_time_text($shift) : __('Отсутствует', 'mbl_admin');?>
                                                                <li><?php _e('Собственное смещение', 'mbl_admin'); ?> <b><?php echo $shift;?></b></li>
                                                                <?php if($data['transmitted_shift'] > 0 && $data['is_postponed_due_to_homework']):?>
                                                                    <li><?php _e('Домашнее задание с автоподтверждением и со смещением в одном из предыдущих материалов', 'mbl_admin'); ?> <b>+<?php echo wpm_get_time_text($data['transmitted_shift']/3600);?></b></li>
                                                                <?php endif;?>
                                                            </ul>
                                                        <?php else:?>
                                                            <b><?php _e('Нет смещения.', 'mbl_admin'); ?></b>
                                                        <?php endif;?>
                                                    </div>
                                                    <div class="homework">
                                                        <b><?php _e('Домашнее задание', 'mbl_admin'); ?>: <?php echo $data['is_homework'] ? __('Есть', 'mbl_admin') :  __('Отсутствует', 'mbl_admin');?></b>
                                                        <?php if($data['is_homework']):?>
                                                            <ul>
                                                                <li>
                                                                    <?php echo wpm_get_homework_title($data['homework_info']);?>
                                                                </li>
                                                            </ul>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <?php if($cnt != $cnt_schedule):?>
                                                    <div class="timeline-arrow"></div>
                                                <?php endif;?>
                                            </div>
                                            <?php $cnt++;?>
                                        <?php endforeach;?>

                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            <?php else:?>
                <p><?php _e('График публикции материалов еще не составлен.', 'mbl_admin'); ?></p>
            <?php endif;?>

        <?php else:?>
            <p><?php _e('Произошла ошибка: Автотренинг не найден', 'mbl_admin'); ?></p>
        <?php endif;?>

    </div>

    <?php
}

function wpm_autotrainings_list ()
{
    global $wpdb;

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";

    $autotrainings = $wpdb->get_results("SELECT a.*, b.count, b.parent
                                         FROM " . $terms_table . " AS a
                                         JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                         WHERE b.taxonomy='wpm-category';", OBJECT);

    $categories = array();

    if (count($autotrainings)) {
        foreach ($autotrainings as $autotraining) {
            if (wpm_is_autotraining($autotraining->term_id)) {
                $categories[] = array(
                    'edit_url'  => admin_url('/edit-tags.php?action=edit&taxonomy=wpm-category&tag_ID=' . $autotraining->term_id . '&post_type=wpm-page'),
                    'map_url'   => admin_url('/edit.php?post_type=wpm-page&page=wpm-view-autotraining&cat_id=' . $autotraining->term_id),
                    'cat_url'   => site_url('?wpm-category=' . $autotraining->slug),
                    'posts_url' => admin_url('/edit.php?wpm-category=' . $autotraining->slug . '&post_type=wpm-page'),
                    'count'     => $autotraining->count,
                    'parent'    => $autotraining->parent,
                    'name'      => $autotraining->name,
                    'slug'      => $autotraining->slug,
                    'id'        => $autotraining->term_id,
                );
            }
        }
    }
    $nb_categories = count($categories);
    ?>

    <div class="wrap nosubsub">
        <h2><?php _e('Автотренинги', 'mbl_admin'); ?></h2>

        <div id="ajax-response"></div>

        <form class="search-form" method="get" action="">
            <input type="hidden" value="wpm-view-autotraining" name="taxonomy">
            <input type="hidden" value="wpm-page" name="post_type">
            <p class="search-box">
                <label class="screen-reader-text" for="tag-search-input"><?php _e('Найти автотренинг', 'mbl_admin'); ?>:</label>
                <input id="tag-search-input" type="search" value="" name="s">
                <input id="search-submit" class="button" type="submit" value="<?php _e('Найти автотренинг', 'mbl_admin'); ?>" name="">
            </p>
        </form>

        <br class="clear">

        <div id="col-container">
            <div class="col-wrap">
                <form id="posts-filter" method="post" action="">

                    <div class="tablenav top">
                        <div class="tablenav-pages one-page">
                            <span class="displaying-num"><?php echo $nb_categories;?> <?php _e('элемента', 'mbl_admin'); ?></span>
                        </div>
                        <br class="clear">
                    </div>

                    <?php if ($nb_categories > 0):?>
                        <table class="wp-list-table widefat fixed tags">
                            <thead>
                            <tr>
                                <th class="manage-column column-name sortable"><a><span><?php _e('Название', 'mbl_admin'); ?></span></a></th>
                                <th class="manage-column column-description sortable"><a><span><?php _e('Описание', 'mbl_admin'); ?></span></a></th>
                                <th class="manage-column column-slug sortable"><a><span><?php _e('Ярлык', 'mbl_admin'); ?></span></a></th>
                                <th class="manage-column column-posts num sortable"><a><span>МemberLux</span></a></th>
                            </tr>
                            </thead>

                            <tfoot>
                            <tr>
                                <th class="manage-column column-name sortable"><a><span><?php _e('Название', 'mbl_admin'); ?></span></a></th>
                                <th class="manage-column column-description sortable"><a><span><?php _e('Описание', 'mbl_admin'); ?></span></a></th>
                                <th class="manage-column column-slug sortable"><a><span><?php _e('Ярлык', 'mbl_admin'); ?></span></a></th>
                                <th class="manage-column column-posts num sortable"><a><span>МemberLux</span></a></th>
                            </tr>
                            </tfoot>

                            <tbody id="the-list" class="ui-sortable">

                            <?php $is_alternate = true;?>
                            <?php foreach($categories as $category):?>
                                <tr class="<?php echo $is_alternate ? 'alternate' : '';?>">
                                    <td class="name column-name">
                                        <strong>
                                            <a class="row-title" title="<?php _e('Обзор автотренинга', 'mbl_admin'); ?> «<?php echo $category['name'];?>»" href="<?php echo $category['map_url'];?>"><?php echo $category['name'];?></a>
                                        </strong>
                                        <br>
                                        <div class="row-actions">
                                                <span class="map">
                                                    <a href="<?php echo $category['map_url']?>"><?php _e('Обзор автотренинга', 'mbl_admin'); ?></a>|
                                                </span>
                                                <span class="edit">
                                                    <a href="<?php echo $category['edit_url']?>"><?php _e('Изменить', 'mbl_admin'); ?></a>|
                                                </span>
                                                <span class="view">
                                                    <a href="<?php echo $category['cat_url'];?>"><?php _e('Перейти', 'mbl_admin'); ?></a>
                                                </span>
                                        </div>
                                        <div id="inline_2" class="hidden">
                                            <div class="name"><?php echo $category['name'];?></div>
                                            <div class="slug"><?php echo $category['slug'];?></div>
                                            <div class="parent"><?php echo $category['count'];?></div>
                                        </div>
                                    </td>
                                    <td class="description column-description"></td>
                                    <td class="slug column-slug"><?php echo urldecode($category['slug']);?></td>
                                    <td class="posts column-posts">
                                        <a href="<?php echo $category['posts_url']?>"><?php echo $category['count'];?></a>
                                    </td>
                                </tr>
                                <?php $is_alternate = !$is_alternate;?>
                            <?php endforeach;?>

                            </tbody>
                        </table>

                        <div class="tablenav bottom">
                            <div class="tablenav-pages one-page">
                                <span class="displaying-num"><?php echo $nb_categories;?> <?php _e('элемента', 'mbl_admin'); ?></span>
                            </div>
                            <br class="clear">
                        </div>
                    <?php else:?>
                        <p><?php _e('Нет автотренингов', 'mbl_admin'); ?></p>
                    <?php endif;?>

                </form>
            </div>
        </div>
    </div>

<?php
}
