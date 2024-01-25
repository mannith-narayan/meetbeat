<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingViewController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//add a route that takes the user to the meetings view
Route::get('/meetings', [MeetingController :: class, 'index'])
    -> middleware(['auth', 'verified'])->name('home');

Route::get('/meetings/create', [MeetingController :: class, 'create'])
    -> middleware(['auth', 'verified'])->name('meetings.create');

Route::post('/meetings', [MeetingController :: class, 'store'])
    -> middleware(['auth', 'verified'])->name('meetings.store');

Route::get('/meetings/{meeting}', [MeetingController :: class, 'show'])
    -> middleware(['auth', 'verified'])->name('meetings.show');

Route::delete('/meetings/{meeting}', [MeetingController :: class, 'destroy'])
    -> middleware(['auth', 'verified'])->name('meetings.destroy');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
