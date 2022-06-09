<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\LogController;
use App\Http\Controllers\BookmarksController;

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

$app_url = env('APP_URL');
if(!empty($app_url)){
    URL::forceRootUrl($app_url);
    $schema = explode(':', $app_url)[0];
    URL::forceScheme($schema);
}

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [SessionController::class, 'index'])->name('login');
Route::post('/login', [SessionController::class, 'login']);
Route::get('/logout', [SessionController::class, 'logout'])->name('logout');

Route::get('/register', [RegistrationController::class, 'index'])->name('register');
Route::post('/register', [RegistrationController::class, 'register']);

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::get('/user/disable/{id}', [SettingsController::class, 'disable'])->middleware('auth')->name('user.disable');
Route::get('/user/enable/{id}', [SettingsController::class, 'enable'])->middleware('auth')->name('user.enable');
Route::get('/user/promote/{id}', [SettingsController::class, 'promote'])->middleware('auth')->name('user.promote');
Route::get('/user/demote/{id}', [SettingsController::class, 'demote'])->middleware('auth')->name('user.demote');
Route::get('/user/give_permission/{id}', [SettingsController::class, 'give_permission'])->middleware('auth')->name('user.give_permission');
Route::get('/user/remove_permission/{id}', [SettingsController::class, 'remove_permission'])->middleware('auth')->name('user.remove_permission');

Route::get('/logs', [LogController::class, 'index'])->middleware('auth')->name('logs');

Route::get('/bookmarks', [BookmarksController::class, 'index'])->middleware('auth')->name('bookmarks');
Route::post('/bookmarks/create', [BookmarksController::class, 'create'])->middleware('auth')->name('bookmarks.create');
Route::get('/bookmarks/delete/{id}', [BookmarksController::class, 'delete'])->middleware('auth')->name('bookmarks.delete');
