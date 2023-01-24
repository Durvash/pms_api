<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = true;


////// MY CUSTOM ROUTES HERE...
$route['signup'] = 'user/signup';
$route['confirm_account/(:any)'] = 'user/confirmAccount/$1';
$route['update_account'] = 'user/updateAccount';
$route['login'] = 'user/login';
$route['logout'] = 'user/logout';

$route['add_company'] = 'company/addCompany';
$route['update_company'] = 'company/updateCompany';
$route['delete_company'] = 'company/deleteCompany';

$route['add_project'] = 'project/addProject';
$route['update_project'] = 'project/updateProject';
$route['delete_project'] = 'project/deleteProject';
