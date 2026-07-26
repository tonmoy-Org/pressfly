<?php

use \App\Http\Controllers as BC;
use Illuminate\Support\Facades\Route;

Route::any('/adlinkfly/article-view', [BC\Api\AdLinkFlyController::class, 'articleView'])
    ->name('api.adlinkfly.article.view');
Route::post('/adlinkfly/article-out', [BC\Api\AdLinkFlyController::class, 'articleOut'])
    ->name('api.adlinkfly.article.out');
