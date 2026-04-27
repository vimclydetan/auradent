<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
// PUBLIC ROUTES
$routes->get('/', 'Home::index');

$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->post('register', 'AuthController::register');
$routes->get('reset-admin', 'AuthController::reset_admin');

// Email link: /appointments/confirm/{id}/{token}
$routes->get('appointments/confirm/(:num)/(:segment)', 'Patient\AppointmentConfirmationController::confirm/$1/$2');

// Process form submission
$routes->post('appointments/confirm/process', 'Patient\AppointmentConfirmationController::processConfirmation');

// ==============================
// ADMIN ROUTES
// ==============================
$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter' => 'authCheck:admin'
], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/stats', 'DashboardController::getStats');
    $routes->get('dashboard/appointments', 'DashboardController::getAppointmentsByDate');
    $routes->get('dashboard/today', 'DashboardController::getTodaySchedule');

    // Patients
    $routes->group('patient', function ($routes) {
        $routes->get('/', 'PatientsController::index');
        $routes->post('store', 'PatientsController::store');
        $routes->get('search', 'PatientsController::search');
    });

    // Services
    $routes->group('services', function ($routes) {
        $routes->get('/', 'ServiceController::index');
        $routes->post('store', 'ServiceController::store');
        $routes->post('update/(:num)', 'ServiceController::update/$1');
        $routes->get('delete/(:num)', 'ServiceController::delete/$1');
    });

    // Appointments
    $routes->group('appointments', function ($routes) {
        $routes->get('/', 'AppointmentController::index');
        $routes->get('searchPatients', 'AppointmentController::searchPatients');
        $routes->get('status/(:num)/(:segment)', 'AppointmentController::updateStatus/$1/$2');
        $routes->get('view/(:num)', 'AppointmentController::view/$1');
        $routes->get('history/(:num)', 'AppointmentController::patientHistory/$1');
    });

    // Calendar
    $routes->group('calendar', function ($routes) {
        $routes->get('/', 'CalendarController::index');
        $routes->get('events', 'CalendarController::getEvents');
    });

    // Dentists
    $routes->group('dentists', function ($routes) {
        $routes->get('/', 'DentistController::index');
        $routes->post('store', 'DentistController::store');
        $routes->get('view/(:num)', 'DentistController::view/$1');
        $routes->get('edit/(:num)', 'DentistController::edit/$1');
        $routes->post('update/(:num)', 'DentistController::update/$1');
        $routes->get('activate/(:num)', 'DentistController::activate/$1');
        $routes->get('deactivate/(:num)', 'DentistController::deactivate/$1');
    });
    $routes->get('user-logs', 'UserLogsController::index');
    $routes->get('user-logs/export', 'UserLogsController::export');

    $routes->post('user-logs/verify-export', 'UserLogsController::verifyExportPassword');
});


// ==============================
// PATIENT ROUTES
// ==============================
$routes->group('patient', [
    'namespace' => 'App\Controllers\Patient',
    'filter' => 'authCheck:patient'
], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('appointments', 'AppointmentController::index');
    $routes->post('save-personal-info', 'DashboardController::savePersonalInfo');
    $routes->post('save-medical-history', 'DashboardController::saveMedicalHistory');

    $routes->group('appointments', function ($routes) {
        $routes->get('/', 'AppointmentController::index');
        $routes->post('store', 'AppointmentController::store');
        $routes->post('reschedule', 'AppointmentController::reschedule');
        $routes->get('status/(:num)/(:segment)', 'AppointmentController::status/$1/$2');
        $routes->get('check-availability', 'AppointmentController::checkAvailability');
        $routes->get('view/(:num)', 'AppointmentController::view/$1');
        $routes->post('request-cancellation/(:num)', 'AppointmentController::requestCancellation/$1');
    });
});


// ==============================
// RECEPTIONIST ROUTES
// ==============================
$routes->group('receptionist', [
    'namespace' => 'App\Controllers\Receptionist',
    'filter' => 'authCheck:receptionist'
], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    $routes->group('appointments', function ($routes) {
        $routes->get('/', 'AppointmentController::index');
        $routes->get('searchPatients', 'AppointmentController::searchPatients');
        $routes->post('store', 'AppointmentController::store');
        $routes->post('reschedule', 'AppointmentController::reschedule');

        // ✅ EXISTING: GET for link clicks (browser navigation)
        $routes->get('status/(:num)/(:segment)', 'AppointmentController::updateStatus/$1/$2');

        // ✅ ADD THIS: POST for AJAX calls (JavaScript fetch)
        $routes->post('update-status/(:num)/(:segment)', 'AppointmentController::updateStatus/$1/$2');

        $routes->get('history/(:num)', 'AppointmentController::patientHistory/$1');
        $routes->get('checkAvailability', 'AppointmentController::checkAvailability');
        $routes->post('cancel', 'AppointmentController::cancel');
        $routes->post('reject', 'AppointmentController::reject');
        $routes->post('deny-cancellation', 'AppointmentController::denyCancellation');
        $routes->post('handle-cancellation-request', 'AppointmentController::handleCancellationRequest');
        $routes->post('mark-no-show/(:num)', 'AppointmentController::updateStatus/$1/no-show');
        $routes->get('rebooking-fee-preview','AppointmentController::getRebookingFeePreview');
    });

    $routes->group('walkin', function ($routes) {
        $routes->get('/', 'WalkinController::index');
        $routes->post('store', 'WalkinController::store');
    });

    $routes->group('patients', function ($routes) {
        $routes->get('/', 'PatientsController::index');
        $routes->get('view/(:num)', 'PatientsController::view/$1');
        $routes->get('history/(:num)', 'PatientsController::history/$1');
        $routes->get('edit/(:num)', 'PatientsController::edit/$1');
    });

    $routes->group('billing', function ($routes) {
        $routes->get('/', 'BillingController::index');
        $routes->post('save', 'BillingController::save');
        $routes->get('history/(:num)', 'BillingController::history/$1');
        $routes->get('searchPatients', 'AppointmentController::searchPatients');
        $routes->get('details/(:num)', 'BillingController::getActiveDetails/$1');
    });

    $routes->group('calendar', function ($routes) {
        $routes->get('/', 'CalendarController::index');
        $routes->get('events', 'CalendarController::getEvents');
    });
});


// ==============================
// DENTIST ROUTES (NEW)
// ==============================
$routes->group('dentist', [
    'namespace' => 'App\Controllers\Dentist',
    'filter' => 'authCheck:dentist'
], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');

    $routes->group('appointments', function ($routes) {
        $routes->get('/', 'AppointmentController::index');
        $routes->get('get-data/(:num)', 'AppointmentController::getAppointmentData/$1');
        $routes->post('finalize', 'AppointmentController::finalizeTreatment');
        $routes->post('save-chart', 'AppointmentController::saveChart');
    });

    $routes->group('patient', function ($routes) {
        $routes->get('/', 'PatientsController::index');
        $routes->get('view/(:num)', 'PatientsController::view/$1');
        $routes->get('medical_history/(:num)', 'PatientController::viewHistory/$1');
    });

    $routes->group('calendar', function ($routes) {
        $routes->get('/', 'CalendarController::index');
        $routes->get('events', 'CalendarController::getEvents');
    });
});
