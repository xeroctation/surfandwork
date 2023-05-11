<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PerformersController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Task\CreateController;
use App\Http\Controllers\Task\ResponseController;
use App\Http\Controllers\Task\SearchTaskController;
use App\Http\Controllers\Task\UpdateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

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

Route::get('/',[HomeController::class, 'home'])->name('home');
Route::get('/lang/{lang}', [HomeController::class, 'lang'])->name('lang');
Route::get('/categories/{id}', [HomeController::class, 'category'])->name("categories");



Route::get('/login', [LoginController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.loginPost')->middleware('guest');
Route::get('/register', [UserController::class, 'signup'])->name('user.signup')->middleware('guest');
Route::post('/register', [LoginController::class, 'customRegister'])->name('login.customRegister')->middleware('guest');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');
Route::get('account/verify/{user}/{hash}', [LoginController::class, 'verifyAccount'])->name('login.verifyAccount');
Route::get('account/verification/email', [LoginController::class, 'send_email_verification'])->name('login.send_email_verification')->middleware('auth');

Route::post('/request',[ReportController::class,'request'])->name('request');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/my-tasks', [HomeController::class, 'my_tasks'])->name('searchTask.mytasks');
});



Route::prefix("task")->group(function () {
    Route::prefix("create")->group(function () {
        Route::get('/', [CreateController::class, 'name'])->name('task.create.name');
        Route::post('/name', [CreateController::class, 'name_store'])->name('task.create.name.store');
        Route::get('/custom/{task}', [CreateController::class, 'custom_get'])->name('task.create.custom.get');
        Route::post('/custom/{task}/store', [CreateController::class, 'custom_store'])->name('task.create.custom.store');
        Route::get('/address/{task}', [CreateController::class, 'address'])->name('task.create.address');
        Route::post('/address/{task}/store', [CreateController::class, 'address_store'])->name('task.create.address.store');
        Route::get('/date/{task}', [CreateController::class, 'date'])->name('task.create.date');
        Route::post('/date/{task}/store', [CreateController::class, 'date_store'])->name('task.create.date.store');
        Route::get('/budget/{task}', [CreateController::class, 'budget'])->name('task.create.budget');
        Route::post('/budget/{task}/store', [CreateController::class, 'budget_store'])->name('task.create.budget.store');
        Route::get('/note/{task}', [CreateController::class, 'note'])->name('task.create.note');
        Route::post('/note/{task}/store', [CreateController::class, 'note_store'])->name('task.create.note.store');
        Route::post('/note/{task}/images/store', [CreateController::class, 'images_store'])->name('task.create.images.store');
        Route::get('/contact/{task}', [CreateController::class, 'contact'])->name('task.create.contact');
        Route::post('/contact/{task}/store', [CreateController::class, 'contact_store'])->name('task.create.contact.store.phone')->middleware('auth');
        Route::post('/contact/{task}/store/register', [CreateController::class, 'contact_register'])->name('task.create.contact.store.register')->middleware('guest');
        Route::post('/contact/{task}/store/login/', [CreateController::class, 'contact_login'])->name('task.create.contact.store.login')->middleware('guest');
        Route::get('/verify/{task}/{user}', [CreateController::class, 'verify'])->name('task.create.verify');
        Route::post('/verify/{user}', [UserController::class, 'verifyProfile'])->name('task.create.verification');
        Route::get('/remote/{task}', [CreateController::class, 'remote_get'])->name('task.create.remote');
        Route::post('/remote/{task}', [CreateController::class, 'remote_store'])->name('task.create.remote.store');
        Route::post("/detailed-task/{task}/response", [ResponseController::class, 'store'])->name('task.response.store');

    });
});
Route::post('select-performer/{response}', [ResponseController::class, 'selectPerformer'])->name('response.selectPerformer');
Route::post('send-review-user/{task}', [UpdateController::class, 'sendReview'])->name('update.sendReview');
Route::post('tasks/{task}/not-complete', [UpdateController::class, 'not_completed'])->name('update.not_completed');


Route::group(['prefix' => 'performers'], function () {
    Route::get('/', [PerformersController::class, 'service'])->name('performers.service');
    Route::get('/{user}', [PerformersController::class, 'performer'])->name('performers.performer');

});

Route::get('/completed-task-names', [SearchTaskController::class, 'taskNames'])->name('search.task_name');
Route::get('task-search', [SearchTaskController::class, 'search_new'])->name('searchTask.task_search');
Route::post('tasks-search', [SearchTaskController::class, 'search_new2'])->name('searchTask.ajax_tasks');
Route::get('task/{task}/map', [SearchTaskController::class, 'task_map'])->name('task.map');

Route::get('/detailed-tasks/{task}', [SearchTaskController::class, 'task'])->name("searchTask.task");
Route::post('/detailed-tasks', [SearchTaskController::class, 'compliance_save'])->name("searchTask.comlianse_save");


Route::post('/request',[ReportController::class,'request'])->name('request');
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::group(['middleware' => 'auth'], static function () {
        Route::get('report', [ReportController::class, "index"])->name("index");
        Route::get('report/get', [ReportController::class, "report"])->name("report");
        Route::get('report/get/child', [ReportController::class, "report_sub"])->name("report_sub");
        Route::get('report/{id}', [ReportController::class, "index_sub"])->name("index_sub");
    });
});
