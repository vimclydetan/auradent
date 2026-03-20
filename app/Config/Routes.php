<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Dashboard (Dito magse-switch ang view depende sa role)
$routes->get('/dashboard', 'DashboardController::index');

// Admin Routes
$routes->group('admin', function($routes) {
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
    $routes->get('appointments/calendar', 'AppointmentController::calendar');
    $routes->get('appointments/getCalendarEvents', 'AppointmentController::getCalendarEvents');
    
     
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
$routes->get('reset-admin', 'AuthController::reset_admin');