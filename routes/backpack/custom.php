<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('page', 'PageCrudController');
    Route::crud('sticker', 'StickerCrudController');
    Route::crud('user', 'UserCrudController');
    Route::post('user/{id}/ban', 'UserCrudController@banUser')->name('user.ban');
    Route::post('user/{id}/give-packs', 'UserCrudController@givePacks')->name('user.give-packs');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
