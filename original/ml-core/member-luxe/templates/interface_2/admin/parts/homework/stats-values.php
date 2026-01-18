<?php /** @var $stats array */ ?>
<?php foreach ($stats as $stat => $statNb) : ?>
    <input type="hidden" class="wpm-hw-stats-input" data-type="<?php echo $stat; ?>" value="<?php echo $statNb; ?>">
<?php endforeach; ?>