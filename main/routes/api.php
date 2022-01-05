<?php

use App\Http\Controllers\AgendamientoController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaracterizacionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CupController;
use App\Http\Controllers\DataInit\PersonController as DataInitPersonController;
use App\Http\Controllers\DurationController;
use App\Http\Controllers\FormularioController;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PeopleTypeController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\SubTypeAppointmentController;
use App\Http\Controllers\TypeAppointmentController;
use App\Http\Controllers\WaitingListController;
use App\Http\Controllers\SubcategoryController;
use App\Models\Person;

use App\Models\RegimenType;
use App\Models\Level;
use App\Models\Municipality;
use App\Models\Department;

use App\Models\Appointment;
use App\Models\Agendamiento;
use App\Models\Contract;
use App\Models\Location;
use App\Models\TypeDocument;
use App\Models\Cup;
use Illuminate\Support\Facades\Http;
use App\Models\TypeAppointment;
use App\Models\Space;
use App\Models\Company;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// use App\Models\Person;
// use App\Models\CallIn;
// use App\Models\SpaceT;
// use App\Models\Usuario;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("auth")->group(
	function () {
		Route::post("login", "AuthController@login");
		Route::post("register", [AuthController::class, "register"]);
		Route::middleware("auth.jwt")->group(function () {
			Route::post("logout", [AuthController::class, "logout"]);
			Route::post("refresh", [AuthController::class, "refresh"]);
			Route::post("me", [AuthController::class, "me"]);
			Route::get("renew", [AuthController::class, "renew"]);
			Route::get("change-password", [
				AuthController::class,
				"changePassword",
			]);
		});
	}
);


