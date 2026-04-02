<?php

use Core\Router;

/** @var Router $router */

// Public Routes
$router->get('/', 'Public\HomeController@index');
$router->get('/login', 'Auth\LoginController@showForm', ['guest']);
$router->post('/login', 'Auth\LoginController@login', ['guest']);
$router->get('/register', 'Auth\RegisterController@showForm', ['guest']);
$router->post('/register', 'Auth\RegisterController@register', ['guest']);
$router->post('/logout', 'Auth\LoginController@logout', ['auth']);

// Password Reset
$router->get('/forgot-password', 'Auth\PasswordController@showForgot', ['guest']);
$router->post('/forgot-password', 'Auth\PasswordController@sendResetLink', ['guest']);
$router->get('/reset-password', 'Auth\PasswordController@showReset', ['guest']);
$router->post('/reset-password', 'Auth\PasswordController@reset', ['guest']);

// Dashboard
$router->get('/dashboard', 'DashboardController@index', ['auth']);

// Author Routes
$router->get('/author/submissions', 'Author\SubmissionController@index', ['auth', 'role:author']);
$router->get('/author/submissions/create', 'Author\SubmissionController@create', ['auth', 'role:author']);
$router->post('/author/submissions', 'Author\SubmissionController@store', ['auth', 'role:author']);
$router->get('/author/submissions/{id}/edit', 'Author\SubmissionController@edit', ['auth', 'role:author']);
$router->post('/author/submissions/{id}/upload', 'Author\SubmissionController@upload', ['auth', 'role:author']);
$router->post('/author/submissions/{id}/finalize', 'Author\SubmissionController@finalize', ['auth', 'role:author']);

// Editor Routes
$router->get('/editor/submissions', 'Editor\SubmissionController@index', ['auth', 'role:editor,chair']);
$router->get('/editor/submissions/{id}', 'Editor\SubmissionController@show', ['auth', 'role:editor,chair']);
$router->get('/editor/submissions/{id}/assign', 'Editor\AssignmentController@create', ['auth', 'role:editor,chair']);
$router->post('/editor/submissions/{id}/assign', 'Editor\AssignmentController@store', ['auth', 'role:editor,chair']);
$router->get('/editor/submissions/{id}/decision', 'Editor\DecisionController@create', ['auth', 'role:editor,chair']);
$router->post('/editor/submissions/{id}/decision', 'Editor\DecisionController@store', ['auth', 'role:editor,chair']);
$router->post('/editor/submissions/{id}/ai-analyze', 'Editor\AiAnalysisController@analyze', ['auth', 'role:editor,chair']);

// Reviewer Routes
$router->get('/reviewer/assignments', 'Reviewer\AssignmentController@index', ['auth', 'role:reviewer']);
$router->get('/reviewer/reviews/{id}/form', 'Reviewer\ReviewController@create', ['auth', 'role:reviewer']);
$router->post('/reviewer/reviews/{id}/store', 'Reviewer\ReviewController@store', ['auth', 'role:reviewer']);

// Public Proceedings
$router->get('/proceedings', 'Public\ProceedingController@index');
$router->get('/proceedings/volume/{id}', 'Public\ProceedingController@volume');
$router->get('/article/{id}', 'Public\ArticleController@show');
$router->get('/article/{id}/download', 'Public\ArticleController@download');
$router->get('/article/{id}/cite', 'Public\ArticleController@cite');
$router->get('/search', 'Public\SearchController@index');
$router->get('/oai', 'Public\OaiPmhController@handle');

// Admin Routes
$router->get('/admin', 'Admin\DashboardController@index', ['auth', 'role:super_admin']);
$router->get('/admin/users', 'Admin\UserController@index', ['auth', 'role:super_admin']);
$router->get('/admin/reports', 'Admin\ReportController@dashboard', ['auth', 'role:super_admin,chair']);
$router->get('/admin/audit-logs', 'Admin\AuditLogController@index', ['auth', 'role:super_admin']);
