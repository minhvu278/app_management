<?php

// Custom routes
// You can delete this route group if you don't need to add your custom routes.
Route::group(['namespace' => 'Theme\Demo\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        // Add your custom route here
        // Ex: Route::get('hello', 'DemoController@getHello');
        Route::post('/checkpass', 'DemoController@checkPass')
            ->name('ajax.checkpass');
        Route::post('/update-code', 'DemoController@updateCode')
            ->name('ajax.updatecode');
    });
});

Theme::routes();

Route::group(['namespace' => 'Theme\Demo\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {

        Route::get('/', 'DemoController@getIndex')
            ->name('public.index');

        Route::get('sitemap.xml', 'DemoController@getSiteMap')
            ->name('public.sitemap');

        Route::get('{slug?}' . config('core.base.general.public_single_ending_url'), 'DemoController@getView')
            ->name('public.single');

    });
});
