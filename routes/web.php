<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\compaignController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/',[authController::class,"adsApiResponse"]);
Route::get('/ads',[authController::class,"generateAuthforuser"]);
Route::get('/add-compaign',[compaignController::class,"createCompaign"]);