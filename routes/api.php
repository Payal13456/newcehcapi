<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SpecializationController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\PrivacyPolicyController;
use App\Http\Controllers\API\HospitalController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\StateController;
use App\Http\Controllers\API\ScheduleController;

use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PromoController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\CasesummaryController;
use App\Http\Controllers\API\DiagnosisController;
use App\Http\Controllers\API\PrescriptionController;
use App\Http\Controllers\API\RazorPaymentController;
use App\Http\Controllers\API\PushNotificationController;
use App\Http\Controllers\API\AgoraVideoController;



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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'auth'], function () {

	Route::get('test-mail',function(){
	        $fromEmail = env('MAIL_FROM_ADDRESS', 'appointment@cehcchennai.com');
               $fromName = env('MAIL_USERNAME', 'CEHC App');
		Mail::send(
                    'emails.test',
                    [],
                    function ($message) use($fromEmail,$fromName) {
                        $message->to('testmail@mailinator.com')->subject("This is Test Subject");
                        $message->from($fromEmail, $fromName);  
                    }
                );
                echo "MAIL SENT";
	});
	Route::get('/privacy-policy',[PrivacyPolicyController::class, 'privacyPolicyDoctor']);
		Route::get('/privacy-policy-doctor',[PrivacyPolicyController::class, 'privacyPolicyDoctor']);
		Route::get('/privacy-policy-patient',[PrivacyPolicyController::class, 'privacyPolicyPatient']);
        
        Route::get('paywithrazorpay', [RazorPaymentController::class, 'payWithRazorpay'])->name('paywithrazorpay');

        Route::get('dashboard-count', [AppointmentController::class, 'dashboardCount'])->name('dashboardCount');

        Route::get('notification-list',[PushNotificationController::class,'index']);
        Route::get('disableSchedule/{doctor_id}/{day}',[ScheduleController::class,'disableSchedule']);

        Route::get('readNotification/{id}',[PushNotificationController::class,'readNotification']);

        Route::post('payment', [RazorPaymentController::class, 'payment'])->name('payment');

        Route::get('refund/{id}', [RazorPaymentController::class, 'refund'])->name('refund');
        
        Route::get('getStates',[SpecializationController::class,'getStates']);
        Route::post('verifyAccessToken',[AuthController::class,'verifyAccessToken']);
        Route::post('login',[AuthController::class,'login']);
        Route::post('loginByNumber',[AuthController::class,'loginWithNumber']);
        Route::post('register',[AuthController::class,'register']);
        Route::post('signup',[AuthController::class,'signup']);
        Route::post('forgot_password', [AuthController::class,'forgot_password']);
        Route::post('resetPassword',[AuthController::class,'reset_password']);

        Route::get('getAllRoles',[AuthController::class,'getAllRoles']);
        Route::get('getAllPermissions',[AuthController::class,'getAllPermissions']);
        Route::get('getRolePermission/{id}',[AuthController::class,'getRolePermission']);

        //Specilization Route Here  
        Route::resources(['specializations' => SpecializationController::class]);
        Route::put('statusUpdate/{id}',[UserController::class,'statusUpdate']);
        // after login - Auth Api

        Route::get('pendingList',[UserController::class , 'pendingList']);

        Route::get('cancelledAppointment/{id}',[AppointmentController::class , 'cancelledAppointment']);

        Route::get('changeStatus/{id}/{status}',[UserController::class , 'changeStatus']);

        Route::get('upcommingAppointment/{id}',[AppointmentController::class , 'upcommingAppointment']);
        Route::resources(['states'=>StateController::class]);

        Route::post('/fcm-token', [UserController::class, 'updateToken'])->name('fcmToken');
        
        Route::resource('roles', RoleController::class);
        Route::get('states',[UserController::class,'states']);
        Route::get('/address', [UserController::class,'address']);
        Route::group(['middleware' => 'auth:api'], function() { 

        
        Route::post('/logout',[AuthController::class,'logout'])->name('logout');
        Route::resources(['users' => UserController::class]);
        Route::get('getRolewiseAllUser',[UserController::class,'getRolewiseAllUser']);

        Route::get('medicines/{keyword}',[PrescriptionController::class,'medicines']);

        Route::post('changePassword','App\Http\Controllers\API\AuthController@change_password');
        Route::put('profile_update/{id}',[UserController::class,'profileUpdate']);
        Route::put('userBlock/{id}',[UserController::class,'userBlockUnblock']);
        Route::get('getProfile/{id}',[UserController::class,'getProfileData']);
         //blog Routes
        Route::resources(['blogs'=>BlogController::class]);
        Route::put('blogStatusUpdate/{id}',[BlogController::class,'blogStatusUpdate']);
	
        Route::post('sendHelp',[UserController::class,'sendHelp']);

        //Blog Category
        Route::get('categorys',[BlogController::class,'listCategorys']);
        Route::get('employeeDetails/{id}',[UserController::class,'employeeDetails']);
        Route::post('categorys',[BlogController::class,'createCategory']);
        Route::get('categorys/{id}',[BlogController::class,'getCategorys']);
        Route::get('getPatients/{id}',[PatientController::class,'getPatients']);
        Route::put('categorys/{id}',[BlogController::class,'updateCategory']);
        Route::delete('categorys/{id}',[BlogController::class,'removeCategorys']);
        Route::post('addPatient',[PatientController::class,'addPatient']);
        Route::put('updatePatient/{id}',[PatientController::class,'updatePatient']);
        //patientStatusUpdate
        Route::resources(['patients' => PatientController::class]);
        Route::put('patientStatusUpdate/{id}',[PatientController::class,'patientStatusUpdate']);
        

        Route::resources(['faqs'=>FaqController::class]);
        Route::put('faqStatusUpdate/{id}',[FaqController::class,'faqStatusUpdate']);

        Route::resources(['policies'=>PrivacyPolicyController::class]);
        Route::put('policiesStatusUpdate/{id}',[PrivacyPolicyController::class,'policiesStatusUpdate']);
        //plans
        Route::resources(['plans'=>PlanController::class]);
        Route::put('planStatusUpdate/{id}',[PlanController::class,'planStatusUpdate']);

        //Hospitals
        
        Route::get('check-user-status',[UserController::class , 'checkUserStatus']);
        
        Route::resources(['hospitals'=>HospitalController::class]);

        Route::resources(['schedule'=>ScheduleController::class]);

        Route::get('/doctorsList',[ScheduleController::class,'doctorsList'])->name('schedule.doctorslist');
        Route::post('/doctorSchedule',[ScheduleController::class,'doctorSchedule'])->name('doctor.schedule');

        Route::post('/appointmentScheduleDoctor',[AppointmentController::class,'appointmentScheduleDoctor'])->name('appointmentScheduleDoctor');

        Route::get('/bookingHistory/{patient_id}',[AppointmentController::class,'bookingHistory'])->name('bookingHistory');

        Route::get('/scheduleDetails/{doctor_id}',[ScheduleController::class,'scheduleDetails'])->name('scheduleDetails');
        Route::get('/getScheduleStatus/{doctor_id}',[ScheduleController::class,'getScheduleStatus'])->name('getScheduleStatus');

        Route::get('/appointmentlist/{doctor_id}',[AppointmentController::class,'appointmentlist'])->name('appointmentlist');

        Route::get('/appointmentHistory/{doctor_id}',[AppointmentController::class,'appointmentHistory'])->name('appointmentHistory');

        Route::get('notificationCount', [PushNotificationController::class , 'notificationCount']);

        Route::get('/patientAppointment/{patient_id}/{doctor_id}',[AppointmentController::class,'patientAppointment'])->name('patientAppointment');

        Route::get('/patientList/{doctor_id}',[AppointmentController::class,'patientList'])->name('patientList');
        
        Route::put('scheduleStatusUpdate/{id}',[ScheduleController::class,'scheduleStatusUpdate']);

        Route::put('hospitalstatusUpdate/{id}',[HospitalController::class,'statusUpdate']);
        Route::put('hospitalBlock/{id}',[HospitalController::class,'hospitalBlockUnblock']);

        Route::resources(['notifications'=>NotificationController::class]);

        Route::resources(['promocodes'=>PromoController::class]);

        Route::post('createSchedule',[ScheduleController::class,'createSchedule']);
        Route::put('promocodeStatusUpdate/{id}',[PromoController::class,'promocodeStatusUpdate']);

        Route::resources(['appointment'=>AppointmentController::class]);
        Route::resources(['casesummary'=>CasesummaryController::class]);
        Route::resources(['diagnosis'=>DiagnosisController::class]);
        Route::resources(['prescription'=>PrescriptionController::class]);

        Route::get('case-summary/{appointment_id}',[CasesummaryController::class,'summary']);
        Route::get('diagnosis-list/{appointment_id}',[DiagnosisController::class,'diagnosisList']);
        Route::get('prescription-details/{appointment_id}',[PrescriptionController::class,'prescriptionDetails']);
        Route::get('search-medicine/{keyword}',[PrescriptionController::class,'searchMedicine']);
        Route::get('get-medicine',[PrescriptionController::class,'getMedicine']);

        Route::put('appointmentStatusUpdate/{id}',[AppointmentController::class,'appointmentStatusUpdate']);

        Route::get('doctorsBySpecification/{id}', [UserController::class,'doctorsBySpecification']);

        Route::get('doctorsByDate/{id}/{date}', [ScheduleController::class,'doctorsByDate']);

        Route::put('cancelBooking/{id}',[AppointmentController::class,'cancelBooking']);

        Route::post('createPrescription',[PrescriptionController::class,'createPrescription']);

        Route::post('createOptics',[PrescriptionController::class,'createOptics']);

        Route::post('addOptics',[PrescriptionController::class,'addOptics']);

        Route::post('createDiagnosis',[DiagnosisController::class,'createDiagnosis']);
        
        Route::post('scheduleDates',[ScheduleController::class,'scheduleDates']);

        Route::get('/opticsList/{id}', [PrescriptionController::class,'opticsList']);

        Route::get('/agora-chat', [AgoraVideoController::class,'index']);
        Route::get('/payment-details/{payment_id}',[RazorPaymentController::class,'refundDetails']);
        Route::get('/end-consultation/{id}', [AppointmentController::class,'endConsultation']);
        Route::post('agora-token', [AgoraVideoController::class,'token']);
        Route::post('/agora/call-user', [AgoraVideoController::class,'callUser']);
	Route::get('/payment-detail',[RazorPaymentController::class, 'paymentDetails']);

        // Route::get('/send-pushnotification', [PushNotificationController::class, 'sendPush'])->name('send.push');


        // Route::get('/', function() {
        //     return response()->json(request()->user());
        // });
    // });
});
});



