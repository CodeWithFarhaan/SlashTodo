<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/signup', 'Home::Signup');
$routes->post('/signup', 'Home::Signup');
$routes->get('/login', 'Home::Login');
$routes->post('/login', 'Home::Login');
$routes->get('/dashboard', 'Home::Dashboard');
$routes->get('/logout', 'Home::Logout');
$routes->post('/logout', 'Home::Logout');

$routes->post('/update-user', 'Home::updateUser');
$routes->get('/delete-user/(:num)', 'Home::deleteUser/$1');