<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'ad_id' => '',
    ],
    $widget
);
?>

<?= applyShortCodes('[ads id="' . $widget['ad_id'] . '"]') ?>
