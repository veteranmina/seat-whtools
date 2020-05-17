<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2017  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

// Namespace all of the routes for this package.

Route::group([
    'namespace' => 'veteranmina\Seat\ContractStock\Http\Controllers',
    'prefix' => 'contractstock',
    'middleware' => ['web', 'auth', 'locale']
], function () {
    // Your route definitions go here.
    Route::get('/', [
        'as' => 'view',
        'uses' => 'ContractStockController@getHome'
    ]);
    //Routes for Doctine stocking
    Route::get('/stocking', [
        'as' => 'contractstock.stocking',
        'uses' => 'ContractStockController@getStockingView',
        'middleware' => 'bouncer:contractstock.stockview'
    ]);

    Route::post('/saveStocking', [
        'as' => 'contractstock.saveStocking',
        'uses' => 'ContractStockController@saveStocking',
        'middleware' => 'bouncer:contractstock.stockedit'
    ]);
    Route::get('/delstockingbyid/{id}', [
        'uses' => 'ContractStockController@deleteStockingById',
        'middleware' => 'bouncer:contractstock.stockedit'
    ]);
    Route::get('/showContractIG/{id}/{token}', [
        'as' => 'whtools.contractstock',
        'uses' => 'ContractStockController@testEseye',
        'middleware' => 'bouncer:contractstock.stockedit'
    ]);

    Route::get('/config}', [
        'as' => 'contractstock.config',
        'uses' => 'ContractStockController@getConfigView',
        'middleware' => 'bouncer:contractstock.bluetaxview'
    ]);
    Route::post('/config/post', [
        'as' => 'contractstock.config.post',
        'uses' => 'ContractStockController@postConfig'
    ]);
    Route::get('/test/', [
        'as' => 'whtools.test',
        'uses' => 'SkillCheckerController@test',
        'middleware' => 'bouncer:whtools.certview'
    ]);
});
