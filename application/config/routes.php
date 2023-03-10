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
$route['add_multi_project_members'] = 'user/addMultiProjectMembers';

$route['add_company'] = 'company/addCompany';
$route['update_company'] = 'company/updateCompany';
$route['delete_company'] = 'company/deleteCompany';

$route['project_list'] = 'project';
$route['add_project'] = 'project/addProject';
$route['update_project'] = 'project/updateProject';
$route['delete_project'] = 'project/deleteProject';

$route['task_section_list'] = 'task/taskTabList';
$route['add_task_section'] = 'task/addTaskTab';
$route['add_multi_task_section'] = 'task/addMultiTaskTab';
$route['update_task_section'] = 'task/updateTaskTab';
$route['delete_task_section'] = 'project/deleteTaskTab';

$route['add_task'] = 'task/addTask';
$route['add_multi_task'] = 'task/addMultiTask';
$route['update_task'] = 'task/updateTask';
$route['delete_task'] = 'project/deleteTask';
