<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[MapController::class,'addMap'])->name('add.map');
Route::get('map-location',[MapController::class,'mapLocation'])->name('map.location');
Route::get('map-route-view/{id}',[MapController::class,'mapRouteView'])->name('map.view');
Route::post('map-data-store',[MapController::class,'store'])->name('map.store');
Route::post('get-route-map-info',[MapController::class,'get_route_map_info'])->name('get_route_map_info');
