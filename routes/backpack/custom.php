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
    // Override default dashboard routes to redirect to users
    Route::get('dashboard', 'AdminController@dashboard')->name('backpack.dashboard');
    Route::get('/', 'AdminController@redirect')->name('backpack');

    Route::crud('page', 'PageCrudController');
    Route::get('sticker/import', 'StickerCrudController@importForm')->name('sticker.import');
    Route::post('sticker/import', 'StickerCrudController@import')->name('sticker.import.store');
    Route::get('sticker/import/template', 'StickerCrudController@importTemplate')->name('sticker.import.template');
    Route::get('sticker/shiny-manager', 'StickerCrudController@shinyManager')->name('sticker.shiny-manager');
    Route::post('sticker/bulk-rarity', 'StickerCrudController@bulkSetRarity')->name('sticker.bulk-rarity');
    Route::post('sticker/{sticker}/toggle-shiny', 'StickerCrudController@toggleShiny')->name('sticker.toggle-shiny');
    Route::crud('sticker', 'StickerCrudController');
    Route::crud('user', 'UserCrudController');
    Route::post('user/{id}/ban', 'UserCrudController@banUser')->name('user.ban');
    Route::post('user/{id}/give-packs', 'UserCrudController@givePacks')->name('user.give-packs');
    Route::crud('setting', 'SettingCrudController');

    // Sticker Mapper Tool
    Route::get('sticker-mapper', 'StickerMapperController@index')->name('sticker-mapper.index');
    Route::put('sticker-mapper/{sticker}/position', 'StickerMapperController@updatePosition')->name('sticker-mapper.update-position');
    Route::put('sticker-mapper/{sticker}/dimensions', 'StickerMapperController@updateDimensions')->name('sticker-mapper.update-dimensions');
    Route::post('sticker-mapper/{sticker}/toggle-horizontal', 'StickerMapperController@toggleHorizontal')->name('sticker-mapper.toggle-horizontal');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
