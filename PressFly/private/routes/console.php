<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// php artisan images:regenerate
Artisan::command('images:regenerate', function () {
    \App\Helpers\Image::regenerateImages();
})->purpose('Regenerate image different sizes');

// php artisan images:regenerate
Artisan::command('images:deleteRegeneratedSizes', function () {
    \App\Helpers\Image::deleteRegeneratedSizes();
})->purpose('Delete regenerated sizes');
