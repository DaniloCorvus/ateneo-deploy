<?php

use App\Http\Controllers\AccountPlanController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AgendamientoController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ArlController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaracterizacionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CupController;
use App\Http\Controllers\DataInit\PersonController as DataInitPersonController;
use App\Http\Controllers\DependencyController;

use App\Http\Controllers\DisabilityLeaveController;
use DisabilityLeaveController as CoreDisabilityLeaveController;

use DocumentTypesController as CoreDocumentTypesController;
use App\Http\Controllers\DocumentTypesController;
use App\Http\Controllers\DotationController;
use App\Http\Controllers\DrivingLicenseController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\DurationController;
use App\Http\Controllers\EgressTypesController;
use App\Http\Controllers\ExtraHoursController;
use App\Http\Controllers\FixedAssetTypeController;
use FixedAssetTypeController as CoreFixedAssetTypeController;


use EgressTypesController as CoreEgressTypesController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FormularioController;
use IngressTypesController as CoreIngressTypesController;
use App\Http\Controllers\IngressTypesController;
use App\Http\Controllers\InventaryDotationController;
use App\Http\Controllers\LateArrivalController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LunchControlller;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollFactorController;
use App\Http\Controllers\PensionFundController;
use App\Http\Controllers\PeopleTypeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductDotationTypeController;
use ProfessionController as CoreProfessionController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReporteHorariosController;
use App\Http\Controllers\RetentionTypeController;
use App\Http\Controllers\RiskTypesController;
use App\Http\Controllers\RotatingTurnHourController;
use App\Http\Controllers\RrhhActivityTypeController;
use RiskTypesController as CoreRiskTypesController;
use App\Http\Controllers\SalaryTypesController;
use SalaryTypesController as CoreSalaryTypesController;
use App\Http\Controllers\ServiceGlobhoController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\SubTypeAppointmentController;
use App\Http\Controllers\ThirdPartyController;
use App\Http\Controllers\TravelExpenseController;
use App\Http\Controllers\TypeAppointmentController;
use App\Http\Controllers\VisaTypeController;
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

use App\Http\Controllers\WorkContractController as CoreWorkContractController;
use App\Http\Controllers\WorkContractTypeController;
use App\Http\Controllers\ZonesController;
use App\Http\Controllers\BonificationsController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EpsController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\SeveranceFundController;
use App\Http\Controllers\TaxiControlller;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BanksController;
use App\Http\Controllers\WorkContractController;
use App\Http\Controllers\BankAccountsController;
use App\Http\Controllers\ClinicalHistoryController;
use App\Http\Controllers\CountableIncomeController;
use App\Http\Controllers\ElectronicPayrollController;
use App\Http\Controllers\PayrollConfigController;
use App\Http\Controllers\PayrollPaymentController;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\PayrollParametersController;
use App\Http\Controllers\RCuentaMedicaController;

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

//Dan Radication Routes
Route::get('get-company', [CompanyController::class, 'getCompanyByIdentifier']);
Route::post('save-radicacion', [RCuentaMedicaController::class, 'store']);
Route::get('get-companies', [CompanyController::class, 'getCompanies']);
//End Radicatioon routes


