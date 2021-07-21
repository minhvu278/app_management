<?php

Route::group(['namespace' => 'Botble\Filescode\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'filescodes', 'as' => 'filescode.'], function () {
            Route::resource('', 'FilescodeController')->parameters(['' => 'filescode']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'FilescodeController@deletes',
                'permission' => 'filescode.destroy',
            ]);
        });
    });

});
