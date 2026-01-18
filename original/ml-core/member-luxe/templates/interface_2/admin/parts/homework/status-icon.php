<?php /** @var string $status */ ?>
<?php if ($status == 'archive') : ?>
    <i class="fa fa-trash archived wpm-hw-status-icon"></i>
<?php elseif ($status == 'unarchive') : ?>
    <i class="fa fa-undo unarchive wpm-hw-status-icon"></i>
<?php elseif ($status == 'opened') : ?>
    <i class="fa fa-clock-o opened wpm-hw-status-icon"></i>
<?php elseif (in_array($status, ['approved', 'accepted'])) : ?>
    <i class="fa fa-check-circle-o approved wpm-hw-status-icon"></i>
<?php elseif ($status == 'rejected') : ?>
    <i class="fa fa-times-circle-o rejected wpm-hw-status-icon"></i>
<?php elseif ($status == 'delete') : ?>
    <i class="fa fa-times-circle-o rejected wpm-hw-status-icon"></i>
<?php endif; ?>