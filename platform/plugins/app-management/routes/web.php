<?php

Route::group(['namespace' => 'Botble\AppManagement\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'app-managements', 'as' => 'app-management.'], function () {
            Route::resource('', 'AppManagementController')->parameters(['' => 'app-management']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AppManagementController@deletes',
                'permission' => 'app-management.destroy',
            ]);
        });
        Route::group(['prefix' => 'apps', 'as' => 'app.'], function () {
            Route::resource('', 'AppController')->parameters(['' => 'app']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AppController@deletes',
                'permission' => 'app.destroy',
            ]);
        });
        Route::group(['prefix' => 'app-versions', 'as' => 'app-version.'], function () {
            Route::resource('', 'AppVersionController')->parameters(['' => 'app-version']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AppVersionController@deletes',
                'permission' => 'app-version.destroy',
            ]);
        });
    });

});
