<?php

use App\Http\Controllers\RegisterController;
use App\Mail\Invitation;
use App\Models\Invite;
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

Route::get('/', function () {
    return view('welcome');
});

Route::post('invites', function () {
    Mail::to(request()->email)->send(new Invitation());
    Invite::create(['email' => request()->email]);
});

Route::post('register', RegisterController::class)->name('register');