Route::group(
	[
		"middleware" => ["api", "cors", 'auth.verify'],
	],

	function ($router) {
		Route::post('create-menu',  [MenuController::class, 'store']);
		Route::post('/save-menu',  [MenuController::class, 'storePermissions']);
		Route::post("formulario/save-responses", [FormularioController::class, "saveResponse"]);
		Route::post("agendamientos-cancel", [AgendamientoController::class, "cancel"]);
		Route::post("space-cancel", [SpaceController::class, "cancel"]);
		Route::post("cancel-appointment/{id}", "AppointmentController@cancel");
		Route::post("another-formality", "AnotherFormality@store");
		Route::post("presentianCall", "CallInController@presentialCall");
		Route::post("patientforwaitinglist", "CallInController@patientforwaitinglist");
		Route::post("imports", [CupController::class, "import"]);

		Route::post("cancell-agenda", [AgendamientoController::class, "cancellAgenda"]);
		Route::post("cancell-waiting-appointment", [WaitingListController::class, "cancellWaitingAppointment"]);

		Route::post("confirm-appointment", [AppointmentController::class, "confirmAppointment"]);

		Route::get('reporte',  [ReporteController::class, 'general']);
		Route::get('get-menu',  [MenuController::class, 'getByPerson']);
		Route::get("spaces-statistics", [SpaceController::class, "statistics"]);
		Route::get("waiting-list-statistics", [WaitingListController::class, "statistics"]);
		Route::get("spaces-statistics-detail", [SpaceController::class, "statisticsDetail"]);
		Route::get("get-type_appointments/{query?}", [TypeAppointmentController::class, "index"]);

		Route::get("get-durations", [DurationController::class, "index"]);
		Route::get("appointments-pending", [AppointmentController::class, "getPending"]);
		Route::get("get-statistics-by-collection", [AppointmentController::class, "getstatisticsByCollection"]);

		Route::get("get-type_subappointments/{query?}", [SubTypeAppointmentController::class, "index"]);
		Route::get("get-companys/{query?}", [CompanyController::class, "index"]);
		Route::get("get-companys-based-on-city/{company?}", [CompanyController::class, "getCompanyBaseOnCity"]);
		Route::get("get-sedes/{ips?}/{procedure?}", [LocationController::class, "index"]);
		Route::get("get-specialties/{sede?}/{procedure?}", [SpecialityController::class, "index",]);
		Route::get("get-professionals/{ips?}/{speciality?}", [PersonController::class, "index"]);
		Route::get("get-formulario/{formulario?}", [FormularioController::class, "getFormulario"]);
		Route::get("agendamientos/paginate", [AgendamientoController::class, "indexPaginate"]);
		Route::get("agendamientos/detail/{id}", [AgendamientoController::class, "showDetail"]);
		Route::get("people-type-custom", [PeopleTypeController::class, "indexCustom"]);
		Route::get("people-paginate", [PersonController::class, "indexPaginate"]);
		Route::get("get-patient-fill/{id}", "PatientController@getPatientResend");
		Route::get("type-service/formality/{id}", "TypeServiceController@allByFormality");
		Route::get("opened-spaces", "SpaceController@index");
		Route::get("opened-spaces/{especialidad?}/{profesional?}", "SpaceController@indexCustom");
		Route::get("get-patient", "PatientController@getPatientInCall");
		Route::get("clean-info/{id?}", [AppointmentController::class, "cleanInfo"]);
		Route::get("clean-info", [AppointmentController::class, "getDataCita"]);

		Route::get("validate-info-patient", [DataInitPersonController::class, "validatePatientByLineFront"]);


		Route::resource("agendamientos", "AgendamientoController");
		Route::resource("appointments", "AppointmentController");
		Route::resource("patients", "PatientController");
		Route::resource("calls", "CallController");
		Route::resource("cie10s", "Cie10Controller");
		Route::resource("cups", "CupController");
		Route::resource("person", "PersonController");
		Route::resource("company", "CompanyController");
		Route::resource("people-type", "PeopleTypeController");
		Route::resource("departments", "DepartmentController");
		Route::resource("contract", "ContractController");
		Route::resource("cities", "MunicipalityController");
		Route::resource("agreements", "AgreementController");
		Route::resource("type-documents", "TypeDocumentController");
		Route::resource("eps", "EpsController");
		Route::resource("type-regimens", "RegimenTypeController");
		Route::resource("levels", "LevelController");
		Route::resource("waiting-appointment", "WaitingListController");
		Route::resource("formality", "FormalityController");
		Route::resource("ambit", "AmbitController");
		Route::resource("type-locations", "TypeLocationController");
		Route::resource("menus", "MenuController");
		Route::resource("fees", "FeeController");


        //se ejecuta al crear
        Route::resource("subcategory", "SubcategoryController");
		Route::post("subcategory-variable/{id}", "SubcategoryController@deleteVariable");

        //se ejecuta al crear
        Route::get("subcategory-field/{id}", "SubcategoryController@getField");

        //se ejecuta al editar
        Route::get("subcategory-edit/{id?}/{idSubcategoria}", "SubcategoryController@getFieldEdit");
		Route::resource("subcategory", "SubcategoryController");
        Route::resource("category", "CategoryController");
        
        Route::resource("product", "ProductController");


        Route::resource("catalogo", "CatalogoController");



	}
);

Route::group(["middleware" => ["jwt.verify"]], function () {
	Route::get(
		"/caracterizacion/pacientesedadsexo",
		"CaracterizacionController@PacienteEdadSexo"
	);
	Route::get(
		"/caracterizacion/pacientespatologiasexo",
		"CaracterizacionController@PacientePatologiaSexo"
	);
	Route::get(
		"/pacientes/listapacientes",
		"PacienteController@ListaPacientes"
	);
});

Route::group(["middleware" => ["globho.verify"]], function () {
	Route::post('create-professional', [PersonController::class, 'storeFromGlobho']);
	Route::put('professional', [PersonController::class, 'updateFromGlobho']);
	Route::put('appointment/{code?}/{state?}', [AppointmentController::class, 'updateFromGlobho']);
	Route::post('create-appoinment', [AppointmentController::class, 'createFromGlobho']);
});