Route::post('/asistencia/validar', [AsistenciaController::class, 'validar']);
Route::get('/', function () {
	dd(Hash::make(12345));
});

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


		/**
		 * Rutas de integracion
		 */

		Route::post("presentianCall", "CallInController@presentialCall");
		Route::post("get-call-by-identifier", "CallInController@getCallByIdentifier");
		Route::resource("r-cuentas", 'RCuentaMedicaController');


		Route::get('paginateContractType', [WorkContractTypeController::class, 'paginate']);
		Route::resource('work-contract-type', 'WorkContractTypeController');

		Route::get('periodoP', [CoreWorkContractController::class, 'getTrialPeriod']);
		Route::get('contractsToExpire', [CoreWorkContractController::class, 'contractsToExpire']);
		Route::get('filter-all-depencencies', [DependencyController::class, 'dependencies']);
		Route::get('filter-all-positions', [PositionController::class, 'positions']);
		Route::get('preLiquidado', [CoreWorkContractController::class, 'getPreliquidated']);
		Route::get('/payroll-factor-people',  [PayrollFactorController::class, 'indexByPeople']);

		Route::get('electronic-payroll/{id}',  [ElectronicPayrollController::class, 'getElectronicPayroll']);
		Route::get('electronic-payroll-paginate/{id}',  [ElectronicPayrollController::class, 'paginate']);
		Route::get('electronic-payroll-statistics/{id}',  [ElectronicPayrollController::class, 'statistics']);
		Route::delete('electronic-payroll/{id}', [ElectronicPayrollController::class, 'deleteElectroincPayroll']);

		/*CONFIG NOMINA*/
		Route::get('parametrizacion/nomina/extras', [PayrollConfigController::class, 'horasExtrasDatos']);
		Route::get('parametrizacion/nomina/incapacidades', [PayrollConfigController::class, 'incapacidadesDatos']);
		Route::get('parametrizacion/nomina/parafiscales', [PayrollConfigController::class, 'parafiscalesDatos']);
		Route::get('parametrizacion/nomina/riesgos', [PayrollConfigController::class, 'riesgosArlDatos']);
		Route::get('parametrizacion/nomina/ssocial_empresa', [PayrollConfigController::class, 'sSocialEmpresaDatos']);
		Route::get('parametrizacion/nomina/ssocial_funcionario', [PayrollConfigController::class, 'sSocialFuncionarioDatos']);



		/**/

		Route::get('paginateRetentionType', [RetentionTypeController::class, 'paginate']);
		Route::resource('retention-type', 'RetentionTypeController');

		Route::resource('fixed_asset_type', CoreFixedAssetTypeController::class);
		Route::get('paginateFixedAssetType', [FixedAssetTypeController::class, 'paginate']);

		Route::get('account-plan', [AccountPlanController::class, 'accountPlan']);

		Route::resource('professions', CoreProfessionController::class);
		Route::get('paginateProfessions', [ProfessionController::class, 'paginate']);

		Route::resource('disability-leaves', CoreDisabilityLeaveController::class);
		Route::get('paginateNoveltyTypes', [DisabilityLeaveController::class, 'paginate']);

		Route::resource('risk', CoreRiskTypesController::class);
		Route::get('paginateRiskTypes', [RiskTypesController::class, 'paginate']);

		Route::resource('documentTypes', CoreDocumentTypesController::class);
		Route::get('paginateDocumentType', [DocumentTypesController::class, 'paginate']);

		Route::resource('ingress_types', CoreIngressTypesController::class);
		Route::get('paginateIngressTypes', [IngressTypesController::class, 'paginate']);

		Route::resource('egress_types', CoreEgressTypesController::class);
		Route::get('paginateEgressTypes', [EgressTypesController::class, 'paginate']);

		Route::resource('salaryTypes', CoreSalaryTypesController::class);
		Route::get('paginateSalaryType', [SalaryTypesController::class, 'paginate']);

		Route::get('paginateVisaTypes', [VisaTypeController::class, 'paginate']);
		Route::resource('visa-types', 'VisaTypeController');

		Route::resource('work_contracts', 'WorkContractController');

		Route::get('paginatePensionFun', [PensionFundController::class, 'paginate']);
		Route::resource('pension-funds', 'PensionFundController');


		Route::put('liquidateOrActivate/{id}', [PersonController::class, 'liquidateOrActivate']);
		Route::put('liquidate/{id}', [PersonController::class, 'liquidate']);
		Route::get('liquidado/{id}', [WorkContractController::class, 'getLiquidated']);



        /** Rutas inventario dotacion rrhh */
        /*
		Route::get('/inventary-dotation-by-category',  [InventaryDotationController::class, 'indexGruopByCategory']);
		Route::get('/inventary-dotation-statistics',  [InventaryDotationController::class, 'statistics']);
		Route::get('/inventary-dotation-stock',  [InventaryDotationController::class, 'getInventary']);
		Route::post('/dotations-update/{id}',  [DotationController::class, 'update']);
		Route::get('/dotations-total-types',  [DotationController::class, 'getTotatlByTypes']);
*/

		Route::resource('dotations', 'DotationController');
		Route::resource('product-dotation-types', 'ProductDotationTypeController');

		Route::resource('inventary-dotation', 'InventaryDotationController');
	    /** end*/



		/** Rutas inventario dotacion rrhh */
		Route::get('/inventary-dotation-by-category',  [InventaryDotationController::class, 'indexGruopByCategory']);
		Route::get('/inventary-dotation-statistics',  [InventaryDotationController::class, 'statistics']);
		Route::get('/inventary-dotation-stock',  [InventaryDotationController::class, 'getInventary']);
		Route::post('/dotations-update/{id}',  [DotationController::class, 'update']);
		Route::get('/dotations-total-types',  [DotationController::class, 'getTotatlByTypes']);

		Route::get('/get-selected',  [InventaryDotationController::class, 'getSelected']);
		Route::get('/get-total-inventary',  [InventaryDotationController::class, 'getTotatInventary']);
		Route::get('/inventary-dotation-stock-epp',  [InventaryDotationController::class, 'getInventaryEpp']);
		Route::post('/dotations-approve/{id}',  [DotationController::class, 'approve']);
		Route::get('/dotations-list-product',  [DotationController::class, 'getListProductsDotation']);

		Route::get('dotations/download/{inicio?}/{fin?}', [InventaryDotationController::class, 'download']);
		Route::get('downloadeliveries/download/{inicio?}/{fin?}', [InventaryDotationController::class, 'downloadeliveries']);


		/** end*/








		/** Rutas actividades rrhh */
		Route::resource('rrhh-activity-types', 'RrhhActivityTypeController');
		Route::get('/rrhh-activity-people/{id}',  [RrhhActivityController::class, 'getPeople']);
		Route::get('/rrhh-activity/cancel/{id}',  [RrhhActivityController::class, 'cancel']);
		Route::post('/rrhh-activity-types/set',  [RrhhActivityTypeController::class, 'setState']);
		Route::resource('rrhh-activity', 'RrhhActivityController');
		/** end*/

		Route::get('/late_arrivals/statistics/{fechaInicio}/{fechaFin}', [LateArrivalController::class, 'statistics']);
		/** Rutas del m��dulo de llegadas tarde */
		Route::get('/late_arrivals/data/{fechaInicio}/{fechaFin}', [LateArrivalController::class, 'getData'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);

		/** ---------  horas extras */
		Route::get('/horas_extras/turno_rotativo/{fechaInicio}/{fechaFin}/{tipo}', [ExtraHoursController::class, 'getDataRotative'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);

		Route::resource('fixed-turns', FixedTurnController::class);
		Route::get('fixed_turn', [PersonController::class, 'fixed_turn']);
		Route::post('/fixed-turns/change-state/{id}', [FixedTurnController::class, 'changeState']);
		Route::get('/fixed-turn-hours', [FixedTurnHourController::class, 'index']);
		Route::get('/reporte/horarios/{fechaInicio}/{fechaFin}/turno_fijo', [ReporteHorariosController::class, 'fixed_turn_diaries'])->where([
			'fechaInicio' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'fechaFin'    => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		]);

		Route::get("people-all", [PersonController::class, "getAll"]);

		/** Rutas inventario dotacion rrhh */
		Route::get('/inventary-dotation-by-category',  [InventaryDotationController::class, 'indexGruopByCategory']);
		Route::get('/inventary-dotation-statistics',  [InventaryDotationController::class, 'statistics']);
		Route::get('/inventary-dotation-stock',  [InventaryDotationController::class, 'getInventary']);
		Route::post('/dotations-update/{id}',  [DotationController::class, 'update']);
		Route::get('/dotations-total-types',  [DotationController::class, 'getTotatlByTypes']);
		/** end*/

		Route::resource('dotations', 'DotationController');
		Route::resource('product-dotation-types', 'ProductDotationTypeController');

		Route::resource('inventary-dotation', 'InventaryDotationController');
		Route::resource('disciplinary_process', 'DisciplinaryProcessController');

		Route::get('/horarios/datos/generales/{semana}', [RotatingTurnHourController::class, 'getDatosGenerales']);
		Route::resource('alerts', 'AlertController');

		Route::resource('countable_incomes', CountableIncomeController::class);
		Route::resource('countable_deductions', 'CountableDeductionController');

		Route::get('countable_income', [BonificationsController::class, 'countable_income']);
		Route::resource('lunch', 'LunchControlller');
		Route::put('state-change', [LunchControlller::class, 'activateOrInactivate']);

		Route::resource('loan', 'LoanController');
		Route::get("payroll-nex-mouths", [PayrollController::class, "nextMonths"]);
		Route::get('account-plan-list', [AccountPlanController::class, 'list']);
		Route::resource('pay-vacation', 'PayVacationController');

		Route::post('travel-expense/update/{id}', [TravelExpenseController::class, 'update']);
		Route::get('travel-expense/pdf/{id}', [TravelExpenseController::class, 'pdf']);
		Route::resource('travel-expense', 'TravelExpenseController');
		Route::get('paginateDrivingLicences', [DrivingLicenseController::class, 'paginate']);

		Route::get('paginateCountries', [CountryController::class, 'paginate']);
		Route::resource('countries', 'CountryController');

		Route::get('paginateDepartment', [DepartmentController::class, 'paginate']);
		Route::get('paginateMunicipality', [MunicipalityController::class, 'paginate']);

		Route::get('paginateArl', [ArlController::class, 'paginate']);
		Route::get('paginateSeveranceFunds', [SeveranceFundController::class, 'paginate']);
		Route::get('paginateBanks', [BanksController::class, 'paginate']);
		Route::get('paginateBankAccount', [BankAccountsController::class, 'paginate']);
		Route::get('paginateHotels', [HotelController::class, 'paginate']);
		Route::get('paginateTaxis', [TaxiControlller::class, 'paginate']);
		Route::get('paginateCities', [CityController::class, 'paginate']);

		Route::resource('taxis', 'TaxiControlller');
		Route::resource('taxi-city', 'TaxiCityController');
		Route::resource('city', 'CityController');
		Route::resource('hotels', 'HotelController');
		Route::resource('drivingLicenses', 'DrivingLicenseController');


		Route::resource('third-party', 'ThirdPartyController');
		Route::resource('third-party-person', 'ThirdPartyPersonController');

		Route::get('fields-third', [ThirdPartyController::class, 'getFields']);
		Route::resource('dian-address', 'DianAddressController');
		Route::resource('ciiu-code', 'CiiuCodeController');

		Route::get('all-zones', [ZonesController::class, 'allZones']);
		Route::get('all-municipalities', [MunicipalityController::class, 'allMunicipalities']);
		Route::resource('zones', 'ZonesController');

		Route::resource('winnings-list', 'WinningListController');

		Route::resource('countable_incomes', 'CountableIncomeController');
		Route::resource('countable-incomes', 'BenefitIncomeController');
		Route::resource('countable-not-incomes', 'BenefitNotIncomeController');

		Route::get('countable_income', [BonificationsController::class, 'countable_income']);
		Route::resource('bonifications', 'BonificationsController');

		Route::get('companyData', [CompanyController::class, 'getBasicData']);
		Route::post('saveCompanyData', [CompanyController::class, 'saveCompanyData']);

		Route::get('proyeccion_pdf/{id}', [LoanController::class, 'loanpdf']);

		Route::resource('payroll-factor', 'PayrollFactorController');


		Route::put('liquidateOrActivate/{id}', [PersonController::class, 'liquidateOrActivate']);
		Route::put('liquidate/{id}', [PersonController::class, 'liquidate']);
		Route::get('liquidado/{id}', [WorkContractController::class, 'getLiquidated']);

		Route::get('get-clinical-historial', [ClinicalHistoryController::class, 'index']);
		Route::get('get-clinical-historial-detail', [ClinicalHistoryController::class, 'show']);


		/********************************************************************* */

		Route::post('create-menu',  [MenuController::class, 'store']);

		Route::post('/save-menu',  [MenuController::class, 'store']);

		Route::post("formulario/save-responses", [FormularioController::class, "saveResponse"]);
		Route::post("agendamientos-cancel", [AgendamientoController::class, "cancel"]);
		Route::post("space-cancel", [SpaceController::class, "cancel"]);
		Route::post("cancel-appointment/{id}", "AppointmentController@cancel");
		Route::post("another-formality", "AnotherFormality@store");

		Route::post("patientforwaitinglist", "CallInController@patientforwaitinglist");
		Route::post("imports", [CupController::class, "import"]);

		Route::post("cancell-agenda", [AgendamientoController::class, "cancellAgenda"]);
		Route::post("cancell-waiting-appointment", [WaitingListController::class, "cancellWaitingAppointment"]);

		Route::post("confirm-appointment", [AppointmentController::class, "confirmAppointment"]);
		Route::post("appointment-recursive", [AppointmentController::class, "appointmentRecursive"]);
		Route::post("migrate-appointment", [AppointmentController::class, "appointmentMigrate"]);
		Route::get("appointments/tomigrate", [AppointmentController::class, "toMigrate"]);


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

		Route::get("get-formulario/{formulario?}", [FormularioController::class, "getFormulario"]);
		Route::get("agendamientos/paginate", [AgendamientoController::class, "indexPaginate"]);
		Route::get("agendamientos/detail/{id}", [AgendamientoController::class, "showDetail"]);

		Route::get("people-type-custom", [PeopleTypeController::class, "indexCustom"]);
		Route::get("people-paginate", 'PersonController@indexPaginate');
		Route::resource('people', 'PersonController');


		/* PAYROLL ROUTES */
		Route::get('nomina/pago/funcionario/{identidad}', [PayrollController::class, 'getFuncionario']);
		Route::get('nomina/pago/funcionarios/{inicio?}/{fin?}', [PayrollController::class, 'payPeople']);
		Route::get('nomina/pago/{inicio?}/{fin?}', [PayrollController::class, 'getPayrollPay']);

		Route::get('payroll/overtimes/person/{id}/{dateStart}/{dateEnd}', [PayrollController::class, 'getExtrasTotales']);

		Route::get('payroll/salary/person/{id}/{dateStart}/{dateEnd}', [PayrollController::class, 'getSalario']);
		Route::get('payroll/factors/person/{id}/{dateStart}/{dateEnd}', [PayrollController::class, 'getNovedades']);
		Route::get('payroll/incomes/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getIngresos']);
		Route::get('payroll/retentions/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getRetenciones']);
		Route::get('payroll/deductions/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getDeducciones']);
		Route::get('payroll/net-pay/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getPagoNeto']);
		/* 	Route::get('payroll/social-security/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getPorcentajes']); */
		Route::get('payroll/social-security/person', [PayrollController::class, 'getPorcentajes']);
		Route::get('payroll/history/payments', [PayrollPaymentController::class, 'getPagosNomina']);


		Route::get('payroll/security/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getSeguridad']);
		Route::get('payroll/provisions/person/{id}/{fechaInicio}/{fechaFin}', [PayrollController::class, 'getProvisiones']);
		Route::post('payroll/pay', [PayrollController::class, 'store']);
		Route::post('payroll/report-electronic/{id}/{idPersonPayroll?}', [PayrollController::class, 'reportDian']);


		Route::get('/company-global', [CompanyController::class, 'getGlobal']);




		/* END PAYROLL ROUTES */
		Route::resource('deductions', 'DeductionController');

		/**
		 * PARAMETRIZACION NOMINA
		 */
		/* Route::get('params/payroll/overtimes/percentages', [PayrollOvertimeController::class, 'horasExtrasPorcentajes']); */
		Route::get('params/payroll/ssecurity_company/percentages/{id}', [PayrollParametersController::class, 'porcentajesSeguridadRiesgos']);


		Route::get("get-patient-fill/{id}", "PatientController@getPatientResend");
		Route::get("type-service/formality/{id}", "TypeServiceController@allByFormality");
		Route::get("opened-spaces", "SpaceController@index");
		Route::get("opened-spaces/{especialidad?}/{profesional?}", "SpaceController@indexCustom");
		Route::get("get-patient", "PatientController@getPatientInCall");
		Route::get("clean-info/{id?}", [AppointmentController::class, "cleanInfo"]);
		Route::get("clean-info", [AppointmentController::class, "getDataCita"]);
		Route::get("validate-info-patient", [DataInitPersonController::class, "validatePatientByLineFront"]);

		Route::resource('dependencies', 'DependencyController');
		Route::resource('rotating-turns', RotatingTurnController::class);
		Route::resource('group', GroupController::class);
		Route::resource('positions', 'PositionController');

		Route::resource("agendamientos", "AgendamientoController");
		Route::resource("appointments", "AppointmentController");
		Route::resource("patients", "PatientController");
		Route::resource("calls", "CallController");
		Route::resource("cie10s", "Cie10Controller");
		Route::resource("person", "PersonController");

		Route::resource("professionals", "ProfessionalController");

		Route::resource("company", "CompanyController");
		Route::resource("people-type", "PeopleTypeController");

		Route::resource("departments", "DepartmentController");

		Route::resource('municipalities', 'MunicipalityController');

		Route::resource("contract", "ContractController");
		Route::resource("contract-for-select", "ContractController");

		Route::post("contracts", [ContractController::class, 'store']);
		Route::get("contracts", [ContractController::class, 'paginate']);
		Route::get("contracts-for-select", [ContractController::class, 'index']);
		Route::get("contracts/{id?}", [ContractController::class, 'edit']);

		Route::resource("cities", "MunicipalityController");
		Route::resource("agreements", "AgreementController");
		Route::resource("type-documents", "TypeDocumentController");
		// Eps
		Route::resource("eps", "AdministratorController");
		Route::post("save-eps", [AdministratorController::class, 'saveEps']);
		Route::resource("administrators", "AdministratorController");
		Route::get("paginate-eps", [EpsController::class, "paginate"]);
		Route::resource("epss", "EpsController");
		// Cups
		Route::resource("cups", "CupController");
		Route::get("paginate-cup", [CupController::class, "paginate"]);


		// Specialities
		Route::get("get-specialties/{sede?}/{procedure?}", [SpecialityController::class, "index",]);
		Route::get("get-professionals/{ips?}/{speciality?}", 'PersonController@index');
		Route::resource("specialities", "SpecialityController");
		Route::get("get-specialties-by-procedure/{cup?}", "SpecialityController@byProcedure");
		Route::get("paginate-especialities", [SpecialityController::class, "paginate"]);


		Route::resource('compensation-funds', CompensationFundController::class);
		Route::resource('severance-funds', 'SeveranceFundController');


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


        /** Rutas inventario dotacion rrhh */
		Route::get('/inventary-dotation-by-category',  [InventaryDotationController::class, 'indexGruopByCategory']);
		Route::get('/inventary-dotation-statistics',  [InventaryDotationController::class, 'statistics']);
		Route::get('/inventary-dotation-stock',  [InventaryDotationController::class, 'getInventary']);
		Route::get('/get-selected',  [InventaryDotationController::class, 'getSelected']);
		Route::get('/get-total-inventary',  [InventaryDotationController::class, 'getTotatInventary']);
		Route::get('/inventary-dotation-stock-epp',  [InventaryDotationController::class, 'getInventaryEpp']);

		Route::post('/dotations-update/{id}',  [DotationController::class, 'update']);

		Route::get('/dotations-type',  [DotationController::class, 'getDotationType']);

		Route::post('/dotations-approve/{id}',  [DotationController::class, 'approve']);
		Route::get('/dotations-total-types',  [DotationController::class, 'getTotatlByTypes']);
		Route::get('/dotations-list-product',  [DotationController::class, 'getListProductsDotation']);

		Route::get('dotations/download/{inicio?}/{fin?}', [InventaryDotationController::class, 'download']);
		Route::get('downloadeliveries/download/{inicio?}/{fin?}', [InventaryDotationController::class, 'downloadeliveries']);


		/** end*/



		Route::resource("reasons", "ReasonController");
		Route::resource("method-pays", "MethodPayController");
		Route::resource("banks", "BanksController");


		//Payment Method
		Route::resource('payment_methods', PaymentMethodController::class);
		Route::get('paginatePaymentMethod', [PaymentMethodController::class, 'paginate']);

		Route::get('type_reportes', [ReporteController::class, 'getReportes']);
		Route::get('info-grafical-by-regional', [ReporteController::class, 'getDataByRegional']);
		Route::get('info-grafical-by-formality', [ReporteController::class, 'getDataByFormality']);
		Route::get('info-grafical-by-deparment', [ReporteController::class, 'getDataByDepartment']);


		//Price List
		Route::resource('price_lists', PriceListController::class);
		Route::get('paginatePriceList', [PriceListController::class, 'paginate']);

		//Benefits_plan
		Route::resource('benefits_plans', BenefitsPlanController::class);
		Route::get('paginateBenefitsPlan', [BenefitsPlanController::class, 'paginate']);

		Route::resource('arl', 'ArlController');
		Route::get('afiliation/{id}', [PersonController::class, 'afiliation']);
		Route::post('updateAfiliation/{id}', [PersonController::class, 'updateAfiliation']);

		Route::get('person/{id}', [PersonController::class, 'basicData']);
		Route::get('basicData/{id}', [PersonController::class, 'basicDataForm']);
		Route::post('updatebasicData/{id}', [PersonController::class, 'updateBasicData']);

		Route::get('salary/{id}', [PersonController::class, 'salary']);
		Route::post('salary', [PersonController::class, 'updateSalaryInfo']);

		Route::resource('work_contracts', 'WorkContractController');

		Route::post('mycita', function () {
			return response()->json(request()->all());
		});
		Route::post('change-company-work/{id}', [PersonController::class, 'changeCompanyWorked']);
		Route::post('person/set-companies/{personId}', [PersonController::class, 'setCompaniesWork']);
		Route::get('person/get-companies/{personId}', [PersonController::class, 'personCompanies']);

		//se ejecuta al crear
		Route::resource("subcategory", "SubcategoryController");
		Route::post("subcategory-variable/{id}", "SubcategoryController@deleteVariable");

		//se ejecuta al crear
		Route::get("subcategory-field/{id}", "SubcategoryController@getField");

		//se ejecuta al editar
		Route::get("subcategory-edit/{id?}/{idSubcategoria}", "SubcategoryController@getFieldEdit");
		Route::resource("subcategory", "SubcategoryController");
        Route::resource("category", "CategoryController");
		Route::resource("product-accounting", "ProductAccountingPlanController");
       
		Route::resource("product", "ProductController");
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
	Route::post('update-appointment-by-globho', [ServiceGlobhoController::class, 'updateStateByGlobhoId']);
	Route::get("get-appointments-by-globho-id", [ServiceGlobhoController::class, "getInfoByGlobhoId"]);
	Route::post('create-appoinment', [AppointmentController::class, 'createFromGlobho']);
});
