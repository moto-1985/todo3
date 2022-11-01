<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BookmarkController;
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
})->name('top');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

// 教わったこと
// bookmarksとかtasks複数にする → 一覧で出るから複数 {$id}をつければそのうちの一個だとURLからわかる。
// resourceは無駄なルートが増える しかもrouteを見ただけだと何を使っているかわからない
// /task などprefixが一緒でgroupingする機能がある。
Route::middleware(['auth'])->group(function () {
    // RouteServiceProviderで決めた Homeページ
    Route::get('/mypage', [TaskController::class, 'mypage'])->name('mypage');
    Route::prefix('tasks')->name('tasks.')->group(function () {

        // ブックマークコントローラのCRUD
        Route::post('/{task}/bookmark', [BookmarkController::class, 'bookmark'])->name('bookmark');
        Route::get('/bookmarks', [BookmarkController::class, 'showbookmarks'])->name('showbookmarks');

        // タスクコントローラのCRUD
        Route::get('/showalltasks', [TaskController::class, 'showAllTasks'])->name('showAllTasks');
        Route::get('/new', [TaskController::class, 'create'])->name('create');
        Route::post('', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });
});