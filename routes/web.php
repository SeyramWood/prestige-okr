<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\InvitePeopleNotification;

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

Route::get('/config', function () {
  Artisan::call('config:clear');
  Artisan::call('cache:clear');
  Artisan::call('config:cache');
  Artisan::call('storage:link');
  // Artisan::call('route:cache');
  return 'Done';
});

Route::get('/notification', function () {
  return (new InvitePeopleNotification('/url'))
    ->toMail('wood@mail.com');
});


Route::get('/', 'AuthController@login')
  ->middleware('guest')
  ->name('login');
Route::get('/register', 'AuthController@register')
  ->middleware('guest')
  ->name('register');


Route::post('/create-admin', 'AdminController@store');

Route::get('/invite/{email}', 'AuthController@invite')->name('invite');
Route::post('/add-invite', 'AuthController@addInvite')->name('save.invite');
Route::post('/signout', 'AuthController@signout')->name('signout');
Route::post('/logout', 'AuthController@signOut')->name('logout')->middleware(['auth']);


//Access by both admin and users
Route::prefix('dashboard')->group(function () {
  Route::get('/', 'DashboardController@index')->name('dashboard');
  Route::get('/my-okr', 'DashboardController@myOkr')->name('myokr');
  Route::get('/teams/{team?}/{slug?}', 'DashboardController@teams')->name('teams');
  Route::get('/people', 'DashboardController@people')->name('people');
  Route::get('/companies', 'DashboardController@companies')->name('companies');
  Route::get('/settings', 'DashboardController@settings')->name('settings.index');
  Route::get('/settings/profile', 'DashboardController@profile')->name('settings.profile');
  Route::get('/settings/manage-team', 'DashboardController@manageTeam')->name('settings.team');

  //Objective routes
  Route::post('/add-new-objective', 'ObjectiveController@addObjective');
  Route::put('/save-check-in/{objectiveMetric}/{keyResultMetric}', 'ObjectiveController@saveCheckIn');
  Route::put('/update-objective/{id}', 'ObjectiveController@editObjective');
  Route::delete('/delete-objective/{objective}', 'ObjectiveController@deleteObjective');

  //Key Resoult routes
  Route::post('/add-new-key-result', 'KeyResultController@addKeyResult');
  Route::put('/update-key-result/{keyResult}', 'KeyResultController@editKeyResult');
  Route::delete('/delete-key-result/{keyResult}', 'KeyResultController@deleteKeyResult');

  //Profile routes
  Route::put('/update-profile', 'ProfileController@updateUserProfile');
  Route::put('/update-password', 'ProfileController@updateUserPassword');
  Route::post('/update-profile-avatar', 'ProfileController@updateUserAvatar');
  Route::put('/update-company', 'ProfileController@updateCompanyProfile');
  Route::post('/update-company-logo', 'ProfileController@updateCompanyLogo');

  //Team routes
  Route::post('/add-team', 'TeamController@store');
  Route::put('/update-team/{team}', 'TeamController@update')->middleware('can:update,team');
  Route::delete('/delete-team/{team}', 'TeamController@destroy');
  Route::delete('/delete-teams/{teams}', 'TeamController@destroyMany');
  Route::post('assign-team/{user}', 'TeamController@assignTeam');
  Route::post('assign-teams', 'TeamController@assignTeams');

  //Member routes

  Route::post('/send-invite', 'AdminController@sendInvite')->name('send.invite');
  Route::post('/notifications', 'AdminController@notifications')->name('notifications');
  Route::put('/update-user-role/{user}/{role}', 'AdminController@updateUserRole');
  Route::delete('/delete-user/{user}', 'AdminController@deleteUser');
  Route::delete('/delete-users/{users}', 'AdminController@deleteUsers');
});
