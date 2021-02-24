<?php

use Illuminate\Support\Facades\Route;
use Unitable\GrahamStripe\Http\Controllers;

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

Route::group(['as' => 'graham-stripe.', 'prefix' => 'graham-stripe/'], function() {

    Route::post('webhook', [Controllers\WebhookController::class, 'handleWebhook'])->name('webhook');

});
