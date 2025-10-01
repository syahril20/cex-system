<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
| Please see the user guide for complete details:
| https://codeigniter.com/userguide3/general/routing.html
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
*/

$route['default_controller'] = 'welcome';
$route['404_override'] = 'welcome/error_404';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';

// Dashboard
$route['dashboard'] = 'welcome/dashboard';

// Order routes
$route['order'] = 'order';
$route['order/create'] = 'order/order_form';
$route['order/do_create'] = 'order/create';
$route['order/upload_form'] = 'order/upload_form';
$route['order/do_uploads'] = 'order/do_upload';

// User
$route['user'] = 'user';
$route['user/edit'] = 'user/user_edit';
$route['user/do_edit/(:num)'] = 'user/do_edit/$1';
$route['user/delete/(:num)'] = 'user/delete/$1';

// Roles 
$route['role'] = 'role';
$route['role/create'] = 'role/role_form';
$route['role/do_create'] = 'role/create';
$route['role/edit/(:num)'] = 'role/role_edit/$1';
$route['role/do_edit/(:num)'] = 'role/do_edit/$1';
$route['role/delete/(:num)'] = 'role/delete/$1';

// 