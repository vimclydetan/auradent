<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Dashboard (Dito magse-switch ang view depende sa role)


// Admin Routes
$routes->group('admin', ['filter' => 'authCheck:admin'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    // Patients
    $routes->get('patients', 'AdminController::patients');
    $routes->post('save-patient', 'AdminController::save_patient');

    // Services
    $routes->get('services', 'ServiceController::index');
    $routes->post('services/store', 'ServiceController::store');

    // ITO ANG IMPORTANTE:
    // Huwag nang lagyan ng 'admin/' sa unahan dahil nasa loob na ng group
    $routes->post('services/update/(:num)', 'ServiceController::update/$1');

    $routes->get('services/delete/(:num)', 'ServiceController::delete/$1');

    $routes->get('appointments/searchPatients', 'AppointmentController::searchPatients');

    $routes->get('appointments', 'AppointmentController::index');
    $routes->post('appointments/store', 'AppointmentController::store');
    $routes->get('appointments/status/(:num)/(:segment)', 'AppointmentController::updateStatus/$1/$2');
    $routes->post('appointments/reschedule', 'AppointmentController::reschedule');
    $routes->get('calendar', 'Admin\CalendarController::index');
    $routes->get('calendar/events', 'Admin\CalendarController::getEvents');

    // DENTIST ROUTES
    $routes->get('dentists', 'Admin\DentistController::index');
    $routes->post('dentists/store', 'Admin\DentistController::store');

    // TANGGALIN ang 'admin/' dito dahil nasa loob na ng group:
    $routes->get('dentists/view/(:num)', 'Admin\DentistController::view/$1');

    // Para sa Edit at Delete sa susunod:
    $routes->get('dentists/edit/(:num)', 'Admin\DentistController::edit/$1');
    $routes->get('dentists/delete/(:num)', 'Admin\DentistController::delete/$1');
    $routes->get('admin/patients/search', 'Admin\Patients::search', ['filter' => 'auth']);
});

$routes->group('patient', ['namespace' => 'App\Controllers\Patient', 'filter' => 'authCheck:patient'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('appointments', 'AppointmentController::index');
    $routes->post('appointments/store', 'AppointmentController::store');
    $routes->post('appointments/reschedule', 'AppointmentControllerF::reschedule');
    $routes->get('appointments/status/(:num)/(:any)', 'AppointmentController::status/$1/$2');


    // Sa loob ng iyong patient group o kahit sa labas
    $routes->post('save-medical-history', 'DashboardController::saveMedicalHistory');
});

// --- RECEPTIONIST ROUTES ---
$routes->group('receptionist', ['namespace' => 'App\Controllers\Receptionist', 'filter' => 'authCheck:receptionist'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Appointment Management Group
    $routes->group('appointments', function ($routes) {
        // Main List: /receptionist/appointments
        $routes->get('/', 'AppointmentController::index');

        // AJAX Patient Search: /receptionist/appointments/searchPatients
        $routes->get('searchPatients', 'AppointmentController::searchPatients');

        // Save New Appointment: /receptionist/appointments/store
        $routes->post('store', 'AppointmentController::store');

        // Reschedule Appointment: /receptionist/appointments/reschedule
        $routes->post('reschedule', 'AppointmentController::reschedule');

        // Update Status: /receptionist/appointments/status/9/Confirmed
        // (:num) para sa ID, (:segment) para sa status string
        $routes->get('status/(:num)/(:segment)', 'AppointmentController::updateStatus/$1/$2');
    });

    $routes->group('walkin', function ($routes) {
        // Main Form: auradent.com/receptionist/walkin
        $routes->get('/', 'WalkinController::index');

        // Process Save: auradent.com/receptionist/walkin/store
        $routes->post('store', 'WalkinController::store');
    });

    $routes->group('patients', function ($routes) {
        $routes->get('/', 'PatientsController::index');
        $routes->get('view/(:num)', 'PatientsController::view/$1');
        $routes->get('history/(:num)', 'PatientsController::history/$1');
        $routes->get('edit/(:num)', 'PatientsController::edit/$1');
    });
    $routes->group('billing', function ($routes) {
        // Main Form: /receptionist/billing
        $routes->get('/', 'BillingController::index');

        // Process Save: /receptionist/billing/save
        $routes->post('save', 'BillingController::save');
        $routes->get('history/(:num)', 'BillingController::history/$1'); // Eto yung bago
        // Optional: Kung gusto mong sa BillingController din dadaan ang search
        $routes->get('searchPatients', '/AppointmentController::searchPatients');
        $routes->get('getActiveDetails/(:num)', 'BillingController::getActiveDetails/$1');
    });
});


$routes->get('reset-admin', 'AuthController::reset_admin');
