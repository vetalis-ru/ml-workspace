<?php /** @var MBLPage $mblPage */ ?>
<div class="content-wrap">
    <div class="lesson-files-list">
        <?php foreach ($mblPage->getAttachments() as $attachment) : ?>
            <?php if (isset($attachment->thumbnailUrl) || $attachment->icon_class == 'file-video') : ?>
                <a href="<?php echo wpm_remove_protocol($attachment->url); ?>"
                   title="<?php echo $attachment->name; ?>"
                   data-caption="<?php echo $attachment->name; ?>"
                   data-fancybox="wpm_homework_file_material"
                   class="fancybox homework-attachment mbl-hw-attachment"
                >
                    <i class="fa fa-<?php echo $attachment->icon_class; ?>-o" aria-hidden="true"></i>
                    <span class="extension"><?php echo $attachment->extension; ?></span>
                    <span class="filename"><?php echo $attachment->clean_name; ?></span>
                </a>
            <?php elseif ($attachment->icon_class == 'file-audio') : ?>
                <?php $id = 'wpm_audio' . md5($attachment->name) . rand(1, 1000); ?>
                <a data-height="100px"
                   href="#<?php echo $id; ?>"
                   title="<?php echo $attachment->name; ?>"
                   data-caption="<?php echo $attachment->name; ?>"
                   data-fancybox="wpm_homework_file_material"
                   class="fancybox homework-attachment mbl-hw-attachment"
                >
                    <i class="fa fa-<?php echo $attachment->icon_class; ?>-o" aria-hidden="true"></i>
                    <span class="extension"><?php echo $attachment->extension; ?></span>
                    <span class="filename"><?php echo $attachment->clean_name; ?></span>
                    <audio controls id="<?php echo $id; ?>" style="display:none;" src="<?php echo wpm_remove_protocol($attachment->url); ?>"></audio>
                </a>
            <?php elseif (strtolower($attachment->extension) == 'pdf') : ?>
                <a href="<?php echo wpm_remove_protocol($attachment->url); ?>"
                   title="<?php echo $attachment->name; ?>"
                   class="homework-attachment mbl-hw-attachment"
                   target="_blank"
                >
                    <i class="fa fa-<?php echo $attachment->icon_class; ?>-o" aria-hidden="true"></i>
                    <span class="extension">pdf</span><span class="filename"><?php echo $attachment->clean_name; ?></span>
                </a>
            <?php else : ?>
                <a href="<?php echo wpm_remove_protocol($attachment->url); ?>"
                   title="<?php echo $attachment->name; ?>"
                   class="homework-attachment mbl-hw-attachment"
                   download
                   target="_blank"
                >
                    <i class="fa fa-<?php echo $attachment->icon_class; ?>-o" aria-hidden="true"></i>
                    <span class="extension"><?php echo $attachment->extension; ?></span>
                    <span class="filename"><?php echo $attachment->clean_name; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>