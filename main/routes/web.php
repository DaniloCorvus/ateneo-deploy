<?php

use App\Events\AppointmentModify;
use App\Events\ModifiedAppointment;
use App\Http\Controllers\CupController;
use App\Http\Controllers\DataInit\EspecialidadController;
use App\Http\Controllers\DataInit\PersonController;
use App\Http\Controllers\DataInit\AgreementController;
use App\Http\Controllers\DataInit\AppointmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TypeAppointmentController;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Models\Appointment;
use App\Models\Functionary;
use App\Models\Globho;
use App\Models\Space;
use App\Models\Person;
use App\Models\CallIn;
use App\Models\SpaceT;
use App\Models\Usuario;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Mail\NotifyMail;
use App\Traits\manipulateDataFromExternalService;
use App\Models\Company;

use App\Models\Agendamiento;
use App\Http\Resources\AgendmientoResource;
use App\Services\SpaceService;
use App\HistoryAppointment;
use App\HistoryAgendamiento;
use App\Models\TypeAppointment;


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



Route::get('/test', 'TestController@test');

Route::get('/get-info', 'TestController@getAppointmentByPatient');

//Log::info('test');

Route::get('/clear-cache', function () {
    
    
     $people = DB::table('people')->get();
  
          foreach($people as $person ){
              
            if (!file_exists('../DOCUMENTOS/' . $person->id)) {
				mkdir('../DOCUMENTOS/' . $person->id, 0777, true);
                echo $person->id;
                echo '<br>';
                echo $person->first_name;
			}

            
            
          }
  

  
  
  dd('finalizado');
  
  

  $exitCode = Artisan::call('config:clear');

  $exitCode = Artisan::call('cache:clear');

  $exitCode = Artisan::call('config:cache');

  return 'DONE'; //Return anything

});

Auth::routes();

Route::get('insert-contracts', function ($id) {});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('detalle-paciente/{identificacion}',  [PacienteController::class, 'DetallePacienteOld']);
// ->middleware(EnsureTokenIsValid::class);
Route::get('detalle-paciente/{identificacion}/{tipo_documento}',  [PersonController::class, 'customUpdateOld']);
// ->middleware(EnsureTokenIsValid::class);
Route::post('save-call', [PacienteController::class, 'store'])->middleware(EnsureTokenIsValid::class);
Route::get('/get-tipos-agendamiento', [TypeAppointmentController::class, 'get']);
Route::get('/create-tipos-agendamiento', [TypeAppointmentController::class, 'store']);
Route::get('/get-appoinments', [AppointmentController::class, 'get']);
Route::get('/create-appoinments', [AppointmentController::class, 'store']);
// Route::get('/get-regimes', [RegimenController::class, 'get']);
// Route::get('/create-regimes', [RegimenController::class, 'store']);
// Route::get('/get-ips', [IpsController::class, 'get']);
// Route::get('/create-ips', [IpsController::class, 'store']);
// Route::get('/get-especialidades', [EspecialidadController::class, 'get']);
// Route::get('/create-especialidades', [EspecialidadController::class, 'store']);
Route::get('/get-persons', [PersonController::class, 'get']);
Route::get('/create-persons', [PersonController::class, 'store']);
// specialties
Route::get('/create-especialidades', [EspecialidadController::class, 'store']);
// cups
Route::get('/create-cups', 'CupController@storeFromMedical');
// agreements
Route::get('/create-cups', 'CupController@storeFromMedical');
Route::get('/create-agreements',  [AgreementController::class, 'store']);

Route::view('{any}', 'home')->where('any', '.*');
